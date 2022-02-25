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

/** Эта функция позволит плагинам и темам изменять метатег <title> */
add_theme_support('title-tag');

/** Включает поддержку html5 разметки для списка комментариев, формы комментариев, формы поиска, галереи и т.д. */
add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'script', 'style']);

/** Включает поддержку WooCommerce */
add_theme_support('woocommerce');

/** Удаление кнопки WP в админ панеле */
add_action('admin_bar_menu', fn ($wp_admin_bar) => $wp_admin_bar->remove_node('wp-logo'), 999);

/** Добавляем области меню */
add_action('after_setup_theme', fn () => register_nav_menus(['header' => 'Header', 'footer' => 'Footer', 'footer-socials' => 'Footer Socials', 'cart' => 'Cart']));

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

/** Выводим поле для купонов после checkout_order_review */
add_action('woocommerce_checkout_order_review', fn () => printf('<div class="checkout_coupon"><input type="text" name="coupon_code" class="input-text" placeholder="%s" id="coupon_code_fake" value=""><button type="submit" class="button" name="apply_coupon" value="%s">%s</button></div>', esc_attr__('Coupon code', 'woocommerce'), esc_attr__('Apply coupon', 'woocommerce'), esc_html__('Apply coupon', 'woocommerce')));

function get_the_weight($with_unit = false) {
	global $product;
	if (empty($product->get_weight())) return '';
	return $with_unit ? ($product->get_weight() . ' ' . get_option('woocommerce_weight_unit')) : $product->get_weight();
}
