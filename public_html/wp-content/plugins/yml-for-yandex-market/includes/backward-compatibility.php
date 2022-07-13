<?php if (!defined('ABSPATH')) {exit;}
/*
Version: 1.0.0
Date: 20-02-2022
Author: Maxim Glazunov
Author URI: https://icopydoc.ru 
License: GPLv2
Description: This code helps ensure backward compatibility with older versions of the plugin.
*/

define('yfym_DIR', plugin_dir_path(__FILE__)); // yfym_DIR contains /home/p135/www/site.ru/wp-content/plugins/myplagin/		
define('yfym_URL', plugin_dir_url(__FILE__)); // yfym_URL contains http://site.ru/wp-content/plugins/myplagin/		
$upload_dir = (object)wp_get_upload_dir(); // yfym_UPLOAD_DIR contains /home/p256/www/site.ru/wp-content/uploads
define('yfym_UPLOAD_DIR', $upload_dir->basedir);
$name_dir = $upload_dir->basedir."/yfym"; 
define('yfym_NAME_DIR', $name_dir); // yfym_UPLOAD_DIR contains /home/p256/www/site.ru/wp-content/uploads/yfym
$yfym_keeplogs = yfym_optionGET('yfym_keeplogs');
define('yfym_KEEPLOGS', $yfym_keeplogs);
define('yfym_VER', '3.6.16');
if (!defined('yfym_ALLNUMFEED')) {
    define('yfym_ALLNUMFEED', '5');
}

