<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits for variable products
*
* @author		Maxim Glazunov
* @link			https://icopydoc.ru/
* @since		1.0.0
*
* @return 		$result_xml (string)
*
* @depends		class:		YFYM_Get_Paired_Tag
*				methods: 	get_product
*							get_offer
*							get_feed_id
*				functions:	yfym_optionGET
*				variable:	feed_category_id (set it)
*/
trait YFYM_T_Common_Skips {
	public function get_skips() {
		$product = $this->get_product();
		$skip_flag = false;
	
		if ($product == null) {
			$this->add_skip_reason(array('reason' => __('There is no product with this ID', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return '';
		}

		if ($product->is_type('grouped')) {
			$this->add_skip_reason(array('reason' => __('Product is grouped', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return '';
		}

		if ($product->is_type('external')) {
			$this->add_skip_reason(array('reason' => __('Product is External/Affiliate product', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return '';
		}
		 
		// что выгружать
		$yfym_whot_export = yfym_optionGET('yfym_whot_export', $this->get_feed_id(), 'set_arr');
		if ($product->is_type('variable')) {	
			if ($yfym_whot_export === 'simple') {
				$this->add_skip_reason(array('reason' => __('Product is simple', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return ''; 
			}
		}
		if ($product->is_type('simple')) {	
			if ($yfym_whot_export === 'variable') {
				$this->add_skip_reason(array('reason' => __('Product is variable', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return '';
			}
		}
		
//		$special_data_for_flag = '';
//		$special_data_for_flag = apply_filters('yfym_special_data_for_flag_filter', $special_data_for_flag, $product, $this->get_feed_id()); 
		
		if (get_post_meta($product->get_id(), 'yfymp_removefromyml', true) === 'on')  {
			$this->add_skip_reason(array('reason' => __('The "Remove product from feed" condition worked', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return '';
		}

// на удаление в след версиях
		$skip_flag = apply_filters('yfym_skip_flag', $skip_flag, $product->get_id(), $product, $this->get_feed_id());
		if ($skip_flag === true) {
			$this->add_skip_reason(array('reason' => __('Flag', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return '';
		}
// на удаление в след версиях
		/* С версии 3.7.13 */
		$skip_flag = apply_filters('y4ym_f_skip_flag', $skip_flag, array('product' => $product), $this->get_feed_id());
		if ($skip_flag !== false) {
			$this->add_skip_reason(array('reason' => $skip_flag, 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return '';
		}

		// пропуск товаров, которых нет в наличии
		$yfym_skip_missing_products = yfym_optionGET('yfym_skip_missing_products', $this->get_feed_id(), 'set_arr');
		if ($yfym_skip_missing_products == 'on') {
			if ($product->is_in_stock() == false) { 
				$this->add_skip_reason(array('reason' => __('Skip missing products', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return '';
			}
		}

		// пропускаем товары на предзаказ
		$skip_backorders_products = yfym_optionGET('yfym_skip_backorders_products', $this->get_feed_id(), 'set_arr');
		if ($skip_backorders_products == 'on') {
			if ($product->get_manage_stock() == true) { // включено управление запасом  
				if (($product->get_stock_quantity() < 1) && ($product->get_backorders() !== 'no')) { 
					$this->add_skip_reason(array('reason' => __('Skip backorders products', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return '';
				}
			} else {
				if ($product->get_stock_status() !== 'instock') { 
					$this->add_skip_reason(array('reason' => __('Skip backorders products', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return '';
				}
			}
		}

		if ($product->is_type('variable')) {	
			$offer = $this->offer;

			// пропуск вариаций, которых нет в наличии
			$yfym_skip_missing_products = yfym_optionGET('yfym_skip_missing_products', $this->get_feed_id(), 'set_arr');
			if ($yfym_skip_missing_products == 'on') {
				if ($offer->is_in_stock() == false) { 
					$this->add_skip_reason(array('offer_id' => $offer->get_id(), 'reason' => __('Skip missing products', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'traits-yfym-variable.php', 'line' => __LINE__)); return '';
				}
			}
					
			// пропускаем вариации на предзаказ
			$skip_backorders_products = yfym_optionGET('yfym_skip_backorders_products', $this->get_feed_id(), 'set_arr');
			if ($skip_backorders_products == 'on') {
				if ($offer->get_manage_stock() == true) { // включено управление запасом			  
					if (($offer->get_stock_quantity() < 1) && ($offer->get_backorders() !== 'no')) {
						$this->add_skip_reason(array('offer_id' => $offer->get_id(), 'reason' => __('Skip backorders products', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'traits-yfym-variable.php', 'line' => __LINE__)); return '';
					}
				}
			}
// на удаление в след версиях
			$skip_flag = apply_filters('yfym_skip_flag_variable', $skip_flag, $product->get_id(), $product, $offer, $this->get_feed_id()); 
			if ($skip_flag === true) {
				$this->add_skip_reason(array('offer_id' => $offer->get_id(), 'reason' => __('Flag', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'traits-yfym-variable.php', 'line' => __LINE__)); return '';
			}
			if ($skip_flag === 'continue') {
				$this->add_skip_reason(array('offer_id' => $offer->get_id(), 'reason' => __('Flag', 'yfym'), 'post_id' => $product->get_id(), 'file' => 'traits-yfym-variable.php', 'line' => __LINE__)); 
				return '';	// return 'continue';		
			} 
// на удаление в след версиях
			/* С версии 3.7.13 */
			$skip_flag = apply_filters('y4ym_f_skip_flag_variable', $skip_flag, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
			if ($skip_flag !== false) {
				$this->add_skip_reason(array('offer_id' => $offer->get_id(), 'reason' => $skip_flag, 'post_id' => $product->get_id(), 'file' => 'trait-yfym-t-common-skips.php', 'line' => __LINE__)); return '';
			}
		}
	}

}
?>