# AppGro Backend

FastAPI + SQLModel + PostgreSQL agricultural API.

## Setup

```bash
cd backend
python -m venv venv
# Windows: .\venv\Scripts\Activate.ps1
# Unix: source venv/bin/activate

pip install -e ".[dev]"
cp .env.example .env
# Edit .env with your PostgreSQL URL
```

## Run Dev

```bash
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

## Test

```bash
pytest -v tests/
```

## Database

Requires PostgreSQL 13+. See docs/guides/db-setup.md for Docker setup.

### Migrations

```bash
alembic revision --autogenerate -m "description"
alembic upgrade head
alembic downgrade -1
```

## Project Structure

```
backend/
├── app/
│   ├── models/       # SQLModel ORM models
│   ├── routers/      # FastAPI route handlers
│   ├── schemas/      # Pydantic request/response
│   ├── services/     # Business logic
│   ├── main.py       # FastAPI app
│   └── db.py         # Database config
├── migrations/       # Alembic schema versions
├── tests/            # Integration + unit tests
└── pyproject.toml    # Dependencies
```

## Principles

- Multi-tenant: Every entity scoped to `organization_id`
- Soft delete: Use `is_archived=true` not hard DELETE
- Audit: All entities have `created_at`, `updated_at`
- Org isolation: No cross-org data leaks (test exhaustively)