function yfym_add_settings_arr() {
	$numFeed = '1';

	$yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
	$yfym_settings_arr_keys_arr = array_keys($yfym_settings_arr);
	for ($i = 0; $i < count($yfym_settings_arr_keys_arr); $i++) {
		$feed_id = $yfym_settings_arr_keys_arr[$i];

  
	   wp_clear_scheduled_hook('yfym_cron_period', array($feed_id));
	   wp_clear_scheduled_hook('yfym_cron_sborki', array($feed_id));
	}
 
	$yfym_settings_arr = array();
	$numFeed = '1';  
	for ($i = 1; $i<$allNumFeed+1; $i++) { 
		$yfym_settings_arr[$numFeed]['yfym_status_cron'] = yfym_optionGET('yfym_status_cron', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_step_export'] = yfym_optionGET('yfym_step_export', $numFeed, 'for_update_option');
//		$yfym_settings_arr[$numFeed]['yfym_status_sborki'] = yfym_optionGET('yfym_status_sborki', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_date_sborki'] = yfym_optionGET('yfym_date_sborki', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_type_sborki'] = yfym_optionGET('yfym_type_sborki', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_file_url'] = yfym_optionGET('yfym_file_url', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_file_file'] = yfym_optionGET('yfym_file_file', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_file_ids_in_yml'] = yfym_optionGET('yfym_file_ids_in_yml', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_ufup'] = yfym_optionGET('yfym_ufup', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_magazin_type'] = yfym_optionGET('yfym_magazin_type', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_vendor'] = yfym_optionGET('yfym_vendor', $numFeed, 'for_update_option'); 
		$yfym_settings_arr[$numFeed]['yfym_vendor_post_meta'] = yfym_optionGET('yfym_vendor_post_meta', $numFeed, 'for_update_option'); 
		$yfym_settings_arr[$numFeed]['yfym_whot_export'] = yfym_optionGET('yfym_whot_export', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_yml_rules'] = yfym_optionGET('yfym_yml_rules', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_skip_missing_products'] = yfym_optionGET('yfym_skip_missing_products', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_date_save_set'] = yfym_optionGET('yfym_date_save_set', $numFeed, 'for_update_option');	
		$yfym_settings_arr[$numFeed]['yfym_separator_type'] = yfym_optionGET('yfym_separator_type', $numFeed, 'for_update_option'); 
		$yfym_settings_arr[$numFeed]['yfym_behavior_onbackorder'] = yfym_optionGET('yfym_behavior_onbackorder', $numFeed, 'for_update_option'); 
		$yfym_settings_arr[$numFeed]['yfym_behavior_stip_symbol'] = yfym_optionGET('yfym_behavior_stip_symbol', $numFeed, 'for_update_option'); 
		$yfym_settings_arr[$numFeed]['yfym_feed_assignment'] = yfym_optionGET('yfym_feed_assignment', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_file_extension'] = yfym_optionGET('yfym_file_extension', $numFeed, 'for_update_option');

		$yfym_settings_arr[$numFeed]['yfym_shop_sku'] = yfym_optionGET('yfym_shop_sku', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_count'] = yfym_optionGET('yfym_count', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_auto_disabled'] = yfym_optionGET('yfym_auto_disabled', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_amount'] = yfym_optionGET('yfym_amount', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_manufacturer'] = yfym_optionGET('yfym_manufacturer', $numFeed, 'for_update_option');	

		$yfym_settings_arr[$numFeed]['yfym_shop_name'] = yfym_optionGET('yfym_shop_name', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_company_name'] = yfym_optionGET('yfym_company_name', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_currencies'] = 'enabled';
		$yfym_settings_arr[$numFeed]['yfym_main_product'] = yfym_optionGET('yfym_main_product', $numFeed, 'for_update_option');		
		$yfym_settings_arr[$numFeed]['yfym_adult'] = yfym_optionGET('yfym_adult', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_wooc_currencies'] = yfym_optionGET('yfym_wooc_currencies', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_desc'] = yfym_optionGET('yfym_desc', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_the_content'] = yfym_optionGET('yfym_the_content', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_var_desc_priority'] = yfym_optionGET('yfym_var_desc_priority', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_clear_get'] = yfym_optionGET('yfym_clear_get', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_price_from'] = yfym_optionGET('yfym_price_from', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_oldprice'] = yfym_optionGET('yfym_oldprice', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_vat'] = yfym_optionGET('yfym_vat', $numFeed, 'for_update_option');

//		$yfym_settings_arr[$numFeed]['yfym_params_arr'] = yfym_optionGET('yfym_params_arr', serialize(array()), $numFeed, 'for_update_option');
//		$yfym_settings_arr[$numFeed]['yfym_add_in_name_arr'] = yfym_optionGET('yfym_add_in_name_arr', serialize(array()), $numFeed, 'for_update_option');
//		$yfym_settings_arr[$numFeed]['yfym_no_group_id_arr'] = yfym_optionGET('yfym_no_group_id_arr', serialize(array()), $numFeed, 'for_update_option');

		$yfym_settings_arr[$numFeed]['yfym_product_tag_arr'] = yfym_optionGET('yfym_product_tag_arr', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_store'] = yfym_optionGET('yfym_store', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_delivery'] = yfym_optionGET('yfym_delivery', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_delivery_options'] = yfym_optionGET('yfym_delivery_options', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_delivery_cost'] = yfym_optionGET('yfym_delivery_cost', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_delivery_days'] = yfym_optionGET('yfym_delivery_days', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_order_before'] = yfym_optionGET('yfym_order_before', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_delivery_options2'] = yfym_optionGET('yfym_delivery_options2', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_delivery_cost2'] = yfym_optionGET('yfym_delivery_cost2', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_delivery_days2'] = yfym_optionGET('yfym_delivery_days2', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_order_before2'] = yfym_optionGET('yfym_order_before2', $numFeed, 'for_update_option');		
		$yfym_settings_arr[$numFeed]['yfym_sales_notes_cat'] = yfym_optionGET('yfym_sales_notes_cat', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_sales_notes'] = yfym_optionGET('yfym_sales_notes', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_model'] = yfym_optionGET('yfym_model', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_pickup'] = yfym_optionGET('yfym_pickup', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_barcode'] = yfym_optionGET('yfym_barcode', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_barcode_post_meta'] = yfym_optionGET('yfym_barcode_post_meta', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_barcode_post_meta_var'] = '';	
		$yfym_settings_arr[$numFeed]['yfym_vendorcode'] = yfym_optionGET('yfym_vendorcode', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_enable_auto_discount'] = yfym_optionGET('yfym_enable_auto_discount', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_expiry'] = yfym_optionGET('yfym_expiry', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_period_of_validity_days'] = 'disabled';
		$yfym_settings_arr[$numFeed]['yfym_downloadable'] = yfym_optionGET('yfym_downloadable', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_age'] = yfym_optionGET('yfym_age', $numFeed, 'for_update_option');	
		$yfym_settings_arr[$numFeed]['yfym_country_of_origin'] = yfym_optionGET('yfym_country_of_origin', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_source_id'] = 'disabled';
		$yfym_settings_arr[$numFeed]['yfym_source_id_post_meta'] = '';
		$yfym_settings_arr[$numFeed]['yfym_ebay_stock'] = '0';
		$yfym_settings_arr[$numFeed]['yfym_manufacturer_warranty'] = yfym_optionGET('yfym_manufacturer_warranty', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_errors'] = yfym_optionGET('yfym_errors', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_enable_auto_discounts'] = yfym_optionGET('yfym_enable_auto_discounts', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_skip_backorders_products'] = yfym_optionGET('yfym_skip_backorders_products', $numFeed, 'for_update_option');
		$yfym_settings_arr[$numFeed]['yfym_no_default_png_products'] = yfym_optionGET('yfym_no_default_png_products', $numFeed, 'for_update_option');	
		$yfym_settings_arr[$numFeed]['yfym_skip_products_without_pic'] = yfym_optionGET('yfym_skip_products_without_pic', $numFeed, 'for_update_option');
		$numFeed++;  
		$yfym_registered_feeds_arr = array(
			0 => array('last_id' => $i),
			1 => array('id' => $i)
		);
	}

	if (is_multisite()) {
		update_blog_option(get_current_blog_id(), 'yfym_settings_arr', $yfym_settings_arr);
		update_blog_option(get_current_blog_id(), 'yfym_registered_feeds_arr', $yfym_registered_feeds_arr);
	} else {
		update_option('yfym_settings_arr', $yfym_settings_arr);
		update_option('yfym_registered_feeds_arr', $yfym_registered_feeds_arr);
	}
	$numFeed = '1';  
	for ($i = 1; $i<$allNumFeed+1; $i++) {		
		yfym_optionDEL('yfym_shop_sku', $numFeed);
		yfym_optionDEL('yfym_count', $numFeed);
		yfym_optionDEL('yfym_auto_disabled', $numFeed);
		yfym_optionDEL('yfym_amount', $numFeed);
		yfym_optionDEL('yfym_manufacturer', $numFeed);

		yfym_optionDEL('yfym_shop_name', $numFeed);
		yfym_optionDEL('yfym_company_name', $numFeed);
		yfym_optionDEL('yfym_main_product', $numFeed);			
		yfym_optionDEL('yfym_version', $numFeed);
		yfym_optionDEL('yfym_status_cron', $numFeed);
		yfym_optionDEL('yfym_whot_export', $numFeed);
		yfym_optionDEL('yfym_yml_rules', $numFeed);
		yfym_optionDEL('yfym_skip_missing_products', $numFeed);
		yfym_optionDEL('yfym_date_save_set', $numFeed);
		yfym_optionDEL('yfym_separator_type', $numFeed);
		yfym_optionDEL('yfym_behavior_onbackorder', $numFeed);
		yfym_optionDEL('yfym_behavior_stip_symbol', $numFeed); 
		yfym_optionDEL('yfym_feed_assignment', $numFeed);
		yfym_optionDEL('yfym_file_extension', $numFeed);
//		yfym_optionDEL('yfym_status_sborki', $numFeed);
		yfym_optionDEL('yfym_date_sborki', $numFeed);
		yfym_optionDEL('yfym_type_sborki', $numFeed);
		yfym_optionDEL('yfym_vendor', $numFeed);
		yfym_optionDEL('yfym_vendor_post_meta', $numFeed);
		yfym_optionDEL('yfym_model', $numFeed);
//		yfym_optionDEL('yfym_params_arr', $numFeed);
//		yfym_optionDEL('yfym_add_in_name_arr', $numFeed);
//		yfym_optionDEL('yfym_no_group_id_arr', $numFeed);
/*?*/	yfym_optionDEL('yfym_product_tag_arr', $numFeed);
		yfym_optionDEL('yfym_file_url', $numFeed);
		yfym_optionDEL('yfym_file_file', $numFeed);
		yfym_optionDEL('yfym_ufup', $numFeed);
		yfym_optionDEL('yfym_magazin_type', $numFeed);
		yfym_optionDEL('yfym_pickup', $numFeed);
		yfym_optionDEL('yfym_store', $numFeed);
		yfym_optionDEL('yfym_delivery', $numFeed);
		yfym_optionDEL('yfym_delivery_options', $numFeed);		
		yfym_optionDEL('yfym_delivery_cost', $numFeed);
		yfym_optionDEL('yfym_delivery_days', $numFeed);
		yfym_optionDEL('yfym_order_before', $numFeed);	
		yfym_optionDEL('yfym_delivery_options2', $numFeed);
		yfym_optionDEL('yfym_delivery_cost2', $numFeed);
		yfym_optionDEL('yfym_delivery_days2', $numFeed);
		yfym_optionDEL('yfym_order_before2', $numFeed);		
		yfym_optionDEL('yfym_sales_notes_cat', $numFeed);
		yfym_optionDEL('yfym_sales_notes', $numFeed);
		yfym_optionDEL('yfym_price_from', $numFeed);	
		yfym_optionDEL('yfym_desc', $numFeed);
		yfym_optionDEL('yfym_the_content', $numFeed);
		yfym_optionDEL('yfym_var_desc_priority', $numFeed);
		yfym_optionDEL('yfym_clear_get', $numFeed);
		yfym_optionDEL('yfym_barcode', $numFeed);
		yfym_optionDEL('yfym_barcode_post_meta', $numFeed);
		yfym_optionDEL('yfym_vendorcode', $numFeed);
		yfym_optionDEL('yfym_enable_auto_discount', $numFeed);
		yfym_optionDEL('yfym_expiry', $numFeed);
		yfym_optionDEL('yfym_downloadable', $numFeed);
		yfym_optionDEL('yfym_age', $numFeed);
		yfym_optionDEL('yfym_country_of_origin', $numFeed);
		yfym_optionDEL('yfym_manufacturer_warranty', $numFeed);
		yfym_optionDEL('yfym_adult', $numFeed);
		yfym_optionDEL('yfym_wooc_currencies', $numFeed);
		yfym_optionDEL('yfym_oldprice', $numFeed);
		yfym_optionDEL('yfym_vat', $numFeed);
		yfym_optionDEL('yfym_step_export', $numFeed);
		yfym_optionDEL('yfym_errors', $numFeed);
		yfym_optionDEL('yfym_enable_auto_discounts', $numFeed);
		yfym_optionDEL('yfym_skip_backorders_products', $numFeed);
		yfym_optionDEL('yfym_no_default_png_products', $numFeed);
		yfym_optionDEL('yfym_skip_products_without_pic', $numFeed);
		$numFeed++;
	}

	// перезапустим крон-задачи
	for ($i = 1; $i < yfym_number_all_feeds(); $i++) {
		$numFeed = (string)$i;
		$status_sborki = (int)yfym_optionGET('yfym_status_sborki', $numFeed);
		$yfym_status_cron = yfym_optionGET('yfym_status_cron', $numFeed, 'set_arr');
		if ($yfym_status_cron === 'off') {continue;}
		$recurrence = $yfym_status_cron;
		wp_clear_scheduled_hook('yfym_cron_period', array($numFeed));
		wp_schedule_event(time(), $recurrence, 'yfym_cron_period', array($numFeed));
		yfym_error_log('FEED № '.$numFeed.'; yfym_cron_period внесен в список заданий; Файл: export.php; Строка: '.__LINE__, 0);
	}
}

/* Вероятно можно удалять */
/*
* @since 1.1.0
*
* @return array
* Возвращает массив настроек фида по умолчанию
*/
function yfym_set_default_feed_settings_arr($whot = 'feed') {
	if ($whot === 'feed') {
		$blog_title = get_bloginfo('name');
		$blog_title = mb_strimwidth($blog_title, 0, 20);
		$result_arr = array(
			'yfym_status_cron' => 'off',
			'yfym_step_export' => '500',
	//		'yfym_status_sborki' => '-1', // статус сборки файла
			'yfym_date_sborki' => 'unknown', // дата последней сборки
			'yfym_type_sborki' => 'yml', // тип собираемого файла yml или xls
			'yfym_file_url' => '', // урл до файла
			'yfym_file_file' => '', // путь до файла
			'yfym_file_ids_in_yml' => '',
			'yfym_ufup' => '0',
			'yfym_magazin_type' => 'woocommerce', // тип плагина магазина 
			'yfym_vendor' => 'disabled', 
			'yfym_vendor_post_meta' => '', 

			'yfym_whot_export' => 'all', // что выгружать (все или там где галка)
			'yfym_yml_rules' => 'yandex_market',
			'yfym_skip_missing_products' => '0',
			'yfym_date_save_set' => 'unknown', // дата сохранения настроек		
			'yfym_separator_type' => 'type1', 
			'yfym_behavior_onbackorder' => 'false', 
			'yfym_behavior_stip_symbol' => 'default', 
			'yfym_feed_assignment' => '',
			'yfym_file_extension' => 'xml',
	
			'yfym_shop_sku' => 'disabled',
			'yfym_count' => 'disabled',
			'yfym_auto_disabled' => 'disabled',
			'yfym_amount' => 'disabled',
			'yfym_manufacturer' => 'disabled',	
	
			'yfym_shop_name' => $blog_title,
			'yfym_company_name' => $blog_title,
			'yfym_currencies' => 'enabled',
			'yfym_main_product' => 'other',		
			'yfym_adult' => 'no',
			'yfym_wooc_currencies' => '',
			'yfym_desc' => 'fullexcerpt',
			'yfym_the_content' => 'enabled',
			'yfym_var_desc_priority' => 'on',
			'yfym_clear_get' => 'no',
			'yfym_price_from' => 'no', // разрешить "цена от"
			'yfym_oldprice' => 'no',
			'yfym_vat' => 'disabled',
	//		'yfym_params_arr', serialize(array()),
	//		'yfym_add_in_name_arr', serialize(array()),
	//		'yfym_no_group_id_arr', serialize(array()),
	/* ? */	'yfym_product_tag_arr' => '', // id меток таксономии product_tag
			'yfym_store' => 'false',
			'yfym_delivery' => 'false',
			'yfym_delivery_options' => '0',
			'yfym_delivery_cost' => '0',
			'yfym_delivery_days' => '32',
			'yfym_order_before' => '',
			'yfym_delivery_options2' => '0',
			'yfym_delivery_cost2' => '0',
			'yfym_delivery_days2' => '32',
			'yfym_order_before2' => '',		
			'yfym_sales_notes_cat' => 'off',
			'yfym_sales_notes' => '',
			'yfym_model' => 'disabled', // атрибут model магазина
			'yfym_pickup' => 'true',
			'yfym_barcode' => 'disabled',
			'yfym_barcode_post_meta' => '',
			'yfym_barcode_post_meta_var' => '',
			'yfym_vendorcode' => 'disabled',
			'yfym_enable_auto_discount' => '',
			'yfym_expiry' => 'off',
			'yfym_period_of_validity_days' => 'disabled',
			'yfym_downloadable' => 'off',
			'yfym_age' => 'off',	
			'yfym_country_of_origin' => 'off',
			'yfym_source_id' => 'disabled',
			'yfym_source_id_post_meta' => '',
			'yfym_ebay_stock' => '0', 
			'yfym_manufacturer_warranty' => 'off',
			'yfym_errors' => '',
			'yfym_enable_auto_discounts' => '',
			'yfym_skip_backorders_products' => '0',
			'yfym_no_default_png_products' => '0',	
			'yfym_skip_products_without_pic' => '0',
		);
		do_action('yfym_set_default_feed_settings_result_arr_action', $result_arr, $whot); /* с версии 3.6.4. */
		$result_arr = apply_filters('yfym_set_default_feed_settings_result_arr_filter', $result_arr, $whot); /* с версии 3.6.4. */	
		return $result_arr;
	} 
}

/*
* С версии 2.0.0
* Функция склейки
*/
function yfym_onlygluing($numFeed = '1') {
    yfym_error_log('FEED № '.$numFeed.'; NOTICE: Стартовала yfym_onlygluing; Файл: functions.php; Строка: '.__LINE__, 0); 	
    do_action('yfym_before_construct', 'cache');
    $result_yml = yfym_feed_header($numFeed);
    /* создаем файл или перезаписываем старый удалив содержимое */
    $result = yfym_write_file($result_yml, 'w+', $numFeed);
    if ($result !== true) {
       yfym_error_log('FEED № '.$numFeed.'; yfym_write_file вернула ошибку! $result ='.$result.'; Файл: functions.php; Строка: '.__LINE__, 0);
    } 
    
    yfym_optionUPD('yfym_status_sborki', '-1', $numFeed); 
    $whot_export = yfym_optionGET('yfym_whot_export', $numFeed, 'set_arr');
   
    $result_yml = '';
    $step_export = -1;
    $prod_id_arr = array(); 
    
    if ($whot_export === 'vygruzhat') {
       $args = array(
           'post_type' => 'product',
           'post_status' => 'publish',
           'posts_per_page' => $step_export, // сколько выводить товаров
           // 'offset' => $offset,
           'relation' => 'AND',
           'fields'  => 'ids',
           'meta_query' => array(
               array(
                   'key' => 'vygruzhat',
                   'value' => 'on'
               )
           )
       );	
    } else { //  if ($whot_export == 'all' || $whot_export == 'simple')
       $args = array(
           'post_type' => 'product',
           'post_status' => 'publish',
           'posts_per_page' => $step_export, // сколько выводить товаров
           // 'offset' => $offset,
           'relation' => 'AND',
           'fields'  => 'ids'
       );
    }
   
    $args = apply_filters('yfym_query_arg_filter', $args, $numFeed);
    yfym_error_log('FEED № '.$numFeed.'; NOTICE: yfym_onlygluing до запуска WP_Query RAM '.round(memory_get_usage()/1024, 1) . ' Кб; Файл: functions.php; Строка: '.__LINE__, 0); 
    $featured_query = new WP_Query($args);
    yfym_error_log('FEED № '.$numFeed.'; NOTICE: yfym_onlygluing после запуска WP_Query RAM '.round(memory_get_usage()/1024, 1) . ' Кб; Файл: functions.php; Строка: '.__LINE__, 0); 
    
    global $wpdb;
    if ($featured_query->have_posts()) { 
       for ($i = 0; $i < count($featured_query->posts); $i++) {
           /*	
           *	если не юзаем 'fields'  => 'ids'
           *	$prod_id_arr[$i]['ID'] = $featured_query->posts[$i]->ID;
           *	$prod_id_arr[$i]['post_modified_gmt'] = $featured_query->posts[$i]->post_modified_gmt;
           */
           $curID = $featured_query->posts[$i];
           $prod_id_arr[$i]['ID'] = $curID;
   
           $res = $wpdb->get_results($wpdb->prepare("SELECT post_modified_gmt FROM $wpdb->posts WHERE id=%d", $curID), ARRAY_A);
           $prod_id_arr[$i]['post_modified_gmt'] = $res[0]['post_modified_gmt']; 	
           // get_post_modified_time('Y-m-j H:i:s', true, $featured_query->posts[$i]);
       }
       wp_reset_query(); /* Remember to reset */
       unset($featured_query); // чутка освободим память
    }
    if (!empty($prod_id_arr)) {
       yfym_error_log('FEED № '.$numFeed.'; NOTICE: yfym_onlygluing передала управление yfym_gluing; Файл: functions.php; Строка: '.__LINE__, 0);
       yfym_gluing($prod_id_arr, $numFeed);
    }
    
    // если постов нет, пишем концовку файла
    $result_yml = "</offers>". PHP_EOL; 
    $result_yml = apply_filters('yfym_after_offers_filter', $result_yml, $numFeed);
    $result_yml .= "</shop>". PHP_EOL ."</yml_catalog>";
    /* создаем файл или перезаписываем старый удалив содержимое */
    $result = yfym_write_file($result_yml, 'a', $numFeed);
    yfym_rename_file($numFeed);	 
    // выставляем статус сборки в "готово"
    $status_sborki = -1;
    if ($result == true) {
       yfym_optionGET('yfym_status_sborki', $status_sborki, $numFeed);	
       // останавливаем крон сборки
       wp_clear_scheduled_hook('yfym_cron_sborki');
       do_action('yfym_after_construct', 'cache');
    } else {
       yfym_error_log('FEED № '.$numFeed.'; yfym_write_file вернула ошибку! Я не смог записать концовку файла... $result ='.$result.'; Файл: functions.php; Строка: '.__LINE__, 0);
       do_action('yfym_after_construct', 'false');
    }
} // end function yfym_onlygluing()
/*
* С версии 2.0.0
* Функция склейки/сборки
*/
function yfym_gluing($id_arr, $numFeed = '1') {
    /*	
    * $id_arr[$i]['ID'] - ID товара
    * $id_arr[$i]['post_modified_gmt'] - Время обновления карточки товара
    * global $wpdb;
    * $res = $wpdb->get_results("SELECT ID, post_modified_gmt FROM $wpdb->posts WHERE post_type = 'product' AND post_status = 'publish'");	
    */	
    yfym_error_log('FEED № '.$numFeed.'; Стартовала yfym_gluing; Файл: functions.php; Строка: '.__LINE__, 0);
    if ($numFeed === '1') {$prefFeed = '';} else {$prefFeed = $numFeed;} 
    $upload_dir = (object)wp_get_upload_dir();
    $name_dir = $upload_dir->basedir.'/yfym/feed'.$numFeed;
    if (!is_dir($name_dir)) {
       if (!mkdir($name_dir)) {
           error_log('FEED № '.$numFeed.'; Нет папки yfym! И создать не вышло! $name_dir ='.$name_dir.'; Файл: functions.php; Строка: '.__LINE__, 0);
       } else {
           error_log('FEED № '.$numFeed.'; Создали папку yfym! Файл: functions.php; Строка: '.__LINE__, 0);
       }
    }
    
    $yfym_file_file = urldecode(yfym_optionGET('yfym_file_file', $numFeed, 'set_arr'));
    $yfym_file_ids_in_yml = urldecode(yfym_optionGET('yfym_file_ids_in_yml', $numFeed, 'set_arr'));
   
    $yfym_date_save_set = yfym_optionGET('yfym_date_save_set', $numFeed, 'set_arr');
    clearstatcache(); // очищаем кэш дат файлов
    // $prod_id
    foreach ($id_arr as $product) {
       $filename = $name_dir.'/'.$product['ID'].'.tmp';
       $filenameIn = $name_dir.'/'.$product['ID'].'-in.tmp'; /* с версии 3.1.0 */
       yfym_error_log('FEED № '.$numFeed.'; RAM '.round(memory_get_usage()/1024, 1).' Кб. ID товара/файл = '.$product['ID'].'.tmp; Файл: functions.php; Строка: '.__LINE__, 0);
       if (is_file($filename) && is_file($filenameIn)) { // if (file_exists($filename)) {
           $last_upd_file = filemtime($filename); // 1318189167			
           if (($last_upd_file < strtotime($product['post_modified_gmt'])) || ($yfym_date_save_set > $last_upd_file)) {
               // Файл кэша обновлен раньше чем время модификации товара
               // или файл обновлен раньше чем время обновления настроек фида
               yfym_error_log('FEED № '.$numFeed.'; NOTICE: Файл кэша '.$filename.' обновлен РАНЬШЕ чем время модификации товара или время сохранения настроек фида! Файл: functions.php; Строка: '.__LINE__, 0);	
               $result_yml_unit = yfym_unit($product['ID'], $numFeed);
               if (is_array($result_yml_unit)) {
                   $result_yml = $result_yml_unit[0];
                   $ids_in_yml = $result_yml_unit[1];
               } else {
                   $result_yml = $result_yml_unit;
                   $ids_in_yml = '';
               }	
               if (yfym_optionGET('yzen_yandex_zen_rss') == 'enabled') {$result_yml = yfym_optionGET('yfym_feed_content');};
               yfym_wf($result_yml, $product['ID'], $numFeed, $ids_in_yml);
               file_put_contents($yfym_file_file, $result_yml, FILE_APPEND);			
               file_put_contents($yfym_file_ids_in_yml, $ids_in_yml, FILE_APPEND);
           } else {
               // Файл кэша обновлен позже чем время модификации товара
               // или файл обновлен позже чем время обновления настроек фида
               yfym_error_log('FEED № '.$numFeed.'; NOTICE: Файл кэша '.$filename.' обновлен ПОЗЖЕ чем время модификации товара или время сохранения настроек фида; Файл: functions.php; Строка: '.__LINE__, 0);
               yfym_error_log('FEED № '.$numFeed.'; Пристыковываем файл кэша без изменений; Файл: functions.php; Строка: '.__LINE__, 0);
               $result_yml = file_get_contents($filename);
               file_put_contents($yfym_file_file, $result_yml, FILE_APPEND);
               $ids_in_yml = file_get_contents($filenameIn);
               file_put_contents($yfym_file_ids_in_yml, $ids_in_yml, FILE_APPEND);
           }
       } else { // Файла нет
           yfym_error_log('FEED № '.$numFeed.'; NOTICE: Файла кэша товара '.$filename.' ещё нет! Создаем... Файл: functions.php; Строка: '.__LINE__, 0);		
           $result_yml_unit = yfym_unit($product['ID'], $numFeed);
           if (is_array($result_yml_unit)) {
               $result_yml = $result_yml_unit[0];
               $ids_in_yml = $result_yml_unit[1];
           } else {
               $result_yml = $result_yml_unit;
               $ids_in_yml = '';
           }
           yfym_wf($result_yml, $product['ID'], $numFeed, $ids_in_yml);
           yfym_error_log('FEED № '.$numFeed.'; Создали! Файл: functions.php; Строка: '.__LINE__, 0);
           file_put_contents($yfym_file_file, $result_yml, FILE_APPEND);
           file_put_contents($yfym_file_ids_in_yml, $ids_in_yml, FILE_APPEND);
       }
    }
} // end function yfym_gluing()
?>