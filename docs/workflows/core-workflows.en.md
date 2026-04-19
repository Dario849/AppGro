# Core Workflows

## Purpose

Document the primary cross-module workflows required to start implementation safely.

## Audience

Frontend and backend implementers.

## Source references

- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`
- `OLD/MainDocumentation/Survey/2025 APP-CAMPO.csv`

## Workflow 1 - Authenticated request with role enforcement

```mermaid
sequenceDiagram
    participant User
    participant Web
    participant API
    participant Service
    participant DB

    User->>Web: Open protected page
    Web->>API: GET /resource with JWT
    API->>API: Validate token
    API->>Service: Pass current user context
    Service->>DB: Query with organization scope
    DB-->>Service: Scoped records
    Service-->>API: Result or permission error
    API-->>Web: 200 / 401 / 403
```

## Workflow 2 - Task assignment and notification

```mermaid
sequenceDiagram
    participant Manager
    participant Web
    participant API
    participant DB
    participant Worker
    participant Operator

    Manager->>Web: Create task
    Web->>API: POST /tasks
    API->>DB: Store task with priority, due date, assignee
    API->>Worker: Queue notification event
    Worker-->>Operator: In-app assignment notification
    API-->>Web: Task created response
```

## Workflow 3 - Offline field update and sync

```mermaid
flowchart TD
    A[Operator updates task or livestock record offline] --> B[Store local copy]
    B --> C[Queue pending mutation]
    C --> D{Connectivity restored?}
    D -->|No| C
    D -->|Yes| E[Send sync batch to API]
    E --> F[Validate auth, role, and organization]
    F --> G[Apply mutation and return result]
    G --> H[Mark local record synced or failed]
```

## Workflow rules

- Offline changes must never be silently discarded.
- Server validation remains authoritative after reconnect.
- Conflict resolution must be explicit and user-visible when needed.

## Open questions

- Should the first release use a generic batch sync endpoint or domain-specific endpoints only?
- How should failed offline mutations be surfaced to users who have low digital literacy?

## Testability notes

- Verify task-notification linkage.
- Verify sync retry and failed-state handling.
- Verify permission enforcement during sync replay.
