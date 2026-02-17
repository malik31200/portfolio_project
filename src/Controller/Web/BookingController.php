<?php

namespace App\Controller\Web;

use App\Entity\Payment;
use App\Entity\Registration;
use App\Entity\Session;
use App\Entity\SessionBook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class BookingController extends AbstractController
{
    #[Route('/booking/reserve/{id}', name: 'booking_reserve', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function reserve(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Retrieve the session
        $session = $em->getRepository(Session::class)->find($id);

        if (!$session) {
            $this->addFlash('error', 'Session non trouvée');
            return $this->redirectToRoute('app_sessions');
        }

        // Check if the session is in the future
        if ($session->getStartTime() <= new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))) {
            $this->addFlash('error', 'Cette session est déjà passée et ne peut plus être réservée.');
            return $this->redirectToRoute('app_sessions');
        }

        // Check if the session is available
        if ($session->getAvailableSpots() <= 0) {
            $this->addFlash('error', 'Il n\'y a plus de places disponibles pour cette session.');
            return $this->redirectToRoute('app_sessions');
        }

        // Check if the user has already registered this session
        $existingRegistration = $em->getRepository(Registration::class)->findOneBy([
            'user' => $user,
            'session' => $session,
            'status' => 'confirmed'
        ]);

        if ($existingRegistration) {
            $this->addFlash('error', 'Vous avez déjà réservé cette session.');
            return $this->redirectToRoute('app_sessions');
        }

        // Check if the user has a sessionbook with remaining sessions
        $sessionBook = $em->createQueryBuilder()
            ->select('sb')
            ->from(SessionBook::class, 'sb')
            ->where('sb.user = :user')
            ->andWhere('sb.remainingSessions > 0')
            ->andWhere('sb.expiresAt > :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('sb.expiresAt', 'ASC') // Use the sessionbook that expires the first
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // if a sessionbook is available, use it
        if($sessionBook) {
            // Create the registration with the sessionBook
            $registration = new Registration();
            $registration->setUser($user);
            $registration->setSession($session);
            $registration->setStatus('confirmed');
            $registration->setRegisteredAt(new \DateTimeImmutable());
            $registration->setsessionBook($sessionBook);

            $sessionBook->setRemainingSessions($sessionBook->getRemainingSessions() - 1);
            $session->setAvailableSpots($session->getAvailableSpots() - 1);

            $em->persist($registration);
            $em->persist($sessionBook);
            $em->persist($session);
            $em->flush();

            $this->addFlash('success', 'Session réservée avec succès en utilisant votre carnet ! Il vous reste ' . $sessionBook->getRemainingSessions() . ' session(s).');

            return $this->redirectToRoute('app_dashboard');
        } else {
            // No sessionbook - redirect to Strip payment
            $baseUrl = $request->getSchemeAndHttpHost();
            $price = $session->getCourse()->getPrice();
        }
        
        // Initializing Stripe
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        try {
        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int)($price * 100),
                    'product_data' => [
                        'name' => $session->getCourse()->getName(),
                        'description' => 'Session du ' . $session->getStartTime()->format('d/m/Y à H:i'),
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $baseUrl . '/booking/payment-success?sessionId=' . $id . '&stripe_session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $baseUrl . '/sessions',
            'customer_email' => $user->getEmail(),
        ]);

    return $this->redirect($checkoutSession->url);
    } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur lors de la création du paiement : ' . $e->getMessage());
        return $this->redirectToRoute('app_sessions');
    }
}

#[Route('/booking/cancel/{id}', name: 'booking_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancel(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Retrieve the registration
        $registration = $em->getRepository(Registration::class)->find($id);
        
        if (!$registration) {
            $this->addFlash('error', 'Réservation non trouvée.');
            return $this->redirectToRoute('app_dashboard');
        }

        // Check that this is indeed the user's registration.
        if ($registration->getUser()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas annuler cette réservation.');
            return $this->redirectToRoute('app_dashboard');
        }

        // Check if registartion is confirmed
        if ($registration->getStatus() !== 'confirmed') {
            $this->addFlash('error', 'Cette réservation est déjà annulée.');
            return $this->redirectToRoute('app_dashboard');
        }

        // Check if session is in the future
        if ($registration->getSession()->getStartTime() <= new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'))) {
            $this->addFlash('error', 'Cette session est passée, vous ne pouvez plus l\'annuler.');
            return $this->redirecToRoute('app_dashboard');
        }

        // Cancel the registration
        $registration->setStatus('cancelled');
        $registration->setCancelledAt(new \DateTimeImmutable());

        // Free the spot in the session
        $session = $registration->getSession();
        $session->setAvailableSpots($session->getAvailableSpots() +1);

        // if a sessionbook was used, recredit it
        if ($registration->getSessionBook()) {
            $sessionBook = $registration->getSessionBook();
            $sessionBook->setRemainingSessions($sessionBook->getRemainingSessions() +1);
            $em->persist($sessionBook);

            $this->addFlash('success', 'Réservation annulée avec succès. Votre crédit de session a été restauré.');
        } else {
            $this->addFlash('success', 'Réservation annulée avec succès.');
        }

        $em->persist($registration);
        $em->persist($session);
        $em->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/booking/payment-success', name: 'booking_payment_success')]
    #[IsGranted('ROLE_USER')]
    public function paymentSuccess(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Retrieve the parameters
        $sessionId = (int) $request->query->get('sessionId');
        $stripeSessionId = $request->query->get('stripe_session_id');

        if (!$sessionId || !$stripeSessionId) {
            $this->addFlash('error', 'Paramètres manquants');
            return $this->redirectToRoute('app_sessions');
        }

        // Retrieve the session
        $session = $em->getRepository(Session::class)->find($sessionId);

        if(!$session) {
            $this->addFlash('error', 'Session introuvable.');
            return $this->redirectToRoute('app_sessions');
        }

        // Check the payment with Stripe
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        try {
            $stripeSession = \Stripe\Checkout\Session::retrieve($stripeSessionId);

            if ($stripeSession->payment_status !== 'paid') {
                $this->addFlash('error', 'Le paiement n\'a pas été complété.');
                return $this->redirectToRoute('app_sessions');
            }

            // Check that this payment has not already been processed
            $existingPayment = $em->getRepository(Payment::class)->findOneBy([
                'stripePaymentId' => $stripeSessionId
            ]);

            if ($existingPayment) {
                $this->addFlash('info', 'Cette réservation a déjà été enregistrée.');
                return $this->redirectToRoute('app_dashboard');
            }

            // Create the registration
            $registration = new Registration();
            $registration->setUser($user);
            $registration->setSession($session);
            $registration->setStatus('confirmed');
            $registration->setRegisteredAt(new \DateTimeImmutable());

            // Create the payment
            $payment = new Payment();
            $payment->setUser($user);
            $payment->setRegistration($registration);
            $payment->setAmount($session->getCourse()->getPrice());
            $payment->setStripePaymentId($stripeSessionId);
            $payment->setCreatedAt(new \DateTimeImmutable());

            // Decrement the places
            $session->setAvailableSpots($session->getAvailableSpots() - 1);

            // Save
            $em->persist($registration);
            $em->persist($payment);
            $em->persist($session);
            $em->flush();

            $this->addFlash('success', 'Paiement réussi ! Votre session a été réservée.');
            return $this->redirectToRoute('app_dashboard');


        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la vérification du paiement : ' . $e->getMessage());
            return $this->redirectToRoute('app_sessions');
        }
    }
}
