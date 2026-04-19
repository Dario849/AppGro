---
name: appgro-data-model
description: Data schema and entity relationships for AppGro based on agricultural domain requirements. Covers entities for tasks, livestock, crops, accounting, map sectors, and notifications with normalization and audit trail patterns.
---

# AppGro Data Model

## Overview

The AppGro data model reflects agricultural operations: tasks with priorities, livestock and crop lifecycle tracking, financial records, and spatial organization through sectors and plots (lotes).

All mutable entities maintain created/updated timestamps and audit context (user, datetime).

## Core Entities

### Users & Roles

```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'operator', 'viewer') NOT NULL,
    organization_id INTEGER NOT NULL REFERENCES organizations(id),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_organization_id (organization_id),
    INDEX idx_email (email)
);

CREATE TABLE organizations (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    country VARCHAR(2),
    timezone VARCHAR(50) DEFAULT 'UTC',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Roles:**
- admin: Full access, user management, system configuration
- manager: Create/edit tasks, livestock/crops, accounting; view reports
- operator: View assigned tasks, log work, record livestock observations
- viewer: Read-only access to reports and public data

### Map & Sectors

```sql
CREATE TABLE sectors (
    id SERIAL PRIMARY KEY,
    organization_id INTEGER NOT NULL REFERENCES organizations(id),
    name VARCHAR(255) NOT NULL,          -- "Sector 1", "North field"
    area_hectares DECIMAL(10, 2),
    location_geom GEOMETRY(POLYGON, 4326),  -- GIS geometry for boundaries
    crop_type VARCHAR(100),               -- "Maize", "Pasture", "Rotation"
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by_id INTEGER NOT NULL REFERENCES users(id),
    updated_by_id INTEGER REFERENCES users(id),
    
    INDEX idx_organization_id (organization_id),
    UNIQUE KEY unique_name_per_org (organization_id, name)
);

CREATE TABLE lotes (
    id SERIAL PRIMARY KEY,
    sector_id INTEGER NOT NULL REFERENCES sectors(id),
    name VARCHAR(100),                   -- Optional sub-division label
    area_hectares DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_sector_id (sector_id)
);
```

### Tasks & Calendar

```sql
CREATE TABLE tasks (
    id SERIAL PRIMARY KEY,
    organization_id INTEGER NOT NULL REFERENCES organizations(id),
    sector_id INTEGER REFERENCES sectors(id),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    due_date TIMESTAMP NOT NULL,
    assigned_to_id INTEGER REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by_id INTEGER NOT NULL REFERENCES users(id),
    updated_by_id INTEGER REFERENCES users(id),
    completed_at TIMESTAMP,              -- Audit: when marked complete
    completed_by_id INTEGER REFERENCES users(id),
    
    INDEX idx_organization_id (organization_id),
    INDEX idx_sector_id (sector_id),
    INDEX idx_assigned_to_id (assigned_to_id),
    INDEX idx_status_due_date (status, due_date),
    INDEX idx_priority (priority)
);

CREATE TABLE task_comments (
    id SERIAL PRIMARY KEY,
    task_id INTEGER NOT NULL REFERENCES tasks(id) ON DELETE CASCADE,
    author_id INTEGER NOT NULL REFERENCES users(id),
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_task_id (task_id)
);
```

### Livestock Tracking

```sql
CREATE TABLE livestock (
    id SERIAL PRIMARY KEY,
    organization_id INTEGER NOT NULL REFERENCES organizations(id),
    sector_id INTEGER REFERENCES sectors(id),
    identifier VARCHAR(100) NOT NULL,   -- Ear tag, breed code, etc.
    species ENUM('cattle', 'sheep', 'goat', 'pig', 'poultry') NOT NULL,
    breed VARCHAR(100),
    date_of_birth DATE,
    current_weight_kg DECIMAL(8, 2),
    status ENUM('active', 'sick', 'sold', 'deceased') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by_id INTEGER NOT NULL REFERENCES users(id),
    
    INDEX idx_organization_id (organization_id),
    INDEX idx_sector_id (sector_id),
    INDEX idx_identifier (identifier),
    INDEX idx_status (status)
);

