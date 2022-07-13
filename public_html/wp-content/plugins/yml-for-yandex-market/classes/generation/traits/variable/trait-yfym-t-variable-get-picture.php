<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Picture for variable products
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
*							add_skip_reason
*				functions:	yfym_optionGET 
*							yfym_replace_domain
*							get_from_url 
*/

trait YFYM_T_Variable_Get_Picture {
	public function get_picture($tag_name = 'picture', $result_xml = '') {
		$product = $this->product;
		$offer = $this->offer;
		$picture_yml = '';

		$thumb_yml = get_the_post_thumbnail_url($offer->get_id(), 'full');
		if (empty($thumb_yml)) {			
			// убираем default.png из фида
			$no_default_png_products = yfym_optionGET('yfym_no_default_png_products', $this->get_feed_id(), 'set_arr');
			if (($no_default_png_products === 'on') && (!has_post_thumbnail($product->get_id()))) {$picture_yml = '';} else {
				$thumb_id = get_post_thumbnail_id($product->get_id());
				$thumb_url = wp_get_attachment_image_src($thumb_id,'full', true);
				$tag_value = $thumb_url[0]; /* урл оригинал миниатюры товара */
				$tag_value = get_from_url($tag_value);
				$picture_yml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
			}
		} else {
			$tag_value = get_from_url($thumb_yml);
			$picture_yml = new YFYM_Get_Paired_Tag($tag_name, $tag_value);
		}
		$picture_yml = apply_filters('yfym_pic_variable_offer_filter', $picture_yml, $product, $this->get_feed_id(), $offer);
			
		// пропускаем вариации без картинок
		$yfym_skip_products_without_pic = yfym_optionGET('yfym_skip_products_without_pic', $this->get_feed_id(), 'set_arr'); 
		if (($yfym_skip_products_without_pic === 'on') && ($picture_yml == '')) {				
			$this->add_skip_reason(array('offer_id' => $offer->get_id(), 'reason' => __('Product has no images', 'yfym'), 'post_id' => $offer->get_id(), 'file' => 'trait-yfym-t-variable-get-picture.php', 'line' => __LINE__)); return '';
		}

		$result_xml = $picture_yml;

		$result_xml = yfym_replace_domain($result_xml, $this->get_feed_id());
		$result_xml = apply_filters('y4ym_f_variable_tag_picture', $result_xml, array('product' => $product, 'offer' => $offer), $this->get_feed_id());
		return $result_xml;
	}
}
?>