---
name: fastapi-backend-patterns
description: FastAPI routing, dependency injection, validation, error handling, and testing patterns for AppGro backend. Covers modular router design, pydantic schemas, database access patterns, and agricultural domain constraints.
---

# FastAPI Backend Patterns for AppGro

## Overview

AppGro backend is built with FastAPI to provide RESTful APIs for the Astro frontend. This skill guides modular architecture, schema design, database access, and testing.

## Core Principles

1. **Domain-driven routers**: Each domain module (tasks, livestock, crops, accounting) has its own router
2. **Schema-first design**: Pydantic models define request/response contracts before implementation
3. **Dependency injection**: Use FastAPI dependencies for auth, database, services
4. **Explicit validation**: Enforce business rules in schemas and service layer
5. **Immutable audit trails**: Track mutations with timestamps and user context

## Project Structure

```
app/
  routers/
    __init__.py
    auth.py           # /api/auth
    tasks.py          # /api/tasks
    livestock.py      # /api/livestock
    crops.py          # /api/crops
    accounting.py     # /api/accounting
    map.py            # /api/map
    notifications.py  # /api/notifications
    weather.py        # /api/weather
  schemas/
    task.py
    livestock.py
    # ...
  models/             # SQLAlchemy models
    __init__.py
    task.py
    user.py
    # ...
  services/           # Business logic
    task_service.py
    livestock_service.py
    # ...
  dependencies.py     # DI functions (get_current_user, get_db, etc.)
  config.py           # Settings, env vars
  main.py             # App initialization
```

## Router Pattern

### Modular Router Template

```python
# app/routers/tasks.py
from fastapi import APIRouter, Depends, status
from sqlalchemy.orm import Session
from app.schemas.task import TaskCreate, TaskUpdate, TaskResponse
from app.services.task_service import TaskService
from app.dependencies import get_db, get_current_user

router = APIRouter(prefix="/api/tasks", tags=["tasks"])

@router.get("/", response_model=list[TaskResponse])
async def list_tasks(
    skip: int = 0,
    limit: int = 100,
    sector_id: int | None = None,
    db: Session = Depends(get_db),
    current_user = Depends(get_current_user),
):
    """
    List tasks filtered by sector, ordered by priority then due date.
    Only returns tasks visible to current_user's role.
    """
    service = TaskService(db, current_user)
    return service.list_tasks(skip=skip, limit=limit, sector_id=sector_id)

@router.post("/", status_code=status.HTTP_201_CREATED, response_model=TaskResponse)
async def create_task(
    task: TaskCreate,
    db: Session = Depends(get_db),
    current_user = Depends(get_current_user),
):
    """Create a new task. Only managers and admins can create tasks."""
    if current_user.role not in ["manager", "admin"]:
        raise HTTPException(status_code=403, detail="Not authorized")
    service = TaskService(db, current_user)
    return service.create_task(task)

# ... PATCH, DELETE endpoints follow similar pattern
```

## Schema Design

### Request Schema

```python
# app/schemas/task.py
from pydantic import BaseModel, Field, validator
from datetime import datetime
from enum import Enum

class TaskPriority(str, Enum):
    LOW = "low"
    MEDIUM = "medium"
    HIGH = "high"
    URGENT = "urgent"

class TaskCreate(BaseModel):
    title: str = Field(..., min_length=3, max_length=200)
    description: str | None = None
    sector_id: int
    priority: TaskPriority
    due_date: datetime
    assigned_to_id: int | None = None
    
    @validator('due_date')
    def due_date_not_past(cls, v):
        if v < datetime.now():
            raise ValueError('due_date must be in the future')
        return v
    
    @validator('title')
    def title_not_duplicate_in_sector(cls, v, values):
        # Could check database here if needed
        # For now, basic string validation
        return v.strip()
```

### Response Schema

```python
class TaskResponse(BaseModel):
    id: int
    title: str
    description: str | None
    sector_id: int
    priority: TaskPriority
    due_date: datetime
    assigned_to_id: int | None
    created_at: datetime
    updated_at: datetime
    completed_at: datetime | None
    
    class Config:
        from_attributes = True  # Read from SQLAlchemy models
```

## Dependency Injection Pattern

```python
# app/dependencies.py
from fastapi import Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthCredentials
from sqlalchemy.orm import Session
from app.config import get_db_session
from app.services.auth_service import AuthService

security = HTTPBearer()

async def get_db() -> Session:
    db = get_db_session()
    try:
        yield db
    finally:
        db.close()

async def get_current_user(
    credentials: HTTPAuthCredentials = Depends(security),
    db: Session = Depends(get_db),
):
    """Extract and validate JWT token, return User object."""
    auth_service = AuthService(db)
    user = auth_service.verify_token(credentials.credentials)
    if not user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid token"
        )
    return user

async def get_current_admin(
    current_user = Depends(get_current_user),
):
    """Verify user is admin."""
    if current_user.role != "admin":
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Admin role required"
        )
    return current_user
```

## Service Layer Pattern

