# Contexto y contenedores

## Propósito

Definir los contenedores principales de ejecución y sus responsabilidades en la reescritura.

## Audiencia

Desarrolladores y revisores técnicos que preparan la estructura de frontend y backend.

## Referencias de origen

- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`
- `OLD/MainDocumentation/Survey/2025 APP-CAMPO.csv`

## Diagrama

```mermaid
C4Container
    title Reescritura AppGro - Contenedores

    Person(user, "Usuario", "Usuario de campo o administrativo")

    System_Boundary(appgro, "AppGro") {
        Container(web, "Aplicación web Astro", "Astro", "UI, páginas SSR, islas cliente e interacciones con soporte offline")
        Container(api, "API de aplicación", "FastAPI", "Auth, servicios de dominio, validación, RBAC y auditoría")
        ContainerDb(db, "Base operativa", "PostgreSQL", "Datos transaccionales normalizados")
        Container(queue, "Trabajos en segundo plano", "Worker", "Notificaciones, sync y generación de resúmenes")
    }

    System_Ext(weather, "Proveedor climático", "Datos climáticos externos")

    Rel(user, web, "Usa", "HTTPS")
    Rel(web, api, "Consume APIs", "JSON/HTTPS")
    Rel(api, db, "Lee y escribe")
    Rel(api, queue, "Programa trabajo asíncrono")
    Rel(queue, db, "Lee trabajo pendiente y escribe resultados")
    Rel(api, weather, "Consulta contexto climático")
```

## Responsabilidades por contenedor

| Contenedor | Responsabilidades principales |
| --- | --- |
| Aplicación Astro | Renderizado, formularios, islas cliente, caché local y awareness de sync |
| API FastAPI | Auth, validación, aislamiento multi-tenant, reglas de negocio y auditoría |
| PostgreSQL | Estado transaccional durable y esquema apto para reporting |
| Trabajos en segundo plano | Notificaciones, importaciones, resúmenes periódicos y sync diferido |

## Notas de diseño

- Los límites de módulos deben coincidir con los dominios documentados.
- Los contratos de API deben poder versionarse donde el riesgo de workflow sea alto.
- Los jobs en segundo plano son una preocupación de entrega, no un lugar para esconder reglas de negocio.

## Preguntas abiertas

- ¿La primera versión necesita una tecnología de cola dedicada o puede empezar con ejecución en proceso?
- ¿Qué salidas de reporting requieren pre-cálculo versus consulta en vivo?
