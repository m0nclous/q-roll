<?php // https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html https://wp-kama.ru/function/wp_list_table
class YFYM_Settings_Feed_WP_List_Table extends WP_List_Table {
	private $feed_id;

	function __construct($feed_id) {
		$this->feed_id = $feed_id;

		global $status, $page;
		parent::__construct( array(
			'plural'	=> '', 		// По умолчанию: '' ($this->screen->base); Название для множественного числа, используется во всяких заголовках, например в css классах, в заметках, например 'posts', тогда 'posts' будет добавлен в класс table.
			'singular'	=> '', 		// По умолчанию: ''; Название для единственного числа, например 'post'. 
			'ajax'		=> false,	// По умолчанию: false; Должна ли поддерживать таблица AJAX. Если true, класс будет вызывать метод _js_vars() в подвале, чтобы передать нужные переменные любому скрипту обрабатывающему AJAX события.
			'screen'	=> null, 	// По умолчанию: null; Строка содержащая название хука, нужного для определения текущей страницы. Если null, то будет установлен текущий экран.
		) );
		add_action('admin_footer', array($this, 'admin_header')); // меняем ширину колонок	
	}

	/*	Сейчас у таблицы стандартные стили WordPress. Чтобы это исправить, вам нужно адаптировать классы CSS, которые были 
	*	автоматически применены к каждому столбцу. Название класса состоит из строки «column-» и ключевого имени 
	* 	массива $columns, например «column-isbn» или «column-author».
	*	В качестве примера мы переопределим ширину столбцов (для простоты, стили прописаны непосредственно в HTML разделе head)
	*/
	function admin_header() {
/*		echo '<style type="text/css">'; 
		echo '#yfym_google_attribute, .column-yfym_google_attribute {width: 7%;}';
		echo '</style>';*/
	}

