# Flujos núcleo

## Propósito

Documentar los flujos principales entre módulos necesarios para comenzar la implementación de forma segura.

## Audiencia

Implementadores de frontend y backend.

## Referencias de origen

- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`
- `OLD/MainDocumentation/Survey/2025 APP-CAMPO.csv`

## Flujo 1 - Solicitud autenticada con control de roles

```mermaid
sequenceDiagram
    participant User as Usuario
    participant Web
    participant API
    participant Service as Servicio
    participant DB

    User->>Web: Abre pagina protegida
    Web->>API: GET /resource con JWT
    API->>API: Valida token
    API->>Service: Pasa contexto de usuario actual
    Service->>DB: Consulta con alcance de organizacion
    DB-->>Service: Registros acotados
    Service-->>API: Resultado o error de permiso
    API-->>Web: 200 / 401 / 403
```

## Flujo 2 - Asignación de tarea y notificación

```mermaid
sequenceDiagram
    participant Manager
    participant Web
    participant API
    participant DB
    participant Worker
    participant Operator

    Manager->>Web: Crea tarea
    Web->>API: POST /tasks
    API->>DB: Guarda tarea con prioridad, vencimiento y responsable
    API->>Worker: Encola evento de notificacion
    Worker-->>Operator: Notificacion in-app de asignacion
    API-->>Web: Respuesta de tarea creada
```

## Flujo 3 - Actualización offline de campo y sync

```mermaid
flowchart TD
    A[Operator actualiza tarea o ganado offline] --> B[Guardar copia local]
    B --> C[Encolar mutacion pendiente]
    C --> D{Volvio la conectividad?}
    D -->|No| C
    D -->|Si| E[Enviar lote de sync a la API]
    E --> F[Validar auth, rol y organizacion]
    F --> G[Aplicar mutacion y devolver resultado]
    G --> H[Marcar registro local como sincronizado o fallido]
```

## Reglas de workflow

- Los cambios offline nunca deben descartarse silenciosamente.
- La validación del servidor sigue siendo autoritativa al reconectar.
- La resolución de conflictos debe ser explícita y visible para el usuario cuando haga falta.

## Preguntas abiertas

- ¿La primera versión usará un endpoint genérico de sync por lotes o solo endpoints por dominio?
- ¿Cómo deben mostrarse las mutaciones offline fallidas a usuarios con baja alfabetización digital?

## Notas de testabilidad

- Verificar vínculo entre tarea y notificación.
- Verificar reintentos de sync y manejo de estado fallido.
- Verificar permisos durante el replay del sync.
