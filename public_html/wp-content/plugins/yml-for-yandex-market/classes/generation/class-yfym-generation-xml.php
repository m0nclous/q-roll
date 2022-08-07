<?php if (!defined('ABSPATH')) {exit;}
/**
* Starts feed generation
*
* @link       https://icopydoc.ru/
* @since      1.0.0
*/

class YFYM_Generation_XML {
	private $pref = 'yfym';
	protected $feed_id;
	protected $result_xml = '';

	public function __construct($feed_id) {
		$this->feed_id = $feed_id;
	}

	public function write_file($result_xml, $cc) {
		$filename = urldecode(yfym_optionGET('yfym_file_file', $this->get_feed_id(), 'set_arr'));
		if ($this->get_feed_id() === '1') {$prefFeed = '';} else {$prefFeed = $this->get_feed_id();}
		 
		if ($filename == '') {	
			$upload_dir = (object)wp_get_upload_dir(); // $upload_dir->basedir
			$filename = $upload_dir->basedir."/".$prefFeed."feed-yml-0-tmp.xml"; // $upload_dir->path
		}
		if (file_exists($filename)) { // файл есть
			if (!$handle = fopen($filename, $cc)) {
				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Не могу открыть файл '.$filename.'; Файл: class-generation-xml.php; Строка: '.__LINE__);
			}
			if (fwrite($handle, $result_xml) === FALSE) {
				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Не могу произвести запись в файл '.$handle.'; Файл: class-generation-xml.php; Строка: '.__LINE__);
			} else {
				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Ура! Записали; Файл: Файл: class-generation-xml.php; Строка: '.__LINE__);
				return true;
			}
			fclose($handle);
		} else {
			new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Файла $filename = '.$filename.' еще нет. Файл: class-generation-xml.php; Строка: '.__LINE__);
			// файла еще нет
			// попытаемся создать файл
			if (is_multisite()) {
				$upload = wp_upload_bits($prefFeed.'feed-yml-'.get_current_blog_id().'-tmp.xml', null, $result_xml ); // загружаем shop2_295221-xml в папку загрузок
			} else {
				$upload = wp_upload_bits($prefFeed.'feed-yml-0-tmp.xml', null, $result_xml ); // загружаем shop2_295221-xml в папку загрузок
			}
			/*
			*	для работы с csv или xml требуется в плагине разрешить загрузку таких файлов
			*	$upload['file'] => '/var/www/wordpress/wp-content/uploads/2010/03/feed-xml.xml', // путь
			*	$upload['url'] => 'http://site.ru/wp-content/uploads/2010/03/feed-xml.xml', // урл
			*	$upload['error'] => false, // сюда записывается сообщение об ошибке в случае ошибки
			*/
			// проверим получилась ли запись
			if ($upload['error']) {
				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Запись вызвала ошибку: '. $upload['error'].'; Файл: class-generation-xml.php; Строка: '.__LINE__);
			} else {
				yfym_optionUPD('yfym_file_file', urlencode($upload['file']), $this->get_feed_id(), 'yes', 'set_arr');
				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Запись удалась! Путь файла: '. $upload['file'] .'; УРЛ файла: '. $upload['url']);
				return true;
			}		
		}
	}

