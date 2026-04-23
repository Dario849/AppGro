"""Test model instantiation and basic CRUD."""

from sqlmodel import Session, select
import pytest
from app.models import (
    Organization, User, Sector, Lote, Task, Livestock, LivestockEvent,
    Crop, CropTreatment, AccountingTransaction, Notification, WeatherObservation,
    Asset, MaintenanceLog
)


def test_organization_create(session: Session):
    """Organization creation."""
    org = Organization(name="Test Org", email="test@org.local")
    session.add(org)
    session.commit()
    session.refresh(org)

    assert org.id is not None
    assert org.name == "Test Org"
    assert org.is_active is True


def test_user_create(session: Session, test_org1):
    """User creation with org."""
    user = User(
        organization_id=test_org1.id,
        email="user@test.local",
        hashed_password="hash",
        full_name="Test User",
        role="operator",
    )
    session.add(user)
    session.commit()
    session.refresh(user)

    assert user.id is not None
    assert user.organization_id == test_org1.id
    assert user.role == "operator"


def test_sector_create(session: Session, test_org1):
    """Sector creation."""
    sector = Sector(
        organization_id=test_org1.id,
        name="Test Sector",
        area_hectares=100.0,
    )
    session.add(sector)
    session.commit()
    session.refresh(sector)

    assert sector.id is not None
    assert sector.name == "Test Sector"


def test_lote_create(session: Session, test_org1):
    """Lote creation."""
    sector = Sector(organization_id=test_org1.id, name="Sector")
    session.add(sector)
    session.commit()

    lote = Lote(
        organization_id=test_org1.id,
        sector_id=sector.id,
        name="Plot A",
        area_hectares=50.0,
    )
    session.add(lote)
    session.commit()
    session.refresh(lote)

    assert lote.id is not None
    assert lote.sector_id == sector.id


def test_task_create(session: Session, test_org1, test_user_org1):
    """Task creation with assignment."""
    task = Task(
        organization_id=test_org1.id,
        title="Sample Task",
        priority=1,
        status="pending",
        assigned_to_user_id=test_user_org1.id,
    )
    session.add(task)
    session.commit()
    session.refresh(task)

    assert task.id is not None
    assert task.assigned_to_user_id == test_user_org1.id


def test_livestock_create(session: Session, test_org1):
    """Livestock creation."""
    livestock = Livestock(
        organization_id=test_org1.id,
        identifier="001",
        animal_type="bovine",
        breed="Angus",
    )
    session.add(livestock)
    session.commit()
    session.refresh(livestock)

    assert livestock.id is not None
    assert livestock.identifier == "001"


def test_livestock_event_create(session: Session, test_org1):
    """Livestock event creation."""
    livestock = Livestock(
        organization_id=test_org1.id,
        identifier="001",
        animal_type="bovine",
    )
    session.add(livestock)
    session.commit()

    event = LivestockEvent(
        organization_id=test_org1.id,
        livestock_id=livestock.id,
        event_type="vaccination",
        event_date="2026-04-23",
        notes="Annual vaccine",
    )
    session.add(event)
    session.commit()
    session.refresh(event)

    assert event.id is not None
    assert event.event_type == "vaccination"


def test_crop_create(session: Session, test_org1):
    """Crop creation."""
    crop = Crop(
        organization_id=test_org1.id,
        crop_type="soybean",
        planting_date="2026-04-01",
    )
    session.add(crop)
    session.commit()
    session.refresh(crop)

    assert crop.id is not None
    assert crop.crop_type == "soybean"


def test_crop_treatment_create(session: Session, test_org1):
    """Crop treatment creation."""
    crop = Crop(organization_id=test_org1.id, crop_type="corn")
    session.add(crop)
    session.commit()

    treatment = CropTreatment(
        organization_id=test_org1.id,
        crop_id=crop.id,
        treatment_type="pesticide",
        treatment_date="2026-04-23",
        product_name="Roundup",
    )
    session.add(treatment)
    session.commit()
    session.refresh(treatment)

    assert treatment.id is not None
    assert treatment.product_name == "Roundup"


def test_accounting_transaction_create(session: Session, test_org1):
    """Accounting transaction creation."""
    transaction = AccountingTransaction(
        organization_id=test_org1.id,
        transaction_type="expense",
        amount=1000.00,
        category="seeds",
        transaction_date="2026-04-23",
    )
    session.add(transaction)
    session.commit()
    session.refresh(transaction)

    assert transaction.id is not None
    assert transaction.amount == 1000.00


def test_notification_create(session: Session, test_org1, test_user_org1):
    """Notification creation."""
    notification = Notification(
        organization_id=test_org1.id,
        user_id=test_user_org1.id,
        title="Task Due",
        message="Your task is due tomorrow",
        notification_type="task",
    )
    session.add(notification)
    session.commit()
    session.refresh(notification)

    assert notification.id is not None
    assert notification.is_read is False


def test_weather_observation_create(session: Session, test_org1):
    """Weather observation creation."""
    obs = WeatherObservation(
        organization_id=test_org1.id,
        observation_date="2026-04-23",
        weather_type="rain",
        notes="Heavy rain",
    )
    session.add(obs)
    session.commit()
    session.refresh(obs)

    assert obs.id is not None
    assert obs.weather_type == "rain"


def test_asset_create(session: Session, test_org1):
    """Asset creation."""
    asset = Asset(
        organization_id=test_org1.id,
        name="Tractor",
        asset_type="equipment",
        status="active",
    )
    session.add(asset)
    session.commit()
    session.refresh(asset)

    assert asset.id is not None
    assert asset.asset_type == "equipment"


def test_maintenance_log_create(session: Session, test_org1):
    """Maintenance log creation."""
    asset = Asset(
        organization_id=test_org1.id,
        name="Tractor",
        asset_type="equipment",
    )
    session.add(asset)
    session.commit()

    log = MaintenanceLog(
        organization_id=test_org1.id,
        asset_id=asset.id,
        maintenance_date="2026-04-23",
        maintenance_type="repair",
        cost=500.00,
    )
    session.add(log)
    session.commit()
    session.refresh(log)

    assert log.id is not None
    assert log.cost == 500.00
