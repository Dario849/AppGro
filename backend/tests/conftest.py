"""Pytest fixtures for DB testing."""

import pytest
from sqlmodel import Session, create_engine, SQLModel
from sqlalchemy.pool import StaticPool
from app.db import get_session
from app.main import app
from fastapi.testclient import TestClient


@pytest.fixture(name="session")
def session_fixture():
    """In-memory SQLite session for tests."""
    engine = create_engine(
        "sqlite:///:memory:",
        connect_args={"check_same_thread": False},
        poolclass=StaticPool,
    )
    SQLModel.metadata.create_all(engine)
    with Session(engine) as session:
        yield session


@pytest.fixture(name="client")
def client_fixture(session: Session):
    """FastAPI test client with injected session."""
    def get_session_override():
        return session

    app.dependency_overrides[get_session] = get_session_override
    client = TestClient(app)
    yield client
    app.dependency_overrides.clear()


@pytest.fixture(name="test_org1")
def test_org1_fixture(session: Session):
    """Create test organization 1."""
    from app.models import Organization
    org = Organization(name="Test Org 1", email="org1@test.local")
    session.add(org)
    session.commit()
    session.refresh(org)
    return org


@pytest.fixture(name="test_org2")
def test_org2_fixture(session: Session):
    """Create test organization 2."""
    from app.models import Organization
    org = Organization(name="Test Org 2", email="org2@test.local")
    session.add(org)
    session.commit()
    session.refresh(org)
    return org


@pytest.fixture(name="test_user_org1")
def test_user_org1_fixture(session: Session, test_org1):
    """Create test user in org1."""
    from app.models import User
    user = User(
        organization_id=test_org1.id,
        email="user1@org1.local",
        hashed_password="hashed_pwd",
        full_name="User One",
        role="operator",
    )
    session.add(user)
    session.commit()
    session.refresh(user)
    return user


@pytest.fixture(name="test_user_org2")
def test_user_org2_fixture(session: Session, test_org2):
    """Create test user in org2."""
    from app.models import User
    user = User(
        organization_id=test_org2.id,
        email="user1@org2.local",
        hashed_password="hashed_pwd",
        full_name="User Two",
        role="operator",
    )
    session.add(user)
    session.commit()
    session.refresh(user)
    return user
