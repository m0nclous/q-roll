<!DOCTYPE html>
<html <?php language_attributes() ?>>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head() ?>
</head>

<body <?php body_class() ?>>
	<?php wp_body_open() ?>

	<header>
		<?php the_custom_logo() ?>
		<?php wp_nav_menu(['sort_column' => 'menu_order', 'theme_location' => 'header']) ?>
	</header>