	public function gluing($id_arr) {
		/*	
		* $id_arr[$i]['ID'] - ID товара
		* $id_arr[$i]['post_modified_gmt'] - Время обновления карточки товара
		* global $wpdb;
		* $res = $wpdb->get_results("SELECT ID, post_modified_gmt FROM $wpdb->posts WHERE post_type = 'product' AND post_status = 'publish'");	
		*/
		if ($this->get_feed_id() === '1') {$prefFeed = '';} else {$prefFeed = $this->get_feed_id();} 
		$upload_dir = (object)wp_get_upload_dir();
		$name_dir = $upload_dir->basedir.'/yfym/feed'.$this->get_feed_id();
		if (!is_dir($name_dir)) {
			if (!mkdir($name_dir)) {
				error_log('FEED № '.$this->get_feed_id().'; Нет папки yfym! И создать не вышло! $name_dir ='.$name_dir.'; Файл: class-yfym-generation-xml.php; Строка: '.__LINE__, 0);
			} else {
				error_log('FEED № '.$this->get_feed_id().'; Создали папку yfym! Файл: class-yfym-generation-xml.php; Строка: '.__LINE__, 0);
			}
		}
	   
		$yfym_file_file = urldecode(yfym_optionGET('yfym_file_file', $this->get_feed_id(), 'set_arr'));
		$yfym_file_ids_in_xml = urldecode(yfym_optionGET('yfym_file_ids_in_xml', $this->get_feed_id(), 'set_arr'));
		if (empty($yfym_file_ids_in_xml)) { // если не указан адрес файла с id-шниками
			$yfym_file_ids_in_xml = YFYM_PLUGIN_UPLOADS_DIR_PATH .'/feed'.$this->get_feed_id().'/ids_in_xml.tmp';		
			yfym_optionUPD('yfym_file_ids_in_xml', urlencode($yfym_file_ids_in_xml), $this->get_feed_id(), 'yes', 'set_arr');

			/* ! возможно нужно добавить эти строки */
			$yfym_file_ids_in_xml = YFYM_PLUGIN_UPLOADS_DIR_PATH .'/feed'.$this->get_feed_id().'/ids_in_yml.tmp';		
			yfym_optionUPD('yfym_file_ids_in_yml', urlencode($yfym_file_ids_in_xml), $this->get_feed_id(), 'yes', 'set_arr');
			/* ! end возможно нужно добавить эти строки */
		}
	   
		$yfym_date_save_set = yfym_optionGET('yfym_date_save_set', $this->get_feed_id(), 'set_arr');
		clearstatcache(); // очищаем кэш дат файлов

		foreach ($id_arr as $product) {
			$filename = $name_dir.'/'.$product['ID'].'.tmp';
			$filenameIn = $name_dir.'/'.$product['ID'].'-in.tmp'; /* с версии 2.0.0 */
			new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; RAM '.round(memory_get_usage()/1024, 1).' Кб. ID товара/файл = '.$product['ID'].'.tmp; Файл: class-yfym-generation-xml.php; Строка: '.__LINE__);
			if (is_file($filename) && is_file($filenameIn)) { // if (file_exists($filename)) {
				$last_upd_file = filemtime($filename); // 1318189167			
				if (($last_upd_file < strtotime($product['post_modified_gmt'])) || ($yfym_date_save_set > $last_upd_file)) {
					// Файл кэша обновлен раньше чем время модификации товара
					// или файл обновлен раньше чем время обновления настроек фида
					new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; NOTICE: Файл кэша '.$filename.' обновлен РАНЬШЕ чем время модификации товара или время сохранения настроек фида! Файл: class-yfym-generation-xml.php; Строка: '.__LINE__);	
					$result_get_unit_obj = new YFYM_Get_Unit($product['ID'], $this->get_feed_id());
					$result_xml = $result_get_unit_obj->get_result();
					$ids_in_xml = $result_get_unit_obj->get_ids_in_xml();

					yfym_wf($result_xml, $product['ID'], $this->get_feed_id(), $ids_in_xml);
					new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Обновили кэш товара. Файл: functions.php; Строка: '.__LINE__);
// /				file_put_contents($yfym_file_file, $result_xml, FILE_APPEND);
					file_put_contents($yfym_file_ids_in_xml, $ids_in_xml, FILE_APPEND);

					/* if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}	
					if (yfym_optionGET('yzen_yandex_zeng_rss') == 'enabled') {$result_xml = yfym_optionGET('yfym_feed_content');};
					*/
				} else {
					// Файл кэша обновлен позже чем время модификации товара
					// или файл обновлен позже чем время обновления настроек фида
					new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; NOTICE: Файл кэша '.$filename.' обновлен ПОЗЖЕ чем время модификации товара или время сохранения настроек фида; Файл: class-yfym-generation-xml.php; Строка: '.__LINE__);
					new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Пристыковываем файл кэша без изменений; Файл: class-yfym-generation-xml.php; Строка: '.__LINE__);
					$result_xml = file_get_contents($filename);
// /				file_put_contents($yfym_file_file, $result_xml, FILE_APPEND);
					$ids_in_xml = file_get_contents($filenameIn);
					file_put_contents($yfym_file_ids_in_xml, $ids_in_xml, FILE_APPEND);
				}
			} else { // Файла нет
				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; NOTICE: Файла кэша товара '.$filename.' ещё нет! Создаем... Файл: class-yfym-generation-xml.php; Строка: '.__LINE__);
				$result_get_unit_obj = new YFYM_Get_Unit($product['ID'], $this->get_feed_id());
				$result_xml = $result_get_unit_obj->get_result();
				$ids_in_xml = $result_get_unit_obj->get_ids_in_xml();

				yfym_wf($result_xml, $product['ID'], $this->get_feed_id(), $ids_in_xml);
				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Создали кэш товара. Файл: functions.php; Строка: '.__LINE__);
// /			file_put_contents($yfym_file_file, $result_xml, FILE_APPEND);
				file_put_contents($yfym_file_ids_in_xml, $ids_in_xml, FILE_APPEND);
			}
		}
	} // end function gluing()

