<?php
require('system/main.php');
$layout = new HTML(title: 'Gradiente radial por elemento');
?>
<main class="main__content">
  <div class="main_container">
    <style>
      /* Layout mínimo para observar el efecto */
      :root {
        color-scheme: dark;
      }

      body {
        margin: 0;
        font: 14px/1.4 system-ui;
        background: #0e1116;
      }

      .grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        padding: 16px;
        min-height: 100vh;
      }

      /* El padre aporta perspectiva real 3D */
      .with-perspective {
        perspective: 1000px;
      }

      /* Estilo base del componente interactivo */
      .has-gradient-tracker {
        /* Paleta por elemento (se puede sobreescribir vía JS) */
        --gt-color1: #00d18b;
        /* color del centro del gradiente */
        --gt-color2: #00787a;
        /* color externo del gradiente */

        /* Variables reactivas por elemento */
        --X: 50%;
        /* posición horizontal del hotspot (0%-100%) */
        --Y: 50%;
        /* posición vertical del hotspot (0%-100%) */
        --rotation-X: 0deg;
        /* inclinación sobre eje X (vertical) */
        --rotation-Y: 0deg;
        /* inclinación sobre eje Y (horizontal) */

        position: relative;
        min-height: 260px;
        border-radius: 16px;
        padding: 16px;
        color: #fff;

        /* Hotspot + fondo. No animamos background en CSS para evitar repaints caros */
        background: radial-gradient(circle at var(--X) var(--Y), var(--gt-color1) 0%, var(--gt-color2) 100%);
        /* La animación fluida sucede en la capa de composición (transform) */
        transform: rotateX(var(--rotation-X)) rotateY(var(--rotation-Y));
        transform-style: preserve-3d;
        backface-visibility: hidden;
        transition: transform 80ms ease-out;
        /* solo transform */
        will-change: transform;
        contain: paint;
        /* limita el área de repintado */
        display: flex;
        align-items: center;
        justify-content: center;
      }
    </style>
      <div class="with-perspective">
        <div class="has-gradient-tracker stats-graph">.stats-graph</div>
      </div>
      <div class="with-perspective">
        <div class="has-gradient-tracker weather-container">.weather-container</div>
      </div>
    <script>
      /**
       * gradientTracker(config)
       * Aplica tilt + hotspot por elemento con:
       *  - cache de bounding rect
       *  - throttle por requestAnimationFrame
       *  - lógica de grados:
       *      rotY = (px - 0.5) * maxTilt
       *      rotX = (0.5 - py) * maxTilt
       * @param {Object} config
       *  - selector       : string CSS para los elementos objetivo
       *  - color1,color2  : colores del gradiente
       *  - maxTilt        : grados máximos de inclinación
       *  - resetOnLeave   : recentra al salir
       */
      (function (global, $) {
        function gradientTracker(config) {
          var defaults = {
            selector: '',
            color1: '#00d18b',
            color2: '#00787a',
            maxTilt: 20,
            resetOnLeave: true
          };
          var options = $.extend({}, defaults, config || {});
          var $targets = $(options.selector);
          if (!$targets.length) return { detach: $.noop, setCoords: $.noop };

          // Colores por elemento
          $targets.each(function () {
            this.style.setProperty('--gt-color1', options.color1);
            this.style.setProperty('--gt-color2', options.color2);
          });

          // Estado por elemento
          var elementState = new WeakMap();
          $targets.each(function () {
            elementState.set(this, {
              cachedRect: null,     // último getBoundingClientRect()
              rafId: null,          // requestAnimationFrame activo
              lastClientX: 0,       // último X en px de viewport
              lastClientY: 0        // último Y en px de viewport
            });
            centerElement(this);
          });

          // --- Utilidades ---

          /** Mide y cachea el rectángulo del elemento */
          function measureBoundingRect(element) {
            var state = elementState.get(element);
            state.cachedRect = element.getBoundingClientRect();
          }

          /** Aplica variables CSS locales usando clientX/clientY */
          function applyCSSVariables(element, clientX, clientY) {
            var state = elementState.get(element);
            var rect = state.cachedRect || element.getBoundingClientRect(); // fallback

            // Normalización 0..1 dentro del elemento
            var normalizedX = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
            var normalizedY = Math.max(0, Math.min(1, (clientY - rect.top) / rect.height));

            // Lógica pedida: presiona del lado del cursor
            var tiltYDeg = (normalizedX - 0.5) * options.maxTilt; // rotación sobre eje Y
            var tiltXDeg = (0.5 - normalizedY) * options.maxTilt; // rotación sobre eje X

            element.style.setProperty('--rotation-X', tiltXDeg + 'deg');
            element.style.setProperty('--rotation-Y', tiltYDeg + 'deg');
            element.style.setProperty('--X', (normalizedX * 100) + '%');
            element.style.setProperty('--Y', (normalizedY * 100) + '%');
          }

          /** Agenda un frame y evita calcular en cada mousemove */
          function scheduleAnimationFrame(element, clientX, clientY) {
            var state = elementState.get(element);
            state.lastClientX = clientX;
            state.lastClientY = clientY;
            if (state.rafId) return; // ya hay un frame pendiente

            state.rafId = requestAnimationFrame(function () {
              state.rafId = null;
              applyCSSVariables(element, state.lastClientX, state.lastClientY);
            });
          }

          /** Recentra y quita tilt */
          function centerElement(element) {
            if (!options.resetOnLeave) return;
            element.style.setProperty('--rotation-X', '0deg');
            element.style.setProperty('--rotation-Y', '0deg');
            element.style.setProperty('--X', '50%');
            element.style.setProperty('--Y', '50%');
          }

          // --- Eventos ---

          // Medición al entrar
          $targets.on('mouseenter', function () { measureBoundingRect(this); });

          // Re-medición ante cambios globales de layout
          $(window).on('resize scroll', function () {
            $targets.each(function () { measureBoundingRect(this); });
          });

          // Movimiento con rAF
          $targets.on('mousemove', function (e) {
            scheduleAnimationFrame(this, e.clientX, e.clientY);
          });
          $targets.on('mouseleave', function () { centerElement(this); });

          // Soporte touch
          $targets.on('touchstart', function () { measureBoundingRect(this); });
          $targets.on('touchmove', function (e) {
            var touch = e.originalEvent.touches && e.originalEvent.touches[0];
            if (touch) scheduleAnimationFrame(this, touch.clientX, touch.clientY);
          });
          $targets.on('touchend', function () { centerElement(this); });

          // API mínima
          return {
            detach: function () {
              $targets.off('mouseenter mousemove mouseleave touchstart touchmove touchend');
              $(window).off('resize scroll');
              $targets.each(function () {
                var state = elementState.get(this);
                if (state && state.rafId) cancelAnimationFrame(state.rafId);
              });
            },
            setCoords: function (xPercent, yPercent, index) {
              var element = $targets.get(index || 0); if (!element) return;
              element.style.setProperty('--X', xPercent + '%');
              element.style.setProperty('--Y', yPercent + '%');
            }
          };
        }

        // Exponer a window
        window.gradientTracker = gradientTracker;
      })(window, jQuery);

      // Instancias independientes
      gradientTracker({
        selector: '.stats-graph',
        color1: '#007950',
        color2: '#00787a',
        maxTilt: 20
      });

      gradientTracker({
        selector: '.weather-container',
        color1: '#00d18b',
        color2: '#00787a',
        maxTilt: 14
      });
    </script>