---
name: astro-frontend-patterns
description: Frontend architecture, component patterns, and island strategy for Astro-based AppGro interface. Covers layouts, islands, API clients, form handling, and state management for agricultural workflows.
---

# Astro Frontend Patterns for AppGro

## Overview

AppGro frontend is built with Astro, using Islands Architecture for interactive regions while keeping most pages static. This skill guides component structure, client-side state, and API integration.

## Core Principles

1. **Server-first rendering**: Astro renders HTML on build or demand; only interactive regions become islands.
2. **Minimal JavaScript**: Keep islands lightweight; offload business logic to FastAPI backend.
3. **Progressive enhancement**: Forms and lists work without JS; islands add polish and interactivity.
4. **Agricultural context**: Respect field workflows (online/offline, seasonal, manual operations).

## Project Structure

```
src/
  layouts/          # Page shells (authenticated, public, fieldwork)
  components/       # Reusable UI components
    islands/        # Interactive Astro islands (framework: React, Vue, etc.)
    static/         # Pure HTML/CSS components
  pages/            # Astro route files
  lib/
    api/            # FastAPI client layer
    auth/           # Auth state and checks
    formatters/     # Date, unit, number formatting for agricultural context
    validators/     # Client-side input validation
    types/          # Shared TypeScript types
  styles/           # Global CSS
```

## Island Strategy

### When to Use Islands

- Task creation/editing forms (interactive priority, calendar pop-ins)
- Map interactions (sector labeling, zoom, click handlers)
- Real-time notifications (toast, badges)
- Bulk operations (multi-select, batch actions)
- Calendar/timeline widgets

### When to Keep Static

- Dashboard summaries and reports
- Read-only record lists (before filtering/search)
- Help text and documentation
- Sector/livestock/crop inventory tables (initial load)

### Island Configuration

```astro
// Example: TaskForm island
<TaskForm client:load
  sector={sectorId}
  defaultPriority="medium"
/>
```

Use `client:load` for critical interactive forms, `client:idle` for secondary features, `client:visible` for below-fold islands.

## API Client Layer

### Structure

```typescript
// src/lib/api/client.ts
export class AppGroAPIClient {
  private baseURL: string;
  private token: string | null;

  async getTasks(filters?: TaskFilter): Promise<Task[]> {
    return this.request('GET', '/api/tasks', { query: filters });
  }

  async createTask(data: TaskCreate): Promise<Task> {
    return this.request('POST', '/api/tasks', { body: data });
  }

  private async request(method: string, path: string, options?: any) {
    const headers = {
      'Content-Type': 'application/json',
      ...(this.token && { Authorization: `Bearer ${this.token}` }),
    };
    // Error handling, retry logic, offline fallback
  }
}
```

### Error Handling

- Network errors: Show offline indicator, queue mutations for sync
- 401/403: Redirect to login, clear auth state
- 400/422: Display field-level validation errors in forms
- 500: Log error, show generic user message, alert admin
- Timeout: Retry with exponential backoff (3 attempts max)

## Form Patterns

### Task Creation Form

```astro
---
const sectors = await db.getSectors();
---
<TaskFormIsland
  client:load
  sectors={sectors}
  onSubmit={(formData) => fetch('/api/tasks', { method: 'POST', body: formData })}
/>
```

### Validation

- Client-side: HTML5 + custom validators (date ranges, unit conversions, name uniqueness)
- Server-side: FastAPI pydantic models enforce final validation
- Display errors inline per field; show success toast after POST

## State Management

### Server State (Preferred)

- Tasks, livestock records, accounting entries: fetch on demand, cache in Astro data
- Use Astro's `Astro.locals` for request-scoped auth and user context

### Client State (Minimal)

- Form input (before submit)
- UI toggles (sidebar open, filter panel visible)
- Real-time notification badges

### Sync Strategy

- POST/PUT/DELETE mutations: optimistic UI update + server confirm
- If server rejects: revert UI change, show error with retry option
- For offline: queue mutations in IndexedDB, sync when online

## Agricultural Workflow Considerations

1. **Seasonal workflows**: Support date-range filters, year boundaries for reporting
2. **Low digital literacy**: Minimize modals, prefer inline editing, show confirmation messages
3. **Field accessibility**: Ensure mobile-friendly forms, large touch targets, high contrast
4. **Bulk operations**: Support CSV export/import for livestock records, accounting entries
5. **Notifications**: Deliver task reminders via email, SMS if configured

## Authentication & Authorization

- Astro middleware checks JWT in cookies
- Pass user context to islands via props (id, role, permissions)
- Islands should not perform auth checks; backend enforces all access control
- Example:
  ```astro
  { user.role === 'admin' && <AdminPanel client:load /> }
  ```

## Testing Strategy

- Unit test island components (React Testing Library, Vitest)
- Integration test API client with mock backend
- E2E test critical workflows (create task, edit livestock record, generate report)
- Test offline behavior (disable network, verify queue, test sync)

## Performance Goals

- Largest island bundle: <50KB (gzipped)
- First Contentful Paint: <2s
- Time to Interactive: <5s
- Cache API responses aggressively (stale-while-revalidate pattern)

## Common Pitfalls

- Fetching data in islands when it should be in page layout
- Tightly coupling island to specific API response shape
- Missing error states in island UI
- Assuming always-online connectivity for field operations
- Using heavy JS libraries when lighter alternatives exist
