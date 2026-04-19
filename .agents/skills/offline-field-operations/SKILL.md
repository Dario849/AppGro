---
name: offline-field-operations
description: Patterns for offline-first and field-friendly operations in AppGro. Covers local caching, mutation queuing, background sync, manual entry fallbacks, and low-connectivity workflows for agricultural field operations.
---

# Offline-First & Field Operations

## Overview

AppGro must support operators working in fields where internet connectivity is intermittent, unreliable, or absent. This skill provides patterns for local caching, offline mutations, sync mechanisms, and graceful degradation.

## Design Principles

1. **Offline-first architecture**: Assume offline; sync when online
2. **Local-first data**: Store application state locally (IndexedDB, SQLite)
3. **Optimistic updates**: Update UI immediately; validate on server
4. **Manual fallbacks**: Support pen-and-paper backup workflows
5. **Progressive connectivity**: Gracefully handle flaky networks
6. **Data safety**: Never lose user input; queue for later sync

## Frontend: IndexedDB Storage & Sync

### Local Database Schema

```typescript
// src/lib/db/schema.ts
import Dexie, { Table } from 'dexie';

export interface Task {
  id?: number;
  serverId?: number;           // ID from server; may be null if not synced
  title: string;
  sectorId: number;
  priority: string;
  dueDate: Date;
  status: string;
  syncStatus: 'pending' | 'synced' | 'failed';
  lastSyncAttempt?: Date;
  syncError?: string;
}

export interface Livestock {
  id?: number;
  serverId?: number;
  identifier: string;
  species: string;
  currentWeightKg: number;
  status: string;
  weightLog: Array<{ date: Date; weight: number }>;
  syncStatus: 'pending' | 'synced' | 'failed';
  lastSyncAttempt?: Date;
}

export interface SyncQueue {
  id?: number;
  entityType: 'task' | 'livestock' | 'crop';
  operation: 'create' | 'update' | 'delete';
  entityId: number;          // Local ID
  payload: any;              // Full object to send to server
  timestamp: Date;
  syncStatus: 'pending' | 'synced' | 'failed';
  retries: number;
  lastError?: string;
}

export class AppGroDB extends Dexie {
  tasks!: Table<Task>;
  livestock!: Table<Livestock>;
  syncQueue!: Table<SyncQueue>;

  constructor() {
    super('AppGroDB');
    this.version(1).stores({
      tasks: '++id, serverId, syncStatus, dueDate',
      livestock: '++id, serverId, syncStatus, identifier',
      syncQueue: '++id, entityType, syncStatus, timestamp',
    });
  }
}

export const db = new AppGroDB();
```

### Offline Data Sync Service

