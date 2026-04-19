---
name: rewrite-documentation-specs
description: Documentation-first skill for writing technical specs, decision records, and Mermaid diagrams for the AppGro rewrite program.
---

# Rewrite Documentation Specs Skill

## Purpose

Produce implementation-ready docs that connect legacy requirements to new architecture.

## Document Types

- Product requirement specs by module
- API specs and error semantics
- Data model specs and constraints
- Architecture decisions (ADR style)
- Test scenarios and acceptance criteria

## Mermaid Expectations

Use Mermaid for:
- C4 context/container diagrams
- Sequence diagrams (critical workflows)
- ER diagrams (data model)
- Flowcharts (task and approval flows)

## Traceability Rule

Each spec should include:
- Source requirement reference (`OLD/` or `docs/projectPlan.md`)
- Design choice summary
- Open questions
- Testability notes

## Quality Checklist

- Terms are domain-accurate
- Assumptions are explicit
- Edge cases are listed
- Security/privacy implications are noted
- Success criteria are measurable
