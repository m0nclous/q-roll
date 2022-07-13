<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Condition for variable products
*
* @author		Maxim Glazunov
* @link			https://icopydoc.ru/
* @since		1.0.0
*
* @return 		$result_xml (string)
*
* @depends		class:		YFYM_Get_Open_Tag
*							YFYM_Get_Paired_Tag
*							YFYM_Get_Closed_Tag
*				methods: 	get_product
*							get_offer
*							get_feed_id
*				functions:	 
*/

trait YFYM_T_Variable_Get_Condition {
	public function get_condition($tag_name = 'condition', $result_xml = '') {
		$product = $this->get_product();
		$offer = $this->get_offer();

		if ((get_post_meta($product->get_id(), 'yfym_condition', true) !== '') && (get_post_meta($product->get_id(), 'yfym_condition', true) !== 'off') && (get_post_meta($product->get_id(), 'yfym_reason', true) !== '')) {
			$yfym_condition = get_post_meta($product->get_id(), 'yfym_condition', true);
			$yfym_reason = get_post_meta($product->get_id(), 'yfym_reason', true);	
			$result_xml = new YFYM_Get_Open_Tag($tag_name, array('type' => $yfym_condition));
			$result_xml .= new YFYM_Get_Paired_Tag('reason', $yfym_reason);
			$result_xml .= new YFYM_Get_Closed_Tag($tag_name);;	 
		}

		$result_xml = apply_filters('y4ym_f_variable_tag_condition', $result_xml, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		return $result_xml;
	}
}
?>