# Traceability Matrix

## Purpose

Map rewrite documentation outputs to the legacy discovery sources and current rewrite intent.

| Topic | Primary sources | Planned document outputs | Primary diagrams |
| --- | --- | --- | --- |
| Rewrite scope | `docs/projectPlan.md` | `architecture\system-overview.*`, ADR-001 | Context, container |
| Roles and permissions | Survey permissions questions, legacy narrative | `domain\auth-permissions\spec.*` | Sequence, permission flow |
| Tasks and calendar | Legacy task and priority discovery | `domain\tasks-calendar\spec.*`, `workflows\core-workflows.*` | Flowchart, sequence |
| Livestock | Survey livestock depth and health tracking | `domain\livestock\spec.*`, `data\core-entity-model.*` | ERD, lifecycle flow |
| Crops | Survey crop detail and follow-up needs | `domain\crops\spec.*`, `data\core-entity-model.*` | ERD, lifecycle flow |
| Accounting | Survey purchase and sale data needs | `domain\accounting\spec.*` | Flowchart, ERD |
| Map and land allocation | Legacy map/sector intent | `domain\map-sectors\spec.*` | Flowchart, component map |
| Notifications | Legacy priority/awareness expectations | `domain\notifications\spec.*` | Sequence |
| Weather context | Legacy weather visibility objective | `domain\weather\spec.*` | Flowchart |
| Tools and maintenance | Survey maintenance and asset notes | `domain\equipment-maintenance\spec.*` | Flowchart |

## Traceability rules

- Every new document must trace to at least one row in this matrix.
- Every new module spec must declare which source inputs are authoritative.
- When a requirement is inferred rather than directly stated, label it as an assumption.
