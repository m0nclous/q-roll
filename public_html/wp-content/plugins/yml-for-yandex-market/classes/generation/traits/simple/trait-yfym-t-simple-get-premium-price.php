<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Premium_Price for simple products
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

trait YFYM_T_Simple_Get_Premium_Price {
	public function get_premium_price($tag_name = 'premium_price', $result_xml = '') {
		$product = $this->get_product();

		if (get_post_meta($product->get_id(), '_yfym_premium_price', true) !== '')  {
			$premium_price = get_post_meta($product->get_id(), '_yfym_premium_price', true);
			$result_xml .= new YFYM_Get_Paired_Tag($tag_name, $premium_price);
		}

		$result_xml = apply_filters('y4ym_f_simple_tag_premium_price', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}
}
?>