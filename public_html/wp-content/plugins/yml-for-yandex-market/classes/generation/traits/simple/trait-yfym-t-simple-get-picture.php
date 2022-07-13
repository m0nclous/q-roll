<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Picture for simple products
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
*							yfym_replace_domain
*							get_from_url
*/

trait YFYM_T_Simple_Get_Picture {
	public function get_picture($tag_name = 'picture', $result_xml = '') {
		$product = $this->product;
		$picture_yml = '';

		// убираем default.png из фида
		$no_default_png_products = yfym_optionGET('yfym_no_default_png_products', $this->get_feed_id(), 'set_arr');
		if (($no_default_png_products === 'on') && (!has_post_thumbnail($product->get_id()))) {$picture_yml = '';} else {
			$thumb_id = get_post_thumbnail_id($product->get_id());
			$thumb_url = wp_get_attachment_image_src($thumb_id, 'full', true);	
			$tag_value = $thumb_url[0]; /* урл оригинал миниатюры товара */
			$tag_value = get_from_url($tag_value);
			$picture_yml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}
		$picture_yml = apply_filters('yfym_pic_simple_offer_filter', $picture_yml, $product, $this->get_feed_id());

		$result_xml = $picture_yml;

		$result_xml = yfym_replace_domain($result_xml, $this->get_feed_id());
		$result_xml = apply_filters('y4ym_f_simple_tag_picture', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}
}
?>