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
  function gradientTracker(config) { // función principal
		var opt = $.extend( // opciones por defecto
			{
				selector: '',
				color1: '#00d18b',
				color2: '#00787a',
				maxTilt: 20,
				resetOnLeave: true,
			},
			config || {}, 
		);
		var $els = $(opt.selector); 
		if (!$els.length) return; // no hay elementos

		$els.each(function () { // inicializa variables CSS
			this.style.setProperty('--gt-color1', opt.color1);
			this.style.setProperty('--gt-color2', opt.color2);
			this.style.setProperty('--rotation-X', '0deg');
			this.style.setProperty('--rotation-Y', '0deg');
			this.style.setProperty('--X', '50%');
			this.style.setProperty('--Y', '50%');
		});

		var st = new WeakMap(); 
		$els.each(function () { // estado por elemento
			st.set(this, { rect: null, raf: null, lastX: 0, lastY: 0 });
		});

		function measure(el) { // mide y cachea el bounding rect
			st.get(el).rect = el.getBoundingClientRect();
		}
		function apply(el, cx, cy) { // aplica rotación y hotspot
			var r = st.get(el).rect || el.getBoundingClientRect();
			var px = Math.max(0, Math.min(1, (cx - r.left) / r.width));
			var py = Math.max(0, Math.min(1, (cy - r.top) / r.height));
			var rotY = (px - 0.5) * opt.maxTilt; // eje Y
			var rotX = (0.5 - py) * opt.maxTilt; // eje X
			el.style.setProperty('--rotation-X', rotX + 'deg');
			el.style.setProperty('--rotation-Y', rotY + 'deg');
			el.style.setProperty('--X', px * 100 + '%');
			el.style.setProperty('--Y', py * 100 + '%');
		}
		function schedule(el, cx, cy) { // throttle por rAF
			var s = st.get(el);
			s.lastX = cx;
			s.lastY = cy;
			if (s.raf) return;
			s.raf = requestAnimationFrame(function () {
				s.raf = null;
				apply(el, s.lastX, s.lastY);
			});
		}
		function reset(el) { // recentra
			if (!opt.resetOnLeave) return;
			el.style.setProperty('--rotation-X', '0deg');
			el.style.setProperty('--rotation-Y', '0deg');
			el.style.setProperty('--X', '50%');
			el.style.setProperty('--Y', '50%');
		}

    // EVENTOS
		$els.on('mouseenter', function () {
			measure(this);
		});
		$(window).on('resize scroll', function () {
			$els.each(function () {
				measure(this);
			});
		});
		$els.on('mousemove', function (e) {
			schedule(this, e.clientX, e.clientY);
		});
		$els.on('mouseleave', function () {
			reset(this);
		});
		$els.on('touchstart', function () {
			measure(this);
		});
		$els.on('touchmove', function (e) {
			var t = e.originalEvent.touches && e.originalEvent.touches[0];
			if (t) schedule(this, t.clientX, t.clientY);
		});
		$els.on('touchend', function () {
			reset(this);
		});
	}

	// EXPONE LA FUNCIÓN EN GLOBAL
	global.gradientTracker = gradientTracker;
})(window, jQuery);
