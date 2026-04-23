"""FastAPI app entry point."""

from fastapi import FastAPI
from app.db import create_db_and_tables

app = FastAPI(
    title="AppGro API",
    description="Agricultural management API",
    version="0.1.0"
)


@app.on_event("startup")
def on_startup():
    """Initialize DB tables."""
    create_db_and_tables()


@app.get("/health")
def health():
    """Health check."""
    return {"status": "ok"}
