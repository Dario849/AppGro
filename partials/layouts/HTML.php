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
			<script src="public/js/jquery-3.7.1.min.js"></script>
			<!-- Assets generados por Vite -->
			<?php foreach (vite_css('src/main.js') as $css): ?>
				<link rel="stylesheet" href="<?= $css ?>">
			<?php endforeach; ?>
			<?php foreach (vite_css('src/styles/global.scss') as $css): ?>
				<link rel="stylesheet" href="<?= $css ?>">
			<?php endforeach; ?>
			<script type="module" src="<?= vite_asset('src/main.js') ?>"></script>

			<script src="src/scripts/perspectiveCard.js"></script>
		</head>

		<body class="w-screen h-screen flex items-center justify-center bg-neutral-50">
			<div class="container mx-auto p-4">
				<h1 class="text-3xl font-bold text-blue-600">¡Hola Mundo!</h1>
				<p class="mt-4 text-gray-600">Si ves estilos, Tailwind está funcionando!</p>
			</div>
			<?= renderNavbar($_SESSION['user_id'] ?? null), $output; ?>
		</body>

		</html>
		<?php
		die(ob_get_clean());
	}
}
