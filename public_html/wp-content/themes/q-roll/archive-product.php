<?php get_header() ?>

<main>
	<?php $categories = get_terms(['taxonomy' => 'product_cat', 'type' => 'product']) ?>

	<ul id="menu-categories" class="menu">
		<?php foreach ($categories as $category) : ?>
			<li id="menu-category-<?= $category->term_id ?>" class="menu-category menu-category-<?= $category->term_id ?>">
				<a href="<?= site_url('#' . $category->slug) ?>"><?= '' //$category->name ?></a>
			</li>
		<?php endforeach ?>
	</ul>

	<?php foreach ($categories as $category) : ?>
		<div class="products-container" id="<?= $category->slug ?>">
			<h1><?= $category->name ?></h1>
			<?= do_shortcode('[products per_page="-1" category="' . $category->slug . '" columns="4" orderby="menu_order" order="asc"]') ?>
		</div>
	<?php endforeach ?>
</main>

<?php get_footer() ?>