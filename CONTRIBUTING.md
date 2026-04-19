# Contributing

Thanks for contributing to the AppGro rewrite.

## Scope

This repository is focused on migration/rewrite to Astro + FastAPI using legacy documentation as product input.

## Workflow

1. Create a branch from the rewrite mainline.
2. Keep PRs focused on one concern.
3. Update docs when adding or changing behavior.
4. Add tests for backend logic and critical workflows.

## Commit Style

Use clear, imperative commit messages.

Recommended prefixes:
- `feat:` new functionality
- `fix:` bug fixes
- `docs:` documentation only
- `refactor:` structure change without behavior change
- `test:` test additions/changes
- `chore:` maintenance

## Pull Requests

PRs should include:
- Summary of intent
- Linked requirement/module from docs
- Risk notes
- Testing evidence

If a PR changes domain behavior, include which module is affected:
- Users/Auth
- Tasks/Calendar
- Livestock
- Crops
- Accounting
- Map
- Tools
- Notifications
- Weather

## AI Contributions

If generated with AI tools:
- Verify correctness manually
- Confirm domain terminology aligns with `OLD/` findings
- Ensure no sensitive data is introduced
- Keep outputs deterministic and reviewable
