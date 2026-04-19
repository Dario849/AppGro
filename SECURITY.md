# Security Policy

## Supported Scope

This is an active rewrite repository. Security posture is evolving while architecture is stabilized.

## Reporting a Vulnerability

Please report vulnerabilities privately to repository maintainers.
Do not publish exploitable details in public issues.

Report should include:
- Affected area (frontend, backend, auth, data layer, infra)
- Reproduction steps
- Impact level
- Suggested mitigation (if known)

## Baseline Security Expectations

- Never commit secrets or credentials
- Use environment variables for secrets
- Validate all API inputs
- Enforce role checks server-side
- Log security-relevant events in auth and privileged operations
- Apply least privilege for database access
