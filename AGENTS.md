# AGENTS

This file defines repository-level guidance for AI coding agents and human collaborators.

## Project Context

This repository is a rewrite effort for AppGro, an agricultural management and analytics application.

Rewrite targets:
- Frontend: Astro
- Backend: Python/FastAPI
- Documentation: Mermaid-based technical documentation under `docs/`

Legacy materials are authoritative references and are located in `OLD/`.

## Primary Objectives

1. Build from zero, not by patching old PHP/Vite code.
2. Preserve domain intent from legacy discovery and surveys.
3. Keep architecture modular and testable.
4. Keep documentation synchronized with implementation.

## Domain Priorities

Features to preserve or improve from legacy requirements:
- Task management by priority with notifications and calendar support
- Livestock and crop lifecycle tracking
- Interactive map and sector/lote management
- Accounting and periodic summaries
- Roles and permissions
- Weather visibility and event-awareness

## Rules For AI Agents

1. Read `docs/projectPlan.md` before substantial design or coding.
2. Treat `OLD/` as discovery input, not as code to replicate line-by-line.
3. Prefer explicit API contracts and typed schemas.
4. Add or update docs when behavior changes.
5. Avoid introducing hidden coupling between map, task, and accounting modules.
6. Keep changes minimal and focused; no unrelated refactors.

## Required Reading Order For New Work

1. `docs/projectPlan.md`
2. `AGENTS.md`
3. `.github/copilot-instructions.md`
4. Relevant skill in `.agents/skills/*/SKILL.md`
5. Related docs in `docs/`

## Expected Deliverables Pattern

For medium/large changes, provide:
- Problem statement
- Design summary
- File-level implementation changes
- Test strategy and coverage notes
- Follow-up backlog items
