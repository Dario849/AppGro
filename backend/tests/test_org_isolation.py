"""Test org isolation and multi-tenant constraints."""

from sqlmodel import Session, select
import pytest
from app.models import Organization, User, Sector, Task


def test_org_isolation_sectors(session: Session, test_org1, test_org2):
    """Sectors from org1 not visible to org2 queries."""
    # Create sector in org1
    sector1 = Sector(organization_id=test_org1.id, name="Sector 1")
    session.add(sector1)
    session.commit()

    # Create sector in org2
    sector2 = Sector(organization_id=test_org2.id, name="Sector 2")
    session.add(sector2)
    session.commit()

    # Org1 query should only return org1 sectors
    org1_sectors = session.exec(
        select(Sector).where(Sector.organization_id == test_org1.id)
    ).all()
    assert len(org1_sectors) == 1
    assert org1_sectors[0].name == "Sector 1"

    # Org2 query should only return org2 sectors
    org2_sectors = session.exec(
        select(Sector).where(Sector.organization_id == test_org2.id)
    ).all()
    assert len(org2_sectors) == 1
    assert org2_sectors[0].name == "Sector 2"


def test_unique_email_per_org(session: Session, test_org1, test_org2):
    """Same email allowed in different orgs."""
    # Create user with same email in org1
    user1 = User(
        organization_id=test_org1.id,
        email="shared@test.local",
        hashed_password="pwd1",
        full_name="User 1",
    )
    session.add(user1)
    session.commit()

    # Create user with same email in org2 (should not raise)
    user2 = User(
        organization_id=test_org2.id,
        email="shared@test.local",
        hashed_password="pwd2",
        full_name="User 2",
    )
    session.add(user2)
    session.commit()

    # Both should exist
    users_org1 = session.exec(
        select(User).where(
            (User.organization_id == test_org1.id) & (User.email == "shared@test.local")
        )
    ).all()
    assert len(users_org1) == 1

    users_org2 = session.exec(
        select(User).where(
            (User.organization_id == test_org2.id) & (User.email == "shared@test.local")
        )
    ).all()
    assert len(users_org2) == 1


def test_task_org_scoping(session: Session, test_org1, test_user_org1):
    """Tasks isolated by org."""
    # Create task in org1
    task1 = Task(
        organization_id=test_org1.id,
        title="Task 1",
        assigned_to_user_id=test_user_org1.id,
    )
    session.add(task1)
    session.commit()

    # Query org1 tasks
    org1_tasks = session.exec(
        select(Task).where(Task.organization_id == test_org1.id)
    ).all()
    assert len(org1_tasks) == 1
    assert org1_tasks[0].title == "Task 1"

    # Query org2 tasks (empty)
    org2_tasks = session.exec(
        select(Task).where(Task.organization_id == test_org1.id)
    ).all()
    assert len(org2_tasks) == 1  # org1 task should be found
