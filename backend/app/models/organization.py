"""Organization and user models."""

from typing import Optional
from sqlmodel import SQLModel, Field
from app.models.base import BaseModel


class Organization(BaseModel, table=True):
    """Root tenant entity."""
    id: Optional[int] = Field(default=None, primary_key=True)
    name: str = Field(index=True, nullable=False)
    email: Optional[str] = None
    address: Optional[str] = None
    phone: Optional[str] = None
    is_active: bool = Field(default=True, nullable=False)
    
    __tablename__ = "organization"


class User(BaseModel, table=True):
    """User with org scope and role."""
    id: Optional[int] = Field(default=None, primary_key=True)
    organization_id: int = Field(foreign_key="organization.id", nullable=False, index=True)
    email: str = Field(nullable=False, index=True)
    hashed_password: str = Field(nullable=False)
    full_name: str = Field(nullable=False)
    role: str = Field(default="operator", nullable=False)
    is_active: bool = Field(default=True, nullable=False)
    
    __tablename__ = "user"
