# Domain Glossary

| Spanish term | English term | Usage note |
| --- | --- | --- |
| sector | sector | Operational land area used for grouping activity and records |
| lote | lot / plot | Subdivision inside a sector with its own operational identity |
| caravana | ear tag / livestock identifier | Unique visible identifier for an animal |
| ganado | livestock | Animal population tracked by lifecycle and health history |
| cultivo | crop | Planting record tied to sector or lote |
| tarea | task | Planned unit of work with priority, due date, and assignee |
| encargado | manager / supervisor | Operational leader with elevated permissions |
| peon | operator / field worker | Field-facing user who executes assigned work |
| agronomo | agronomist | Specialist role for crop-related decisions and records |
| veterinario | veterinarian | Specialist role for animal health actions and records |
| sanidad animal | animal health history | Vaccines, treatments, incidents, and clinical observations |
| resumen semanal | weekly summary | Periodic operational or accounting rollup |
| resumen mensual | monthly summary | Monthly operational or accounting rollup |
| insumo | input / supply | Product consumed by crop, livestock, or maintenance work |
| alambrado | fencing | Infrastructure maintenance context |
| humedad del grano | grain moisture | Harvest quality metric |
| qq/ha | quintals per hectare | Yield measure used in crop reporting |

## Glossary rules

- Keep Spanish domain terms visible when they carry stakeholder meaning that English does not fully preserve.
- Prefer `sector` and `lote` over generic alternatives in architecture and data docs.
- When a specialist role appears in permissions or workflow docs, mention both the normalized system role and the field-facing job title.