	private function clear_file_ids_in_xml($feed_id) {
		$yfym_file_ids_in_xml = urldecode(yfym_optionGET('yfym_file_ids_in_xml', $feed_id, 'set_arr'));
		if (is_file($yfym_file_ids_in_xml)) {
			new YFYM_Error_Log('FEED № '.$feed_id.'; NOTICE: Обнуляем файл $yfym_file_ids_in_xml = '.$yfym_file_ids_in_xml.'; Файл: class-generation-xml.php; Строка: '.__LINE__);
			file_put_contents($yfym_file_ids_in_xml, '');
		} else {		
			new YFYM_Error_Log('FEED № '.$feed_id.'; WARNING: Файла c idшниками $yfym_file_ids_in_xml = '.$yfym_file_ids_in_xml.' нет! Создадим пустой; Файл: class-generation-xml.php; Строка: '.__LINE__);
			$yfym_file_ids_in_xml = YFYM_PLUGIN_UPLOADS_DIR_PATH .'/feed'.$feed_id.'/ids_in_xml.tmp';		
			$res = file_put_contents($yfym_file_ids_in_xml, '');
			if ($res !== false) {
				new YFYM_Error_Log('FEED № '.$feed_id.'; NOTICE: Файл c idшниками $yfym_file_ids_in_xml = '.$yfym_file_ids_in_xml.' успешно создан; Файл: class-generation-xml.php; Строка: '.__LINE__);
				yfym_optionUPD('yfym_file_ids_in_xml', urlencode($yfym_file_ids_in_xml), $feed_id, 'yes', 'set_arr');
			} else {
				new YFYM_Error_Log('FEED № '.$feed_id.'; ERROR: Ошибка создания файла $yfym_file_ids_in_xml = '.$yfym_file_ids_in_xml.'; Файл: class-generation-xml.php; Строка: '.__LINE__);
			}
		}
	}

