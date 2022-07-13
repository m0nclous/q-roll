<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Pickup for simple products
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

trait YFYM_T_Simple_Get_Pickup {
	public function get_pickup($tag_name = 'pickup', $result_xml = '') {
		$product = $this->product;

		if (get_post_meta($product->get_id(), 'yfym_individual_pickup', true) !== '') {	
			$pickup = get_post_meta($product->get_id(), 'yfym_individual_pickup', true);
			if ($pickup === 'off') {$pickup = yfym_optionGET('yfym_pickup', $this->get_feed_id(), 'set_arr');}
		} else {
			$pickup = yfym_optionGET('yfym_pickup', $this->get_feed_id(), 'set_arr');
		}
		if ($pickup === false || $pickup == '') {
//			yfym_error_log('FEED № '.$this->get_feed_id().'; WARNING: Товар с postId = '.$product->get_id().' вернул пустой $pickup; Файл: trait-yfym-t-simple-get-pickup.php; Строка: '.__LINE__, 0);
			$result_yml_pickup = '';
		} else {
			$result_xml = new YFYM_Get_Paired_Tag($tag_name, $pickup);
		}

		$result_xml = apply_filters('y4ym_f_simple_tag_pickup', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}
}
?>