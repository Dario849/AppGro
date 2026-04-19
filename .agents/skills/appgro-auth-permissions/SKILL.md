---
name: appgro-auth-permissions
description: Authentication and role-based access control patterns for AppGro. Covers JWT tokens, user roles (admin/manager/operator/viewer), permission checking, multi-tenant isolation, and audit logging.
---

# AppGro Authentication & Permissions

## Overview

AppGro uses JWT tokens for stateless authentication and role-based access control (RBAC) to enforce agricultural operational boundaries. All operations are scoped to organization and user role.

## Authentication Flow

### Login & Token Issuance

```python
# app/routers/auth.py
from fastapi import APIRouter, Depends, HTTPException, status
from datetime import datetime, timedelta
from jose import JWTError, jwt
from passlib.context import CryptContext
from app.schemas.auth import LoginRequest, TokenResponse
from app.models.user import User
from app.config import settings

router = APIRouter(prefix="/api/auth", tags=["auth"])

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

def verify_password(plain: str, hashed: str) -> bool:
    return pwd_context.verify(plain, hashed)

def hash_password(password: str) -> str:
    return pwd_context.hash(password)

def create_access_token(data: dict, expires_delta: timedelta | None = None) -> str:
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(hours=24)
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, settings.SECRET_KEY, algorithm="HS256")
    return encoded_jwt

@router.post("/login", response_model=TokenResponse)
async def login(credentials: LoginRequest, db: Session = Depends(get_db)):
    """
    Authenticate user and return JWT token.
    Token contains: user_id, email, role, organization_id.
    """
    user = db.query(User).filter(User.email == credentials.email).first()
    if not user or not verify_password(credentials.password, user.password_hash):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid email or password"
        )
    if not user.is_active:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="User account is inactive"
        )
    
    access_token = create_access_token(data={
        "sub": str(user.id),
        "email": user.email,
        "role": user.role,
        "organization_id": user.organization_id,
    })
    
    return TokenResponse(
        access_token=access_token,
        token_type="bearer",
        user=UserResponse.from_orm(user)
    )

@router.post("/logout")
async def logout(current_user = Depends(get_current_user)):
    """
    Logout endpoint (optional; token validation is stateless).
    Can blacklist token in Redis if needed for immediate revocation.
    """
    # Optionally add token to blacklist
    return {"message": "Logged out successfully"}
```

### Token Validation & Extraction

```python
# app/dependencies.py
from fastapi import Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthCredentials
from jose import JWTError, jwt
from app.config import settings
from app.models.user import User

security = HTTPBearer()

async def get_current_user(
    credentials: HTTPAuthCredentials = Depends(security),
    db: Session = Depends(get_db),
) -> User:
    """
    Extract JWT from Authorization header, validate, and return User object.
    Raises 401 if token is invalid or expired.
    """
    token = credentials.credentials
    try:
        payload = jwt.decode(token, settings.SECRET_KEY, algorithms=["HS256"])
        user_id: int = int(payload.get("sub"))
        email: str = payload.get("email")
        role: str = payload.get("role")
        organization_id: int = payload.get("organization_id")
        
        if user_id is None or organization_id is None:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid token payload"
            )
    except JWTError:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid token"
        )
    
    # Verify user still exists and is active
    user = db.query(User).filter(
        User.id == user_id,
        User.organization_id == organization_id
    ).first()
    
    if not user or not user.is_active:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="User not found or inactive"
        )
    
    return user
```

## Role-Based Access Control (RBAC)

### Role Definitions

```python
# app/models/enums.py
from enum import Enum

class UserRole(str, Enum):
    ADMIN = "admin"           # System-wide administration
    MANAGER = "manager"       # Regional/farm management, create/edit operations
    OPERATOR = "operator"     # Field work, logging observations, task completion
    VIEWER = "viewer"         # Read-only access to reports
```

### Permission Matrix

