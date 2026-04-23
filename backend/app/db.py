"""Database engine and session factory."""

from sqlmodel import create_engine, Session, SQLModel
from sqlalchemy.pool import NullPool
import os
from typing import Generator

DATABASE_URL = os.getenv(
    "DATABASE_URL",
    "postgresql://appgro:appgro@localhost:5432/appgro_dev"
)

engine = create_engine(
    DATABASE_URL,
    echo=os.getenv("ENVIRONMENT") == "development",
    poolclass=NullPool,
    connect_args={"connect_timeout": 10}
)


def create_db_and_tables():
    """Create all tables from SQLModel metadata."""
    SQLModel.metadata.create_all(engine)


def get_session() -> Generator[Session, None, None]:
    """FastAPI dependency for DB session."""
    with Session(engine) as session:
        yield session
