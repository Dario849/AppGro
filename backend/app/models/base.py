"""Base models with audit and scoping mixins."""

from datetime import datetime
from typing import Optional
from sqlmodel import SQLModel, Field


class TimestampMixin:
    """Audit timestamp fields."""
    created_at: datetime = Field(default_factory=datetime.utcnow, nullable=False, index=True)
    updated_at: datetime = Field(default_factory=datetime.utcnow, nullable=False, index=True)


class OrgScopedMixin:
    """Multi-tenant organization scoping."""
    organization_id: int = Field(foreign_key="organization.id", nullable=False, index=True)


class BaseModel(SQLModel, TimestampMixin):
    """All entities have created_at/updated_at."""
    pass


class OrgScopedModel(BaseModel, OrgScopedMixin):
    """Operational entities scoped to organization."""
    pass
