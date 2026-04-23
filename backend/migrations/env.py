import os
from logging.config import fileConfig
from dotenv import load_dotenv

from sqlalchemy import pool, create_engine
from sqlalchemy.engine import Connection

from alembic import context
from sqlmodel import SQLModel
from app.models import *  # noqa: F401, F403

load_dotenv()

config = context.config

if config.config_file_name is not None:
    fileConfig(config.config_file_name)

target_metadata = SQLModel.metadata

def run_migrations_offline() -> None:
    """Run migrations in 'offline' mode."""
    url = os.getenv("DATABASE_URL", "postgresql://appgro:appgro@localhost:5432/appgro_dev")
    context.configure(
        url=url,
        target_metadata=target_metadata,
        literal_binds=False,
        dialect_opts={"paramstyle": "named"},
    )

    with context.begin_transaction():
        context.run_migrations()


def do_run_migrations(connection: Connection) -> None:
    context.configure(connection=connection, target_metadata=target_metadata)

    with context.begin_transaction():
        context.run_migrations()


def run_migrations_online() -> None:
    """Run migrations in 'online' mode (sync)."""
    url = os.getenv("DATABASE_URL")
    
    if not url:
        # Fall back to offline if DB URL not set
        run_migrations_offline()
        return
    
    connectable = create_engine(
        url,
        poolclass=pool.NullPool,
    )

    with connectable.connect() as connection:
        connection.run_sync(do_run_migrations)

    connectable.dispose()


if context.is_offline_mode():
    run_migrations_offline()
else:
    run_migrations_online()
