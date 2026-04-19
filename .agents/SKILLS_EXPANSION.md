# AppGro Agent Skills Expansion – Summary

## Overview

5 new specialized skills have been created for the AppGro Astro+FastAPI rewrite. Combined with existing skills, they provide concrete patterns, code examples, and implementation guidance for the entire stack and domain.

**Date**: April 2026  
**Project**: AppGro Rewrite (from PHP+Vite to Astro+FastAPI)  
**Skills Total**: 9 (4 existing + 5 new)

---

## New Skills Created

### 1. **astro-frontend-patterns**
- **Purpose**: Frontend architecture and component patterns for Astro
- **Covers**:
  - Islands Architecture strategy (when to use interactive islands vs. static components)
  - API client layer design and error handling
  - Form patterns (validation, error display, submission)
  - State management (server-side vs. client-side)
  - Agricultural UX considerations (field accessibility, offline support)
  - Performance goals and testing strategy
- **Use When**: Building Astro pages, components, islands, API integration
- **Key Patterns**: Client-side state with hooks, API client abstraction, island configuration

### 2. **fastapi-backend-patterns**
- **Purpose**: FastAPI routing, services, and API design for AppGro
- **Covers**:
  - Modular router template for domain-driven routers
  - Pydantic schema design (request/response contracts)
  - Dependency injection patterns (get_current_user, get_db, role checks)
  - Service layer pattern (business logic isolation)
  - Custom exception handling and error responses
  - Agricultural domain constraints (units, conversions, seasonal reporting)
  - Testing strategy (unit, integration, E2E)
  - Performance considerations (indexing, caching, N+1 prevention)
- **Use When**: Designing FastAPI endpoints, services, error handling
- **Key Patterns**: FastAPI routers by domain, pydantic validation, service layer

### 3. **appgro-data-model**
- **Purpose**: Complete database schema with agricultural entities
- **Covers**:
  - Core entities: Users/Roles, Map/Sectors/Lotes, Tasks, Livestock, Crops, Accounting, Notifications, Weather
  - Entity relationships and cascade rules (soft delete patterns)
  - Audit trail pattern (created_at/by, updated_at/by)
  - Uniqueness constraints and multi-tenant isolation
  - Denormalization & performance optimization
  - Reporting views (task summaries, livestock status)
  - Migration strategy (Alembic pattern)
- **Use When**: Planning database schema, designing queries, understanding entity lifecycle
- **Key Constraints**: Organization isolation, soft deletes, immutable audit trails, agricultural semantics

### 4. **appgro-auth-permissions**
- **Purpose**: JWT authentication and role-based access control
- **Covers**:
  - Login & token issuance (JWT with exp, role, organization_id)
  - Token validation and extraction
  - 4-tier role system: Admin, Manager, Operator, Viewer
  - Permission matrix (who can do what)
  - Role-based dependency injectors (`get_current_admin`, `get_current_manager`, etc.)
  - Multi-tenant isolation (organization_id checks in every query)
  - Service-layer permission enforcement
  - Audit logging (all sensitive operations)
  - Password policy and secret storage
  - Token refresh and session management
- **Use When**: Implementing auth, permission checks, multi-tenant isolation
- **Key Rules**: Check permissions in service layer, always include org_id in queries, log sensitive ops

### 5. **offline-field-operations**
- **Purpose**: Offline-first architecture for field workers with intermittent connectivity
- **Covers**:
  - IndexedDB local database schema and initialization
  - Offline Sync Service (queue mutations, retry, reconcile)
  - UI indicators (online/offline status, pending sync count)
  - Backend bulk sync endpoint (batch POST for queued operations)
  - Field-friendly UX patterns (task completion, livestock logging, manual fallbacks)
  - Conflict detection and resolution (last-write-wins)
  - Data consistency and sync guarantees
- **Use When**: Building offline-capable features, field operation workflows
- **Key Pattern**: Local IndexedDB + sync queue + batch server endpoint

---

## Existing Skills (Enhanced with New Skills)

1. **appgro-domain** – Domain language and business logic heuristics
2. **astro-fastapi-migration** – High-level architecture and delivery sequence
3. **rewrite-documentation-specs** – Technical specs, ADRs, Mermaid diagrams
4. **mermaid-diagrams** – Diagram syntax and patterns (from external toolkit)

---

## Skill Relationships & Usage Flow

```
START: New Feature or Bug Fix
    │
    ├─→ appgro-domain (understand business context)
    ├─→ astro-fastapi-migration (understand architecture)
    │
    ├─→ Design Phase:
    │   ├─→ appgro-data-model (if data changes)
    │   ├─→ appgro-auth-permissions (if auth/roles affected)
    │   ├─→ mermaid-diagrams (visualize flow/schema)
    │   └─→ rewrite-documentation-specs (write ADR or spec)
    │
    └─→ Implementation Phase:
        ├─→ fastapi-backend-patterns (if backend code)
        ├─→ astro-frontend-patterns (if frontend code)
        ├─→ offline-field-operations (if field operations)
        └─→ appgro-auth-permissions (if permission-gated)
```

---

## Practical Examples

### Example 1: Add Livestock Weight Logging Feature

