<?php
class HTML
{
	public function __construct(public string $title, public int $uid = 0, public string $lang = 'en')
	{
		ob_start();
	}
	public function __destruct()
	{
		$output = ob_get_clean();

		ob_start();
		?>
		<!DOCTYPE html>
		<html lang="<?= $this->lang; ?>">

		<head>
			<meta charset="UTF-8" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
			<title><?= $this->title; ?></title>
			<input type="hidden" id="uid_n" value="<?= $this->uid; ?>">
			<link href="/src/styles/tailwind.css" rel="stylesheet" />
			<link href="/src/styles/global.scss" rel="stylesheet" />
			<script src="../js/jquery-3.7.1.min.js"></script>
			<script src="/src/scripts/perspectiveCard.js"></script>
			<script src="https://www.google.com/recaptcha/api.js?render=6LdT2NcrAAAAAOGcZpBzPxpkbUHJvCz7aT7Rmqwq"></script>
			<script src="/node_modules/tinymce/tinymce.min.js" type="module"></script>
			<script type="text/javascript" src="../js/xlsx.full.min.js"></script>
			<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
			<script>
				$(function () {
					$('#navBar').load('/partials/navBar.html'); //Carga barra lateral para navegación de toda la página
					$('#submitLoginButton').click(function () { // función on click, activa submit por POST a backend, añade token recaptcha y action explicita de login/validarUsuario
						grecaptcha.ready(function () { // grecaptcha, añade token, action al form y lo envía (función propia de google API)
							grecaptcha.execute('6LdT2NcrAAAAAOGcZpBzPxpkbUHJvCz7aT7Rmqwq', {
								action: 'validarUsuario'
							}).then(function (token) {
								$('#form_Login').prepend('<input type="hidden" name="token" value="' + token + '" >');
								$('#form_Login').prepend('<input type="hidden" name="action" value="validarUsuario" >');
								$('#form_Login').submit();
							});
						});
					});

					// Genera o recupera el ID único del usuario para reportes de problemas
					function obtenerOAsignarID() {
						// 1. Intentar recuperar el ID del almacenamiento local
						let idUsuario = localStorage.getItem('report_device_id');

						// 2. Si no existe, generar uno nuevo (UUID v4)
						if (!idUsuario) {
							idUsuario = crypto.randomUUID();
							localStorage.setItem('report_device_id', idUsuario);
						}

						return idUsuario;
					}
					// Manejo de eventos mouseenter y mouseleave en el cuadro de reporte de problemas
					$('#reportBox').on({
						'mouseenter': function () {
							//Construye el contenido para el formulario de reporte de problemas de la página, presenta cuadro de texto, botón para enviar reporte.
							let reportContent = `<h3 class="reportFormContent">Reportar un problema</h3>
							<h4>No salga de esta ventana hasta enviar el reporte.</h4>
							<textarea class="reportFormContent" id="txtReportProblem" placeholder="Describe el problema..." rows="4" cols="50" maxlength="3500"></textarea>
							<br>
							<span class="reportFormContent" id="charCountLabel">0 / 3500 caracteres</span>
							<br>
							<button class="reportFormContent" id="btnSubmitReport">Enviar reporte</button>`;
							$('#reportBox').html(reportContent);
							$('#txtReportProblem').on('input', function () {
								let charCount = $(this).val().length;
								$('#charCountLabel').text(charCount + ' / 3500 caracteres');
							});
							
						},
						'mouseleave': function () {
							$('#reportBox').empty();
						}
					});
					// Ajax envia a backend reporte de usuario a soporte técnico
					$('#reportBox').on('click', '#btnSubmitReport', function () {
						let reportText = $('#txtReportProblem').val();
						if (reportText.trim() === '') {
							swal.fire({
								title: 'Atención',
								text: 'Por favor, ingresa una descripción del problema antes de enviar el reporte.',
								icon: 'warning',
								timer: 2500,
								showConfirmButton: false
							});
							return;
						}
						let visitorID = obtenerOAsignarID();
						let uid = $('#uid_n').val();
						$.ajax({
							type: 'POST',
							url: '/system/admin/ReportIssue',
							data: {
								action: 'reportIssue',
								uid: uid,
								visitor_id: visitorID,
								report: reportText
							},
							success: function (response) {
								// SweetAler2 con timer de auto-cerrado del cuadro de reporte tras 5 segundos
								swal.fire({
									title: 'Reporte enviado',
									text: 'Gracias por tu colaboración. El equipo de soporte técnico revisará el problema.',
									icon: 'success',
									timer: 2500,
									showConfirmButton: false
								});

								$('#reportBox').empty();
							},
							error: function () {
								swal.fire({
									title: 'Ups...',
									text: 'Error al enviar el reporte. Inténtalo de nuevo más tarde.',
									icon: 'error',
									timer: 2500,
									showConfirmButton: false
								});
							}
						});
					});
				});
			</script>

		</head>

		<body class="w-screen h-screen flex items-center justify-center bg-neutral-50">
			<div id="navBar" style="height: inherit;"></div>
			<?= $output; ?>
			<div id="reportBox"></div>
		</body>

		</html>
		<?php
		die(ob_get_clean());
	}
}
