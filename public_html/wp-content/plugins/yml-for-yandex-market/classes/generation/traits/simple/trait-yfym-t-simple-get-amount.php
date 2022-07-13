<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Amount for simple products
*
* @author		Maxim Glazunov
* @link			https://icopydoc.ru/
* @since		1.0.0
*
* @return 		$result_xml (string)
*
* @depends		class:		YFYM_Get_Paired_Tag
*				methods: 	get_product
*							get_feed_id
*				functions:	yfym_optionGET
*/

trait YFYM_T_Simple_Get_Amount {
	public function get_amount($tag_name = 'amount', $result_xml = '') {
		$product = $this->get_product();
		$tag_value = '';

		$yfym_amount = yfym_optionGET('yfym_amount', $this->get_feed_id(), 'set_arr');
		if ($yfym_amount === 'enabled') {
			if ($product->get_manage_stock() == true) { // включено управление запасом
				$stock_quantity = $product->get_stock_quantity();
				if ($stock_quantity > -1) {$tag_value = $stock_quantity;}
			} 		
		}

		$tag_value = apply_filters('y4ym_f_simple_tag_value_amount', $tag_value, array('product' => $product), $this->get_feed_id());
		if ($tag_value !== '') {
			$tag_name = apply_filters('y4ym_f_simple_tag_name_amount', $tag_name, array('product' => $product), $this->get_feed_id());
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}

		$result_xml = apply_filters('y4ym_f_simple_tag_amount', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}
}
?>