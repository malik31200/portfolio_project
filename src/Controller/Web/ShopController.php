<?php

namespace App\Controller\Web;

use App\Entity\SessionBook;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class ShopController extends AbstractController
{
    private function getAvailableBooks(): array
    {
        return [
            10 => [
                'name' => 'Carnet 10 séances',
                'totalSessions' => 10,
                'price' => 200.00,
                'description' => 'Idéal pour commencer',
                'savings' => '10% d\'économie'
            ],
            20 => [
            'name' => 'Carnet 20 séances',
                'totalSessions' => 20,
                'price' => 380.00,
                'description' => 'Le plus populaire',
                'savings' => '15% d\'économie'
            ],
            30 => [
                'name' => 'Carnet 30 séances',
                'totalSessions' => 30,
                'price' => 520.00,
                'description' => 'Le meilleur rapport qualité/prix',
                'savings' => '20% d\'économie'
            ]
        ];
    }

    #[Route('/shop', name: 'app_shop')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        // Use the centralized method
        $sessionBooks = array_values($this->getAvailableBooks());
           
        return $this->render('web/shop.html.twig', [
            'sessionBooks' => $sessionBooks
        ]);
    }

    #[Route('/shop/checkout', name: 'app_shop_checkout', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function checkout(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Retrieve uniquely the totalSessions from the form
        $totalSessions = (int) $request->request->get('totalSessions');

        // Valide and retrieve the truth data from our source of truth
        $availableBooks = $this->getAvailableBooks();

        if (!isset($availableBooks[$totalSessions])) {
            $this->addFlash('error', 'Carnet invalide.');
            return $this->redirectToRoute('app_shop');
        }

        // Retrieve secure data (not modifiable by the client)
        $book = $availableBooks[$totalSessions];
        $name = $book['name'];
        $price = $book['price'];

        // Build the URL to database
        $baseUrl = $request->getSchemeAndHttpHost();

        // Initialize Stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        try {
            // Create a Stripe Checkout payment session
        $checkoutSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int)($price * 100),
                    'product_data' => [
                        'name' => $name,
                        'description' => "Carnet de $totalSessions séances de sport",
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $baseUrl . '/shop/success?totalSessions=' . $totalSessions . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $baseUrl . '/shop',
            'customer_email' => $user->getEmail(),
        ]);

     // Redirect to Stripe Checkout
    return $this->redirect($checkoutSession->url);
    } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur lors de la création de la session de paiement : ' . $e->getMessage());
        return $this->redirectToRoute('app_shop');
    }
}
    #[Route('/shop/success', name: 'app_shop_success')]
    #[IsGranted('ROLE_USER')]
    public function success(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        
        // Retrieve the parameterss
        $sessionId = $request->query->get('session_id');
        $totalSessions = (int) $request->query->get('totalSessions');

        if (!$sessionId || !$totalSessions) {
            $this->addFlash('error', 'Paramètres manquants.');
            return $this->redirectToRoute('app_shop');
        }
// Retrieve REAL data from our source of truth
    $availableBooks = $this->getAvailableBooks();
    
    if (!isset($availableBooks[$totalSessions])) {
        $this->addFlash('error', 'Carnet invalide.');
        return $this->redirectToRoute('app_shop');
    }

    $name = $availableBooks[$totalSessions]['name'];
    $price = $availableBooks[$totalSessions]['price'];

    // Check that the payment is valid with Stripe
    Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    
    try {
        $session = StripeSession::retrieve($sessionId);
        
        if ($session->payment_status !== 'paid') {
            $this->addFlash('error', 'Le paiement n\'a pas été complété.');
            return $this->redirectToRoute('app_shop');
        }

         // Verify that the amount paid corresponds to the actual price.
        $expectedAmount = (int)($price * 100); // Stripe uses pennies
        if ($session->amount_total !== $expectedAmount) {
            $this->addFlash('error', 'Le montant du paiement ne correspond pas au prix du carnet.');
            return $this->redirectToRoute('app_shop');
        }

        // Check if this payment has not already been processed
        $existingPayment = $em->getRepository(Payment::class)->findOneBy([
            'stripePaymentId' => $sessionId
        ]);

        if ($existingPayment) {
            $this->addFlash('info', 'Ce carnet a déjà été ajouté à votre compte.');
            return $this->redirectToRoute('app_dashboard');
        }

        // Create the SessionBook
        $sessionBook = new SessionBook();
        $sessionBook->setUser($user);
        $sessionBook->setName($name);
        $sessionBook->setTotalSessions($totalSessions);
        $sessionBook->setRemainingSessions($totalSessions);
        $sessionBook->setPrice($price);
        $sessionBook->setCreatedAt(new \DateTimeImmutable());
        $sessionBook->setExpiresAt((new \DateTimeImmutable())->modify('+1 year'));

        // Create the payment
        $payment = new Payment();
        $payment->setUser($user);
        $payment->setSessionBook($sessionBook);
        $payment->setAmount($price);
        $payment->setStripePaymentId($sessionId);
        $payment->setCreatedAt(new \DateTimeImmutable());

        // Save to base
        $em->persist($sessionBook);
        $em->persist($payment);
        $em->flush();

        $this->addFlash('success', "Félicitations ! Votre $name a été ajouté à votre compte. Vous avez maintenant $totalSessions séances disponibles !");
        
        return $this->redirectToRoute('app_dashboard');

    } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur lors de la vérification du paiement : ' . $e->getMessage());
        return $this->redirectToRoute('app_shop');
    }
    }
}