| Action | Admin | Manager | Operator | Viewer |
|--------|-------|---------|----------|--------|
| Create task | ✓ | ✓ | | |
| Edit task | ✓ | ✓ | (own only) | |
| Complete task | ✓ | ✓ | ✓ | |
| Delete task | ✓ | | | |
| Manage livestock | ✓ | ✓ | ✓ | |
| View reports | ✓ | ✓ | ✓ | ✓ |
| Export data | ✓ | ✓ | | |
| User management | ✓ | | | |
| System config | ✓ | | | |

### Dependency Injectors for Roles

```python
# app/dependencies.py

async def get_current_admin(
    current_user: User = Depends(get_current_user),
) -> User:
    """Ensure user is admin."""
    if current_user.role != UserRole.ADMIN:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Admin role required"
        )
    return current_user

async def get_current_manager(
    current_user: User = Depends(get_current_user),
) -> User:
    """Ensure user is manager or above."""
    if current_user.role not in [UserRole.ADMIN, UserRole.MANAGER]:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Manager or above role required"
        )
    return current_user

async def get_current_operator(
    current_user: User = Depends(get_current_user),
) -> User:
    """Ensure user is operator or above."""
    if current_user.role not in [UserRole.ADMIN, UserRole.MANAGER, UserRole.OPERATOR]:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Operator or above role required"
        )
    return current_user
```

## Multi-Tenant Isolation

### Organization Scoping

Every query must include the current user's organization_id:

```python
# app/services/task_service.py

class TaskService:
    def __init__(self, db: Session, current_user: User):
        self.db = db
        self.current_user = current_user  # Contains organization_id
    
    def list_tasks(self, skip=0, limit=100, sector_id=None):
        """
        List tasks only for current user's organization.
        Operator users see only their assigned tasks.
        """
        query = self.db.query(Task).filter(
            Task.organization_id == self.current_user.organization_id
        )
        
        # Operators see only assigned tasks
        if self.current_user.role == UserRole.OPERATOR:
            query = query.filter(Task.assigned_to_id == self.current_user.id)
        
        if sector_id:
            query = query.filter(Task.sector_id == sector_id)
        
        return query.order_by(Task.priority, Task.due_date).offset(skip).limit(limit).all()
    
    def update_task(self, task_id: int, update_data: TaskUpdate) -> Task:
        """
        Update task. Operator can only update assigned tasks.
        """
        task = self.db.query(Task).filter(
            Task.id == task_id,
            Task.organization_id == self.current_user.organization_id
        ).first()
        
        if not task:
            raise HTTPException(status_code=404, detail="Task not found")
        
        # Check permission
        if self.current_user.role == UserRole.OPERATOR:
            if task.assigned_to_id != self.current_user.id:
                raise HTTPException(status_code=403, detail="Not assigned to this task")
        
        # Apply updates
        for key, value in update_data.dict(exclude_unset=True).items():
            setattr(task, key, value)
        task.updated_by_id = self.current_user.id
        
        self.db.commit()
        self.db.refresh(task)
        return task
```

### Sector-Level Permissions (Future)

For advanced multi-farm operations, extend to sector-scoped roles:

```python
# app/models/sector_assignment.py
class SectorAssignment(Base):
    __tablename__ = "sector_assignments"
    
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    sector_id = Column(Integer, ForeignKey("sectors.id"))
    role = Column(Enum(UserRole))  # May differ from global role
    assigned_at = Column(DateTime, default=datetime.utcnow)
    
    # Constraint: operator can only see/edit tasks in assigned sectors
```

## Permission Enforcement Patterns

### Service-Layer Checks

Always check permissions in the service layer, never just in the API route:

```python
# app/services/livestock_service.py

def delete_livestock_record(self, livestock_id: int):
    """
    Only managers and admins can permanently delete livestock records.
    """
    if self.current_user.role not in [UserRole.ADMIN, UserRole.MANAGER]:
        raise PermissionDenied("Only managers can delete records")
    
    livestock = self.db.query(Livestock).filter(
        Livestock.id == livestock_id,
        Livestock.organization_id == self.current_user.organization_id
    ).first()
    
    if not livestock:
        raise NotFound("Livestock not found")
    
    # Soft delete
    livestock.status = "deceased"
    livestock.updated_by_id = self.current_user.id
    self.db.commit()
```

