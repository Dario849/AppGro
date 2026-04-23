# Especificación de notificaciones

## Propósito

Definir recordatorios y alertas in-app vinculados a eventos operativos.

## Audiencia

Desarrolladores que implementan recordatorios, alertas y flujos de awareness para usuarios.

## Referencias de origen

- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`

## Resumen de diseño

Las notificaciones deben mostrar acción útil, no ruido. El alcance inicial debería priorizar notificaciones in-app para tareas, alertas de ganado y awareness climático, dejando canales salientes como extensión posterior.

## Diagrama

```mermaid
sequenceDiagram
    participant Event as Evento de dominio
    participant API
    participant Worker
    participant User as Usuario

    Event->>API: Genera condicion de alerta
    API->>Worker: Encola trabajo de notificacion
    Worker-->>User: Entrega notificacion in-app
    User->>API: Marca leida o sigue enlace
```

## Reglas y restricciones

- Las notificaciones deben referenciar la entidad origen cuando sea posible.
- El estado de lectura debe ser por usuario.
- Las preferencias deben ser específicas por usuario y extensibles por tipo.
- Los recordatorios deberían deduplicarse cuando sea posible.

## Implicancias de roles y permisos

- Cada usuario solo ve notificaciones derivadas de registros que puede acceder.

## Casos borde

- Registros origen borrados o cancelados no deben dejar enlaces rotos.
- Usuarios offline necesitan reconciliar estado pendiente al reconectar.

## Preguntas abiertas

- ¿Qué canales salientes se necesitan después del canal in-app?
   ```Mermaid
    flowchart TD
        A[Notificacion in-app] --> B[Email]
        A --> C[SMS]
        A --> D[Push]
    ```
- ¿Las reglas de escalamiento deberían de ser por tiempo en la primera versión?
  - Otro: Después de X horas sin marcar como leído, escalar a canal secundario. Esto debería de ser configurable por el administrador dentro de su panel.
  

## Notas de testabilidad

- Verificar visibilidad por usuario y organización.
- Verificar estado leído/no leído y preferencias.
