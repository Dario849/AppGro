# Context and Containers

## Purpose

Define the main runtime containers and their responsibilities in the rewrite.

## Audience

Developers and technical reviewers preparing frontend and backend structure.

## Source references

- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`
- `OLD/MainDocumentation/Survey/2025 APP-CAMPO.csv`

## Diagram

```mermaid
C4Container
    title AppGro Rewrite - Containers

    Person(user, "User", "Field or administrative user")

    System_Boundary(appgro, "AppGro") {
        Container(web, "Astro web app", "Astro", "UI, SSR pages, client islands, offline-aware interactions")
        Container(api, "Application API", "FastAPI", "Auth, domain services, validation, RBAC, audit logging")
        ContainerDb(db, "Operational database", "PostgreSQL", "Normalized transactional data")
        Container(queue, "Background jobs", "Worker", "Notifications, sync jobs, report generation")
    }

    System_Ext(weather, "Weather provider", "External climate data")

    Rel(user, web, "Uses", "HTTPS")
    Rel(web, api, "Calls APIs", "JSON/HTTPS")
    Rel(api, db, "Reads and writes")
    Rel(api, queue, "Schedules async work")
    Rel(queue, db, "Reads pending work and writes results")
    Rel(api, weather, "Reads weather context")

    UpdateLayoutConfig($c4ShapeInRow="2")
    UpdateRelStyle(user, web, $offsetY="-40")
    UpdateRelStyle(web, api, $offsetX="-40", $offsetY="-35")
    UpdateRelStyle(api, db, $offsetX="-25")
    UpdateRelStyle(api, queue, $offsetX="-25", $offsetY="25")
    UpdateRelStyle(api, weather, $offsetY="-15", $offsetX="10")
    UpdateRelStyle(queue, db, $offsetX="-60", $offsetY="40")
```

## Container responsibilities

| Container | Primary responsibilities |
| --- | --- |
| Astro web app | Rendering, forms, client islands, local cache, sync awareness |
| FastAPI API | Auth, validation, multi-tenant scoping, business rules, audit logging |
| PostgreSQL | Durable transactional state and reporting-friendly schema |
| Background jobs | Notifications, imports, periodic summaries, deferred sync handling |

## Design notes

- Module boundaries should match the documentation domains.
- API contracts should be versionable where workflow risk is high.
- Background jobs are a delivery concern, not a place to hide business rules.

## Open questions

- Does the first release need a dedicated queue technology, or can background execution start in-process?
  - Given the expected load and simplicity of initial background tasks, an in-process execution model could be sufficient for the first release. However, we should design the background job abstraction layer to allow for easy migration to a dedicated solution like Celery or RQ in the future without significant changes to business logic.
- Which reporting outputs require precomputation versus live query generation?
  - Specific sections could benefit from precomputation to improve performance, while more ad-hoc or infrequently used reports could be generated live. We should analyze the specific use cases and expected load for each type of report to make informed decisions about what should be precomputed.
