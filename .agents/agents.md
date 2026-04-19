# Local Agent Catalog

This file suggests useful agent roles for the AppGro rewrite workflow.

## Skills Registry

All new skills provide concrete patterns and implementation guidance for the Astro+FastAPI stack:

- **astro-frontend-patterns**: Astro islands, layouts, API clients, form handling, state management
- **fastapi-backend-patterns**: Modular routers, schemas, dependency injection, services, error handling
- **appgro-data-model**: Complete schema with agricultural domain (tasks, livestock, crops, accounting, notifications)
- **appgro-auth-permissions**: JWT authentication, RBAC with 4-tier roles, multi-tenant isolation, audit logging
- **offline-field-operations**: Offline-first IndexedDB sync, background mutation queue, field-friendly UX
- **appgro-domain**: Domain language and business logic heuristics from legacy discovery
- **astro-fastapi-migration**: High-level architecture, module partitioning, contract-first delivery
- **rewrite-documentation-specs**: Technical specs, ADRs, Mermaid diagrams
- **mermaid-diagrams**: Syntax and patterns for flowcharts, sequence, ER, C4 diagrams

---

## Product-Architect Agent

Use for:
- Domain model decomposition
- Module boundaries
- Cross-cutting constraints (auth, notifications, reporting)

**Recommended Skills**: `appgro-domain`, `appgro-data-model`, `astro-fastapi-migration`

## Backend-API Agent

Use for:
- FastAPI route design
- Pydantic schemas
- Permission enforcement and validation rules

**Recommended Skills**: `fastapi-backend-patterns`, `appgro-auth-permissions`, `appgro-data-model`, `appgro-domain`

## Frontend-UX Agent

Use for:
- Astro information architecture
- Operator-friendly field workflows
- Accessibility and responsive behavior

**Recommended Skills**: `astro-frontend-patterns`, `offline-field-operations`, `appgro-domain`, `appgro-auth-permissions`

## Data-Model Agent

Use for:
- SQL schema planning
- Entity lifecycle constraints
- Query and reporting performance

**Recommended Skills**: `appgro-data-model`, `appgro-domain`, `fastapi-backend-patterns`

## Documentation Agent

Use for:
- Mermaid diagrams
- ADR-style docs
**Recommended Skills**: `rewrite-documentation-specs`, `mermaid-diagrams`, `appgro-domain`, `astro-fastapi-migration`

## QA-Scenario Agent

Use for:
- Acceptance criteria
- Test matrices by module
- Regression risk checklists

**Recommended Skills**: `offline-field-operations`, `appgro-auth-permissions`, `appgro-data-model`, `appgro-domain`

---

## Skill Application Guide

### When Building a Feature

1. Read the relevant domain/stack skill (e.g., `appgro-domain`, `astro-fastapi-migration`)
2. Check the data model skill (`appgro-data-model`) for schema patterns
3. Apply the pattern skill for your layer:
   - Frontend: `astro-frontend-patterns`
   - Backend: `fastapi-backend-patterns`
4. For auth-gated features: read `appgro-auth-permissions`
5. For field operations: read `offline-field-operations` 
6. Document design in Mermaid using `mermaid-diagrams`, reference in ADR using `rewrite-documentation-specs`

### Example: Adding a Livestock Record Feature

1. Read `appgro-domain` → understand role/lifecycle requirements
2. Read `appgro-data-model` → find livestock entity schema
3. Read `fastapi-backend-patterns` → design `/api/livestock` router + service
4. Read `astro-frontend-patterns` → design form island + local sync
5. Read `offline-field-operations` → ensure offline livestock logging works
6. Read `appgro-auth-permissions` → enforce role checks (only operators/managers can create)
7. Document flow in Mermaid (sequence diagram), link in ADR
- Test matrices by module
- Regression risk checklists
