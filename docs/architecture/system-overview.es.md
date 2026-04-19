# Visión general del sistema

## Propósito

Describir la arquitectura objetivo y la filosofía de entrega para la reescritura de AppGro.

## Audiencia

Desarrolladores, líderes técnicos y revisores que preparan el trabajo de implementación.

## Referencias de origen

- `docs/projectPlan.md`
- `OLD/MainDocumentation/NewTraditionsSolutions.docx.html`
- `OLD/MainDocumentation/Survey/2025 APP-CAMPO.csv`

## Resumen de diseño

AppGro es una reescritura desde cero de una plataforma de gestión agropecuaria. La reescritura conserva la intención de negocio relevada en el material legado y migra hacia:

- Astro para el frontend web
- FastAPI para APIs backend y reglas de negocio del lado servidor
- Documentación Mermaid-first para arquitectura, flujos y modelos de datos

La plataforma se organiza alrededor de operaciones agropecuarias: tareas, ganado, cultivos, contabilidad, sectores/lotes, notificaciones, contexto climático y mantenimiento de equipos.

## Diagrama

```mermaid
---
id: 9b7276cb-3327-404c-b981-c5ab8fb8fe48
---
C4Context
    title Reescritura AppGro - Contexto del sistema

    System_Boundary(Usuarios,"Usuarios"){
        Person(adminUser, "Usuario administrativo", "Admin o manager que revisa datos y configura accesos")
        Person(fieldUser, "Usuario de campo", "Operario, encargado, agrónomo, veterinario")
    }
    System_Ext(email, "Canal de notificación", "Email o futuro transporte de alertas")
    System(appgro, "AppGro", "Plataforma de operaciones agropecuarias")
    System_Ext(weather, "Proveedor climático", "Fuente externa de observaciones o pronóstico")

    Rel(fieldUser, appgro, "Registra tareas, observaciones de campo y eventos de ganado/cultivo")
    Rel(adminUser, appgro, "Configura usuarios, revisa resúmenes y gestiona operaciones")
    Rel(appgro, weather, "Consulta contexto climático")
    Rel(appgro, email, "Envía recordatorios y alertas")
    UpdateRelStyle(adminUser, appgro, dashed, $offsetY="-35")
    UpdateRelStyle(fieldUser, appgro, dashed, $offsetY="-10")
    UpdateRelStyle(appgro, weather, dashed, $offsetX="-55", $offsetY="35")
    UpdateRelStyle(appgro, email, dashed, $offsetX="-55", $offsetY="35")
```

## Principios arquitectónicos

1. Preservar la intención de negocio legada, no los detalles técnicos del stack anterior.
2. Mantener los módulos de dominio cohesionados por límite de negocio.
3. Hacer cumplir autenticación y autorización en el servidor.
4. Preferir historial append-only cuando la auditoría sea importante.
5. Diseñar APIs y modelos de datos antes de grandes bloques de implementación.
6. Soportar flujos de campo con conectividad limitada cuando corresponda.

## Implicancias de entrega

- La documentación de dominio y datos precede al trabajo fuerte de implementación.
- Se requiere vocabulario compartido entre frontend, backend y documentación.
- Los escenarios offline y los respaldos manuales deben documentarse en los flujos de campo.

## Preguntas abiertas

- ¿Qué canales de notificación se requieren en la primera versión productiva?
- ¿Qué nivel de edición del mapa debe existir en la primera entrega y qué puede quedar para después?
- ¿Qué fuente climática externa será la autorizada?

## Notas de testabilidad

- La documentación de arquitectura debe derivar tareas explícitas para APIs y modelo de datos.
- Cada especificación de módulo debe definir criterios de aceptación convertibles en pruebas backend e integración.
