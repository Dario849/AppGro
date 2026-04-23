"""Test soft-delete (is_archived) pattern."""

from sqlmodel import Session, select
import pytest
from app.models import Task, Livestock, Crop, CropTreatment


def test_task_soft_delete(session: Session, test_org1):
    """Tasks with is_archived=True excluded from default queries."""
    # Create active task
    task_active = Task(
        organization_id=test_org1.id,
        title="Active Task",
        status="pending",
        is_archived=False,
    )
    session.add(task_active)

    # Create archived task
    task_archived = Task(
        organization_id=test_org1.id,
        title="Archived Task",
        status="completed",
        is_archived=True,
    )
    session.add(task_archived)
    session.commit()

    # Default query (no archived filter) returns both
    all_tasks = session.exec(select(Task)).all()
    assert len(all_tasks) == 2

    # Active-only query
    active_tasks = session.exec(
        select(Task).where(Task.is_archived == False)
    ).all()
    assert len(active_tasks) == 1
    assert active_tasks[0].title == "Active Task"

    # Archive-only query
    archived_tasks = session.exec(
        select(Task).where(Task.is_archived == True)
    ).all()
    assert len(archived_tasks) == 1
    assert archived_tasks[0].title == "Archived Task"


def test_livestock_soft_delete(session: Session, test_org1):
    """Livestock soft-delete via is_archived."""
    livestock1 = Livestock(
        organization_id=test_org1.id,
        identifier="001",
        animal_type="bovine",
        is_archived=False,
    )
    session.add(livestock1)

    livestock2 = Livestock(
        organization_id=test_org1.id,
        identifier="002",
        animal_type="bovine",
        is_archived=True,
    )
    session.add(livestock2)
    session.commit()

    active = session.exec(
        select(Livestock).where(Livestock.is_archived == False)
    ).all()
    assert len(active) == 1
    assert active[0].identifier == "001"


def test_crop_treatment_soft_delete(session: Session, test_org1):
    """Crop treatment records support soft-delete."""
    crop = Crop(
        organization_id=test_org1.id,
        crop_type="soybean",
        is_archived=False,
    )
    session.add(crop)
    session.commit()

    treatment1 = CropTreatment(
        organization_id=test_org1.id,
        crop_id=crop.id,
        treatment_type="pesticide",
        treatment_date="2026-04-23",
        is_archived=False,
    )
    session.add(treatment1)

    treatment2 = CropTreatment(
        organization_id=test_org1.id,
        crop_id=crop.id,
        treatment_type="fertilizer",
        treatment_date="2026-04-20",
        is_archived=True,
    )
    session.add(treatment2)
    session.commit()

    # Active treatments
    active = session.exec(
        select(CropTreatment)
        .where(CropTreatment.crop_id == crop.id)
        .where(CropTreatment.is_archived == False)
    ).all()
    assert len(active) == 1
    assert active[0].treatment_type == "pesticide"
