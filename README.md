# AppGro
<img 
  src="https://github.com/user-attachments/assets/876e192a-299c-4c8c-b139-e36db82526e7" 
  alt="AppGro Logo" 
  width="300"
/>


AppGro es un proyecto de recolección de datos, centralizado en el sector agrícola, permite almacenar datos sobre cultivos, ganado, herramientas, rendimientos.
Todo esto con el propósito principal de permitir la centralizacin, visualización, y graficación de estos datos.
La base del proyecto fue realizada con [php-vite-starter](https://github.com/nititech/php-vite-starter) como plantilla para la estructura del proyecto.
Integrantes:
- @Dario849
- @Kenstar05
- @EnzoTaboada

## Estructura del proyecto

```
.
├── index.php              # Punto de entrada y router principal
├── configs/
│   ├── env.php            # Variables de entorno expuestas a PHP
│   └── routes.php         # Definición de rutas con FastRoute
├── pages/                 # Vistas EJS/PHP manejadas por Vite
├── partials/              # Fragmentos reutilizables de vista
├── src/
│   ├── scripts/           # Archivos TypeScript/JavaScript
│   └── styles/            # Archivos SCSS/CSS
├── public/                # Recursos estáticos (imágenes, fonts, etc.)
├── vite.config.ts         # Configuración de Vite
├── tailwind.config.ts     # Configuración de Tailwind CSS
├── tsconfig.json          # Configuración de TypeScript
└── package.json           # Dependencias y scripts de NPM
```

## Características

- **Hot Module Replacement** para PHP, JS y CSS.
- **FastRoute** para enrutamiento liviano en PHP.
- **Integración con Vite**: compila y sirve assets modernos.
- **Tailwind CSS** y **Sass/SCSS** incluidos por defecto.
- **Carga de variables de entorno** en PHP vía vite-plugin-php.
- **Transformación de imágenes** y manejo de SVG.
- **EJS/Templates**: mezcla plantillas PHP con sintaxis EJS.

## Instalación

1. Clona este repositorio:
   ```bash
   git clone https://github.com/Dario849/AppGro.git
   cd AppGro
   ```
2. Instala dependencias:
   ```bash
   npm install
   composer install
   ```
3. Crea un archivo de entorno:
   ```bash
   cp .env.example .env
   ```
   Ajusta tus variables en `configs/env.php` si es necesario.

## Uso

- **Modo desarrollo** (con HMR):
  ```bash
  npm run dev
  ```
  Accede a `http://localhost:3000` (o el puerto configurado) y PHP recargará con cambios instantáneos.

- **Build de producción**:
  ```bash
  npm run build
  ```
  Los assets compilados se publicarán en `dist/`.

## Scripts NPM disponibles

- `dev`: Inicia el servidor de desarrollo de Vite.
- `build`: Genera los assets optimizados.
- `preview`: Previsualiza el build de producción.

## Créditos

- Base del proyecto: [nititech/php-vite-starter](https://github.com/nititech/php-vite-starter) por [@nititech](https://github.com/nititech).  
- Plugin para variables de entorno HTTP/PHP: [donnikitos/vite-plugin-php](https://github.com/donnikitos/vite-plugin-php) por [@donnikitos](https://github.com/donnikitos).

## Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.
