<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Name for simple products
*
* @author		Maxim Glazunov
* @link			https://icopydoc.ru/
* @since		1.0.0
*
* @return 		$result_xml (string)
*
* @depends		class:		
*				methods: 	get_product
*							get_feed_id
*				functions:	yfym_optionGET
*/

trait YFYM_T_Simple_Get_Name {
	public function get_name($tag_name = 'name', $result_xml = '') {
		$product = $this->product;

		$result_yml_name = htmlspecialchars($product->get_title(), ENT_NOQUOTES); // название товара
		$result_yml_name = apply_filters('y4ym_f_simple_tag_value_name', $result_yml_name, array('product' => $product), $this->get_feed_id());
		$result_xml = apply_filters('yfym_change_name', $result_yml_name, $product->get_id(), $product, $this->get_feed_id());

		$result_xml = "<name>".htmlspecialchars($result_xml, ENT_NOQUOTES)."</name>".PHP_EOL;

		$result_xml = apply_filters('y4ym_f_simple_tag_name', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;	
	}
}
?>