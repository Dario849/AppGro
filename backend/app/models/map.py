"""Sector and lote (plot) models."""

from typing import Optional
from sqlmodel import SQLModel, Field
from app.models.base import OrgScopedModel


class Sector(OrgScopedModel, table=True):
    """Land operational area."""
    id: Optional[int] = Field(default=None, primary_key=True)
    name: str = Field(nullable=False, index=True)
    area_hectares: Optional[float] = None
    location_notes: Optional[str] = None
    is_active: bool = Field(default=True, nullable=False)
    
    __tablename__ = "sector"


class Lote(OrgScopedModel, table=True):
    """Plot subdivision within sector."""
    id: Optional[int] = Field(default=None, primary_key=True)
    sector_id: int = Field(foreign_key="sector.id", nullable=False, index=True)
    name: str = Field(nullable=False)
    area_hectares: Optional[float] = None
    soil_type: Optional[str] = None
    is_active: bool = Field(default=True, nullable=False)
    
    __tablename__ = "lote"
