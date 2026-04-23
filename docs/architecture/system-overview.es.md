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

    Boundary(appgro,"AppGro"){
    System(appgro, "AppGro", "Plataforma de operaciones agropecuarias")
        Container(web, "Frontend web", "Astro", "Interfaz de usuario para usuarios de campo y oficina")
        Container(api, "Backend API", "FastAPI", "Lógica de negocio y capa de acceso a datos")
        ContainerDb(db, "Base de datos", "PostgreSQL", "Almacena datos operativos, históricos y configuración")
    }
        Person(fieldUser, "Usuario de campo", "Operario, encargado, agrónomo, veterinario")
        Person(adminUser, "Usuario administrativo", "Admin o manager que revisa datos y configura accesos")
    System_Ext(weather, "Proveedor climático", "Fuente externa de observaciones o pronóstico")
    System_Ext(email, "Canal de notificación", "Email o futuro transporte de alertas")

    Rel(fieldUser, appgro, "Registra tareas, observaciones de campo y eventos de ganado/cultivo")
    Rel(adminUser, appgro, "Configura usuarios, revisa resúmenes y gestiona operaciones")
    Rel(appgro, weather, "Consulta contexto climático")
    Rel(appgro, email, "Envía recordatorios y alertas")
    UpdateLayoutConfig($c4ShapeInRow="2", $c4BoundaryInRow="1")
    UpdateRelStyle(adminUser, appgro, $offsetY="-150")
    UpdateRelStyle(appgro, email, $offsetY="-50", $offsetX="50")
    UpdateRelStyle(appgro, weather, $offsetY="-15", $offsetX="10")
    UpdateRelStyle(fieldUser, appgro, $offsetY="-125")
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
  - Muy probablemente el email sea el único canal requerido para la versión inicial, ya que es ampliamente utilizado y puede cubrir notificaciones críticas. Sin embargo, deberíamos diseñar el sistema de notificaciones para que sea extensible, de modo que podamos agregar fácilmente canales adicionales como SMS o notificaciones en la aplicación en futuras iteraciones basadas en los comentarios y necesidades de los usuarios.
- ¿Qué nivel de edición del mapa debe existir en la primera entrega y qué puede quedar para después?
  - Una capacidad básica de edición de mapas que permita a los usuarios definir y editar sectores/lotes debería incluirse en la primera entrega, ya que es fundamental para muchos flujos operativos. Características más avanzadas como geocercas, integración con datos GIS externos o edición colaborativa podrían considerarse en fases posteriores una vez que hayamos validado la funcionalidad central y recopilado comentarios de los usuarios sobre la implementación inicial.
- ¿Qué fuente climática externa será la autorizada?
  - WeatherAPI es una opción popular para aplicaciones agrícolas debido a su amplia cobertura de datos y facilidad de integración, pero deberíamos evaluarla frente a otros proveedores según factores como la precisión de los datos en nuestras regiones objetivo, el costo y la fiabilidad de la API. También podríamos diseñar el sistema para admitir múltiples proveedores de clima en caso de que necesitemos cambiar o agregar datos de diferentes fuentes en el futuro.

## Notas de testabilidad

- La documentación de arquitectura debe derivar tareas explícitas para APIs y modelo de datos.
- Cada especificación de módulo debe definir criterios de aceptación convertibles en pruebas backend e integración.
