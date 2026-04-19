# Core Entity Model

## Purpose

Define the primary entities and relationships that support the AppGro rewrite.

## Audience

Backend implementers, API designers, and reviewers aligning schema with domain behavior.

## Source references

- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`
- `OLD/MainDocumentation/Survey/2025 APP-CAMPO.csv`

## Design summary

The core model is multi-tenant by organization and keeps operational history explicit. Mutable summaries may exist for current state, but audits and field history should remain append-only where business evidence matters.

## Diagram

```mermaid
erDiagram
    ORGANIZATION ||--o{ USER : contains
    ORGANIZATION ||--o{ SECTOR : owns
    SECTOR ||--o{ LOTE : subdivides
    ORGANIZATION ||--o{ TASK : plans
    USER ||--o{ TASK : creates
    USER ||--o{ TASK : assigned_to
    SECTOR ||--o{ TASK : scopes
    ORGANIZATION ||--o{ LIVESTOCK : tracks
    SECTOR ||--o{ LIVESTOCK : places
    LIVESTOCK ||--o{ LIVESTOCK_EVENT : records
    ORGANIZATION ||--o{ CROP : tracks
    SECTOR ||--o{ CROP : hosts
    LOTE ||--o{ CROP : optionally_hosts
    CROP ||--o{ CROP_TREATMENT : records
    ORGANIZATION ||--o{ ACCOUNTING_TRANSACTION : books
    ORGANIZATION ||--o{ NOTIFICATION : emits
    USER ||--o{ NOTIFICATION : receives
    SECTOR ||--o{ WEATHER_OBSERVATION : contextualizes
    ORGANIZATION ||--o{ ASSET : registers
    ASSET ||--o{ MAINTENANCE_LOG : has

    ORGANIZATION {
        int id PK
        string name
        string timezone
    }
    USER {
        int id PK
        int organization_id FK
        string email
        string role
        boolean is_active
    }
    SECTOR {
        int id PK
        int organization_id FK
        string name
        decimal area_hectares
    }
    LOTE {
        int id PK
        int sector_id FK
        string name
    }
    TASK {
        int id PK
        int organization_id FK
        int sector_id FK
        int assigned_to_id FK
        string priority
        string status
        datetime due_date
    }
    LIVESTOCK {
        int id PK
        int organization_id FK
        int sector_id FK
        string identifier
        string status
    }
    LIVESTOCK_EVENT {
        int id PK
        int livestock_id FK
        string event_type
        date event_date
    }
    CROP {
        int id PK
        int organization_id FK
        int sector_id FK
        int lote_id FK
        string crop_name
        string status
    }
    CROP_TREATMENT {
        int id PK
        int crop_id FK
        string treatment_type
        date treatment_date
    }
    ACCOUNTING_TRANSACTION {
        int id PK
        int organization_id FK
        string transaction_type
        decimal amount
        date transaction_date
    }
    NOTIFICATION {
        int id PK
        int organization_id FK
        int user_id FK
        string notification_type
        boolean is_read
    }
    WEATHER_OBSERVATION {
        int id PK
        int organization_id FK
        int sector_id FK
        date observation_date
        string source
    }
    ASSET {
        int id PK
        int organization_id FK
        string category
        string location
    }
    MAINTENANCE_LOG {
        int id PK
        int asset_id FK
        date maintenance_date
        string work_type
    }
```

## Core constraints

- Every operational entity must carry `organization_id` directly or through a parent relationship.
- User email, sector name, and livestock identifier should be unique per organization where applicable.
- Destructive deletes should be avoided for tasks, livestock, crops, and accounting records.
- Audit columns should be standard across mutable entities.

## Cross-module links

- Tasks may reference sectors and drive notifications.
- Livestock, crops, and maintenance can generate accounting context.
- Weather observations enrich crop and task decisions.

## Open questions

- Should maintenance assets support quantity-on-hand and spare-part stock in the same model?
- Will task comments and attachments be part of the first schema wave?

## Testability notes

- Verify organization scoping in every relationship path.
- Verify uniqueness and non-destructive lifecycle rules.
