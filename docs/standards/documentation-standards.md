# Documentation Standards

## Purpose

Define how AppGro documentation is written, organized, reviewed, and kept traceable to discovery inputs.

## Scope

These rules apply to architecture docs, domain specs, data model docs, workflow docs, and ADRs under `docs\`.

## Authoring rules

1. English is the canonical drafting source.
2. Spanish mirrors the approved English content in separate paired files.
3. Domain terms must stay aligned with stakeholder language such as `sector`, `lote`, `caravana`, `ganado`, and `cultivo`.
4. Every document must cite at least one source from `docs\projectPlan.md` or `OLD\`.
5. Unknowns must be explicit under an **Open questions** section.

## Required sections

Every specification-level document should include:

1. Purpose
2. Audience
3. Source references
4. Design summary
5. Mermaid diagram
6. Business rules and constraints
7. Roles and permissions implications
8. Edge cases
9. Open questions
10. Testability notes

## Bilingual file rules

- Use `topic.en.md` for canonical English content.
- Use `topic.es.md` for Spanish content.
- Keep headings aligned between both files.
- Update English first, then Spanish.
- If Spanish is temporarily behind, note it in the PR instead of silently drifting.

## Mermaid standards

Use focused diagrams rather than one oversized diagram.

| Need | Mermaid type |
| --- | --- |
| System boundaries | C4 context or container |
| Business steps | Flowchart |
| Cross-service interactions | Sequence diagram |
| Data relationships | ER diagram |
| Lifecycle state | State diagram or flowchart |

### Diagram rules

- One diagram should explain one main concept.
- Use clear labels instead of abbreviations.
- Prefer domain names over technical shorthand.
- Add short notes where relationships are not obvious.
- Keep layout readable in GitHub Markdown rendering.

## Suggested source citation format

```md
## Source references

- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`
- `OLD/MainDocumentation/Survey/2025 APP-CAMPO.csv`
```

## Spec template

```md
# Title

## Purpose
## Audience
## Source references
## Design summary
## Diagram
## Business rules and constraints
## Roles and permissions implications
## Edge cases
## Open questions
## Testability notes
```

## Review checklist

- Does the document preserve business intent rather than legacy implementation?
- Are domain terms consistent with agricultural usage?
- Are server-side authorization responsibilities explicit?
- Are data constraints stated clearly enough for implementation?
- Are offline/manual field-operation scenarios covered when relevant?
