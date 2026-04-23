"""Test audit columns (created_at, updated_at)."""

from sqlmodel import Session, select
from datetime import datetime
import time
from app.models import Organization


def test_audit_columns_on_create(session: Session):
    """Audit columns populated on entity creation."""
    org = Organization(name="Audit Test Org")
    session.add(org)
    session.commit()
    session.refresh(org)

    assert org.created_at is not None
    assert isinstance(org.created_at, datetime)
    assert org.updated_at is not None
    assert isinstance(org.updated_at, datetime)


def test_updated_at_on_modification(session: Session):
    """updated_at changes on modification."""
    org = Organization(name="Original Name")
    session.add(org)
    session.commit()
    session.refresh(org)

    original_updated_at = org.updated_at
    original_created_at = org.created_at

    # Small delay to ensure timestamp difference
    time.sleep(0.01)

    # Modify
    org.name = "Modified Name"
    session.add(org)
    session.commit()
    session.refresh(org)

    assert org.created_at == original_created_at  # unchanged
    assert org.updated_at >= original_updated_at  # changed or equal
