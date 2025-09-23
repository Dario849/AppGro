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
			
			<?php
			// Check if Vite build exists
			if (file_exists(__DIR__ . '/../../dist/manifest.json') || file_exists(__DIR__ . '/../../dist/.vite/manifest.json')) {
				// Production mode with Vite build
				echo '<link rel="stylesheet" href="/dist/assets/app.css">';
				echo '<script src="/js/jquery-3.7.1.min.js"></script>';
				echo '<script type="module" src="/dist/assets/app.js"></script>';
			} else {
				// Development mode - use direct files
				echo '<script src="/js/jquery-3.7.1.min.js"></script>';
				echo '<link href="/src/styles/tailwind.css" rel="stylesheet" />';
				echo '<link href="/src/styles/gridstack.css" rel="stylesheet" />';
				echo '<script src="/src/scripts/perspectiveCard.js"></script>';
				// Note: SCSS files can't be loaded directly, we'll use CSS alternatives
			}
			?>
		</head>

		<body class="w-screen h-screen flex items-center justify-center bg-neutral-50">
			<?= renderNavbar($_SESSION['user_id'] ?? null), $output; ?>
		</body>

		</html>
		<?php
		die(ob_get_clean());
	}
}
