# ADR-001: Reescritura con Astro + FastAPI + documentación Mermaid-first

## Estado

Aceptado

## Contexto

Este repositorio es un esfuerzo de reescritura, no un proyecto de parches sobre el legado. El stack anterior fue PHP más Vite/JavaScript, mientras que la guía actual del repositorio exige una arquitectura desde cero y documentación profesional primero.

## Decisión

Adoptar:

- Astro para el frontend web
- FastAPI para APIs backend y composición de servicios
- Documentación Markdown Mermaid-first bajo `docs\`
- Módulos de dominio explícitos alineados con capacidades agropecuarias

## Fundamentación

- Astro favorece un frontend orientado a composición de UI y documentación primero.
- FastAPI favorece esquemas explícitos, validación y routers modulares.
- Mermaid mantiene los diagramas versionados junto con el código y revisables en PRs.
- Esta combinación habilita una entrega gradual desde documentación hacia contratos e implementación.

## Consecuencias

### Positivas

- Mejor alineación entre documentación y código
- Separación más clara entre responsabilidades de frontend y backend
- Trazabilidad más simple desde requisitos hacia implementación

### Negativas

- Más trabajo documental al inicio antes de ver funcionalidades
- Necesidad de mantener paridad bilingüe a medida que crecen los documentos
- Necesidad de decidir explícitamente comportamiento offline y procesamiento en segundo plano