	public function run() {
		$result_xml	= '';

		$step_export = (int)yfym_optionGET('yfym_step_export', $this->get_feed_id(), 'set_arr');
		$status_sborki = (int)yfym_optionGET('yfym_status_sborki', $this->get_feed_id()); // файл уже собран. На всякий случай отключим крон сборки

		new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; $status_sborki = '.$status_sborki.'; Файл: class-generation-xml.php; Строка: '.__LINE__);

		switch($status_sborki) {
			case -1: // сборка завершена
				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; case -1; Файл: class-generation-xml.php; Строка: '.__LINE__);

				wp_clear_scheduled_hook('yfym_cron_sborki', array($this->get_feed_id()));
			break;
			case 1: // сборка начата		
				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; case 1; Файл: class-generation-xml.php; Строка: '.__LINE__);

				$result_xml = $this->get_feed_header(); 
				$result = $this->write_file($result_xml, 'w+', $this->get_feed_id());
				if ($result !== true) {
					new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; yfym_write_file вернула ошибку! $result ='.$result.'; Файл: class-generation-xml.php; Строка: '.__LINE__);
					$this->stop();
					return; 
				} else {
					new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; yfym_write_file отработала успешно; Файл: class-generation-xml.php; Строка: '.__LINE__);
				}
				$this->clear_file_ids_in_xml($this->get_feed_id()); /* С версии 2.0.0 */
				$status_sborki = 2;
				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; status_sborki увеличен на '.$step_export.' и равен '.$status_sborki.'; Файл: class-generation-xml.php; Строка: '.__LINE__);	  
				yfym_optionUPD('yfym_status_sborki', $status_sborki, $this->get_feed_id());
			break;
			default:
				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; case default; Файл: class-generation-xml.php; Строка: '.__LINE__);

				$offset = (($status_sborki - 1)  * $step_export) - $step_export; // $status_sborki - $step_export;
				$args = array(
					'post_type' => 'product',
					'post_status' => 'publish',
					'posts_per_page' => $step_export,
					'offset' => $offset,
					'relation' => 'AND',
				);
				$whot_export = yfym_optionGET('yfym_whot_export', $this->get_feed_id(), 'set_arr');
				switch($whot_export) {
					case "vygruzhat":
						$args['meta_query'] = array(
							array(
								'key' => 'vygruzhat',
								'value' => 'on'
							)
						);
					break;
					case "xmlset":
						$yfym_xmlset_number = '1';
						$yfym_xmlset_number = apply_filters('yfym_xmlset_number_filter', $yfym_xmlset_number, $this->get_feed_id());
						$yfym_xmlset_key = '_yfym_xmlset'.$yfym_xmlset_number;
						$args['meta_query'] = array(
							array(
								'key' => $yfym_xmlset_key,
								'value' => 'on'
							)
						);
					break;
				} // end switch($whot_export)
				$args = apply_filters('yfym_query_arg_filter', $args, $this->get_feed_id());

				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Полная сборка. $whot_export = '.$whot_export.'; Файл: class-generation-xml.php; Строка: '.__LINE__);

				new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; $args =>; Файл: class-generation-xml.php; Строка: '.__LINE__);
				new YFYM_Error_Log($args);

				$featured_query = new \WP_Query($args);
				$prod_id_arr = array(); 
				if ($featured_query->have_posts()) { 		
					for ($i = 0; $i < count($featured_query->posts); $i++) {
						$prod_id_arr[$i]['ID'] = $featured_query->posts[$i]->ID;
						$prod_id_arr[$i]['post_modified_gmt'] = $featured_query->posts[$i]->post_modified_gmt;
					}
					wp_reset_query(); /* Remember to reset */
					unset($featured_query); // чутка освободим память
					$this->gluing($prod_id_arr);
					$status_sborki++; // = $status_sborki + $step_export;
					new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; status_sborki увеличен на '.$step_export.' и равен '.$status_sborki.'; Файл: class-generation-xml.php; Строка: '.__LINE__);	  
					yfym_optionUPD('yfym_status_sborki', $status_sborki, $this->get_feed_id());		   
				} else { // если постов нет, пишем концовку файла
					$result_xml = $this->get_feed_footer();
					$result = yfym_write_file($result_xml, 'a', $this->get_feed_id());
					new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Файл фида готов. Осталось только переименовать временный файл в основной; Файл: xml-for-google-merchant-center.php; Строка: '.__LINE__);
					yfym_rename_file($this->get_feed_id());

					$this->stop();
				/*	$status_sborki = -1;
					if ($result === true) {
						yfym_optionUPD('yfym_status_sborki', $status_sborki, $this->get_feed_id());				
						wp_clear_scheduled_hook('yfym_cron_sborki', array($this->get_feed_id())); // останавливаем крон сборки
						new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; SUCCESS: Сборка успешно завершена; Файл: class-generation-xml.php; Строка: '.__LINE__);
						do_action('yfym_after_construct', 'full'); // сборка закончена
					} else {
						new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; ERROR: На завершающем этапе yfym_write_file вернула ошибку! Я не смог записать концовку файла... $result ='.$result.'; Файл: class-generation-xml.php; Строка: '.__LINE__);
						do_action('yfym_after_construct', 'false'); // сборка закончена
					}*/
				}
			// end default
		} // end switch($status_sborki)
		return; // final return from public function phase()
	} 

