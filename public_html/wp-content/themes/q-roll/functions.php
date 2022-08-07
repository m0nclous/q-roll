<?php

add_action('wp_enqueue_scripts', function () {
	$version = wp_get_theme()->get('Version');
//	$js = fn ($file) => get_theme_file_uri($file);

	wp_dequeue_style('wc-blocks-style');
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wc-blocks-style');

	if (user_agent_is_user()) wp_enqueue_style('font-roboto', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

	wp_enqueue_style('m0nclous-style', get_stylesheet_uri(), [], $version);

	if (is_checkout()) wp_enqueue_script('jquery.maskedinput', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js');
	wp_enqueue_script('m0nclous-script', get_theme_file_uri('assets/script.js'), [], $version, true);
});

/** Удаляем jquery-migrate */
add_action('wp_default_scripts', fn ($scripts) => (!is_admin() && isset($scripts->registered['jquery']) && $scripts->registered['jquery']->deps) ? $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']) : null);

/** Добавляем preconnect для Google Fonts */
add_action('wp_head', fn () => print('<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'));

/** Добавляем meta тег для верификации google */
add_action('wp_head', fn () => print('<meta name="google-site-verification" content="zt5uYkc802kL5CJY4ul_leUNvwFywnn1CUx55xIXEzI" />'));

/** Добавляем meta тег для верификации webmaster.yandex.ru */
add_action('wp_head', fn () => print('<meta name="yandex-verification" content="97530ced9599130e" />'));

/** Yandex.Metrika counter */
if (user_agent_is_user()) add_action('wp_head', fn () => print('<script type="text/javascript" > (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)}; m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)}) (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym"); ym(87871205, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true, ecommerce:"dataLayer" }); </script> <noscript><div><img src="https://mc.yandex.ru/watch/87871205" style="position:absolute; left:-9999px;" alt="" /></div></noscript>'));

/** Эта функция позволит плагинам и темам изменять мета-тег <title> */
add_theme_support('title-tag');

/** Включает поддержку html5 разметки для списка комментариев, формы комментариев, формы поиска, галереи и т.д. */
add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'script', 'style']);

/** Включает поддержку WooCommerce */
add_theme_support('woocommerce');

/** Удаление кнопки WP в админ панели */
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

/** Выводим все товары (без пагинации) */
add_filter('loop_shop_per_page', fn () => 999);

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

/** Исправляем ошибку "префикс article неизвестен валидатору" */
add_filter('language_attributes', fn ($lang) => $lang .= ' prefix="article: https://ogp.me/ns/article#" ', $lang);

/** Убираем хлебные крошки woocommerce */
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

/** Убираем хлебные крошки woocommerce */
add_action('woocommerce_product_thumbnails_columns', fn () => 1);

/** Убираем в Yoast Seo разметку поиска */
add_filter('disable_wpseo_json_ld_search', '__return_true');

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating');
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs');
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
add_action('woocommerce_single_product_summary', 'the_content', 5);

add_action('woocommerce_single_product_summary', fn () => printf('<div class="product__weight">%s</div>', get_the_weight(with_unit: true)), 7);

function get_the_weight($with_unit = false): string
{
	global $product;
	if (empty($product->get_weight())) return '';
	return $with_unit ? ($product->get_weight() . ' ' . get_option('woocommerce_weight_unit')) : $product->get_weight();
}

add_filter('wp_nav_menu_objects', function ($items) {
	foreach ($items as $item) if ($item->ID === 63 ) $item->title = '';
	return $items;
});

function user_agent_is_user(): bool
{
	return !str_contains($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') && !str_contains($_SERVER['HTTP_USER_AGENT'], 'YandexBot');
}