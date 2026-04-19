---
name: appgro-domain
description: Domain guidance for AppGro rewrite. Use when implementing or documenting agricultural features such as tasks, map sectors, livestock/crops tracking, accounting summaries, notifications, weather context, and role permissions based on legacy discovery in OLD/.
---

# AppGro Domain Skill

## Purpose

Keep implementation and docs aligned with confirmed legacy business needs.

## Canonical Inputs

- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`
- `OLD/MainDocumentation/Survey/2025 APP-CAMPO.csv`

## Domain Language

Prefer terms used by stakeholders:
- Sector / Lote
- Caravana
- Tareas por prioridad
- Historial sanitario
- Resumen semanal/mensual
- Ganado / Cultivo

## High-Value Capabilities

1. Tasks with priorities, deadlines, completion states, and notifications
2. Calendar projection of task deadlines
3. Livestock lifecycle records (weight, health events, diet, status)
4. Crop lifecycle records (sowing, treatments, yields, conditions)
5. Financial records (purchase/sale, categories, balances, notes)
6. Editable interactive map for sector allocation and labeling
7. Role-based access by responsibility profile

## Implementation Heuristics

- Distinguish immutable history from editable current state.
- Keep event logs append-only where auditing matters.
- Validate operational inputs with agricultural context (units, dates, uniqueness constraints).
- Support periodic reporting (weekly, monthly, yearly).

## Common Pitfalls

- Modeling tasks without explicit priority/ordering semantics
- Treating map labels as cosmetic only instead of operational context
- Missing links between accounting entries and production context
- Ignoring low-digital-literacy UX assumptions from stakeholder interviews
