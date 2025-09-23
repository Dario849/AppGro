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
			<script src="../js/jquery-3.7.1.min.js"></script>
			<?php if (MODE_DEV): ?>
				<!-- En desarrollo, Vite maneja los estilos -->
				<script type="module" src="/src/main.js"></script>
			<?php else: ?>
				<!-- En producción, usar los archivos compilados -->
				<link rel="stylesheet" href="/assets/main.css">
				<script type="module" src="/assets/main.js"></script>
			<?php endif; ?>
			<script src="src/scripts/perspectiveCard.js"></script>
		</head>

		<body class="w-screen h-screen flex items-center justify-center bg-neutral-50">
			<?= renderNavbar($_SESSION['user_id'] ?? null), $output; ?>
		</body>

		</html>
		<?php
		die(ob_get_clean());
	}
}
