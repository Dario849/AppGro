---
name: astro-fastapi-migration
description: Migration and rewrite skill for building AppGro from zero with Astro frontend and FastAPI backend while preserving business requirements from legacy documentation.
---

# Astro + FastAPI Migration Skill

## Goals

- Build a clean-slate architecture
- Preserve domain behavior from legacy discovery
- Avoid coupling UI details with backend internals

## Recommended Architecture

- Frontend: Astro routes/components + API client layer
- Backend: FastAPI routers by domain module
- Data: explicit schema migrations and normalized entities
- Docs: Mermaid diagrams for architecture, flows, and data models

## Module Partitioning

Backend routers should map domain modules:
- auth/users
- tasks/calendar
- livestock
- crops
- accounting
- map
- tools
- notifications
- weather

## Contract Rules

- Define request/response schemas before implementation
- Keep error envelopes consistent
- Version critical APIs
- Enforce server-side role checks

## Delivery Sequence

1. Domain and data model docs
2. API contracts
3. Persistence layer
4. Core service logic
5. UI workflows
6. Observability and QA

## Migration Safety

- Never copy old stack internals blindly
- Preserve behavior intent, not code structure
- Track unknowns explicitly in docs