```typescript
// src/lib/api/offlineSync.ts
import { db } from '../db/schema';
import { api } from './client';

export class OfflineSyncService {
  private isOnline = navigator.onLine;
  private syncInProgress = false;
  private retryAttempts = 3;
  private retryDelayMs = 2000;

  constructor() {
    // Listen for online/offline events
    window.addEventListener('online', () => this.onOnline());
    window.addEventListener('offline', () => this.onOffline());
  }

  onOnline() {
    this.isOnline = true;
    console.log('Network online. Starting sync...');
    this.syncAll();
  }

  onOffline() {
    this.isOnline = false;
    console.log('Network offline. Queuing operations.');
  }

  /**
   * Create task locally and queue for sync.
   */
  async createTaskOffline(taskData: any): Promise<Task> {
    const localTask: Task = {
      ...taskData,
      serverId: undefined,
      syncStatus: 'pending',
      lastSyncAttempt: new Date(),
    };

    const localId = await db.tasks.add(localTask);

    // Add to sync queue
    await db.syncQueue.add({
      entityType: 'task',
      operation: 'create',
      entityId: localId,
      payload: taskData,
      timestamp: new Date(),
      syncStatus: 'pending',
      retries: 0,
    });

    // Attempt sync if online; otherwise queue for later
    if (this.isOnline) {
      this.syncSingleOperation('task', localId);
    }

    return { ...localTask, id: localId };
  }

  /**
   * Update task locally and queue for sync.
   */
  async updateTaskOffline(taskId: number, updates: any): Promise<void> {
    // Update local copy
    const existingTask = await db.tasks.get(taskId);
    if (!existingTask) throw new Error('Task not found');

    const updated = { ...existingTask, ...updates, syncStatus: 'pending' };
    await db.tasks.update(taskId, updated);

    // Queue sync
    const queueEntry = await db.syncQueue.where('entityId').equals(taskId).first();
    if (queueEntry) {
      // Update existing queue entry
      await db.syncQueue.update(queueEntry.id, {
        payload: updated,
        syncStatus: 'pending',
        retries: 0,
      });
    } else {
      // Create new queue entry
      await db.syncQueue.add({
        entityType: 'task',
        operation: 'update',
        entityId: taskId,
        payload: updated,
        timestamp: new Date(),
        syncStatus: 'pending',
        retries: 0,
      });
    }

    if (this.isOnline) {
      this.syncSingleOperation('task', taskId);
    }
  }

  /**
   * Sync all pending operations from queue.
   */
  async syncAll(): Promise<void> {
    if (this.syncInProgress) return;
    this.syncInProgress = true;

    try {
      const pendingOps = await db.syncQueue
        .where('syncStatus')
        .equals('pending')
        .toArray();

      console.log(`Syncing ${pendingOps.length} pending operations...`);

      for (const op of pendingOps) {
        try {
          await this.processSyncOperation(op);
        } catch (error) {
          console.error(`Failed to sync ${op.entityType} ${op.entityId}:`, error);
          // Continue with next operation; retry will happen later
        }
      }
    } finally {
      this.syncInProgress = false;
    }
  }

  /**
   * Process a single sync operation from the queue.
   */
  private async processSyncOperation(queueEntry: any): Promise<void> {
    const { id: queueId, entityType, operation, entityId, payload } = queueEntry;

    let response;
    try {
      if (operation === 'create') {
        response = await api.post(`/${entityType}s`, payload);
      } else if (operation === 'update') {
        response = await api.patch(`/${entityType}s/${payload.serverId || entityId}`, payload);
      } else if (operation === 'delete') {
        await api.delete(`/${entityType}s/${payload.serverId || entityId}`);
        response = { id: payload.serverId || entityId };
      }

      // Update local record with server ID if create operation
      if (operation === 'create' && response.id) {
        const entity = await db[`${entityType}s`].get(entityId);
        if (entity) {
          await db[`${entityType}s`].update(entityId, {
            serverId: response.id,
            syncStatus: 'synced',
          });
        }
      } else {
        // Mark as synced
        const entity = await db[`${entityType}s`].get(entityId);
        if (entity) {
          await db[`${entityType}s`].update(entityId, { syncStatus: 'synced' });
        }
      }

      // Remove from queue
      await db.syncQueue.delete(queueId);

      console.log(`✓ Synced ${entityType} ${entityId}`);
    } catch (error: any) {
      const newRetries = (queueEntry.retries || 0) + 1;

      if (newRetries < this.retryAttempts) {
        // Retry later
        await db.syncQueue.update(queueId, {
          retries: newRetries,
          lastError: error.message,
          syncStatus: 'pending',
        });
        console.warn(`Retry ${newRetries}/${this.retryAttempts} for ${entityType} ${entityId}`);
      } else {
        // Mark as failed after max retries
        await db.syncQueue.update(queueId, {
          retries: newRetries,
          lastError: error.message,
          syncStatus: 'failed',
        });
        console.error(`✗ Failed to sync ${entityType} ${entityId} after ${newRetries} attempts`);
      }
    }
  }

  /**
   * Sync a specific operation (called immediately on user action if online).
   */
  private async syncSingleOperation(entityType: string, entityId: number): Promise<void> {
    const queueEntry = await db.syncQueue
      .where('entityId')
      .equals(entityId)
      .first();

    if (!queueEntry) return;

    await this.processSyncOperation(queueEntry);
  }
}

// Initialize once
export const offlineSync = new OfflineSyncService();
```

### UI Indicators

```astro
---
// src/components/OfflineIndicator.astro
---
<OfflineIndicator client:load />

<!-- OfflineIndicator.tsx island -->
import { useState, useEffect } from 'react';
import { db } from '../lib/db/schema';

export default function OfflineIndicator() {
  const [isOnline, setIsOnline] = useState(navigator.onLine);
  const [pendingCount, setPendingCount] = useState(0);

  useEffect(() => {
    const handleOnline = () => setIsOnline(true);
    const handleOffline = () => setIsOnline(false);

    window.addEventListener('online', handleOnline);
    window.addEventListener('offline', handleOffline);

    // Check pending operations
    const checkPending = async () => {
      const pending = await db.syncQueue.where('syncStatus').equals('pending').count();
      setPendingCount(pending);
    };
    checkPending();

    const interval = setInterval(checkPending, 5000); // Poll every 5s

    return () => {
      window.removeEventListener('online', handleOnline);
      window.removeEventListener('offline', handleOffline);
      clearInterval(interval);
    };
  }, []);

  if (isOnline && pendingCount === 0) {
    return null; // All synced, no indicator
  }

  return (
    <div className={`offline-indicator ${isOnline ? 'warning' : 'error'}`}>
      {!isOnline && <span>⚠ Offline – Changes will sync when online</span>}
      {isOnline && pendingCount > 0 && <span>🔄 Syncing {pendingCount} change(s)...</span>}
    </div>
  );
}
```

## Backend: Bulk Sync Endpoint