	/*	Метод get_columns() необходим для маркировки столбцов внизу и вверху таблицы. 
	*	Ключи в массиве должны быть теми же, что и в массиве данных, 
	*	иначе соответствующие столбцы не будут отображены.
	*/
	function get_columns() {
		$columns = array(
//			'cb'							=> '<input type="checkbox" />', // флажок сортировки. см get_bulk_actions и column_cb
			'yfym_google_attribute'			=> __('Attribute', 'yfym'),
			'yfym_attribute_description'	=> __('Attribute description', 'yfym'),
			'yfym_value'					=> __('Value', 'yfym'),
			'yfym_default_value'			=> __('Default value', 'yfym'),
		);
		return $columns;
	}
	/*	
	*	Метод вытаскивает из БД данные, которые будут лежать в таблице
	*	$this->table_data();
	*/
	private function table_data() {
		$result_arr = array();

		$feed_id = $this->get_feed_id();

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Quantity of products', 'yfym').' (СДЭК)', 'amount'),
			'yfym_attribute_description' 	=> __('To make it work you must enable "Manage stock" and indicate "Stock quantity"', 'yfym'),
			'yfym_value' 					=> $this->get_select_html_v2('yfym_amount', $feed_id, array(
													'disabled' => __('Disabled', 'yfym'),
													'enabled' => __('Enabled', 'yfym'),
												)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", 'Shop sku', 'shop-sku'),
			'yfym_attribute_description' 	=> 'Shop sku',
			'yfym_value' 					=> $this->get_select_html('yfym_shop_sku', $feed_id, array('products_id' => true, 'sku' => true)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Quantity of products', 'yfym'), 'count'),
			'yfym_attribute_description' 	=> __('To make it work you must enable "Manage stock" and indicate "Stock quantity"', 'yfym'),
			'yfym_value' 					=> $this->get_select_html_v2('yfym_count', $feed_id, array(
													'disabled' => __('Disabled', 'yfym'),
													'enabled' => __('Enabled', 'yfym'),
												)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Automatically remove products from sale', 'yfym'), 'disabled'),
			'yfym_attribute_description' 	=> __('Automatically remove products from sale', 'yfym'),
			'yfym_value' 					=> $this->get_select_html_v2('yfym_auto_disabled', $feed_id, array(
													'disabled' => __('Disabled', 'yfym'),
													'yes' => __('Yes', 'yfym'),
												)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Add market-sku to feed', 'yfym'), 'market-sku'),
			'yfym_attribute_description' 	=> __('Optional when creating a catalog. A must for price recommendations', 'yfym'),
			'yfym_value' 					=> $this->get_select_html_v2('yfym_market_sku_status', $feed_id, array(
												'disabled' => __('Disabled', 'yfym'),
												'enabled' => __('Enabled', 'yfym'),
											)),
			'yfym_default_value'			=> '',
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Manufacturer company', 'yfym'), 'manufacturer'),
			'yfym_attribute_description' 	=> __('Manufacturer company', 'yfym'),
			'yfym_value' 					=> $this->get_select_html('yfym_manufacturer', $feed_id, array()),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Vendor', 'yfym'), 'vendor'),
			'yfym_attribute_description' 	=> __('Vendor', 'yfym'),
			'yfym_value' 					=> $this->get_select_html('yfym_vendor', $feed_id, array('post_meta' => true, 'default_value' => true, 'brands' => true)),
			'yfym_default_value'			=> $this->get_input_html('yfym_vendor_post_meta', $feed_id, 'type3')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Country of origin', 'yfym'), 'country_of_origin'),
			'yfym_attribute_description' 	=> __('This element indicates the country where the product was manufactured', 'yfym'),
			'yfym_value' 					=> $this->get_select_html('yfym_country_of_origin', $feed_id, array()),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>%2\$s", __('Source ID of the product', 'yfym'), ''),
			'yfym_attribute_description' 	=> '',
			'yfym_value' 					=> $this->get_select_html('yfym_source_id', $feed_id, array('sku' => true, 'post_meta' => true, /*'brands' => true, */ 'germanized' => true)),
			'yfym_default_value'			=> $this->get_input_html('yfym_source_id_post_meta', $feed_id, 'type1')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Pickup', 'yfym'), 'pickup'),
			'yfym_attribute_description' 	=> __('Option to get order from pickup point', 'yfym'),
			'yfym_value' 					=> $this->get_select_html_v2('yfym_pickup', $feed_id, array(
													'true' => __('True', 'yfym'),
													'false' => __('False', 'yfym'),
												)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Price from', 'yfym'), '...from="true"...'),
			'yfym_attribute_description' 	=> __('Apply the setting Price from', 'yfym').' <strong>from="true"</strong> '. __('attribute of', 'yfym').' <strong>price</strong><br /><strong>'. __('Example', 'yfym').'>:</strong><br /><code>&lt;price from=&quot;true&quot;&gt;2000&lt;/price&gt;</code>',
			'yfym_value' 					=> $this->get_select_html_v2('yfym_price_from', $feed_id, array(
													'yes' => __('Yes', 'yfym'),
													'no' => __('No', 'yfym'),
												)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Old price', 'yfym'), 'oldprice'),
			'yfym_attribute_description' 	=> __('In oldprice indicates the old price of the goods, which must necessarily be higher than the new price (price)', 'yfym'),
			'yfym_value' 					=> $this->get_select_html_v2('yfym_oldprice', $feed_id, array(
													'yes' => __('Yes', 'yfym'),
													'no' => __('No', 'yfym'),
												)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Delivery', 'yfym'), 'delivery'),
			'yfym_attribute_description' 	=> __('The delivery item must be set to false if the item is prohibited to sell remotely (jewelry, medicines)', 'yfym'),
			'yfym_value' 					=> $this->get_select_html_v2('yfym_delivery', $feed_id, array(
													'true' => __('True', 'yfym'),
													'false' => __('False', 'yfym'),
												)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('VAT rate', 'yfym'), 'vat'),
			'yfym_attribute_description' 	=> __('This element is used when creating an YML feed for Yandex.Delivery', 'yfym'),
			'yfym_value' 					=> $this->get_select_html_v2('yfym_vat', $feed_id, array(
													'disabled' => __('Disabled', 'yfym'),
													'enabled' => __('Enable. No default value', 'yfym'),
													'NO_VAT' => __('No VAT', 'yfym').' (NO_VAT)',
													'VAT_0' => '0% (VAT_0)',
													'VAT_10' => '10% (VAT_10)',
													'VAT_10_110' => '10/110 (VAT_10_110)',
													'VAT_18' => '18% (VAT_18)',
													'VAT_18_118' => '18/118 (VAT_18_118)',
													'VAT_20' => '20% (VAT_20)',
													'VAT_20_120' => '20/120 (VAT_20_120)',
												)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Barcode', 'yfym'), 'barcode'),
			'yfym_attribute_description' 	=> '',
			'yfym_value' 					=> $this->get_select_html('yfym_barcode', $feed_id, array('sku' => true, 'post_meta' => true, /*'brands' => true, */ 'germanized' => true, 'ean-for-woocommerce' => true)),
			'yfym_default_value'			=> $this->get_input_html('yfym_barcode_post_meta', $feed_id, 'type4').'<br/>'.$this->get_input_html('yfym_barcode_post_meta_var', $feed_id, 'type5')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Vendor Code', 'yfym'), 'vendorCode'),
			'yfym_attribute_description' 	=> __('Vendor Code', 'yfym'),
			'yfym_value' 					=> $this->get_select_html('yfym_vendorcode', $feed_id, array('sku' => true)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);
		
		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", '«Честный ЗНАК»', 'cargo-types'),
			'yfym_attribute_description' 	=> '',
			'yfym_value' 					=> $this->get_select_html_v2('yfym_cargo_types', $feed_id, array(
													'disabled' => __('Disabled', 'yfym'),
													'enabled' => __('Enabled', 'yfym')
												)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Shelf life / service life', 'yfym'), 'expiry'),
			'yfym_attribute_description' 	=> __('Shelf life / service life. expiry date / service life', 'yfym'),
			'yfym_value' 					=> $this->get_select_html('yfym_expiry', $feed_id, array()),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Shelf life', 'yfym'), 'period-of-validity-days'),
			'yfym_attribute_description' 	=> __('Shelf life', 'yfym'),
			'yfym_value' 					=> $this->get_select_html('yfym_period_of_validity_days', $feed_id, array()),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Mark downloadable products', 'yfym'), 'downloadable'),
			'yfym_attribute_description' 	=> '',
			'yfym_value' 					=> $this->get_select_html_v2('yfym_downloadable', $feed_id, array(
													'off' => __('Disabled', 'yfym'),
													'on' => __('On', 'yfym'),
												)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Age', 'yfym'), 'age'),
			'yfym_attribute_description' 	=> __('Age', 'yfym'),
			'yfym_value' 					=> $this->get_select_html('yfym_age', $feed_id, array()),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Model', 'yfym'), 'model'),
			'yfym_attribute_description' 	=> __('Model', 'yfym'),
			'yfym_value' 					=> $this->get_select_html('yfym_model', $feed_id, array('sku' => true)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Manufacturer warrant', 'yfym'), 'manufacturer_warranty'),
			'yfym_attribute_description' 	=> __("This element is used for products that have an official manufacturer's warranty", 'yfym').'.<ul><li>false — '. __('Product does not have an official warranty', 'yfym').'</li><li>true — '. __('Product has an official warranty', 'yfym').'</li></ul>',
			'yfym_value' 					=> $this->get_select_html('yfym_manufacturer_warranty', $feed_id, array('allfalse' => true, 'alltrue' => true)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Sales notes', 'yfym'), 'sales_notes'),
			'yfym_attribute_description' 	=> __('The text may be up to 50 characters in length. Also in the item is forbidden to specify the terms of delivery and price reduction (discount on merchandise)', 'yfym'),
			'yfym_value' 					=> $this->get_select_html('yfym_sales_notes_cat', $feed_id, array('default_value' => true)),
			'yfym_default_value'			=> $this->get_input_html('yfym_sales_notes', $feed_id, 'type2')
		);

		$result_arr[] = array(
			'yfym_google_attribute' 		=> sprintf("<span class='yfym_bold'>%1\$s</span><br/>[%2\$s]", __('Store', 'yfym'), 'store'),
			'yfym_attribute_description' 	=> 	'<ul><li>'. __('true', 'yfym').' — '. __('The product can be purchased in retail stores', 'yfym').'</li>
			<li>'. __('false', 'yfym').' — '. __('the product cannot be purchased in retail stores', 'yfym').'</li></ul>',
			'yfym_value' 					=> $this->get_select_html_v2('yfym_store', $feed_id, array(
													'true' => __('True', 'yfym'),
													'false' => __('False', 'yfym'),
												)),
			'yfym_default_value'			=> __('There are no default settings', 'yfym')
		);

		return $result_arr;
	}

	private function get_input_html($opt_name, $feed_id = '1', $type_placeholder = 'type1') {
		$opt_value = yfym_optionGET($opt_name, $feed_id, 'set_arr');

		switch ($type_placeholder) {
			case 'type1':
				$placeholder = __('Name post_meta', 'yfym');
				break;
			case 'type2':
				$placeholder = __('Default value', 'yfym');
				break;
			case 'type3':
				$placeholder = __('Value', 'yfym') .' / '. __('Name post_meta', 'yfym');
				break;
			case 'type4':
				$placeholder = __('Name post_meta', 'yfym'). ' '. __('for simple products', 'yfym');
				break;
			case 'type5':
				$placeholder = __('Name post_meta', 'yfym'). ' '. __('for variable products', 'yfym');
				break;
			default:
				$placeholder = __('Name post_meta', 'yfym');
		}

		return '<input type="text" maxlength="50" name="'.$opt_name.'" id="'.$opt_name.'" value="'.$opt_value.'" placeholder="'.$placeholder.'" />';
	}
	
	private function get_select_html_v2($opt_name, $feed_id = '1', $otions_arr = array()) {
		$opt_value = yfym_optionGET($opt_name, $feed_id, 'set_arr');

		$res = '<select name="'.$opt_name.'" id="'.$opt_name.'">';
		foreach ($otions_arr as $key => $value) {
			$res .= '<option value="'.$key.'" '.selected($opt_value, $key, false).'>'.$value.'</option>';
		}
		$res .= '</select>';
		return $res;
	}

	private function get_select_desc_html($opt_name, $feed_id = '1', $otions_arr = array()) {
		$opt_value = yfym_optionGET($opt_name, $feed_id, 'set_arr');

		$res = '<select name="'.$opt_name.'" id="'.$opt_name.'">
					<option value="excerpt" '.selected($opt_value, 'excerpt', false).'>'. __('Only Excerpt description', 'yfym').'</option>
					<option value="full" '.selected($opt_value, 'full', false).'>'. __('Only Full description', 'yfym').'</option>
					<option value="excerptfull" '.selected($opt_value, 'excerptfull', false).'>'. __('Excerpt or Full description', 'yfym').'</option>
					<option value="fullexcerpt" '.selected($opt_value, 'fullexcerpt', false).'>'. __('Full or Excerpt description', 'yfym').'</option>
					<option value="excerptplusfull" '.selected($opt_value, 'excerptplusfull', false).'>'. __('Excerpt plus Full description', 'yfym').'</option>
					<option value="fullplusexcerpt" '.selected($opt_value, 'fullplusexcerpt', false).'>'. __('Full plus Excerpt description', 'yfym').'</option>';
					$res = apply_filters('yfym_append_select_yfym_desc_filter', $res, $opt_value, $feed_id); 
		$res .= '</select>';
		return $res;
	}

	private function get_select_html($opt_name, $feed_id = '1', $otions_arr = array()) {
		$opt_value = yfym_optionGET($opt_name, $feed_id, 'set_arr');

		$res = '<select name="'.$opt_name.'" id="'.$opt_name.'">
					<option value="disabled" '.selected($opt_value, 'disabled', false).'>'. __('Disabled', 'yfym').'</option>';

					if (isset($otions_arr['products_id'])) {
						$res .= '<option value="products_id" '.selected($opt_value, 'products_id', false).'>'. __('Add from products ID', 'yfym').'</option>';
					}			

					if (isset($otions_arr['yes'])) {
						$res .= '<option value="yes" '.selected($opt_value, 'yes', false).'>'. __('Yes', 'yfym').'</option>';
					}

					if (isset($otions_arr['no'])) {
						$res .= '<option value="no" '.selected($opt_value, 'no', false).'>'. __('No', 'yfym').'</option>';
					}

					if (isset($otions_arr['true'])) {
						$res .= '<option value="true" '.selected($opt_value, 'true', false).'>'. __('True', 'yfym').'</option>';
					}

					if (isset($otions_arr['false'])) {
						$res .= '<option value="false" '.selected($opt_value, 'false', false).'>'. __('False', 'yfym').'</option>';
					}
	
					if (isset($otions_arr['alltrue'])) {
						$res .= '<option value="alltrue" '.selected($opt_value, 'alltrue', false).'>'. __('Add to all', 'yfym').' true</option>';
					}

					if (isset($otions_arr['allfalse'])) {
						$res .= '<option value="allfalse" '.selected($opt_value, 'allfalse', false).'>'. __('Add to all', 'yfym').' false</option>';
					}

					if (isset($otions_arr['sku'])) {
						$res .= '<option value="sku" '. selected($opt_value, 'sku', false).'>'. __('Substitute from SKU', 'yfym').'</option>';
					}

					if (isset($otions_arr['post_meta'])) {
						$res .= '<option value="post_meta" '. selected($opt_value, 'post_meta', false).'>'. __('Substitute from post meta', 'yfym').'</option>';
					}

					if (isset($otions_arr['default_value'])) {
						$res .= '<option value="default_value" '.selected($opt_value, 'default_value', false).'>'. __('Default value from field', 'yfym').' "'.__('Default value', 'yfym').'"</option>';
					}

					if (class_exists('WooCommerce_Germanized')) {
						if (isset($otions_arr['germanized'])) {
							$res .= '<option value="germanized" '. selected($opt_value, 'germanized', false).'>'. __('Substitute from', 'yfym'). 'WooCommerce Germanized</option>';
						}
					}
					
					if (class_exists('Alg_WC_EAN')) {
						if (isset($otions_arr['ean-for-woocommerce'])) {
							$res .= '<option value="ean-for-woocommerce" '. selected($opt_value, 'ean-for-woocommerce', false).'>'. __('Substitute from', 'yfym'). 'EAN for WooCommerce</option>';
						}
					}					
					
					if (isset($otions_arr['brands'])) {
						if (is_plugin_active('perfect-woocommerce-brands/perfect-woocommerce-brands.php') || is_plugin_active('perfect-woocommerce-brands/main.php') || class_exists('Perfect_Woocommerce_Brands')) {
							$res .= '<option value="sfpwb" '. selected($opt_value, 'sfpwb', false).'>'. __('Substitute from', 'yfym'). 'Perfect Woocommerce Brands</option>';
						}
						if (is_plugin_active('premmerce-woocommerce-brands/premmerce-brands.php')) {
							$res .= '<option value="premmercebrandsplugin" '. selected($opt_value, 'premmercebrandsplugin', false).'>'. __('Substitute from', 'yfym'). 'Premmerce Brands for WooCommerce</option>';
						}
						if (is_plugin_active('woocommerce-brands/woocommerce-brands.php')) {
							$res .= '<option value="woocommerce_brands" '. selected($opt_value, 'woocommerce_brands', false).'>'. __('Substitute from', 'yfym'). 'WooCommerce Brands</option>';
						}
						if (class_exists('woo_brands')) {
							$res .= '<option value="woo_brands" '. selected($opt_value, 'woo_brands', false).'>'. __('Substitute from', 'yfym'). 'Woocomerce Brands Pro</option>';
						}	
					}

					foreach (yfym_get_attributes() as $attribute) {
						$res .= '<option value="'.$attribute['id'].'" '.selected($opt_value, $attribute['id'], false).'>'.$attribute['name'].'</option>';
					}
		$res .= '</select>';
		return $res;
	}
	/*
	*	prepare_items определяет два массива, управляющие работой таблицы:
	*	$hidden определяет скрытые столбцы https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html#screen-options
	*	$sortable определяет, может ли таблица быть отсортирована по этому столбцу.
	*
	*/
	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns(); // вызов сортировки
		$this->_column_headers = array($columns, $hidden, $sortable);
		// блок пагинации пропущен
		$this->items = $this->table_data();
	}
	/*
	* 	Данные таблицы.
	*	Наконец, метод назначает данные из примера на переменную представления данных класса — items.
	*	Прежде чем отобразить каждый столбец, WordPress ищет методы типа column_{key_name}, например, function column_yfym_attribute_description. 
	*	Такой метод должен быть указан для каждого столбца. Но чтобы не создавать эти методы для всех столбцов в отдельности, 
	*	можно использовать column_default. Эта функция обработает все столбцы, для которых не определён специальный метод:
	*/ 
	function column_default($item, $column_name) {
		switch($column_name) {
			case 'yfym_google_attribute':
			case 'yfym_attribute_description':
			case 'yfym_value':
			case 'yfym_default_value':
				return $item[$column_name];
			default:
				return print_r($item, true) ; // Мы отображаем целый массив во избежание проблем
		}
	}
	// Флажки для строк должны быть определены отдельно. Как упоминалось выше, есть метод column_{column} для отображения столбца. cb-столбец – особый случай:
/*	function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="checkbox_xml_file[]" value="%s" />', $item['yfym_google_attribute']
		);
	}*/

	private function get_feed_id() {
		return $this->feed_id;
	}
}
?>