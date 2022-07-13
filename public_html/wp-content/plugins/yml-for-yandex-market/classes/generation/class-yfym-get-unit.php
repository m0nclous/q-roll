<?php if (!defined('ABSPATH')) {exit;}
/**
* The main class for getting the XML-code of the product 
*
* @author		Maxim Glazunov
* @link			https://icopydoc.ru/
* @since		1.0.0
*
* @param string $post_id (require)
* @param string $feed_id (require)
*
* @return 		$result_xml (string)
* @return 		$ids_in_xml (string)
* @return 		$skip_reasons_arr (array)
*
* @depends		class:	WC_Product_Variation
*						YFYM_Get_Unit_Offer
*						(YFYM_Get_Unit_Offer_Simple)
*						(YFYM_Get_Unit_Offer_Varible)
*				traits:	YFYM_T_Get_Post_Id
*						YFYM_T_Get_Feed_Id;
*						YFYM_T_Get_Product
*						YFYM_T_Get_Skip_Reasons_Arr
*/

class YFYM_Get_Unit {
	use YFYM_T_Get_Post_Id;
	use YFYM_T_Get_Feed_Id; 
	use YFYM_T_Get_Product;
	use YFYM_T_Get_Skip_Reasons_Arr;

	protected $result_xml;
	protected $ids_in_xml = '';

	public function __construct($post_id, $feed_id) {
		$this->post_id = $post_id;
		$this->feed_id = $feed_id;

		$args_arr = array('post_id' => $post_id, 'feed_id' => $feed_id);

		do_action('before_wc_get_product', $args_arr);

		$product = wc_get_product($post_id);

		do_action('after_wc_get_product', $args_arr, $product);
		$this->product = $product;
		do_action('after_wc_get_product_this_product', $args_arr, $product);

		$this->create_code(); // создаём код одного простого или вариативного товара и заносим в $result_xml
	}

	public function get_result() {
		return $this->result_xml;
	}

	public function get_ids_in_xml() {
		return $this->ids_in_xml;
	}

	protected function create_code() {
		$product = $this->get_product();
		$feed_id = $this->get_feed_id();
		$post_id = $this->get_post_id();

		if ($product == null) {
			$this->result_xml = '';
			array_push($this->skip_reasons_arr, __('There is no product with this ID', 'yfym'));
			return $this->get_result();
		}
		
		if ($product->is_type('variable')) {
			$variations_arr = $product->get_available_variations();
			$variation_count = count($variations_arr);
			for ($i = 0; $i < $variation_count; $i++) {
				$offer_id = $variations_arr[$i]['variation_id'];
				$offer = new WC_Product_Variation($offer_id); // получим вариацию

				$args_arr = array(
					'feed_id' => $feed_id, 
					'product' => $product,
					'offer' => $offer,
					'variation_count' => $variation_count,
				);

				$offer_variable_obj = new YFYM_Get_Unit_Offer_Variable($args_arr);
				$r = $this->set_result($offer_variable_obj);
				if ($r === true) {
					$this->ids_in_xml .= $product->get_id().';'.$offer->get_id().';'.$offer_variable_obj->get_feed_price().';'.$offer_variable_obj->get_feed_category_id().PHP_EOL; /* с версии 3.1.0 */			
				}

				$one_variable = yfym_optionGET('yfym_one_variable', $this->feed_id, 'set_arr');
				if ($one_variable == 'on') {break;}	

				$stop_flag = false;
				$stop_flag = apply_filters('yfym_after_variable_offer_stop_flag', $stop_flag, $i, $variation_count, $offer->get_id(), $offer, $this->feed_id);
				if ($stop_flag == true) {break;}
			}
		} else {
			$args_arr = array(
				'feed_id' => $feed_id, 
				'product' => $product,
			);
			$offer_simple_obj = new YFYM_Get_Unit_Offer_Simple($args_arr);
			$r = $this->set_result($offer_simple_obj);
			if ($r === true) {
				$this->ids_in_xml .= $product->get_id().';'.$product->get_id().';'.$offer_simple_obj->get_feed_price().';'.$offer_simple_obj->get_feed_category_id().PHP_EOL; /* с версии 3.1.0 */			
			}
		}

		return $this->get_result();
	}
	
	// ожидается потомок класса YFYM_Get_Unit_Offer
	protected function set_result(YFYM_Get_Unit_Offer $offer_obj) {
		if (!empty($offer_obj->get_skip_reasons_arr())) {
			foreach ($offer_obj->get_skip_reasons_arr() as $value) {
				array_push($this->skip_reasons_arr, $value);
			}
		}
		if ($offer_obj->get_do_empty_product_xml() === true) { 
			$this->result_xml = '';	
			return false;
		} else { // если нет причин пропускать товар
			$this->result_xml .= $offer_obj->get_product_xml();
			return true;
		}
	}
}
?>