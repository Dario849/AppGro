"""Livestock and crop models."""

from typing import Optional
from sqlmodel import SQLModel, Field
from app.models.base import OrgScopedModel


class Livestock(OrgScopedModel, table=True):
    """Animal tracked by lifecycle + health."""
    id: Optional[int] = Field(default=None, primary_key=True)
    identifier: str = Field(nullable=False)
    animal_type: str = Field(nullable=False)
    breed: Optional[str] = None
    birth_date: Optional[str] = None
    weight_kg: Optional[float] = None
    is_archived: bool = Field(default=False, nullable=False)
    
    __tablename__ = "livestock"


class LivestockEvent(OrgScopedModel, table=True):
    """Health event or incident for animal."""
    id: Optional[int] = Field(default=None, primary_key=True)
    livestock_id: int = Field(foreign_key="livestock.id", nullable=False)
    event_type: str = Field(nullable=False)
    event_date: str = Field(nullable=False)
    notes: Optional[str] = None
    
    __tablename__ = "livestock_event"


class Crop(OrgScopedModel, table=True):
    """Crop planting record."""
    id: Optional[int] = Field(default=None, primary_key=True)
    crop_type: str = Field(nullable=False)
    sector_id: Optional[int] = Field(foreign_key="sector.id", nullable=True)
    lote_id: Optional[int] = Field(foreign_key="lote.id", nullable=True)
    planting_date: Optional[str] = None
    harvest_date: Optional[str] = None
    quantity_planted: Optional[float] = None
    quantity_harvested: Optional[float] = None
    unit: Optional[str] = None
    is_archived: bool = Field(default=False, nullable=False)
    
    __tablename__ = "crop"


class CropTreatment(OrgScopedModel, table=True):
    """Crop treatment: spray, irrigation, fertilizer."""
    id: Optional[int] = Field(default=None, primary_key=True)
    crop_id: int = Field(foreign_key="crop.id", nullable=False)
    treatment_type: str = Field(nullable=False)
    treatment_date: str = Field(nullable=False)
    product_name: Optional[str] = None
    quantity: Optional[float] = None
    notes: Optional[str] = None
    is_archived: bool = Field(default=False, nullable=False)
    
    __tablename__ = "crop_treatment"
