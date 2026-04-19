# AppGro Documentation Index

## Purpose

This documentation set defines the AppGro rewrite from legacy discovery into a documentation-first, Mermaid-first program.

## Authoring model

- English files are the canonical drafting source.
- Spanish files mirror the approved English content.
- Each topic is documented as paired files: `*.en.md` and `*.es.md`.

## Structure

- `projectPlan.md` - high-level rewrite intent
- `standards\documentation-standards.md` - documentation rules, templates, and Mermaid standards
- `standards\glossary.md` - bilingual domain terminology
- `standards\traceability-matrix.md` - requirement-to-document map
- `architecture\` - system overview, context, containers, and ADRs
- `domain\` - module specifications by business area
- `data\` - core entity and constraint documentation
- `workflows\` - cross-module process and interaction flows

## Key documents

### Architecture

- `architecture\system-overview.en.md`
- `architecture\system-overview.es.md`
- `architecture\context-and-containers.en.md`
- `architecture\context-and-containers.es.md`
- `architecture\adr\ADR-001-rewrite-stack.en.md`
- `architecture\adr\ADR-001-rewrite-stack.es.md`

### Domain specifications

- `domain\auth-permissions\spec.en.md`
- `domain\auth-permissions\spec.es.md`
- `domain\tasks-calendar\spec.en.md`
- `domain\tasks-calendar\spec.es.md`
- `domain\livestock\spec.en.md`
- `domain\livestock\spec.es.md`
- `domain\crops\spec.en.md`
- `domain\crops\spec.es.md`
- `domain\accounting\spec.en.md`
- `domain\accounting\spec.es.md`
- `domain\map-sectors\spec.en.md`
- `domain\map-sectors\spec.es.md`
- `domain\notifications\spec.en.md`
- `domain\notifications\spec.es.md`
- `domain\weather\spec.en.md`
- `domain\weather\spec.es.md`
- `domain\equipment-maintenance\spec.en.md`
- `domain\equipment-maintenance\spec.es.md`

### Shared models and workflows

- `data\core-entity-model.en.md`
- `data\core-entity-model.es.md`
- `workflows\core-workflows.en.md`
- `workflows\core-workflows.es.md`

## Delivery order

1. Standards and glossary
2. Architecture baseline
3. Domain specifications
4. Data model and workflows
5. Editorial parity pass

## Core source references

- `docs\projectPlan.md`
- `OLD\MainDocumentation\NewTraditionsSolutions.docx.html`
- `OLD\MainDocumentation\Survey\2025 APP-CAMPO.csv`
