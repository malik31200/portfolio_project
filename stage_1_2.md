# Stage 1 Report – Team Formation & MVP Definition

## Table of Contents
- [1. Kick-Off Meeting Overview](#1-kick-off-meeting-overview)
- [2. Team Formation & Roles](#2-team-formation--roles)
- [3. Tools & Communication Workflow](#3-tools--communication-workflow)
- [4. Stakeholder Analysis](#4-stakeholder-analysis)
- [5. Brainstorming & Idea Exploration](#5-brainstorming--idea-exploration)
  - [5.1 Research Phase](#51-research-phase)
  - [5.2 Brainstorming Session](#52-brainstorming-session)
- [6. Project Idea Evaluation](#6-project-idea-evaluation)
  - [6.1 Evaluation Criteria](#61-evaluation-criteria)
  - [6.2 Ideas Explored](#62-ideas-explored)
  - [6.3 Project Ranking](#63-project-ranking)
- [7. Final MVP Selection & Refinement](#7-final-mvp-selection--refinement)
  - [7.1 Problem Statement](#71-problem-statement)
  - [7.2 Proposed Solution](#72-proposed-solution)
- [8. Target Audience](#8-target-audience)
- [9. Application Type](#9-application-type)
- [10. Why This MVP Was Selected](#10-why-this-mvp-was-selected)
- [11. Key Features & SMART Objectives](#11-key-features--smart-objectives)
- [12. Project Scope](#12-project-scope)
- [13. Risks & Mitigation Strategies](#13-risks--mitigation-strategies)
- [14. Final Outcome](#14-final-outcome)
- [Stage 2: Project Planning](#stage-2-project-planning)
  - [1. High-Level Timeline (Mandatory)](#1-high-level-timeline-mandatory)
  - [Gantt Chart Timeline](#gantt-chart-timeline)

---

# 1. Kick-Off Meeting Overview

**Purpose of the initial meeting:**
- Introduce team members and their backgrounds
- Define technical and organizational roles
- Establish communication and collaboration rules
- Identify project stakeholders
- Set a solid foundation for project execution

---
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
# 2. Team Formation & Roles

| ![Christophe](images/christophe.jpg){width=60px} | **Christophe BARRERE**<br>Primary Role: Project Manager & Lead Back-End Developer<br>Skills: Back-end architecture, database management, business logic, project coordination<br>Strengths: Global project vision, structured workflow, strong back-end expertise |
|--------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| ![Malik](images/bouanani.jpg){width=60px}      | **Malik BOUANANI**<br>Primary Role: Lead Front-End Developer & UX/UI Designer<br>Skills: Web integration, UX design, UI design, user flow optimization<br>Strengths: Strong visual sense, design coherence, user-centric thinking |

### Technical Roles Breakdown

**Project Manager – Christophe**
- Task planning and prioritization
- Progress tracking
- Team coordination
- Communication with external stakeholders

**Front-End Development**
- **Lead:** Malik
- **Support:** Christophe
- **Reason:** Malik ensures visual and UX consistency; Christophe provides integration support as needed

**Back-End Development**
- **Lead:** Christophe
- **Support:** Malik
- **Reason:** Christophe manages server logic, security, API design, and databases; Malik assists to ensure front/back alignment

**UX/UI Designer – Malik**
- Wireframes & prototypes
- User flows
- UI design and branding consistency

---

# 3. Tools & Communication Workflow
- **Discord:** Internal communication
- **Trello:** Project management
- **GitHub:** Version control
- **Figma:** Design & mockups
- **Google Drive:** Documentation & file sharing

**Team Norms:**
- Clear and respectful communication
- Daily check-ins + weekly progress review
- Advance planning of weekly tasks
- Technical decisions discussed by both developers
- Quick reporting of issues/blockers
- Commitment to deadlines
- Mandatory documentation of code

---
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
# 4. Stakeholder Analysis
### Internal Stakeholders
1. **Development Team:** Designs, builds, tests, and maintains the application
2. **Project Manager:** Clarifies requirements, prioritizes features, organizes workflow, communicates with external stakeholders

### External Stakeholders
1. **Actual (Project Sponsor):** Defines scope and priorities, validates deliverables, expects reliable sports class management solution
2. **Gym Administrators:** Create/manage classes, track registrations, manage users & payments (admin users)
3. **Sports Coach:** Maintains schedule, communicates availability, affected by occupancy & cancellations
4. **Actual Employees (End Users):** View schedule, book/cancel classes, buy session passes, manage account/history
5. **Payment Service Provider:** Handles secure transactions, sends confirmations, affects accounting
6. **Holberton / SWE / Mentors:** Provide guidance, review technical quality, evaluate project

---
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
# 5. Brainstorming & Idea Exploration
## 5.1 Research Phase
Team research included:
- Real-world business problems
- Trends in sports, wellness, and web apps
- Analysis of existing solutions (booking apps, sport platforms, payment systems)

**Key Needs Identified:**
- Limited and controlled class reservations
- Centralized management tool
- Automated payment tracking
- Better UX for employees
- Reduced manual workload for admins

## 5.2 Brainstorming Session
**Methods Used:**
- SCAMPER Framework
- “How Might We” (HMW) questions
- Comparative analysis of existing apps

**SCAMPER Examples:**
- Substitute: Paper/Excel → digital platform
- Combine: Schedule + payment + reservation
- Adapt: Gym apps → corporate context
- Modify: Add session pass features
- Eliminate: Manual errors/overbooking
- Reverse: Flexible reservation vs fixed schedule

**HMW Questions:**
- How might we simplify class registration?
- How might we avoid overbooking?
- How might we secure payments?
- How might we improve schedule visibility?
- How might we enhance employee experience?

---
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
# 6. Project Idea Evaluation
## 6.1 Evaluation Criteria
- Technical feasibility
- User impact
- Team skill alignment
- Technical risks
- Scalability
- Complexity
- Value added to company
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
## 6.2 Ideas Explored
- **🟢 Idea 1 – Sports Class Management App (Actual):** Schedule, register, pay, session pass & history, admin panel
- **🟡 Idea 2 – Amateur Football Social Network (Occitanie):** Calendar, results, media sharing, likes/comments/follow
- **🟠 Idea 3 – Pottery Class Booking App:** Booking calendar, course presentation, online payments

## 6.3 Project Ranking
| Project | Feasibility | Risks | Impact | Scalability | Rank |
|---------|------------|------|--------|------------|------|
| Sports App (Actual) | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | 🥇 1 |
| Football Social Network | ⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | 🥈 2 |
| Pottery Classes | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | 🥉 3 |

---
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
# 7. Final MVP Selection & Refinement
## 7.1 Problem Statement
Current challenges:
- Manual processes
- No centralized system
- Risk of overbooking
- Manual payment handling
- No user history tracking
- Time-consuming admin tasks

**Main Problem:** Lack of a simple, reliable, centralized digital solution for managing classes, registrations, and payments

## 7.2 Proposed Solution
**For Employees:** View schedule, register online, pay securely, track session history  
**For Admins:** Create/manage classes, set capacity, manage users, track payments

**Main Goal:** Centralize and automate sports class management to improve organization and UX

---
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
# 8. Target Audience
- **Primary Users:** Actual employees
- **Secondary Users:** Gym administrators, sports coach

---

# 9. Application Type
- Web application, fully responsive (desktop/tablet/mobile)
- User roles: Admin, Employee

---
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
# 10. Why This MVP Was Selected
- Solves a real, concrete problem
- High value for company
- Technically feasible within timeline
- Aligns with team skills (Front-End, Back-End, UX/UI)
- Strong future scalability (statistics, mobile app, AI coaching)

Other ideas rejected due to higher risk, less relevance, or complexity

---
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
# 11. Key Features & SMART Objectives
**Key MVP Features:**
- Class booking system
- Secure online payment
- Admin class management

**SMART Objectives:**
- S1: Book a class in under 2 minutes
- S2: Guarantee 100% class capacity compliance
- S3: Admin can create a class in under 1 minute

---
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
# 12. Project Scope
**✔ In Scope:**
- Web app, user authentication, class creation & management, registration, payment, session pass, reservation history, user management

**❌ Out of Scope:**
- Native mobile app, advanced analytics, AI coaching, performance tracking, SMS notifications

---
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
# 13. Risks & Mitigation Strategies
| Risk | Mitigation |
|------|-----------|
| Limited experience with payment systems | Tutorials, sandbox testing, official docs |
| Data security issues | Secure authentication, best practices |
| High workload | Clear task distribution, weekly follow-up |

---
[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)
# 14. Final Outcome
- Clearly defined MVP
- Well-established scope
- Measurable objectives
- Solid foundation for Stage 2

---

# Stage 2: Project Planning
## 1. High-Level Timeline
**Major stages:**
- **Stage 1:** Idea Development — Completed
- **Stage 2:** Project Planning — Current stage
- **Stage 3:** Technical Documentation
- **Stage 4:** MVP Development
- **Stage 5:** Project Closure

[🔝 Back to top](#stage-1-report--team-formation--mvp-definition)

### Gantt Chart Timeline
```mermaid
gantt
    title Project Timeline - ACTUAL Sports App
    dateFormat  YYYY-MM-DD
    axisFormat  "Week %W"

    section Project Stages
    Stage 1 – Idea Development        :done,    s1, 2025-12-01, 14d
    Stage 2 – Project Planning        :active,  s2, 2025-12-15, 7d
    Stage 3 – Technical Documentation :         s3, 2025-12-22, 21d
    Stage 4 – MVP Development         :         s4, 2026-01-12, 56d
    Stage 5 – Project Closure         :         s5, 2026-03-09, 7d