```python
# app/services/task_service.py
from sqlalchemy.orm import Session
from sqlalchemy import and_
from app.models.task import Task
from app.schemas.task import TaskCreate, TaskUpdate
from app.exceptions import TaskNotFound, PermissionDenied

class TaskService:
    def __init__(self, db: Session, current_user):
        self.db = db
        self.current_user = current_user
    
    def list_tasks(self, skip=0, limit=100, sector_id=None):
        query = self.db.query(Task)
        if sector_id:
            query = query.filter(Task.sector_id == sector_id)
        query = query.order_by(Task.priority, Task.due_date)
        return query.offset(skip).limit(limit).all()
    
    def create_task(self, task_data: TaskCreate) -> Task:
        new_task = Task(
            title=task_data.title,
            description=task_data.description,
            sector_id=task_data.sector_id,
            priority=task_data.priority,
            due_date=task_data.due_date,
            assigned_to_id=task_data.assigned_to_id,
            created_by_id=self.current_user.id,
        )
        self.db.add(new_task)
        self.db.commit()
        self.db.refresh(new_task)
        return new_task
    
    def update_task(self, task_id: int, task_data: TaskUpdate) -> Task:
        task = self.db.query(Task).filter(Task.id == task_id).first()
        if not task:
            raise TaskNotFound()
        if task.created_by_id != self.current_user.id and self.current_user.role != "admin":
            raise PermissionDenied()
        
        for key, value in task_data.dict(exclude_unset=True).items():
            setattr(task, key, value)
        task.updated_at = datetime.utcnow()
        task.updated_by_id = self.current_user.id
        
        self.db.commit()
        self.db.refresh(task)
        return task
```

## Error Handling

### Custom Exceptions

```python
# app/exceptions.py
from fastapi import HTTPException, status

class TaskNotFound(HTTPException):
    def __init__(self):
        super().__init__(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Task not found"
        )

class PermissionDenied(HTTPException):
    def __init__(self):
        super().__init__(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Permission denied"
        )

class ValidationError(HTTPException):
    def __init__(self, detail: str):
        super().__init__(
            status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
            detail=detail
        )
```

### Global Error Handler

```python
# app/main.py
from fastapi import FastAPI
from fastapi.responses import JSONResponse
from app.exceptions import TaskNotFound, PermissionDenied

app = FastAPI()

@app.exception_handler(TaskNotFound)
async def task_not_found_handler(request, exc):
    return JSONResponse(
        status_code=404,
        content={"detail": exc.detail, "code": "TASK_NOT_FOUND"}
    )
```

## Agricultural Domain Constraints

### Units and Conversions

```python
# app/services/livestock_service.py
from enum import Enum

class WeightUnit(str, Enum):
    KG = "kg"
    LB = "lb"

def normalize_weight(value: float, from_unit: WeightUnit) -> float:
    """Convert to kg (canonical unit)."""
    if from_unit == WeightUnit.LB:
        return value * 0.453592
    return value

def get_weight_in_unit(kg: float, to_unit: WeightUnit) -> float:
    """Convert from kg to target unit."""
    if to_unit == WeightUnit.LB:
        return kg / 0.453592
    return kg
```

### Seasonal Reporting

```python
# app/services/accounting_service.py
from datetime import datetime

def get_accounting_period(date: datetime) -> tuple[datetime, datetime]:
    """
    Agricultural year runs Jan-Dec.
    Return (start, end) of calendar year.
    """
    year = date.year
    return datetime(year, 1, 1), datetime(year, 12, 31, 23, 59, 59)

def get_weekly_summary(start_date: datetime) -> dict:
    """Return accounting summary for the week starting start_date."""
    # Fetch transactions, aggregate by category
    # Return totals for purchases, sales, balances
    pass
```

## Testing Strategy

### Unit Test Example

```python
# tests/services/test_task_service.py
import pytest
from app.services.task_service import TaskService
from app.schemas.task import TaskCreate
from unittest.mock import MagicMock

@pytest.fixture
def mock_db():
    return MagicMock()

@pytest.fixture
def mock_user():
    return MagicMock(id=1, role="manager")

def test_create_task_success(mock_db, mock_user):
    service = TaskService(mock_db, mock_user)
    task_data = TaskCreate(
        title="Water crops",
        sector_id=1,
        priority="high",
        due_date=datetime(2026, 4, 20)
    )
    result = service.create_task(task_data)
    assert result.title == "Water crops"
    assert mock_db.add.called
    assert mock_db.commit.called
```

### Integration Test Example

```python
# tests/routers/test_tasks.py
from fastapi.testclient import TestClient
from app.main import app

client = TestClient(app)

def test_list_tasks_requires_auth():
    response = client.get("/api/tasks")
    assert response.status_code == 401

def test_create_task_success(auth_headers):
    response = client.post(
        "/api/tasks",
        json={
            "title": "Fertilize sector 2",
            "sector_id": 2,
            "priority": "medium",
            "due_date": "2026-04-25T10:00:00"
        },
        headers=auth_headers
    )
    assert response.status_code == 201
    assert response.json()["id"] > 0
```

## Performance Considerations

- Index frequently queried fields (sector_id, due_date, priority)
- Use database query pagination (skip/limit)
- Cache weather data and sector configurations
- Implement query result caching with TTL (Redis)
- Monitor slow queries; optimize N+1 patterns with eager loading

## Common Pitfalls

- Mixing business logic with FastAPI route handlers
- Missing permission checks before database operations
- Exposing internal IDs or relationships to frontend
- Not validating input against agricultural constraints (dates, units)
- Forgetting to audit create/update operations with user context
- Ignoring offline-first requirements (APIs must support batch/bulk operations)
