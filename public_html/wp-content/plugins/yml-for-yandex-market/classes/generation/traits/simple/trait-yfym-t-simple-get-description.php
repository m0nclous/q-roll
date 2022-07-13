<?php if (!defined('ABSPATH')) {exit;}
/**
* Traits Description for simple products
*
* @author		Maxim Glazunov
* @link			https://icopydoc.ru/
* @since		1.0.0
*
* @return 		$result_xml (string)
*
* @depends		class:
*				methods: 	get_product
*							get_offer
*							get_feed_id
*				functions:	yfym_optionGET
*/

trait YFYM_T_Simple_Get_Description {
	public function get_description($tag_name = 'description', $result_xml = '') {
		$product = $this->get_product();
		$tag_value = '';

		$yfym_yml_rules = yfym_optionGET('yfym_yml_rules', $this->get_feed_id(), 'set_arr');
		$yfym_desc = yfym_optionGET('yfym_desc', $this->get_feed_id(), 'set_arr');
		$yfym_the_content = yfym_optionGET('yfym_the_content', $this->get_feed_id(), 'set_arr');
		$yfym_enable_tags_custom = yfym_optionGET('yfym_enable_tags_custom', $this->get_feed_id(), 'set_arr');
		$yfym_enable_tags_behavior = yfym_optionGET('yfym_enable_tags_behavior', $this->get_feed_id(), 'set_arr');
		
		if ($yfym_enable_tags_behavior == 'default') {
			$enable_tags = '<p>,<br/>,<br>';
			$enable_tags = apply_filters('yfym_enable_tags_filter', $enable_tags, $this->get_feed_id());
		} else {
			$enable_tags = trim($yfym_enable_tags_custom);
			if ($enable_tags !== '') {
				$enable_tags = '<'.str_replace(',', '>,<', $enable_tags).'>';
			}			
		}	

		switch ($yfym_desc) { 
		case "full": $description_yml = $product->get_description(); break;
		case "excerpt": $description_yml = $product->get_short_description(); break;
		case "fullexcerpt": 
			$description_yml = $product->get_description(); 
			if (empty($description_yml)) {
				$description_yml = $product->get_short_description();
			}
		break;
		case "excerptfull": 
			$description_yml = $product->get_short_description();		 
			if (empty($description_yml)) {
				$description_yml = $product->get_description();
			} 
		break;
		case "fullplusexcerpt": 
			$description_yml = $product->get_description().'<br/>'.$product->get_short_description();
		break;
		case "excerptplusfull": 
			$description_yml = $product->get_short_description().'<br/>'.$product->get_description(); 
		break;	
		default: $description_yml = $product->get_description(); 
			if (class_exists('YmlforYandexMarketPro')) {
				if ($yfym_desc === 'post_meta') {
					$description_yml = '';
					$description_yml = apply_filters('yfym_description_filter', $description_yml, $product->get_id(), $product, $this->get_feed_id());
					if (!empty($description_yml)) {trim($description_yml);}
				}
			}
		}	

		$result_yml_desc = '';
		$description_yml = apply_filters('yfym_description_yml_filter', $description_yml, $product->get_id(), $product, $this->get_feed_id()); /* с версии 3.3.0 */
		if (!empty($description_yml)) {
			if ($yfym_the_content === 'enabled') {
				$description_yml = html_entity_decode(apply_filters('the_content', $description_yml)); /* с версии 3.3.6 */
			}
			$description_yml = $this->replace_tags($description_yml, $yfym_enable_tags_behavior);
			$description_yml = strip_tags($description_yml, $enable_tags);
			$description_yml = str_replace('<br>', '<br/>', $description_yml);
			$description_yml = strip_shortcodes($description_yml);
			$description_yml = apply_filters('yfym_description_filter', $description_yml, $product->get_id(), $product, $this->get_feed_id());
			$description_yml = apply_filters('yfym_description_filter_simple', $description_yml, $product->get_id(), $product, $this->get_feed_id()); /* с версии 3.3.6 */
			$description_yml = trim($description_yml);
		}

		$description_yml = apply_filters('y4ym_f_simple_val_description', $description_yml, array('product' => $product), $this->get_feed_id());
		if ($description_yml !== '') {
			$result_yml_desc = '<description><![CDATA['.$description_yml.']]></description>'.PHP_EOL;
		} 

		$result_xml .= $result_yml_desc;

		$result_xml = apply_filters('y4ym_f_simple_tag_description', $result_xml, array('product' => $product), $this->get_feed_id());
		return $result_xml;
	}

	private function replace_tags($description_yml, $yfym_enable_tags_behavior) {
		if ($yfym_enable_tags_behavior == 'default') {
			$description_yml = str_replace('<ul>', '', $description_yml);
			$description_yml = str_replace('<li>', '', $description_yml);
			$description_yml = str_replace('</li>', '<br/>', $description_yml);
		}
		return $description_yml;
	}
}
?>