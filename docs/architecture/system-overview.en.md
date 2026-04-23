# System Overview

## Purpose

Describe the intended rewrite architecture and delivery philosophy for AppGro.

## Audience

Developers, technical leads, and reviewers preparing implementation work.

## Source references

- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`
- `OLD/MainDocumentation/Survey/2025 APP-CAMPO.csv`

## Design summary

AppGro is a clean-slate rewrite of an agricultural management platform. The rewrite keeps the business intent from legacy discovery while moving to:

- Astro for the web frontend
- FastAPI for backend APIs and server-side business rules
- Mermaid-first documentation for architecture, workflows, and data models

The platform centers on agricultural operations: tasks, livestock, crops, accounting, map sectors/lotes, notifications, weather context, and equipment maintenance.

## Diagram

```mermaid
C4Context 
    title AppGro Rewrite - System Context

    Boundary(appgro, "AppGro") {
        System(appgro, "AppGro", "Agricultural operations platform")
        Container(web, "Web Frontend", "Astro", "User interface for field and office users")
        Container(api, "API Backend", "FastAPI", "Business logic and data access layer")
        ContainerDb(db, "Database", "PostgreSQL", "Stores operational data, history, and configuration")
    }

    Person(fieldUser, "Field user", "Operator, encargado, agronomist, veterinarian")
    Person(adminUser, "Administrative user", "Admin or manager reviewing data and configuring access")

    System_Ext(weather, "Weather provider", "External weather observations or forecast source")
    System_Ext(email, "Notification channel", "Email or future outbound notification transport")

    Rel(fieldUser, appgro, "Records tasks, field observations, livestock and crop events")
    Rel(adminUser, appgro, "Configures users, reviews summaries, manages operations")
    Rel(appgro, weather, "Reads weather context")
    Rel(appgro, email, "Sends reminders and alerts")
    Rel(appgro,web,"")
    Rel(web,api,"")
    Rel(api,db,"")
    Rel(db,api,"")
    Rel(api,web,"")
    
    UpdateLayoutConfig($c4ShapeInRow="2", $c4BoundaryInRow="1")
    UpdateRelStyle(adminUser, appgro, $offsetY="-150", $offsetX="0")
    UpdateRelStyle(appgro, email, $offsetY="-50", $offsetX="50")
    UpdateRelStyle(appgro, weather, $offsetY="-15", $offsetX="10")
    UpdateRelStyle(fieldUser, appgro, $offsetY="-125", $offsetX="-100")

```

## Architectural principles

1. Preserve legacy business intent, not legacy implementation details.
2. Keep domain modules cohesive by business boundary.
3. Enforce authentication and authorization on the server.
4. Prefer append-only history where auditability matters.
5. Design APIs and data models before large implementation blocks.
6. Support low-connectivity field workflows where relevant.

## Delivery implications

- Domain and data docs precede implementation-heavy work.
- Shared vocabulary is required across frontend, backend, and docs.
- Offline and manual fallback scenarios should be documented for field-facing flows.

## Open questions

- Which notification channels are required in the first production release?
  - Email is likely the only required channel for the initial release, as it is widely used and can cover critical notifications. However, we should design the notification system to be extensible so that we can easily add additional channels like SMS or in-app notifications in future iterations based on user feedback and needs.
- How much map editing must be available in the first delivery versus later phases?
  - A basic map editing capability that allows users to define and edit sectors/lotes should be included in the first delivery, as it is fundamental to many operational flows. More advanced features like geofencing, integration with external GIS data, or collaborative editing could be considered for later phases once we have validated the core functionality and gathered user feedback on the initial implementation.
- What external weather source should be treated as authoritative?
  - WeatherAPI is a popular choice for agricultural applications due to its comprehensive data and ease of integration, but we should evaluate it against other providers based on factors like data accuracy in our target regions, cost, and API reliability. We may also want to design the system to support multiple weather providers in case we need to switch or aggregate data from different sources in the future.

## Testability notes

- Architecture docs should produce explicit API and data-model follow-up tasks.
- Each module spec should define acceptance criteria that can become backend and integration tests.
