<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Params for variable products
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
*/

trait YFYM_T_Variable_Get_Params {
	public function get_params($tag_name = 'params', $result_xml = '') {
		$product = $this->product;
		$offer = $this->offer;
		$params_arr = unserialize(yfym_optionGET('yfym_params_arr', $this->get_feed_id()));

		// Param в вариациях
		if (!empty($params_arr)) {
			$attributes = $product->get_attributes(); // получили все атрибуты товара		 
			foreach ($attributes as $param) {					
				if ($param->get_variation() == false) {
					// это обычный атрибут
					$param_val = $product->get_attribute(wc_attribute_taxonomy_name_by_id($param->get_id())); 
				} else { 
					$param_val = $offer->get_attribute(wc_attribute_taxonomy_name_by_id($param->get_id()));
				}
				// если этот параметр не нужно выгружать - пропускаем
				$variation_id_string = (string)$param->get_id(); // важно, т.к. в настройках id как строки
				if (!in_array($variation_id_string, $params_arr, true)) {continue;}
				$param_name = wc_attribute_label(wc_attribute_taxonomy_name_by_id($param->get_id()));
				// если пустое имя атрибута или значение - пропускаем
				if (empty($param_name) || empty($param_val)) {continue;}
				$result_xml .= '<param name="'.htmlspecialchars($param_name).'">'.ucfirst(yfym_replace_decode($param_val)).'</param>'.PHP_EOL;
				// $param_at_name .= ucfirst(urldecode($param_val)).' ';
			}	
		}

		$yfym_ebay_stock = yfym_optionGET('yfym_ebay_stock', $this->get_feed_id(), 'set_arr');
		if ($yfym_ebay_stock === 'on') {
			if ($offer->get_manage_stock() == true) { // включено управление запасом
				$stock_quantity = $offer->get_stock_quantity();
				$result_xml .= '<param name="stock">'.$stock_quantity.'</param>'.PHP_EOL; 
			} else {
				if ($product->get_manage_stock() == true) { // включено управление запасом
					$stock_quantity = $product->get_stock_quantity();
					$result_xml .= '<param name="stock">'.$stock_quantity.'</param>'.PHP_EOL;
				}
			}
		}
	
		return $result_xml;	
	}
}
?>