```python
# app/routers/sync.py
from fastapi import APIRouter, Depends, HTTPException
from app.schemas.sync import SyncBatch, SyncOperation, SyncResponse
from app.services.task_service import TaskService
from app.dependencies import get_db, get_current_user

router = APIRouter(prefix="/api/sync", tags=["sync"])

class SyncOperation(BaseModel):
    entityType: str          # 'task', 'livestock', etc.
    operation: str           # 'create', 'update', 'delete'
    clientId: int            # Local ID from client
    serverId: int | None     # Server ID if updating
    payload: dict            # Full object

class SyncBatch(BaseModel):
    timestamp: datetime
    operations: list[SyncOperation]

class SyncResult(BaseModel):
    clientId: int
    serverId: int
    status: str              # 'success', 'error'
    error: str | None

class SyncResponse(BaseModel):
    results: list[SyncResult]
    serverTime: datetime

@router.post("/batch", response_model=SyncResponse)
async def sync_batch(
    batch: SyncBatch,
    db: Session = Depends(get_db),
    current_user = Depends(get_current_user),
):
    """
    Accept batch of offline mutations and apply to server.
    Returns success/failure for each operation.
    """
    results = []

    for op in batch.operations:
        result = await _process_sync_operation(op, db, current_user)
        results.append(result)

    return SyncResponse(
        results=results,
        serverTime=datetime.utcnow(),
    )

async def _process_sync_operation(op: SyncOperation, db, current_user) -> SyncResult:
    try:
        if op.entityType == 'task':
            service = TaskService(db, current_user)
            if op.operation == 'create':
                task = service.create_task(op.payload)
                return SyncResult(clientId=op.clientId, serverId=task.id, status='success')
            elif op.operation == 'update':
                task = service.update_task(op.serverId, op.payload)
                return SyncResult(clientId=op.clientId, serverId=task.id, status='success')
            elif op.operation == 'delete':
                service.delete_task(op.serverId)
                return SyncResult(clientId=op.clientId, serverId=op.serverId, status='success')
        
        # Add other entity types similarly...
        
        return SyncResult(clientId=op.clientId, status='error', error='Unknown entity type')
    
    except Exception as e:
        return SyncResult(clientId=op.clientId, status='error', error=str(e))
```

## Field-Friendly UX Patterns

### Task Completion from Field

```astro
---
// src/components/TaskFieldComplete.astro
import { offlineSync } from '../lib/api/offlineSync';

export async function completeTaskFromField(taskId: number, notes: string) {
  await offlineSync.updateTaskOffline(taskId, {
    status: 'completed',
    completedAt: new Date(),
    fieldNotes: notes,  // Observations from field
  });
  // UI updates immediately; syncs in background
}
---

<TaskFieldComplete client:load />
```

### Livestock Weight Log (Field Entry)

```astro
---
// src/components/LivestockWeightLog.astro
import { db } from '../lib/db/schema';

export async function logWeight(livestockId: number, weightKg: number) {
  const livestock = await db.livestock.get(livestockId);
  if (!livestock) return;

  // Update local weight
  const updated = {
    ...livestock,
    currentWeightKg: weightKg,
    weightLog: [
      ...livestock.weightLog,
      { date: new Date(), weight: weightKg },
    ],
    syncStatus: 'pending',
  };
  
  await db.livestock.update(livestockId, updated);
  
  // Queue sync
  await db.syncQueue.add({
    entityType: 'livestock',
    operation: 'update',
    entityId: livestockId,
    payload: updated,
    timestamp: new Date(),
    syncStatus: 'pending',
    retries: 0,
  });
}
---
```

### Manual Fallback: Printable Task Sheet

```astro
---
// src/pages/field/printable-tasks.astro
const tasks = await db.tasks.where('status').equals('pending').toArray();
---

<html>
<body>
  <h1>📋 Tasks – {new Date().toDateString()}</h1>
  {tasks.map(task => (
    <div class="task-card">
      <h3>{task.title}</h3>
      <p>Sector: {task.sectorId}</p>
      <p>Priority: <strong>{task.priority}</strong></p>
      <p>Due: {task.dueDate.toDateString()}</p>
      <div class="checkbox">☐ Complete</div>
      <div class="notes">Notes: ___________________________</div>
    </div>
  ))}
  <p style="margin-top: 2cm; font-size: 0.9em; color: #666;">
    Instructions: Print this sheet. Check off completed tasks and write notes.
    Sync with the app when online.
  </p>
</body>
</html>
```

## Data Consistency & Conflict Resolution

### Last-Write-Wins Strategy

```typescript
// Simpler for agriculture; server timestamp is authoritative
// If offline mutation conflicts with server change, server wins after warning user
```

### Conflict Detection

```typescript
async function detectConflict(entityId: number, localVersion: any, serverVersion: any) {
  // Compare timestamps
  if (serverVersion.updatedAt > localVersion.updatedAt) {
    // Server has newer version
    return {
      hasConflict: true,
      resolution: 'server-wins',
      message: 'This item was updated by another user. Server version used.',
    };
  }
  return { hasConflict: false };
}
```

## Common Pitfalls

- Assuming network works; always test offline mode
- Losing user input on sync failure (queue mutations durably)
- No UI feedback when syncing; users think changes are lost
- Syncing too aggressively (battery drain on mobile)
- Not handling version conflicts when user edits offline
- Missing backup for critical data (no CSV export option)
- Assuming all users have stable connectivity (support manual entry)
