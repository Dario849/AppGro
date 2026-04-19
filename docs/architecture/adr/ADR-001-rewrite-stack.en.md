# ADR-001: Rewrite with Astro + FastAPI + Mermaid-first docs

## Status

Accepted

## Context

The repository is a rewrite effort, not a legacy patch project. The old stack was PHP plus Vite/JavaScript, while current repository guidance requires a clean-slate architecture with professional documentation first.

## Decision

Adopt:

- Astro for the web frontend
- FastAPI for backend APIs and service composition
- Mermaid-first Markdown documentation under `docs\`
- Explicit domain modules aligned to agricultural capabilities

## Rationale

- Astro supports a documentation-first and UI-composition-friendly frontend.
- FastAPI supports explicit schemas, validation, and modular routers.
- Mermaid keeps diagrams versioned with code and reviews.
- This combination supports a gradual delivery path from documentation to contracts to implementation.

## Consequences

### Positive

- Better alignment between documentation and code
- Clearer separation between frontend and backend responsibilities
- Easier traceability from requirements to implementation

### Negative

- More upfront documentation work before visible product features
- Need to maintain bilingual parity as docs expand
- Need explicit decisions for offline behavior and background processing
