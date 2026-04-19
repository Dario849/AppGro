# Modelo de entidades núcleo

## Propósito

Definir las entidades principales y relaciones que soportan la reescritura de AppGro.

## Audiencia

Implementadores backend, diseñadores de API y revisores que alinean esquema con comportamiento de dominio.

## Referencias de origen

- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`
- `OLD/MainDocumentation/Survey/2025 APP-CAMPO.csv`

## Resumen de diseño

El modelo núcleo es multi-tenant por organización y mantiene explícito el historial operativo. Puede existir un resumen mutable del estado actual, pero la auditoría y la historia de campo deben permanecer append-only donde la evidencia de negocio sea importante.

## Diagrama

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

## Restricciones núcleo

- Toda entidad operativa debe llevar `organization_id` directamente o por relación padre.
- El email del usuario, nombre del sector e identificador de ganado deben ser únicos por organización cuando aplique.
- Deben evitarse borrados destructivos para tareas, ganado, cultivos y contabilidad.
- Las columnas de auditoría deberían ser estándar en entidades mutables.

## Vínculos entre módulos

- Las tareas pueden referenciar sectores y disparar notificaciones.
- Ganado, cultivos y mantenimiento pueden generar contexto contable.
- Las observaciones climáticas enriquecen decisiones sobre cultivos y tareas.

## Preguntas abiertas

- ¿Los activos de mantenimiento deben soportar stock de repuestos y cantidades en el mismo modelo?
- ¿Los comentarios y adjuntos de tareas entran en la primera ola de esquema?

## Notas de testabilidad

- Verificar alcance por organización en cada camino relacional.
- Verificar unicidad y reglas de ciclo de vida no destructivas.