CREATE TABLE livestock_health_events (
    id SERIAL PRIMARY KEY,
    livestock_id INTEGER NOT NULL REFERENCES livestock(id) ON DELETE CASCADE,
    event_type ENUM('vaccination', 'treatment', 'injury', 'birth', 'weight_check') NOT NULL,
    event_date DATE NOT NULL,
    description TEXT,
    recorded_by_id INTEGER NOT NULL REFERENCES users(id),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_livestock_id (livestock_id),
    INDEX idx_event_date (event_date)
);

CREATE TABLE livestock_weight_log (
    id SERIAL PRIMARY KEY,
    livestock_id INTEGER NOT NULL REFERENCES livestock(id) ON DELETE CASCADE,
    weight_kg DECIMAL(8, 2) NOT NULL,
    measurement_date DATE NOT NULL,
    recorded_by_id INTEGER NOT NULL REFERENCES users(id),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_livestock_id (livestock_id),
    INDEX idx_measurement_date (measurement_date)
);
```

### Crops & Planting

```sql
CREATE TABLE crops (
    id SERIAL PRIMARY KEY,
    organization_id INTEGER NOT NULL REFERENCES organizations(id),
    sector_id INTEGER NOT NULL REFERENCES sectors(id),
    lote_id INTEGER REFERENCES lotes(id),
    crop_name VARCHAR(100) NOT NULL,     -- "Maize", "Soy", "Wheat"
    variety VARCHAR(100),
    sowing_date DATE NOT NULL,
    estimated_harvest_date DATE,
    actual_harvest_date DATE,
    seed_quantity_kg DECIMAL(10, 2),
    expected_yield_kg DECIMAL(10, 2),
    actual_yield_kg DECIMAL(10, 2),
    status ENUM('planning', 'sowing', 'growing', 'harvested', 'cancelled') DEFAULT 'planning',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by_id INTEGER NOT NULL REFERENCES users(id),
    
    INDEX idx_organization_id (organization_id),
    INDEX idx_sector_id (sector_id),
    INDEX idx_status (status),
    INDEX idx_sowing_date (sowing_date)
);

