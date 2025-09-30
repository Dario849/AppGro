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
			<script src="/js/jquery-3.7.1.min.js"></script>
			<script src="https://cdn.tailwindcss.com"></script>
			<link rel="stylesheet" href="/src/styles/global.css">
			<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
			<script src="/src/scripts/perspectiveCard.js"></script>
			<script src="https://www.google.com/recaptcha/api.js?render=6LdT2NcrAAAAAOGcZpBzPxpkbUHJvCz7aT7Rmqwq"></script>
			<script>
				$(function () {
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
				});
			</script>

		</head>

		<body class="w-screen h-screen flex items-center justify-center bg-neutral-50">
			<?= renderNavbar($_SESSION['user_id'] ?? null), $output; ?>
		</body>

		</html>
		<?php
		die(ob_get_clean());
	}
}
