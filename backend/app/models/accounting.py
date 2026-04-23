"""Accounting and supporting models."""

from typing import Optional
from sqlmodel import SQLModel, Field
from app.models.base import OrgScopedModel


class AccountingTransaction(OrgScopedModel, table=True):
    """Financial transaction (income/expense)."""
    id: Optional[int] = Field(default=None, primary_key=True)
    transaction_type: str = Field(nullable=False)
    amount: float = Field(nullable=False)
    category: str = Field(nullable=False)
    transaction_date: str = Field(nullable=False)
    description: Optional[str] = None
    related_task_id: Optional[int] = Field(foreign_key="task.id", nullable=True)
    
    __tablename__ = "accounting_transaction"


class Notification(OrgScopedModel, table=True):
    """User notification for events/tasks."""
    id: Optional[int] = Field(default=None, primary_key=True)
    user_id: int = Field(foreign_key="user.id", nullable=False)
    title: str = Field(nullable=False)
    message: str = Field(nullable=False)
    notification_type: str = Field(nullable=False)
    is_read: bool = Field(default=False, nullable=False)
    
    __tablename__ = "notification"


class WeatherObservation(OrgScopedModel, table=True):
    """Weather event relevant to operations."""
    id: Optional[int] = Field(default=None, primary_key=True)
    sector_id: Optional[int] = Field(foreign_key="sector.id", nullable=True)
    observation_date: str = Field(nullable=False)
    weather_type: str = Field(nullable=False)
    notes: Optional[str] = None
    
    __tablename__ = "weather_observation"


class Asset(OrgScopedModel, table=True):
    """Equipment or infrastructure (herramientas)."""
    id: Optional[int] = Field(default=None, primary_key=True)
    name: str = Field(nullable=False)
    asset_type: str = Field(nullable=False)
    purchase_date: Optional[str] = None
    status: str = Field(default="active", nullable=False)
    is_archived: bool = Field(default=False, nullable=False)
    
    __tablename__ = "asset"


class MaintenanceLog(OrgScopedModel, table=True):
    """Asset maintenance history."""
    id: Optional[int] = Field(default=None, primary_key=True)
    asset_id: int = Field(foreign_key="asset.id", nullable=False)
    maintenance_date: str = Field(nullable=False)
    maintenance_type: str = Field(nullable=False)
    notes: Optional[str] = None
    cost: Optional[float] = None
    
    __tablename__ = "maintenance_log"
