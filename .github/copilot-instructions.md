# GitHub Copilot Instructions (Repository)

These instructions guide AI-assisted changes in this repository.

## Context

AppGro is being rewritten from legacy materials into:
- Astro frontend
- FastAPI backend
- Mermaid-first docs

Primary references:
- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`
- `OLD/MainDocumentation/Survey/2025 APP-CAMPO.csv`

## Non-Negotiables

1. Do not re-implement legacy stack patterns (PHP/Vite) unless explicitly needed for migration tooling.
2. Keep business language aligned with agricultural domain terms used in legacy discovery (lote, sector, caravana, etc.).
3. Design APIs and data models before writing large implementation blocks.
4. Document behavior changes in `docs/`.

## Coding Preferences

- Prefer explicit types and schema validation.
- Keep modules cohesive by domain boundary.
- Use clear naming and avoid ambiguous abbreviations.
- Add tests for business logic and permission checks.

## Documentation Preferences

- Use Mermaid where diagrams clarify behavior or architecture.
- Keep docs runnable and versioned with code.
- Include assumptions and unresolved questions explicitly.

## Review Checklist (AI output)

- Is the change mapped to a known module?
- Is authorization handled server-side?
- Are data constraints validated?
- Are docs and tests updated?
- Are edge cases covered for offline/manual field operations when relevant?