	public function stop() {
		$status_sborki = -1;
		yfym_optionUPD('yfym_status_sborki', $status_sborki, $this->get_feed_id());				
		wp_clear_scheduled_hook('yfym_cron_sborki', array($this->get_feed_id()));
		do_action('yfym_after_construct', $this->get_feed_id(), 'full'); // сборка закончена
	}

	// проверим, нужна ли пересборка фида при обновлении поста
	public function check_ufup($post_id) {
		$yfym_ufup = yfym_optionGET('yfym_ufup', $this->get_feed_id(), 'set_arr');
		if ($yfym_ufup === 'on') {
			$status_sborki = (int)yfym_optionGET('yfym_status_sborki', $this->get_feed_id());
			if ($status_sborki > -1) { // если идет сборка фида - пропуск
				return false;			
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	protected function get_feed_header($result_xml = '') {
		$unixtime = current_time('Y-m-d H:i'); // время в unix формате 
		$rfc_3339_time = current_time('c'); // 2022-07-17T17:47:19+03:00
		yfym_optionUPD('yfym_date_sborki', $unixtime, $this->get_feed_id(), 'yes', 'set_arr');		
		$shop_name = stripslashes(yfym_optionGET('yfym_shop_name', $this->get_feed_id(), 'set_arr'));
		$company_name = stripslashes(yfym_optionGET('yfym_company_name', $this->get_feed_id(), 'set_arr'));
		$result_xml .= '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
		$yfym_format_date = yfym_optionGET('yfym_format_date', $this->get_feed_id(), 'set_arr');
		if ($yfym_format_date === 'unixtime') {
			$catalog_date = $unixtime;
		} else {
			$catalog_date = (string)$rfc_3339_time;
		}
		$result_xml .= '<yml_catalog date="'.$catalog_date.'">'.PHP_EOL;
		$result_xml .= "<shop>". PHP_EOL ."<name>".esc_html($shop_name)."</name>".PHP_EOL;
		$result_xml .= new YFYM_Get_Paired_Tag('company', esc_html($company_name));
		$res_home_url = home_url('/');
		$res_home_url = apply_filters('yfym_home_url', $res_home_url, $this->get_feed_id());
		$result_xml .= new YFYM_Get_Paired_Tag('url', yfym_replace_domain($res_home_url, $this->get_feed_id()));
		$result_xml .= new YFYM_Get_Paired_Tag('platform', 'WordPress - Yml for Yandex Market');
		$result_xml .= new YFYM_Get_Paired_Tag('version', get_bloginfo('version'));
	   
		if (class_exists('WOOCS')) { 
		   $yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->get_feed_id(), 'set_arr');
		   if ($yfym_wooc_currencies !== '') {
			   global $WOOCS;
			   $WOOCS->set_currency($yfym_wooc_currencies);
		   }
		}
	   
		/* общие параметры */
		$yfym_currencies = yfym_optionGET('yfym_currencies', $this->get_feed_id(), 'set_arr');
		if ($yfym_currencies !== 'disabled') {
		   $res = get_woocommerce_currency(); // получаем валюта магазина
		   $rateCB = '';
		   switch ($res) { /* RUR, USD, EUR, UAH, KZT, BYN */
			   case "RUB": $currencyId_yml = "RUR"; break;
			   case "USD": $currencyId_yml = "USD"; $rateCB = "CB"; break;
			   case "EUR": $currencyId_yml = "EUR"; $rateCB = "CB"; break;
			   case "UAH": $currencyId_yml = "UAH"; break;
			   case "KZT": $currencyId_yml = "KZT"; break;
			   case "BYN": $currencyId_yml = "BYN"; break;	
			   case "BYR": $currencyId_yml = "BYN"; break;
			   case "ABC": $currencyId_yml = "BYN"; break;	
			   default: $currencyId_yml = "RUR"; 
		   }
		   $rateCB = apply_filters('yfym_rateCB', $rateCB, $this->get_feed_id()); /* с версии 2.3.1 */
		   $currencyId_yml = apply_filters('yfym_currency_id', $currencyId_yml, $this->get_feed_id()); /* с версии 3.3.15 */
		   if ($rateCB == '') {
			   $result_xml .= '<currencies>'. PHP_EOL .'<currency id="'.$currencyId_yml.'" rate="1"/>'. PHP_EOL .'</currencies>'.PHP_EOL;
		   } else {
			   $result_xml .= '<currencies>'. PHP_EOL .'<currency id="RUR" rate="1"/>'. PHP_EOL .'<currency id="'.$currencyId_yml.'" rate="'.$rateCB.'"/>'. PHP_EOL .'</currencies>'.PHP_EOL;		
		   }
		}
		// $terms = get_terms("product_cat");
		if (get_bloginfo('version') < '4.5') {
		   $args_terms_arr = array(
			   'hide_empty' => 0, 
			   'orderby' => 'name'
		   );
		   $args_terms_arr = apply_filters('yfym_args_terms_arr_filter', $args_terms_arr, $this->get_feed_id()); /* с версии 3.1.6. */	
		   $terms = get_terms('product_cat', $args_terms_arr);
		} else {
		   $args_terms_arr = array(
			   'hide_empty'  => 0,  
			   'orderby' => 'name',
			   'taxonomy'    => 'product_cat'
		   );
		   $args_terms_arr = apply_filters('yfym_args_terms_arr_filter', $args_terms_arr, $this->get_feed_id()); /* с версии 3.1.6. */	
		   $terms = get_terms($args_terms_arr);		
		}
		$count = count($terms);
		$result_xml .= '<categories>'.PHP_EOL;
		if ($count > 0) {		
		   $result_categories_yml = '';
		   foreach ($terms as $term) {
			   $result_categories_yml .= '<category id="'.$term->term_id.'"';
			   if ($term->parent !== 0) {
				   $result_categories_yml .= ' parentId="'.$term->parent.'"';
			   }
			   $add_attr = '';
			   $add_attr = apply_filters('yfym_add_category_attr_filter', $add_attr, $terms, $term, $this->get_feed_id()); /* c версии 3.4.2 */
			   $result_categories_yml .= $add_attr.'>'.$term->name.'</category>'.PHP_EOL;
		   }
		   $result_categories_yml = apply_filters('yfym_result_categories_yml_filter', $result_categories_yml, $terms, $this->get_feed_id()); /* c версии 3.2.0 */	
		   $result_xml .= $result_categories_yml;
		   unset($result_categories_yml);
		}
		$result_xml = apply_filters('yfym_append_categories_filter', $result_xml, $this->get_feed_id());
		$result_xml .= '</categories>'.PHP_EOL; 

		$yfym_pickup_options = yfym_optionGET('yfym_pickup_options', $this->get_feed_id(), 'set_arr');
		if ($yfym_pickup_options === 'on') {
			$pickup_cost = yfym_optionGET('yfym_pickup_cost', $this->get_feed_id(), 'set_arr');
			$pickup_days = yfym_optionGET('yfym_pickup_days', $this->get_feed_id(), 'set_arr');
			$pickup_order_before = yfym_optionGET('yfym_pickup_order_before', $this->get_feed_id(), 'set_arr');
			if ($pickup_order_before == '') {$pickup_order_before_yml = '';} else {$pickup_order_before_yml = ' order-before="'.$pickup_order_before.'"';} 
			$result_xml .= '<pickup-options>'.PHP_EOL;
			$result_xml .= '<option cost="'.$pickup_cost.'" days="'.$pickup_days.'"'.$pickup_order_before_yml.'/>'.PHP_EOL;
			$result_xml .= '</pickup-options>'.PHP_EOL;
		}

		$yfym_yml_rules = yfym_optionGET('yfym_yml_rules', $this->feed_id, 'set_arr');
		if ($yfym_yml_rules === 'sbermegamarket') {
			$tag_name = 'shipment-options';
		} else {
			$tag_name = 'delivery-options';
		}

		$yfym_delivery_options = yfym_optionGET('yfym_delivery_options', $this->get_feed_id(), 'set_arr');
		if ($yfym_delivery_options === 'on') {
		   $delivery_cost = yfym_optionGET('yfym_delivery_cost', $this->get_feed_id(), 'set_arr');
		   $delivery_days = yfym_optionGET('yfym_delivery_days', $this->get_feed_id(), 'set_arr');
		   $order_before = yfym_optionGET('yfym_order_before', $this->get_feed_id(), 'set_arr');
		   if ($order_before == '') {$order_before_yml = '';} else {$order_before_yml = ' order-before="'.$order_before.'"';} 
		   $result_xml .= '<'.$tag_name.'>'.PHP_EOL;
		   $result_xml .= '<option cost="'.$delivery_cost.'" days="'.$delivery_days.'"'.$order_before_yml.'/>'.PHP_EOL;
		   $yfym_delivery_options2 = yfym_optionGET('yfym_delivery_options2', $this->get_feed_id(), 'set_arr');
		   if ($yfym_delivery_options2 === 'on') {
			   $delivery_cost2 = yfym_optionGET('yfym_delivery_cost2', $this->get_feed_id(), 'set_arr');
			   $delivery_days2 = yfym_optionGET('yfym_delivery_days2', $this->get_feed_id(), 'set_arr');
			   $order_before2 = yfym_optionGET('yfym_order_before2', $this->get_feed_id(), 'set_arr');
			   if ($order_before2 == '') {$order_before_yml2 = '';} else {$order_before_yml2 = ' order-before="'.$order_before2.'"';} 
			   $result_xml .= '<option cost="'.$delivery_cost2.'" days="'.$delivery_days2.'"'.$order_before_yml2.'/>'.PHP_EOL;
		   }
		   $result_xml .= '</'.$tag_name.'>'.PHP_EOL;
		}	
				   
		// магазин 18+
		$adult = yfym_optionGET('yfym_adult', $this->get_feed_id(), 'set_arr');
		if ($adult === 'yes') {$result_xml .= '<adult>true</adult>'.PHP_EOL;}		
		/* end общие параметры */		
		do_action('yfym_before_offers', $this->get_feed_id());
			   
		/* индивидуальные параметры товара */
		$result_xml .= '<offers>'.PHP_EOL;	
		if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}
		do_action('yfym_before_offers', $this->get_feed_id());
		return $result_xml;
	}

	protected function get_ids_in_xml($file_content) {
		/* 
		* $file_content - содержимое файла (Обязательный параметр)
		* Возвращает массив в котором ключи - это id товаров в БД WordPress, попавшие в фид
		*/
		$res_arr = array();
		$file_content_string_arr = explode(PHP_EOL, $file_content);
		for ($i = 0; $i< count($file_content_string_arr)-1; $i++) {
			$r_arr = explode(';', $file_content_string_arr[$i]);
			$res_arr[$r_arr[0]] = '';
		}
		return $res_arr;
	}

	protected function get_feed_body($result_xml = '') {
		$yfym_file_ids_in_xml = urldecode(yfym_optionGET('yfym_file_ids_in_xml', $this->get_feed_id(), 'set_arr'));
		$file_content = file_get_contents($yfym_file_ids_in_xml);
		$ids_in_xml_arr = $this->get_ids_in_xml($file_content);

		$upload_dir = (object)wp_get_upload_dir();
		$name_dir = $upload_dir->basedir.'/yfym/feed'.$this->get_feed_id();

		foreach ($ids_in_xml_arr as $key => $value) {
			$product_id = (int)$key;
			$filename = $name_dir.'/'.$product_id.'.tmp';
			$result_xml .= file_get_contents($filename);
		}

		yfym_optionUPD('yfym_count_products_in_feed', count($ids_in_xml_arr), $this->get_feed_id(), 'yes', 'set_arr');
		// товаров попало в фид - count($ids_in_xml_arr);

		return $result_xml;
	}

	protected function get_feed_footer($result_xml = '') {
		$result_xml .= $this->get_feed_body($result_xml);

		$result_xml .= "</offers>". PHP_EOL; 
		$result_xml = apply_filters('yfym_after_offers_filter', $result_xml, $this->get_feed_id());
		$result_xml .= "</shop>". PHP_EOL ."</yml_catalog>";

		$unixtime = current_time('Y-m-d H:i'); // время в unix формате 			
		yfym_optionUPD('yfym_date_sborki_end', $unixtime, $this->get_feed_id(), 'yes', 'set_arr');

		return $result_xml;
	}

	protected function get_feed_id() {
		return $this->feed_id;
	}

	public function onlygluing() {
		$result_xml = $this->get_feed_header();
		/* создаем файл или перезаписываем старый удалив содержимое */
		$result = yfym_write_file($result_xml, 'w+', $this->get_feed_id());
		if ($result !== true) {
		   	new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; yfym_write_file вернула ошибку! $result ='.$result.'; Файл: functions.php; Строка: '.__LINE__);
		} 

		yfym_optionUPD('yfym_status_sborki', '-1', $this->get_feed_id()); 
		$whot_export = yfym_optionGET('yfym_whot_export', $this->get_feed_id(), 'set_arr');
		  
		$result_xml = '';
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
		  
		$args = apply_filters('yfym_query_arg_filter', $args, $this->get_feed_id());
		new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Быстрая сборка. $whot_export = '.$whot_export.'; Файл: class-generation-xml.php; Строка: '.__LINE__);

		new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; NOTICE: onlygluing до запуска WP_Query RAM '.round(memory_get_usage()/1024, 1) . ' Кб; Файл: functions.php; Строка: '.__LINE__); 
		$featured_query = new WP_Query($args);
		new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; NOTICE: onlygluing после запуска WP_Query RAM '.round(memory_get_usage()/1024, 1) . ' Кб; Файл: functions.php; Строка: '.__LINE__); 

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
			new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; NOTICE: onlygluing передала управление this->gluing; Файл: functions.php; Строка: '.__LINE__);
			$this->gluing($prod_id_arr);
		}

		// если постов нет, пишем концовку файла
		$result_xml = $this->get_feed_footer();
		$result = yfym_write_file($result_xml, 'a', $this->get_feed_id());
		new YFYM_Error_Log('FEED № '.$this->get_feed_id().'; Файл фида готов. Осталось только переименовать временный файл в основной; Файл: xml-for-google-merchant-center.php; Строка: '.__LINE__);
		yfym_rename_file($this->get_feed_id());

		$this->stop();
	} // end function onlygluing()
}
?>