1. **Plan**: Read `appgro-domain` → understand livestock lifecycle
2. **Schema**: Read `appgro-data-model` → find `livestock` and `livestock_weight_log` entities
3. **API Design**: Read `fastapi-backend-patterns` → design POST `/api/livestock/{id}/weights`
4. **Frontend**: Read `astro-frontend-patterns` → build weight form island
5. **Offline Support**: Read `offline-field-operations` → IndexedDB queue for offline weight logging
6. **Auth**: Read `appgro-auth-permissions` → only operators/managers can log weights
7. **Diagram**: Use `mermaid-diagrams` to visualize workflow (form → API → sync)
8. **Document**: Use `rewrite-documentation-specs` to write ADR

### Example 2: Implement Task Assignment Notifications

1. **Domain**: `appgro-domain` → understand notification requirements from legacy
2. **Data Model**: `appgro-data-model` → `notifications`, `notification_preferences` tables
3. **Auth**: `appgro-auth-permissions` → who gets notified (role-based)
4. **Backend**: `fastapi-backend-patterns` → design notification service, router
5. **Frontend**: `astro-frontend-patterns` → notification badge island with real-time update
6. **Offline**: `offline-field-operations` → queue notifications for offline delivery
7. **Diagram**: `mermaid-diagrams` → sequence diagram (task assigned → notification sent → delivered)

---

## Integration with Copilot Instructions

The skills are referenced in:
- [.github/copilot-instructions.md](.github/copilot-instructions.md) – Non-negotiables and review checklist
- [.agents/agents.md](.agents/agents.md) – Agent role definitions with skill mappings

When using GitHub Copilot Chat:
1. Mention the relevant skill by name (e.g., "using astro-frontend-patterns")
2. Copilot will automatically load the skill if recognized
3. Patterns and code examples will be applied consistently across the codebase

---

## Quick Reference: What Each Skill Teaches

| Skill                       | Teaches                                      | Code Examples                                                      | Key Takeaway                                    |
| --------------------------- | -------------------------------------------- | ------------------------------------------------------------------ | ----------------------------------------------- |
| appgro-domain               | Business language, domain concepts           | Task priorities, livestock lifecycle, accounting periods           | Use correct terminology; align to legacy intent |
| astro-frontend-patterns     | Astro islands, forms, API clients            | TaskForm island, API client class, offline sync service            | Keep islands small; separate API layer          |
| fastapi-backend-patterns    | Router structure, services, schemas          | Task router, pydantic TaskCreate, TaskService class                | Service layer isolates business logic           |
| appgro-data-model           | Database schema, relationships               | Tasks, livestock, crops, accounting tables with FK/constraints     | Use soft deletes; maintain audit trail          |
| appgro-auth-permissions     | JWT, RBAC, multi-tenant isolation            | Token generation, role-based dependency, org_id checks             | Always check org_id and role in service layer   |
| offline-field-operations    | IndexedDB, sync queue, conflict resolution   | SyncService, sync batch endpoint, offline indicator UI             | Queue mutations locally; sync in background     |
| rewrite-documentation-specs | ADR format, Mermaid diagrams, spec template  | Architecture Decision Record, ER diagram template                  | Document decisions and reasoning                |
| mermaid-diagrams            | Syntax for flowcharts, sequences, ER, C4     | Flowchart for task workflow, sequence for sync flow, ER for schema | Visualize before implementing                   |
| astro-fastapi-migration     | High-level architecture, module partitioning | Router organization by domain, API contract definition             | Design API contracts before implementation      |

---

## Directory Structure

```
.agents/
  README.md
  agents.md                    (Updated with skill mappings)
  skills/
    appgro-domain/             (Existing)
      SKILL.md
    astro-fastapi-migration/   (Existing)
      SKILL.md
    mermaid-diagrams/          (Existing, external)
      SKILL.md
    rewrite-documentation-specs/ (Existing)
      SKILL.md
    astro-frontend-patterns/   (NEW)
      SKILL.md
    fastapi-backend-patterns/  (NEW)
      SKILL.md
    appgro-data-model/         (NEW)
      SKILL.md
    appgro-auth-permissions/   (NEW)
      SKILL.md
    offline-field-operations/  (NEW)
      SKILL.md

skills-lock.json             (Updated with 5 new local skills)
```

---

## Next Steps

1. **Review**: Read `docs/projectPlan.md` and any existing technical specs
2. **Get Familiar**: Skim all 9 skills to understand coverage
3. **Start Implementing**: Pick a feature, follow the usage flow above
4. **Use Skills in Copilot Chat**: Mention skill names when asking for code patterns
5. **Contribute**: Update skills if you discover better patterns or missing guidance

---

## Skill Maintenance

- Skills are versioned in `skills-lock.json`
- Local skills can be updated in-place (no external dependencies)
- When updating a skill, increment the description or add a date comment
- If a skill becomes outdated, create a replacement and deprecate the old one in a release note

---

## Questions?

- Check the AGENTS.md file for agent role guidance
- Check the Copilot instructions (.github/copilot-instructions.md) for review checklist
- Review the projectPlan.md for rewrite scope and timeline
