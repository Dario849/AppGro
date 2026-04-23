"""Task model."""

from typing import Optional
from sqlmodel import SQLModel, Field
from app.models.base import OrgScopedModel


class Task(OrgScopedModel, table=True):
    """Work unit with priority, assignment, due date."""
    id: Optional[int] = Field(default=None, primary_key=True)
    title: str = Field(nullable=False)
    description: Optional[str] = None
    priority: int = Field(default=3, nullable=False)
    status: str = Field(default="pending", nullable=False)
    assigned_to_user_id: Optional[int] = Field(foreign_key="user.id", nullable=True)
    due_date: Optional[str] = None
    sector_id: Optional[int] = Field(foreign_key="sector.id", nullable=True)
    lote_id: Optional[int] = Field(foreign_key="lote.id", nullable=True)
    is_archived: bool = Field(default=False, nullable=False)
    
    __tablename__ = "task"
