<?php get_header() ?>

<main>
	<?php wp_nav_menu(['sort_column' => 'menu_order', 'theme_location' => 'cart']) ?>

	<?php $categories = get_terms(['taxonomy' => 'product_cat', 'type' => 'product']) ?>

	<div class="menu-categories-container">
		<ul id="menu-categories" class="menu">
			<?php foreach ($categories as $category) : ?>
				<li id="menu-item-<?= $category->term_id ?>" class="menu-item menu-item-<?= $category->term_id ?>">
					<a href="<?= site_url('#' . $category->slug) ?>" title="<?= esc_attr($category->name) ?>"></a>
				</li>
			<?php endforeach ?>
		</ul>
	</div>

	<?php foreach ($categories as $category) : ?>
		<div class="products-container" id="<?= $category->slug ?>">
			<h1><?= $category->name ?></h1>
			<?= do_shortcode('[products per_page="-1" category="' . $category->slug . '" columns="4" orderby="menu_order" order="asc"]') ?>
		</div>
	<?php endforeach ?>
</main>

<?php get_footer() ?>