### Audit Logging

Log all sensitive operations (create, update, delete, permission changes):

```python
# app/services/audit_service.py
from app.models.audit_log import AuditLog
from datetime import datetime

class AuditService:
    def __init__(self, db: Session):
        self.db = db
    
    def log_action(
        self,
        user_id: int,
        organization_id: int,
        action: str,           # 'CREATE', 'UPDATE', 'DELETE'
        entity_type: str,       # 'task', 'livestock', 'user'
        entity_id: int,
        changes: dict = None,   # What changed (old vs new values)
        ip_address: str = None,
    ):
        log_entry = AuditLog(
            user_id=user_id,
            organization_id=organization_id,
            action=action,
            entity_type=entity_type,
            entity_id=entity_id,
            changes=changes,  # Serialize as JSON
            ip_address=ip_address,
            timestamp=datetime.utcnow(),
        )
        self.db.add(log_entry)
        self.db.commit()

# Usage in service:
class TaskService:
    def create_task(self, task_data: TaskCreate):
        task = Task(**task_data.dict())
        self.db.add(task)
        self.db.commit()
        
        # Audit log
        audit_service = AuditService(self.db)
        audit_service.log_action(
            user_id=self.current_user.id,
            organization_id=self.current_user.organization_id,
            action="CREATE",
            entity_type="task",
            entity_id=task.id,
            changes={"task": task_data.dict()},
        )
        
        return task
```

## Passwords & Secrets

### Password Policy

```python
# app/validators/password.py
import re

def validate_password_strength(password: str):
    """
    Enforce password complexity:
    - At least 12 characters
    - At least one uppercase, one lowercase, one digit, one special char
    """
    if len(password) < 12:
        raise ValueError("Password must be at least 12 characters")
    if not re.search(r'[A-Z]', password):
        raise ValueError("Password must contain uppercase letter")
    if not re.search(r'[a-z]', password):
        raise ValueError("Password must contain lowercase letter")
    if not re.search(r'\d', password):
        raise ValueError("Password must contain digit")
    if not re.search(r'[!@#$%^&*(),.?":{}|<>]', password):
        raise ValueError("Password must contain special character")
    return True
```

### Secret Storage

- Store JWT secret in environment variable (never in code)
- Use HTTPS only (no HTTP)
- Rotate secrets quarterly
- Never log tokens or passwords

## Session Management & Token Refresh

### Token Lifetime & Refresh

```python
# app/config.py
class Settings:
    SECRET_KEY: str = os.getenv("SECRET_KEY")
    ACCESS_TOKEN_EXPIRE_MINUTES = 30
    REFRESH_TOKEN_EXPIRE_DAYS = 7

# app/routers/auth.py
@router.post("/refresh", response_model=TokenResponse)
async def refresh_token(
    refresh_token: str,
    db: Session = Depends(get_db),
):
    """
    Refresh access token using a refresh token.
    Refresh token must be stored securely in HTTP-only cookie.
    """
    try:
        payload = jwt.decode(refresh_token, settings.SECRET_KEY, algorithms=["HS256"])
        user_id = int(payload.get("sub"))
    except JWTError:
        raise HTTPException(status_code=401, detail="Invalid refresh token")
    
    user = db.query(User).filter(User.id == user_id).first()
    if not user or not user.is_active:
        raise HTTPException(status_code=401, detail="User not found")
    
    new_access_token = create_access_token(data={
        "sub": str(user.id),
        "email": user.email,
        "role": user.role,
        "organization_id": user.organization_id,
    })
    
    return TokenResponse(access_token=new_access_token, token_type="bearer")
```

## Common Pitfalls

- Checking permissions only in route handlers (middleware can be bypassed)
- Forgetting organization_id checks (data leakage across orgs)
- Storing plaintext passwords in database
- Not logging sensitive operations (audit trail loss)
- Token without expiration (stolen token is valid forever)
- Mixing authentication with business logic validation
- No rate limiting on login attempts (brute force vulnerability)
- Exposing internal error details to frontend (information disclosure)