CREATE TABLE crop_treatments (
    id SERIAL PRIMARY KEY,
    crop_id INTEGER NOT NULL REFERENCES crops(id) ON DELETE CASCADE,
    treatment_type ENUM('fertilizer', 'pesticide', 'herbicide', 'irrigation', 'other') NOT NULL,
    treatment_date DATE NOT NULL,
    product_name VARCHAR(255),
    quantity_applied DECIMAL(10, 2),
    unit VARCHAR(50),                    -- "liters", "kg", "m3"
    recorded_by_id INTEGER NOT NULL REFERENCES users(id),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_crop_id (crop_id),
    INDEX idx_treatment_date (treatment_date)
);
```

### Accounting & Transactions

```sql
CREATE TABLE accounting_transactions (
    id SERIAL PRIMARY KEY,
    organization_id INTEGER NOT NULL REFERENCES organizations(id),
    transaction_date DATE NOT NULL,
    transaction_type ENUM('purchase', 'sale', 'expense', 'income') NOT NULL,
    category VARCHAR(100) NOT NULL,     -- "Feed", "Seed", "Fuel", "Equipment", etc.
    description TEXT,
    amount DECIMAL(12, 2) NOT NULL,     -- Currency agnostic; frontend handles display
    currency VARCHAR(3) DEFAULT 'USD',
    reference_entity VARCHAR(50),        -- 'livestock', 'crop', 'sector', or null
    reference_entity_id INTEGER,
    created_by_id INTEGER NOT NULL REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by_id INTEGER REFERENCES users(id),
    
    INDEX idx_organization_id (organization_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_category (category),
    INDEX idx_transaction_type (transaction_type)
);

CREATE TABLE accounting_periods (
    id SERIAL PRIMARY KEY,
    organization_id INTEGER NOT NULL REFERENCES organizations(id),
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    period_type ENUM('weekly', 'monthly', 'quarterly', 'yearly') DEFAULT 'monthly',
    summary_pdf_url VARCHAR(500),        -- If generated and stored
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_organization_id (organization_id),
    UNIQUE KEY unique_period (organization_id, period_start, period_end)
);
```

### Notifications

```sql
CREATE TABLE notifications (
    id SERIAL PRIMARY KEY,
    organization_id INTEGER NOT NULL REFERENCES organizations(id),
    user_id INTEGER NOT NULL REFERENCES users(id),
    notification_type ENUM('task_reminder', 'task_assigned', 'livestock_alert', 'weather_alert', 'system') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    reference_entity VARCHAR(50),        -- 'task', 'livestock', etc.
    reference_entity_id INTEGER,
    is_read BOOLEAN DEFAULT false,
    read_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
);

CREATE TABLE notification_preferences (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id),
    notification_type VARCHAR(50) NOT NULL,
    enabled_email BOOLEAN DEFAULT true,
    enabled_sms BOOLEAN DEFAULT false,
    enabled_in_app BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_pref (user_id, notification_type)
);
```

### Weather Data

```sql
CREATE TABLE weather_observations (
    id SERIAL PRIMARY KEY,
    organization_id INTEGER NOT NULL REFERENCES organizations(id),
    sector_id INTEGER REFERENCES sectors(id),
    observation_date DATE NOT NULL,
    observation_time TIME,
    temperature_celsius DECIMAL(5, 2),
    humidity_percent DECIMAL(5, 2),
    rainfall_mm DECIMAL(8, 2),
    wind_speed_kmh DECIMAL(6, 2),
    weather_condition VARCHAR(50),      -- 'sunny', 'cloudy', 'rainy', 'stormy'
    source ENUM('manual', 'weather_api', 'station') DEFAULT 'manual',
    recorded_by_id INTEGER REFERENCES users(id),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_organization_id (organization_id),
    INDEX idx_sector_id (sector_id),
    INDEX idx_observation_date (observation_date)
);
```

## Relationships & Constraints

### Foreign Key Cascade Rules

- Task deletion: Leave history; mark as `cancelled` instead
- Livestock deletion: Mark as `deceased`; never hard delete
- Crop deletion: Mark as `cancelled`; preserve history
- Accounting transaction deletion: Forbidden; use reversal entries instead

### Audit Trail Pattern

Every mutable entity should have:
- `created_at`, `created_by_id`
- `updated_at`, `updated_by_id`
- Optional: `deleted_at`, `deleted_by_id` (soft delete if applicable)

### Uniqueness Constraints

- User email per organization (prevent duplicates)
- Sector name per organization (prevent duplicate naming)
- Livestock identifier per organization (prevent ID collisions)
- Notification preference per user per type (only one setting per combo)

## Denormalization & Performance

### Read Optimization

- Cache current livestock weight (denormalized in `livestock.current_weight_kg`)
- Materialize weekly accounting summaries for fast dashboard queries
- Index frequently filtered columns: `status`, `due_date`, `priority`, `organization_id`

### Reporting Views

```sql
-- Example: Weekly task summary
CREATE VIEW v_tasks_by_priority AS
SELECT 
    DATE_TRUNC('week', due_date) as week,
    priority,
    COUNT(*) as task_count,
    organization_id
FROM tasks
WHERE status != 'cancelled'
GROUP BY DATE_TRUNC('week', due_date), priority, organization_id;

-- Example: Livestock age and weight trends
CREATE VIEW v_livestock_current_status AS
SELECT 
    l.id,
    l.identifier,
    l.species,
    l.current_weight_kg,
    DATE_PART('year', AGE(l.date_of_birth)) as age_years,
    l.status,
    l.organization_id
FROM livestock l
WHERE l.status IN ('active', 'sick');
```

## Migration Strategy

- Use schema migration tool (Alembic for SQLAlchemy)
- Version all DDL changes
- Test migrations in staging before production
- Never drop columns directly; mark as deprecated, then drop in next major version
- Support zero-downtime migrations: add column, backfill, add constraint

## Common Pitfalls

- Storing user-entered text without trimming/validation
- Missing indexes on frequently queried columns
- Hard-deleting records that should be audited
- Not enforcing organization isolation (multi-tenant security risk)
- Denormalizing without cache invalidation strategy
- Ignoring timezone handling for international operations
