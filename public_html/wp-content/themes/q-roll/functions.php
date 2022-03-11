<?php

add_action('wp_enqueue_scripts', function () {
	$version = wp_get_theme()->get('Version');
	$js = fn ($file) => get_theme_file_uri($file);

	wp_register_style('font-roboto', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
	wp_enqueue_style('m0nclous-style', get_stylesheet_uri(), ['font-roboto'], $version);

	wp_register_script('jquery.maskedinput', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js');
	wp_enqueue_script('m0nclous-script', get_theme_file_uri('assets/script.js'), ['jquery.maskedinput'], $version, true);
});

/** Удаляем jquery-migrate */
add_action('wp_default_scripts', fn ($scripts) => (!is_admin() && isset($scripts->registered['jquery']) && $scripts->registered['jquery']->deps) ? $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']) : null);

/** Добавляем preconnect для гугл шрифтов */
add_action('wp_head', fn () => print('<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'));

/** Добавляем meta тег для верификации enot */
add_action('wp_head', fn () => print('<meta name="enot" content="8471646207409BHdzxmnzXMMo1pz-cI-yhGdbnbE6gnPV">'));

/** Добавляем meta тег для верификации google */
add_action('wp_head', fn () => print('<meta name="google-site-verification" content="zt5uYkc802kL5CJY4ul_leUNvwFywnn1CUx55xIXEzI" />'));

/** Эта функция позволит плагинам и темам изменять метатег <title> */
add_theme_support('title-tag');

/** Включает поддержку html5 разметки для списка комментариев, формы комментариев, формы поиска, галереи и т.д. */
add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'script', 'style']);

/** Включает поддержку WooCommerce */
add_theme_support('woocommerce');

/** Удаление кнопки WP в админ панеле */
add_action('admin_bar_menu', fn ($wp_admin_bar) => $wp_admin_bar->remove_node('wp-logo'), 999);

/** Добавляем области меню */
add_action('after_setup_theme', fn () => register_nav_menus(['header' => 'Header', 'footer' => 'Footer', 'cart' => 'Cart', 'footer-socials' => 'Footer Socials']));

/** Вывод описания товара после названия в цикле */
add_action('woocommerce_after_shop_loop_item_title', fn () => printf('<div class="woocommerce-loop-product__content">%s</div>', get_the_content()), 5);

/** Вывод веса товара после описания в цикле */
add_action('woocommerce_after_shop_loop_item_title', fn () => printf('<div class="woocommerce-loop-product__weight">%s</div>', get_the_weight(with_unit: true)), 7);

/** Подменяем weight_unit "g" на "г" при получении опции */
add_filter('option_woocommerce_weight_unit', fn ($value) => $value === 'g' ? 'г' : $value);

/** Убираем текст на кнопке "добавить в корзину" */
add_filter('woocommerce_product_add_to_cart_text', '__return_empty_string');

/** Убираем текст на кнопке "добавить в корзину" */
add_filter('woocommerce_product_add_to_cart_text', '__return_empty_string');

/** Предотвращаем вывод "Просмотр корзины" */
add_action('woocommerce_after_shop_loop_item', fn () => print('<div class="added_to_cart"></div>'), 10);

/** Удаляем стандартный заголовок на странице оформления заказа */
add_filter('the_title', fn ($title, $id) => !is_admin() && $id === wc_get_page_id('checkout') ? '' : $title, 10, 2);

/** Удаляем стандартный заголовок "Billing details" на странице оформления заказа */
add_filter('gettext_woocommerce', fn ($translation, $text) => $text === 'Billing details' ? '' : $translation, 10, 2);

/** Удаляем стандартный заголовок "Additional information" на странице оформления заказа */
add_filter('gettext_woocommerce', fn ($translation, $text) => $text === 'Additional information' ? '' : $translation, 10, 2);

/** Добавляем заголовок перед корзиной */
add_action('woocommerce_before_cart', fn () => print('<h1>Корзина</h1>'));

/** Добавляем заголовок перед формой оформления заказа */
add_action('woocommerce_before_checkout_form', fn () => print('<h1>Оформление заказа</h1>'));

/** Меняем ссылку корзины на ссылку оформления заказа */
add_filter('woocommerce_get_cart_url', 'wc_get_checkout_url');

/** Направляем на страницу магазина, если мы находимся на оформлении заказа и у нас пустая корзина */
add_action('template_redirect', fn () => is_checkout() && !(isset($_GET['key']) && is_wc_endpoint_url('order-received')) && count(WC()->cart->cart_contents) === 0 ? exit(wp_redirect(get_home_url())) : '');

/** Корректировка тестов Здоровье сайта */
add_filter('site_status_tests', function ($tests) {
	// Тема по умолчанию
	unset($tests['direct']['theme_version']);

	// git репозиторий
	unset($tests['async']['background_updates']);

	return $tests;
});

/** Убираем хлебные крошки woocommerce */
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

/** Убираем хлебные крошки woocommerce */
add_action('woocommerce_product_thumbnails_columns', fn () => 1);

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
add_action('woocommerce_single_product_summary', 'the_content', 5);

add_action('woocommerce_single_product_summary', fn () => printf('<div class="product__weight">%s</div>', get_the_weight(with_unit: true)), 7);

function get_the_weight($with_unit = false) {
	global $product;
	if (empty($product->get_weight())) return '';
	return $with_unit ? ($product->get_weight() . ' ' . get_option('woocommerce_weight_unit')) : $product->get_weight();
}
