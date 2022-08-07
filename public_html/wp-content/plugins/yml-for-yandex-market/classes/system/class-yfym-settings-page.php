<?php if (!defined('ABSPATH')) {exit;}
/**
* Plugin Settings Page
*
* @link			https://icopydoc.ru/
* @since		1.9.0
*/

class YFYM_Settings_Page {
	private $feed_id;
	private $feedback;

	public function __construct() {
		$this->feedback = new YFYM_Feedback();

		$this->init_hooks(); // подключим хуки
		$this->listen_submit();

		$this->get_html_form();	
	}

	public function get_html_form() { ?>
		<div class="wrap">
  			<h1><?php _e('Exporter', 'yfym'); ?> YML for Yandex Market</h1>
			<?php echo $this->get_html_banner(); ?>
			<div id="poststuff">
				<?php $this->get_html_feeds_list(); ?>

				<div id="post-body" class="columns-2">

					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables">
							<?php $this->get_html_info_block(); ?>
							
							<?php do_action('yfym_before_support_project'); ?>

							<?php $this->feedback->get_block_support_project(); ?>

							<?php do_action('yfym_between_container_1', $this->get_feed_id()); ?>	

							<?php $this->feedback->get_form(); ?>

							<?php do_action('yfym_append_container_1', $this->get_feed_id()); ?>
						</div>
					</div><!-- /postbox-container-1 -->

					<div id="postbox-container-2" class="postbox-container">
						<div class="meta-box-sortables"><?php 
							if (isset($_GET['tab'])) {$tab = $_GET['tab'];} else {$tab = 'main_tab';}
							echo $this->get_html_tabs($tab); ?>

							<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
								<?php do_action('yfym_prepend_form_container_2', $this->get_feed_id()); ?>
								<input type="hidden" name="yfym_num_feed_for_save" value="<?php echo $this->get_feed_id(); ?>">
								<?php switch ($tab) : 
									case 'main_tab' : ?>
										<?php $this->get_html_main_settings(); ?>
										<?php break;
									case 'shop_data' : ?>
										<?php $this->get_html_shop_data(); ?>
										<?php break;
									case 'tags' : ?>
										<?php $this->get_html_tags_settings(); ?>
										<?php $yfym_settings_feed_wp_list_table = new YFYM_Settings_Feed_WP_List_Table($this->get_feed_id()); ?>
										<?php $yfym_settings_feed_wp_list_table->prepare_items(); $yfym_settings_feed_wp_list_table->display(); ?> 
										<?php do_action('yfym_before_button_primary_submit', $this->get_feed_id()); ?>
										<?php break;
									case 'filtration': ?>
										<?php $this->get_html_filtration(); ?>										
										<?php do_action('yfym_after_main_param_block', $this->get_feed_id()); ?>
								<?php break; ?>
								<?php endswitch; ?>

								<?php do_action('yfym_after_optional_elemet_block', $this->get_feed_id()); ?>
								<div class="postbox">
									<div class="inside">
										<table class="form-table"><tbody>
											<tr>
												<th scope="row"><label for="button-primary"></label></th>
												<td class="overalldesc"><?php wp_nonce_field('yfym_nonce_action', 'yfym_nonce_field'); ?><input id="button-primary" class="button-primary" type="submit" name="yfym_submit_action" value="<?php 
												if ($tab === 'main_tab') {
													echo __('Save', 'yfym').' & '. __('Create feed', 'yfym'); 
												} else {
													_e('Save', 'yfym');
												}
												?>"/><br />
												<span class="description"><small><?php _e('Click to save the settings', 'yfym'); ?><small></span></td>
											</tr>
										</tbody></table>
									</div>
								</div>
							</form>
						</div>
					</div><!-- /postbox-container-2 -->

				</div>
			</div><!-- /poststuff -->
			<?php $this->get_html_icp_banners(); ?>
			<?php $this->get_html_my_plugins_list(); ?>
		</div><?php // end get_html_form();
	}

	public function get_html_banner() {
		if (!class_exists('YmlforYandexMarketPro')) {
		return '<div class="notice notice-info">
			<p><span class="yfym_bold">YML for Yandex Market Pro</span> - '. __('a necessary extension for those who want to', 'yfym').' <span class="yfym_bold" style="color: green;">'. __('save on advertising budget', 'yfym').'</span> '. __('on Yandex', 'yfym').'! <a href="https://icopydoc.ru/product/yml-for-yandex-market-pro/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=about-xml-google-pro">'. __('Learn More', 'yfym').'</a>.</p> 
		</div>';
		} else {
			return '';
		}
	} // end get_html_banner();

	public function get_html_feeds_list() { 
		$yfymListTable = new YFYM_WP_List_Table(); ?>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field('yfym_nonce_action_add_new_feed', 'yfym_nonce_field_add_new_feed'); ?><input class="button" type="submit" name="yfym_submit_add_new_feed" value="<?php _e('Add New Feed', 'yfym'); ?>" />
		</form>
		<form method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
			<input type="hidden" name="yfym_form_id" value="yfym_wp_list_table" />
			<?php $yfymListTable->prepare_items(); $yfymListTable->display(); ?>
		</form><?php // end get_html_feeds_list();
	} // end get_html_feeds_list();

	public function get_html_info_block() { 
		$status_sborki = (int)yfym_optionGET('yfym_status_sborki', $this->get_feed_id());
		$yfym_file_url = urldecode(yfym_optionGET('yfym_file_url', $this->get_feed_id(), 'set_arr'));
		$yfym_date_sborki = yfym_optionGET('yfym_date_sborki', $this->get_feed_id(), 'set_arr');
		$yfym_date_sborki_end = yfym_optionGET('yfym_date_sborki_end', $this->get_feed_id(), 'set_arr');
		$yfym_status_cron = yfym_optionGET('yfym_status_cron', $this->get_feed_id(), 'set_arr'); 
		$yfym_count_products_in_feed = yfym_optionGET('yfym_count_products_in_feed', $this->get_feed_id(), 'set_arr');
		?>
		<div class="postbox">
			<?php if (is_multisite()) {$cur_blog_id = get_current_blog_id();} else {$cur_blog_id = '0';} ?>
			<h2 class="hndle"><?php _e('Feed', 'yfym'); ?> <?php echo $this->get_feed_id(); ?>: <?php if ($this->get_feed_id() !== '1') {echo $this->get_feed_id();} ?>feed-yml-<?php echo $cur_blog_id; ?>.xml <?php $assignment = yfym_optionGET('yfym_feed_assignment', $this->get_feed_id(), 'set_arr'); if ($assignment === '') {} else {echo '('.$assignment.')';} ?> <?php if (empty($yfym_file_url)) : ?><?php _e('not created yet', 'yfym'); ?><?php else : ?><?php if ($status_sborki !== -1) : ?><?php _e('updating', 'yfym'); ?><?php else : ?><?php _e('created', 'yfym'); ?><?php endif; ?><?php endif; ?></h2>	
			<div class="inside">
				<p><strong style="color: green;"><?php _e('Instruction', 'yfym'); ?>:</strong> <a href="https://icopydoc.ru/kak-sozdat-woocommerce-yml-instruktsiya/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=main-instruction" target="_blank"><?php _e('How to create a YML-feed', 'yfym'); ?></a>.</p>
				<?php if (empty($yfym_file_url)) : ?> 
					<?php if ($status_sborki !== -1) : ?>
						<p><?php _e('We are working on automatic file creation. YML will be developed soon', 'yfym'); ?>.</p>
					<?php else : ?>		
						<p><?php _e('In order to do that, select another menu entry (which differs from "off") in the box called "Automatic file creation". You can also change values in other boxes if necessary, then press "Save"', 'yfym'); ?>.</p>
						<p><?php _e('After 1-7 minutes (depending on the number of products), the feed will be generated and a link will appear instead of this message', 'yfym'); ?>.</p>
					<?php endif; ?>
				<?php else : ?>
					<?php if ($status_sborki !== -1) : ?>
						<p><?php _e('We are working on automatic file creation. YML will be developed soon', 'yfym'); ?>.</p>
					<?php else : ?>
						<p><span class="fgmc_bold"><?php _e('Your feed here', 'yfym'); ?>:</span><br/><a target="_blank" href="<?php echo $yfym_file_url; ?>"><?php echo $yfym_file_url; ?></a>
						<br/><?php _e('File size', 'yfym'); ?>: <?php clearstatcache();
						if ($this->get_feed_id() == '1') {$prefFeed = '';} else {$prefFeed = $this->get_feed_id();}
						$upload_dir = (object)wp_get_upload_dir();
						if (is_multisite()) {
							$filename = $upload_dir->basedir."/".$prefFeed."feed-yml-".get_current_blog_id().".xml";
						} else {
							$filename = $upload_dir->basedir."/".$prefFeed."feed-yml-0.xml";				
						}
						if (is_file($filename)) {echo yfym_formatSize(filesize($filename));} else {echo '0 KB'.$filename;} ?>
						<br/><?php _e('Start of generation', 'yfym'); ?>: <?php echo $yfym_date_sborki; ?>
						<br/><?php _e('Generated', 'yfym'); ?>: <?php echo $yfym_date_sborki_end; ?>
						<br/><?php _e('Products', 'yfym'); ?>: <?php echo $yfym_count_products_in_feed; ?></p>
					<?php endif; ?>		
				<?php endif; ?>
			</div>
		</div><?php
	} // end get_html_info_block();

	public function get_html_tabs($current = 'main_tab') {
		$tabs = array(
			'main_tab' 		=> __('Main settings', 'yfym'),
			'shop_data'		=> __('Shop data', 'yfym'),			
			'tags'			=> __('Attribute settings', 'yfym'), 
			'filtration'	=> __('Filtration', 'yfym')
		);
		
		$html = '<div class="nav-tab-wrapper" style="margin-bottom: 10px;">';
			foreach ($tabs as $tab => $name) {
				if ($tab === $current) {
					$class = ' nav-tab-active';
				} else {
					$class = ''; 
				}
				if (isset($_GET['feed_id'])) {
					$nf = '&feed_id='.sanitize_text_field($_GET['feed_id']);
				} else {
					$nf = '';
				}
				$html .= sprintf('<a class="nav-tab%1$s" href="?page=yfymexport&tab=%2$s%3$s">%4$s</a>',$class, $tab, $nf, $name);
			}
		$html .= '</div>';

		return $html;
	} // end get_html_tabs();

	public function get_html_main_settings() { 	
		$yfym_status_cron = yfym_optionGET('yfym_status_cron', $this->get_feed_id(), 'set_arr'); 
		$yfym_ufup = yfym_optionGET('yfym_ufup', $this->get_feed_id(), 'set_arr');
		$yfym_whot_export = yfym_optionGET('yfym_whot_export', $this->get_feed_id(), 'set_arr'); 
		$yfym_feed_assignment = yfym_optionGET('yfym_feed_assignment', $this->get_feed_id(), 'set_arr');
		$yfym_file_extension = yfym_optionGET('yfym_file_extension', $this->get_feed_id(), 'set_arr');
		$yfym_format_date = yfym_optionGET('yfym_format_date', $this->get_feed_id(), 'set_arr');
		$yfym_yml_rules = yfym_optionGET('yfym_yml_rules', $this->get_feed_id(), 'set_arr');
		$yfym_step_export = yfym_optionGET('yfym_step_export', $this->get_feed_id(), 'set_arr'); 		
		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Main parameters', 'yfym'); ?> (<?php _e('Feed', 'yfym'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_run_cron"><?php _e('Automatic file creation', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_run_cron" id="yfym_run_cron">
								<option value="off" <?php selected($yfym_status_cron, 'off' ); ?>><?php _e('Off', 'yfym'); ?></option>
								<?php $yfym_enable_five_min = yfym_optionGET('yfym_enable_five_min'); if ($yfym_enable_five_min === 'on') : ?>
								<option value="five_min" <?php selected($yfym_status_cron, 'five_min');?> ><?php _e('Every five minutes', 'yfym'); ?></option>
								<?php endif; ?>
								<option value="hourly" <?php selected($yfym_status_cron, 'hourly');?> ><?php _e('Hourly', 'yfym'); ?></option>
								<option value="six_hours" <?php selected($yfym_status_cron, 'six_hours'); ?> ><?php _e('Every six hours', 'yfym'); ?></option>	
								<option value="twicedaily" <?php selected($yfym_status_cron, 'twicedaily');?> ><?php _e('Twice a day', 'yfym'); ?></option>
								<option value="daily" <?php selected($yfym_status_cron, 'daily');?> ><?php _e('Daily', 'yfym'); ?></option>
								<option value="week" <?php selected($yfym_status_cron, 'week');?> ><?php _e('Once a week', 'yfym'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('The refresh interval on your feed', 'yfym'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_ufup"><?php _e('Update feed when updating products', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="yfym_ufup" id="yfym_ufup" <?php checked($yfym_ufup, 'on' ); ?>/>
						</td>
					</tr>
					<?php do_action('yfym_after_ufup_option', $this->get_feed_id()); /* С версии 3.0.0 */ ?>
					<tr>
						<th scope="row"><label for="yfym_feed_assignment"><?php _e('Feed assignment', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="text" maxlength="20" name="yfym_feed_assignment" id="yfym_feed_assignment" value="<?php echo $yfym_feed_assignment; ?>" placeholder="<?php _e('For Yandex Market', 'yfym');?>" /><br />
							<span class="description"><small><?php _e('Not used in feed. Inner note for your convenience', 'yfym'); ?>.</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_file_extension"><?php _e('Feed file extension', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_file_extension" id="yfym_file_extension">
								<option value="xml" <?php selected($yfym_file_extension, 'xml'); ?>>XML (<?php _e('recommend', 'yfym'); ?>)</option>
								<option value="yml" <?php selected($yfym_file_extension, 'yml'); ?>>YML</option>
								<?php do_action('yfym_after_file_extension_option', $this->get_feed_id()); ?>
							</select><br />
							<span class="description"><small><?php _e('Default', 'yfym'); ?>: XML</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_format_date"><?php _e('Format date', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_format_date" id="yfym_format_date">
								<option value="rfc" <?php selected($yfym_format_date, 'rfc'); ?>>RFC 3339 (<?php _e('recommend', 'yfym'); ?>)</option>
								<option value="unixtime" <?php selected($yfym_format_date, 'unixtime'); ?>>Unix time</option>
							</select><br />
							<span class="description"><small><?php _e('Default', 'yfym'); ?>: RFC 3339</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_yml_rules"><?php _e('To follow the rules', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_yml_rules" id="yfym_yml_rules">
							<option value="yandex_market" <?php selected($yfym_yml_rules, 'yandex_market'); ?>><?php _e('Yandex Market', 'yfym'); ?> ADV (<?php _e('Simplified type', 'yfym'); ?>)</option>
							<option value="dbs" <?php selected($yfym_yml_rules, 'dbs'); ?>><?php _e('Yandex Market', 'yfym'); ?> DBS (<?php _e('Simplified type', 'yfym'); ?>)</option>
							<option value="single_catalog" <?php selected($yfym_yml_rules, 'single_catalog'); ?>>FBY, FBY+, FBS (<?php _e('in a single catalog', 'yfym'); ?>) (beta)</option>
							<option value="sbermegamarket" <?php selected($yfym_yml_rules, 'sbermegamarket'); ?>><?php _e('SberMegaMarket', 'yfym'); ?> (beta)</option>
							<option value="beru" <?php selected($yfym_yml_rules, 'beru'); ?>><?php _e('Former Beru', 'yfym'); ?></option>
							<option value="yandex_webmaster" <?php selected($yfym_yml_rules, 'yandex_webmaster'); ?>><?php _e('Yandex Webmaster', 'yfym'); ?> (turbo)</option>
							<option value="all_elements" <?php selected($yfym_yml_rules, 'all_elements'); ?>><?php _e('No rules', 'yfym'); ?> (<?php _e('Not recommended', 'yfym'); ?>)</option>
							<option value="ozon" <?php selected($yfym_yml_rules, 'ozon'); ?>>OZON</option>
							<?php do_action('yfym_append_select_yfym_yml_rules', $yfym_yml_rules, $this->get_feed_id()); ?>
							</select><br />
							<?php do_action('yfym_after_select_yfym_yml_rules', $yfym_yml_rules, $this->get_feed_id()); ?>
							<span class="description"><small><?php _e('Exclude products that do not meet the requirements', 'yfym'); ?> <i>(<?php _e('missing required elements/data', 'yfym'); ?>)</i>. <?php _e('The plugin will try to automatically remove products from the YML-feed for which the required fields for the feed are not filled', 'yfym'); ?>. <?php _e('Also, this item affects the structure of the file', 'yfym'); ?>.</small></span>
						</td>
					</tr>
					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_step_export"><?php _e('Step of export', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_step_export" id="yfym_step_export">
							<option value="80" <?php selected($yfym_step_export, '80'); ?>>80</option>
							<option value="200" <?php selected($yfym_step_export, '200'); ?>>200</option>
							<option value="300" <?php selected($yfym_step_export, '300'); ?>>300</option>
							<option value="450" <?php selected($yfym_step_export, '450'); ?>>450</option>
							<option value="500" <?php selected($yfym_step_export, '500'); ?>>500</option>
							<option value="800" <?php selected($yfym_step_export, '800'); ?>>800</option>
							<option value="1000" <?php selected($yfym_step_export, '1000'); ?>>1000</option>
							<?php do_action('yfym_step_export_option', $this->get_feed_id(), $yfym_step_export); ?>
							</select><br />
							<span class="description"><small><?php _e('The value affects the speed of file creation', 'yfym'); ?>. <?php _e('If you have any problems with the generation of the file - try to reduce the value in this field', 'yfym'); ?>. <?php _e('More than 500 can only be installed on powerful servers', 'yfym'); ?>.</small></span>
						</td>
					</tr>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_main_settings();

	public function get_html_shop_data() { 
		$yfym_shop_name = stripslashes(htmlspecialchars(yfym_optionGET('yfym_shop_name', $this->get_feed_id(), 'set_arr')));
		$yfym_company_name = stripslashes(htmlspecialchars(yfym_optionGET('yfym_company_name', $this->get_feed_id(), 'set_arr')));
		$yfym_warehouse = stripslashes(htmlspecialchars(yfym_optionGET('yfym_warehouse', $this->get_feed_id(), 'set_arr')));
		$yfym_currencies = yfym_optionGET('yfym_currencies', $this->get_feed_id(), 'set_arr');
		$yfym_main_product = yfym_optionGET('yfym_main_product', $this->get_feed_id(), 'set_arr');
		$yfym_adult = yfym_optionGET('yfym_adult', $this->get_feed_id(), 'set_arr'); 	
		$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->get_feed_id(), 'set_arr');
		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Shop data', 'yfym'); ?> (<?php _e('Feed', 'yfym'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_shop_name"><?php _e('Shop name', 'yfym'); ?></label></th>
						<td class="overalldesc">
						<input maxlength="20" type="text" name="yfym_shop_name" id="yfym_shop_name" value="<?php echo $yfym_shop_name; ?>" /><br />
						<span class="description"><small><?php _e('Required element', 'yfym'); ?> <strong>name</strong>. <?php _e('The short name of the store should not exceed 20 characters', 'yfym'); ?>.</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_company_name"><?php _e('Company name', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="yfym_company_name" id="yfym_company_name" value="<?php echo $yfym_company_name; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'yfym'); ?> <strong>company</strong>. <?php _e('Full name of the company that owns the store', 'yfym'); ?>.</small></span>
						</td>
					</tr>
					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_warehouse"><?php _e('Warehouse', 'yfym'); ?> Name/ID</label></th>
						<td class="overalldesc">
						<input type="text" name="yfym_warehouse" id="yfym_warehouse" value="<?php echo $yfym_warehouse; ?>" /><br />
						<span class="description"><small><?php _e('Warehouse name', 'yfym'); ?> (OZON) <?php _e('or ID', 'yfym'); ?> (<?php _e('SberMegaMarket', 'yfym'); ?>)</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_currencies"><?php _e('Element "currencies"', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_currencies" id="yfym_currencies">
								<option value="enabled" <?php selected($yfym_currencies, 'enabled'); ?>><?php _e('Enabled', 'yfym'); ?></option>	
								<option value="disabled" <?php selected($yfym_currencies, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
							</select>
						</td>
					</tr>		 
					<tr>
						<th scope="row"><label for="yfym_main_product"><?php _e('What kind of products do you sell', 'yfym'); ?>?</label></th>
						<td class="overalldesc">
								<select name="yfym_main_product" id="yfym_main_product">
								<option value="electronics" <?php selected($yfym_main_product, 'electronics'); ?>><?php _e('Electronics', 'yfym'); ?></option>
								<option value="computer" <?php selected($yfym_main_product, 'computer'); ?>><?php _e('Computer techologies', 'yfym'); ?></option>
								<option value="clothes_and_shoes" <?php selected($yfym_main_product, 'clothes_and_shoes'); ?>><?php _e('Clothes and shoes', 'yfym'); ?></option>
								<option value="auto_parts" <?php selected($yfym_main_product, 'auto_parts'); ?>><?php _e('Auto parts', 'yfym'); ?></option>
								<option value="products_for_children" <?php selected($yfym_main_product, 'products_for_children'); ?>><?php _e('Products for children', 'yfym'); ?></option>
								<option value="sporting_goods" <?php selected($yfym_main_product, 'sporting_goods'); ?>><?php _e('Sporting goods', 'yfym'); ?></option>
								<option value="goods_for_pets" <?php selected($yfym_main_product, 'goods_for_pets'); ?>><?php _e('Goods for pets', 'yfym'); ?></option>
								<option value="sexshop" <?php selected($yfym_main_product, 'sexshop'); ?>><?php _e('Sex shop (Adult products)', 'yfym'); ?></option>
								<option value="books" <?php selected($yfym_main_product, 'books'); ?>><?php _e('Books', 'yfym'); ?></option>
								<option value="health" <?php selected($yfym_main_product, 'health'); ?>><?php _e('Health products', 'yfym'); ?></option>	
								<option value="food" <?php selected($yfym_main_product, 'food'); ?>><?php _e('Food', 'yfym'); ?></option>
								<option value="construction_materials" <?php selected($yfym_main_product, 'construction_materials'); ?>><?php _e('Construction Materials', 'yfym'); ?></option>
								<option value="other" <?php selected($yfym_main_product, 'other'); ?>><?php _e('Other', 'yfym'); ?></option>	
							</select><br />
							<span class="description"><small><?php _e('Specify the main category', 'yfym'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_adult"><?php _e('Adult Market', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_adult" id="yfym_adult">
							<option value="no" <?php selected($yfym_adult, 'no'); ?>><?php _e('No', 'yfym'); ?></option>
							<option value="yes" <?php selected($yfym_adult, 'yes'); ?>><?php _e('Yes', 'yfym'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('Optional element', 'yfym'); ?> <strong>adult</strong></small></span>
						</td>
					</tr>
					<?php if (class_exists('WOOCS')) : 		 
						global $WOOCS; $currencies_arr = $WOOCS->get_currencies(); 
						if (is_array($currencies_arr)) : $array_keys = array_keys($currencies_arr); ?>
						<tr>
							<th scope="row"><label for="yfym_wooc_currencies"><?php _e('Feed currency', 'yfym'); ?></label></th>
							<td class="overalldesc">
								<select name="yfym_wooc_currencies" id="yfym_wooc_currencies">
								<?php for ($i = 0; $i < count($array_keys); $i++) : ?>
									<option value="<?php echo $currencies_arr[$array_keys[$i]]['name']; ?>" <?php selected($yfym_wooc_currencies, $currencies_arr[$array_keys[$i]]['name']); ?>><?php echo $currencies_arr[$array_keys[$i]]['name']; ?></option>					
								<?php endfor; ?>
								</select><br />
								<span class="description"><small><?php _e('You have plugin installed', 'yfym'); ?> <strong class="yfym_bold">WooCommerce Currency Switcher by PluginUs.NET. Woo Multi Currency and Woo Multi Pay</strong><br />
								<?php _e('Indicate in what currency the prices should be', 'yfym'); ?>.<br /><strong class="yfym_bold"><?php _e('Please note', 'yfym'); ?>:</strong> <?php _e('Yandex Market only supports the following currencies', 'yfym'); ?>: RUR, RUB, UAH, BYN, KZT, USD, EUR. <?php _e('Choosing a different currency can lead to errors', 'yfym'); ?>
								</small></span>
							</td>
						</tr>
						<?php endif; ?>
					<?php endif; ?>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_shop_data();

	public function get_html_tags_settings() {
		$yfym_no_group_id_arr = unserialize(yfym_optionGET('yfym_no_group_id_arr', $this->get_feed_id())); 
		$add_in_name_arr = unserialize(yfym_optionGET('yfym_add_in_name_arr', $this->get_feed_id()));
		$yfym_separator_type = yfym_optionGET('yfym_separator_type', $this->get_feed_id(), 'set_arr'); 

		$yfym_pickup_options = yfym_optionGET('yfym_pickup_options', $this->get_feed_id(), 'set_arr');
		$yfym_pickup_cost = yfym_optionGET('yfym_pickup_cost', $this->get_feed_id(), 'set_arr'); 
		$yfym_pickup_days = yfym_optionGET('yfym_pickup_days', $this->get_feed_id(), 'set_arr'); 
		$yfym_pickup_order_before = yfym_optionGET('yfym_pickup_order_before', $this->get_feed_id(), 'set_arr');

		$yfym_delivery_options = yfym_optionGET('yfym_delivery_options', $this->get_feed_id(), 'set_arr');
		$yfym_delivery_cost = yfym_optionGET('yfym_delivery_cost', $this->get_feed_id(), 'set_arr'); 
		$yfym_delivery_days = yfym_optionGET('yfym_delivery_days', $this->get_feed_id(), 'set_arr'); 
		$yfym_order_before = yfym_optionGET('yfym_order_before', $this->get_feed_id(), 'set_arr');
		$yfym_delivery_options2 = yfym_optionGET('yfym_delivery_options2', $this->get_feed_id(), 'set_arr');
		$yfym_delivery_cost2 = yfym_optionGET('yfym_delivery_cost2', $this->get_feed_id(), 'set_arr'); 
		$yfym_delivery_days2 = yfym_optionGET('yfym_delivery_days2', $this->get_feed_id(), 'set_arr');  
		$yfym_order_before2 = yfym_optionGET('yfym_order_before2', $this->get_feed_id(), 'set_arr');
		$yfym_ebay_stock = yfym_optionGET('yfym_ebay_stock', $this->get_feed_id(), 'set_arr'); 
		$params_arr = unserialize(yfym_optionGET('yfym_params_arr', $this->get_feed_id()));
		?>
		<?php do_action('yfym_optional_element', $this->get_feed_id()); ?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Tags settings', 'yfym'); ?> (<?php _e('Feed', 'yfym'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_no_group_id_arr"><?php _e('Categories of variable products for which group_id is not allowed', 'yfym'); ?></label></th>
						<td class="overalldesc">
						<select id="yfym_no_group_id_arr" style="width: 100%;" name="yfym_no_group_id_arr[]" size="8" multiple>
							<?php foreach (get_terms('product_cat', array('hide_empty'=>0, 'parent'=>0)) as $term) {
									echo yfym_cat_tree($term->taxonomy, $term->term_id, $yfym_no_group_id_arr); } ?>
						</select><br />
						<span class="description"><small><?php _e('According to Yandex Market rules in this field you need to mark ALL categories of products not related to "Clothes, Shoes and Accessories", "Furniture", "Cosmetics, perfumes and care", "Baby products", "Accessories for portable electronics". Ie categories for which it is forbidden to use the attribute group_id', 'yfym'); ?>.</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_add_in_name_arr"><?php _e('Add attributes to the variable products name', 'yfym'); ?></label><br />(<?php _e('You can only add attributes that are used for variations and that cannot be grouped using', 'yfym'); ?> group_id)</th>
						<td class="overalldesc">
						<select id="yfym_add_in_name_arr" style="width: 100%;" name="yfym_add_in_name_arr[]" size="8" multiple>
							<?php foreach (yfym_get_attributes() as $attribute) : ?>
								<option value="<?php echo $attribute['id']; ?>"<?php if (!empty($add_in_name_arr)) { foreach ($add_in_name_arr as $value) {selected($value, $attribute['id']);}} ?>><?php echo $attribute['name']; ?></option>
							<?php endforeach; ?>
						</select><br />
						<span class="description"><small><?php _e('It works only for variable products that are not in the category "Clothes, Shoes and Accessories", "Furniture", "Cosmetics, perfumes and care", "Baby products", "Accessories for portable electronics"', 'yfym'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_separator_type"><?php _e('Separator options', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_separator_type" id="yfym_separator_type">
								<option value="type1" <?php selected($yfym_separator_type, 'type1'); ?>><?php _e('Type', 'yfym'); ?>_1 (В1:З1, В2:З2, ... Вn:Зn)</option>
								<option value="type2" <?php selected($yfym_separator_type, 'type2')?> ><?php _e('Type', 'yfym'); ?>_2 (В1-З1, В2-З2, ... Вn:Зn)</option>
								<option value="type3" <?php selected($yfym_separator_type, 'type3'); ?> ><?php _e('Type', 'yfym'); ?>_3 В1:З1, В2:З2, ... Вn:Зn</option>
								<option value="type4" <?php selected($yfym_separator_type, 'type4'); ?> ><?php _e('Type', 'yfym'); ?>_4 З1 З2 ... Зn</option>
								<?php do_action('yfym_after_option_separator_type', $this->get_feed_id()); ?>
							</select><br />
							<span class="description"><small><?php _e('Separator options', 'yfym'); ?></small></span>
						</td>
					</tr>

					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_pickup_options"><?php _e('Add', 'yfym'); ?> pickup-options<br/><small>(<?php _e('pickup of products', 'yfym'); ?>)</small></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="yfym_pickup_options" id="yfym_pickup_options" <?php checked($yfym_pickup_options, 'on' ); ?>/><br />
							<span class="description"><small><?php _e('Optional element', 'yfym'); ?> <strong>pickup-options</strong> <a target="_blank" href="//yandex.ru/support/partnermarket/elements/pickup-options.html#structure"><?php _e('Read more on Yandex', 'yfym'); ?></a></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_pickup_cost"><?php _e('Pickup cost', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input min="0" type="number" name="yfym_pickup_cost" id="yfym_pickup_cost" value="<?php echo $yfym_pickup_cost; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'yfym'); ?> <strong>cost</strong> <?php _e('of attribute', 'yfym'); ?> <strong>pickup-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_pickup_days"><?php _e('Pickup days', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="yfym_pickup_days" id="yfym_pickup_days" value="<?php echo $yfym_pickup_days; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'yfym'); ?> <strong>days</strong> <?php _e('of attribute', 'yfym'); ?> <strong>pickup-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_pickup_order_before"><?php _e('The time', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="yfym_pickup_order_before" id="yfym_pickup_order_before" value="<?php echo $yfym_pickup_order_before; ?>" /><br />
							<span class="description"><small><?php _e('Optional element', 'yfym'); ?> <strong>order-before</strong> <?php _e('of attribute', 'yfym'); ?> <strong>pickup-option</strong>.<br /><?php _e('The time in which you need to place an order to get it at this time', 'yfym'); ?></small></span>
						</td>
					</tr>

					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_delivery_options"><?php _e('Use delivery-options', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="yfym_delivery_options" id="yfym_delivery_options" <?php checked($yfym_delivery_options, 'on' ); ?>/><br />
							<span class="description"><small><?php _e('Optional element', 'yfym'); ?> <strong>delivery-options</strong> <a target="_blank" href="//yandex.ru/support/partnermarket/elements/delivery-options.html#structure"><?php _e('Read more on Yandex', 'yfym'); ?></a></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_delivery_cost"><?php _e('Delivery cost', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input min="0" type="number" name="yfym_delivery_cost" id="yfym_delivery_cost" value="<?php echo $yfym_delivery_cost; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'yfym'); ?> <strong>cost</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_delivery_days"><?php _e('Delivery days', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="yfym_delivery_days" id="yfym_delivery_days" value="<?php echo $yfym_delivery_days; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'yfym'); ?> <strong>days</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_order_before"><?php _e('The time', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="yfym_order_before" id="yfym_order_before" value="<?php echo $yfym_order_before; ?>" /><br />
							<span class="description"><small><?php _e('Optional element', 'yfym'); ?> <strong>order-before</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong>.<br /><?php _e('The time in which you need to place an order to get it at this time', 'yfym'); ?></small></span>
						</td>
					</tr>
					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_delivery_options2"><?php _e('Add a second delivery methods', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="yfym_delivery_options2" id="yfym_delivery_options2" <?php checked($yfym_delivery_options2, 'on' ); ?>/><br />
							<span class="description"><small><?php _e('Add a second delivery methods to', 'yfym'); ?> <strong>delivery-options</strong> <a target="_blank" href="//yandex.ru/support/partnermarket/elements/delivery-options.html#structure"><?php _e('Read more on Yandex', 'yfym'); ?></a></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_delivery_cost2"><?php _e('Delivery cost', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input min="0" type="number" name="yfym_delivery_cost2" id="yfym_delivery_cost2" value="<?php echo $yfym_delivery_cost2; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'yfym'); ?> <strong>cost</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_delivery_days2"><?php _e('Delivery days', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="yfym_delivery_days2" id="yfym_delivery_days2" value="<?php echo $yfym_delivery_days2; ?>" /><br />
							<span class="description"><small><?php _e('Required element', 'yfym'); ?> <strong>days</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_order_before2"><?php _e('The time', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="yfym_order_before2" id="yfym_order_before2" value="<?php echo $yfym_order_before2; ?>" /><br />
							<span class="description"><small><?php _e('Optional element', 'yfym'); ?> <strong>order-before</strong> <?php _e('of attribute', 'yfym'); ?> <strong>delivery-option</strong>.<br /><?php _e('The time in which you need to place an order to get it at this time', 'yfym'); ?></small></span>
						</td>
					</tr>	
					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_ebay_stock"><?php _e('Add information about stock to feed for EBay', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" id="yfym_ebay_stock" name="yfym_ebay_stock" <?php checked($yfym_ebay_stock, 'on' ); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_params_arr"><?php _e('Include these attributes in the values Param', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select id="yfym_params_arr" style="width: 100%;" name="yfym_params_arr[]" size="8" multiple>
								<?php foreach (yfym_get_attributes() as $attribute) : ?>
									<option value="<?php echo $attribute['id']; ?>"<?php if (!empty($params_arr)) {foreach ($params_arr as $value) {selected($value, $attribute['id']);}} ?>><?php echo $attribute['name']; ?></option>
								<?php endforeach; ?>
							</select><br />
							<span class="description"><small>
								<?php _e('Optional element', 'yfym'); ?> <strong>param</strong></span><br />
								<span style="color: blue;"><?php _e('Hint', 'yfym'); ?>:</span> <?php _e('To select multiple values, hold down the (ctrl) button on Windows or (cmd) on a Mac. To deselect, press and hold (ctrl) or (cmd), click on the marked items', 'yfym'); ?>
							</small></span>
						</td>
					</tr>
					<?php do_action('yfym_after_manufacturer_warranty', $this->get_feed_id()); ?>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_tags_settings();

	public function get_html_filtration() { 
		$yfym_whot_export = yfym_optionGET('yfym_whot_export', $this->get_feed_id(), 'set_arr'); 
		$yfym_desc = yfym_optionGET('yfym_desc', $this->get_feed_id(), 'set_arr');
//		$yfym_del_all_attributes = yfym_optionGET('yfym_del_all_attributes', $this->get_feed_id(), 'set_arr');
		$yfym_enable_tags_custom = yfym_optionGET('yfym_enable_tags_custom', $this->get_feed_id(), 'set_arr');
		$yfym_enable_tags_behavior = yfym_optionGET('yfym_enable_tags_behavior', $this->get_feed_id(), 'set_arr');
		$yfym_the_content = yfym_optionGET('yfym_the_content', $this->get_feed_id(), 'set_arr');
		$yfym_replace_domain = yfym_optionGET('yfym_replace_domain', $this->get_feed_id(), 'set_arr');
		$yfym_var_desc_priority = yfym_optionGET('yfym_var_desc_priority', $this->get_feed_id(), 'set_arr');
		$yfym_clear_get = yfym_optionGET('yfym_clear_get', $this->get_feed_id(), 'set_arr');
		$yfym_behavior_onbackorder = yfym_optionGET('yfym_behavior_onbackorder', $this->get_feed_id(), 'set_arr'); 
		$yfym_behavior_stip_symbol = yfym_optionGET('yfym_behavior_stip_symbol', $this->get_feed_id(), 'set_arr');
		$yfym_skip_missing_products = yfym_optionGET('yfym_skip_missing_products', $this->get_feed_id(), 'set_arr');
		$yfym_skip_backorders_products = yfym_optionGET('yfym_skip_backorders_products', $this->get_feed_id(), 'set_arr'); 
		$yfym_no_default_png_products = yfym_optionGET('yfym_no_default_png_products', $this->get_feed_id(), 'set_arr');
		$yfym_skip_products_without_pic = yfym_optionGET('yfym_skip_products_without_pic', $this->get_feed_id(), 'set_arr'); 
		?>
		<div class="postbox">
			<h2 class="hndle"><?php _e('Filtration', 'yfym'); ?> (<?php _e('Feed', 'yfym'); ?> ID: <?php echo $this->get_feed_id(); ?>)</h2>
			<div class="inside">
				<table class="form-table"><tbody>
					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_whot_export"><?php _e('Whot export', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_whot_export" id="yfym_whot_export">
								<option value="all" <?php selected($yfym_whot_export, 'all'); ?>><?php _e('Simple & Variable products', 'yfym'); ?></option>
								<option value="simple" <?php selected($yfym_whot_export, 'simple'); ?>><?php _e('Only simple products', 'yfym'); ?></option>
								<?php do_action('yfym_after_whot_export_option', $this->get_feed_id()); ?>
							</select><br />
							<span class="description"><small><?php _e('Whot export', 'yfym'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_desc"><?php _e('Description of the product', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_desc" id="yfym_desc">
							<option value="excerpt" <?php selected($yfym_desc, 'excerpt'); ?>><?php _e('Only Excerpt description', 'yfym'); ?></option>
							<option value="full" <?php selected($yfym_desc, 'full'); ?>><?php _e('Only Full description', 'yfym'); ?></option>
							<option value="excerptfull" <?php selected($yfym_desc, 'excerptfull'); ?>><?php _e('Excerpt or Full description', 'yfym'); ?></option>
							<option value="fullexcerpt" <?php selected($yfym_desc, 'fullexcerpt'); ?>><?php _e('Full or Excerpt description', 'yfym'); ?></option>
							<option value="excerptplusfull" <?php selected($yfym_desc, 'excerptplusfull'); ?>><?php _e('Excerpt plus Full description', 'yfym'); ?></option>
							<option value="fullplusexcerpt" <?php selected($yfym_desc, 'fullplusexcerpt'); ?>><?php _e('Full plus Excerpt description', 'yfym'); ?></option>
							<?php do_action('yfym_append_select_yfym_desc', $yfym_desc, $this->get_feed_id()); /* с версии 3.2.1 */ ?>
							</select><br />
							<?php do_action('yfym_after_select_yfym_desc', $yfym_desc, $this->get_feed_id()); /* с версии 3.2.1 */ ?>
							<span class="description"><small><?php _e('The source of the description', 'yfym'); ?></small></span>
						</td>
					</tr><?php /*
					<tr>
						<th scope="row"><label for="yfym_del_all_attributes"><?php _e('Remove all attributes in tags', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_del_all_attributes" id="yfym_del_all_attributes">
							<option value="disabled" <?php selected($yfym_del_all_attributes, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
							<option value="enabled" <?php selected($yfym_del_all_attributes, 'enabled'); ?>><?php _e('Enabled', 'yfym'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('Remove all attributes in tags from product description', 'yfym'); ?></small></span>
						</td>
					</tr> */ ?>
					<tr>
						<th scope="row"><label for="yfym_enable_tags_custom"><?php _e('List of allowed tags', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_enable_tags_behavior" id="yfym_enable_tags_behavior">
								<option value="default" <?php selected($yfym_enable_tags_behavior, 'default'); ?>><?php _e('Default', 'yfym'); ?></option>
								<option value="custom" <?php selected($yfym_enable_tags_behavior, 'custom'); ?>><?php _e('From the field below', 'yfym'); ?></option>
							</select><br />
							<input style="min-width: 100%;" type="text" name="yfym_enable_tags_custom" id="yfym_enable_tags_custom" value="<?php echo $yfym_enable_tags_custom; ?>" placeholder="p,br,h3" /><br />
							<span class="description"><small><?php _e('For example', 'yfym'); ?>: <code>p,br,h3</code></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_the_content"><?php _e('Use the filter', 'yfym'); ?> the_content</label></th>
						<td class="overalldesc">
							<select name="yfym_the_content" id="yfym_the_content">
							<option value="disabled" <?php selected($yfym_the_content, 'disabled'); ?>><?php _e('Disabled', 'yfym'); ?></option>
							<option value="enabled" <?php selected($yfym_the_content, 'enabled'); ?>><?php _e('Enabled', 'yfym'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('Default', 'yfym'); ?>: <?php _e('Enabled', 'yfym'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_replace_domain"><?php _e('Change the domain to', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="text" name="yfym_replace_domain" id="yfym_replace_domain" value="<?php echo $yfym_replace_domain; ?>" placeholder="https://site.ru" /><br />
							<span class="description"><small><?php _e('The option allows you to change the domain of your site in the feed to any other', 'yfym'); ?></small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_var_desc_priority"><?php _e('The varition description takes precedence over others', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="yfym_var_desc_priority" id="yfym_var_desc_priority" <?php checked($yfym_var_desc_priority, 'on'); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_clear_get"><?php _e('Clear URL from GET-paramrs', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_clear_get" id="yfym_clear_get">
							<option value="no" <?php selected($yfym_clear_get, 'no'); ?>><?php _e('No', 'yfym'); ?></option>
							<option value="yes" <?php selected($yfym_clear_get, 'yes'); ?>><?php _e('Yes', 'yfym'); ?></option>
							</select><br />
							<span class="description"><small><?php _e('This option may be useful when setting up Turbo pages', 'yfym'); ?><br />
							<a target="_blank" href="https://icopydoc.ru/vklyuchaem-turbo-stranitsy-dlya-magazina-woocommerce-instruktsiya/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=settings&utm_term=yandex-turbo-instruction"><?php _e('Tips for configuring Turbo pages', 'yfym'); ?></a></small></span>
						</td>
					</tr>
					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_behavior_onbackorder"><?php _e('For pre-order products, establish availability equal to', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_behavior_onbackorder" id="yfym_behavior_onbackorder">
								<option value="false" <?php selected($yfym_behavior_onbackorder, 'false'); ?>>False</option>
								<option value="true" <?php selected($yfym_behavior_onbackorder, 'true')?> >True</option>
							</select><br />
							<span class="description"><small><?php _e('For pre-order products, establish availability equal to', 'yfym'); ?> false/true</small></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_behavior_stip_symbol"><?php _e('In attributes', 'yfym'); ?> vendorCode <?php _e('and', 'yfym'); ?> shop-sku <?php _e('ampersand', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<select name="yfym_behavior_stip_symbol" id="yfym_behavior_stip_symbol">
								<option value="default" <?php selected($yfym_behavior_stip_symbol, 'default'); ?>><?php _e('Default', 'yfym'); ?></option>
								<option value="del" <?php selected($yfym_behavior_stip_symbol, 'del'); ?>><?php _e('Delete', 'yfym'); ?></option>
								<option value="slash" <?php selected($yfym_behavior_stip_symbol, 'slash'); ?>><?php _e('Replace with', 'yfym'); ?> /</option>
								<option value="amp" <?php selected($yfym_behavior_stip_symbol, 'amp'); ?>><?php _e('Replace with', 'yfym'); ?> amp;</option>
							</select><br />
							<span class="description"><small><?php _e('Default', 'yfym'); ?> "<?php _e('Delete', 'yfym'); ?>"</small></span>
						</td>
					</tr>
					<tr class="yfym_tr">
						<th scope="row"><label for="yfym_skip_missing_products"><?php _e('Skip missing products', 'yfym'); ?> (<?php _e('except for products for which a pre-order is permitted', 'yfym'); ?>.)</label></th>
						<td class="overalldesc">
							<input type="checkbox" name="yfym_skip_missing_products" id="yfym_skip_missing_products" <?php checked($yfym_skip_missing_products, 'on' ); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_skip_backorders_products"><?php _e('Skip backorders products', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="yfym_skip_backorders_products" id="yfym_skip_backorders_products" <?php checked($yfym_skip_backorders_products, 'on' ); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_no_default_png_products"><?php _e('Remove default.png from YML', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="yfym_no_default_png_products" id="yfym_no_default_png_products" <?php checked($yfym_no_default_png_products, 'on' ); ?>/>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="yfym_skip_products_without_pic"><?php _e('Skip products without pictures', 'yfym'); ?></label></th>
						<td class="overalldesc">
							<input type="checkbox" name="yfym_skip_products_without_pic" id="yfym_skip_products_without_pic" <?php checked($yfym_skip_products_without_pic, 'on' ); ?>/>
						</td>
					</tr>
					<?php do_action('yfym_after_step_export', $this->get_feed_id()); ?>

					<?php do_action('yfym_append_main_param_table', $this->get_feed_id()); ?>
				</tbody></table>
			</div>
		</div><?php
	} // end get_html_filtration();

	public function get_html_icp_banners() { ?>
		<div id="icp_slides" class="clear">
			<div class="icp_wrap">
				<input type="radio" name="icp_slides" id="icp_point1">
				<input type="radio" name="icp_slides" id="icp_point2">
				<input type="radio" name="icp_slides" id="icp_point3">
				<input type="radio" name="icp_slides" id="icp_point4">
				<input type="radio" name="icp_slides" id="icp_point5" checked>
				<input type="radio" name="icp_slides" id="icp_point6">
				<input type="radio" name="icp_slides" id="icp_point7">
				<div class="icp_slider">
					<div class="icp_slides icp_img1"><a href="//wordpress.org/plugins/yml-for-yandex-market/" target="_blank"></a></div>
					<div class="icp_slides icp_img2"><a href="//wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"></a></div>
					<div class="icp_slides icp_img3"><a href="//wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"></a></div>
					<div class="icp_slides icp_img4"><a href="//wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"></a></div>
					<div class="icp_slides icp_img5"><a href="//wordpress.org/plugins/xml-for-avito/" target="_blank"></a></div>
					<div class="icp_slides icp_img6"><a href="//wordpress.org/plugins/xml-for-o-yandex/" target="_blank"></a></div>
					<div class="icp_slides icp_img7"><a href="//wordpress.org/plugins/import-from-yml/" target="_blank"></a></div>
				</div>
				<div class="icp_control">
					<label for="icp_point1"></label>
					<label for="icp_point2"></label>
					<label for="icp_point3"></label>
					<label for="icp_point4"></label>
					<label for="icp_point5"></label>
					<label for="icp_point6"></label>
					<label for="icp_point7"></label>
				</div>
			</div> 
		</div><?php 
	} // end get_html_icp_banners()

	public function get_html_my_plugins_list() { ?>
		<div class="metabox-holder">
			<div class="postbox">
				<h2 class="hndle"><?php _e('My plugins that may interest you', 'yfym'); ?></h2>
				<div class="inside">
					<p><span class="yfym_bold">XML for Google Merchant Center</span> - <?php _e('Сreates a XML-feed to upload to Google Merchant Center', 'yfym'); ?>. <a href="https://wordpress.org/plugins/xml-for-google-merchant-center/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p> 
					<p><span class="yfym_bold">YML for Yandex Market</span> - <?php _e('Сreates a YML-feed for importing your products to Yandex Market', 'yfym'); ?>. <a href="https://wordpress.org/plugins/yml-for-yandex-market/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
					<p><span class="yfym_bold">Import from YML</span> - <?php _e('Imports products from YML to your shop', 'yfym'); ?>. <a href="https://wordpress.org/plugins/import-from-yml/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
					<p><span class="yfym_bold">XML for Hotline</span> - <?php _e('Сreates a XML-feed for importing your products to Hotline', 'yfym'); ?>. <a href="https://wordpress.org/plugins/xml-for-hotline/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
					<p><span class="yfym_bold">Gift upon purchase for WooCommerce</span> - <?php _e('This plugin will add a marketing tool that will allow you to give gifts to the buyer upon purchase', 'yfym'); ?>. <a href="https://wordpress.org/plugins/gift-upon-purchase-for-woocommerce/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
					<p><span class="yfym_bold">Import products to ok.ru</span> - <?php _e('With this plugin, you can import products to your group on ok.ru', 'yfym'); ?>. <a href="https://wordpress.org/plugins/import-products-to-ok-ru/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
					<p><span class="yfym_bold">XML for Avito</span> - <?php _e('Сreates a XML-feed for importing your products to', 'yfym'); ?> Avito. <a href="https://wordpress.org/plugins/xml-for-avito/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
					<p><span class="yfym_bold">XML for O.Yandex (Яндекс Объявления)</span> - <?php _e('Сreates a XML-feed for importing your products to', 'yfym'); ?> Яндекс.Объявления. <a href="https://wordpress.org/plugins/xml-for-o-yandex/" target="_blank"><?php _e('Read more', 'yfym'); ?></a>.</p>
				</div>
			</div>
		</div><?php
	} // end get_html_my_plugins_list()

	public function admin_head_css_func() {
		/* печатаем css в шапке админки */
		print '<style>/* Yml for Yandex Market */
			.metabox-holder .postbox-container .empty-container {height: auto !important;}
			.icp_img1 {background-image: url('. YFYM_PLUGIN_DIR_URL .'img/sl1.jpg);}
			.icp_img2 {background-image: url('. YFYM_PLUGIN_DIR_URL .'img/sl2.jpg);}
			.icp_img3 {background-image: url('. YFYM_PLUGIN_DIR_URL .'img/sl3.jpg);}
			.icp_img4 {background-image: url('. YFYM_PLUGIN_DIR_URL .'img/sl4.jpg);}
			.icp_img5 {background-image: url('. YFYM_PLUGIN_DIR_URL .'img/sl5.jpg);}
			.icp_img6 {background-image: url('. YFYM_PLUGIN_DIR_URL .'img/sl6.jpg);}
			.icp_img7 {background-image: url('. YFYM_PLUGIN_DIR_URL .'img/sl7.jpg);}
		</style>';
	}

	private function init_hooks() {
		// наш класс, вероятно, вызывается во время срабатывания хука admin_menu.
		// admin_init - следующий в очереди срабатывания, хуки раньше admin_menu нет смысла вешать
		// add_action('admin_init', array($this, 'listen_submits'), 10);
		add_action('admin_print_footer_scripts', array($this, 'admin_head_css_func'));
	}

	private function get_feed_id() {
		return $this->feed_id;
	}

	private function save_plugin_set($opt_name, $feed_id, $save_if_empty = false) {
		if (isset($_POST[$opt_name])) {
			yfym_optionUPD($opt_name, sanitize_text_field($_POST[$opt_name]), $feed_id, 'yes', 'set_arr');
		} else {
			if ($save_if_empty === true) {
				yfym_optionUPD($opt_name, '0', $feed_id, 'yes', 'set_arr');
			}
		}
		return;
	}

	private function listen_submit() {
	// массовое удаление фидов по чекбоксу checkbox_xml_file
		if (isset($_GET['yfym_form_id']) && ($_GET['yfym_form_id'] === 'yfym_wp_list_table')) {
			if (is_array($_GET['checkbox_xml_file']) && !empty($_GET['checkbox_xml_file'])) {
				if ($_GET['action'] === 'delete' || $_GET['action2'] === 'delete') {
					$checkbox_xml_file_arr = $_GET['checkbox_xml_file'];
					$yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
					for ($i = 0; $i < count($checkbox_xml_file_arr); $i++) {
						$feed_id = $checkbox_xml_file_arr[$i];
						unset($yfym_settings_arr[$feed_id]);
						wp_clear_scheduled_hook('yfym_cron_period', array($feed_id)); // отключаем крон
						wp_clear_scheduled_hook('yfym_cron_sborki', array($feed_id)); // отключаем крон
						$upload_dir = (object)wp_get_upload_dir();
						$name_dir = $upload_dir->basedir."/yfym";
		//				$filename = $name_dir.'/ids_in_xml.tmp'; if (file_exists($filename)) {unlink($filename);}
						yfym_remove_directory($name_dir.'/feed'.$feed_id);
						yfym_optionDEL('yfym_status_sborki', $i);

						$yfym_registered_feeds_arr = yfym_optionGET('yfym_registered_feeds_arr');
						for ($n = 1; $n < count($yfym_registered_feeds_arr); $n++) { // первый элемент не проверяем, тк. там инфо по последнему id
							if ($yfym_registered_feeds_arr[$n]['id'] === $feed_id) {
								unset($yfym_registered_feeds_arr[$n]);
								$yfym_registered_feeds_arr = array_values($yfym_registered_feeds_arr);
								yfym_optionUPD('yfym_registered_feeds_arr', $yfym_registered_feeds_arr);
								break;
							}
						}
					}
					yfym_optionUPD('yfym_settings_arr', $yfym_settings_arr);
					$feed_id = yfym_get_first_feed_id();
				}
			}
		}

		if (isset($_GET['feed_id'])) {
			if (isset($_GET['action'])) {
				$action = sanitize_text_field($_GET['action']);
				switch ($action) {
					case 'edit':
						$feed_id = sanitize_text_field($_GET['feed_id']);
						break;
					case 'delete':
						$feed_id = sanitize_text_field($_GET['feed_id']);
						$yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
						unset($yfym_settings_arr[$feed_id]);
						wp_clear_scheduled_hook('yfym_cron_period', array($feed_id)); // отключаем крон
						wp_clear_scheduled_hook('yfym_cron_sborki', array($feed_id)); // отключаем крон
						$upload_dir = (object)wp_get_upload_dir();
						$name_dir = $upload_dir->basedir."/yfym";
		//				$filename = $name_dir.'/ids_in_xml.tmp'; if (file_exists($filename)) {unlink($filename);}
						yfym_remove_directory($name_dir.'/feed'.$feed_id);		
						yfym_optionUPD('yfym_settings_arr', $yfym_settings_arr);
						yfym_optionDEL('yfym_status_sborki', $feed_id);					
						$yfym_registered_feeds_arr = yfym_optionGET('yfym_registered_feeds_arr');
						for ($n = 1; $n < count($yfym_registered_feeds_arr); $n++) { // первый элемент не проверяем, тк. там инфо по последнему id
							if ($yfym_registered_feeds_arr[$n]['id'] === $feed_id) {
								unset($yfym_registered_feeds_arr[$n]);
								$yfym_registered_feeds_arr = array_values($yfym_registered_feeds_arr); 
								yfym_optionUPD('yfym_registered_feeds_arr', $yfym_registered_feeds_arr);
								break;
							}
						}
		
						$feed_id = yfym_get_first_feed_id();
						break;
					default:
						$feed_id = yfym_get_first_feed_id();
				}
			} else {$feed_id = sanitize_text_field($_GET['feed_id']);}
		} else {$feed_id = yfym_get_first_feed_id();}

		if (isset($_REQUEST['yfym_submit_add_new_feed'])) { // если создаём новый фид
			if (!empty($_POST) && check_admin_referer('yfym_nonce_action_add_new_feed', 'yfym_nonce_field_add_new_feed')) {
				$yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
				
				if (is_multisite()) {
					$yfym_registered_feeds_arr = get_blog_option(get_current_blog_id(), 'yfym_registered_feeds_arr');
					$feed_id = $yfym_registered_feeds_arr[0]['last_id'];
					$feed_id++;
					$yfym_registered_feeds_arr[0]['last_id'] = (string)$feed_id;
					$yfym_registered_feeds_arr[] = array('id' => (string)$feed_id);
					update_blog_option(get_current_blog_id(), 'yfym_registered_feeds_arr', $yfym_registered_feeds_arr);
				} else {
					$yfym_registered_feeds_arr = get_option('yfym_registered_feeds_arr');
					$feed_id = $yfym_registered_feeds_arr[0]['last_id'];
					$feed_id++;
					$yfym_registered_feeds_arr[0]['last_id'] = (string)$feed_id;
					$yfym_registered_feeds_arr[] = array('id' => (string)$feed_id);
					update_option('yfym_registered_feeds_arr', $yfym_registered_feeds_arr);
				}

				$upload_dir = (object)wp_get_upload_dir();
				$name_dir = $upload_dir->basedir.'/yfym/feed'.$feed_id;
				if (!is_dir($name_dir)) {
					if (!mkdir($name_dir)) {
						error_log('ERROR: Ошибка создания папки '.$name_dir.'; Файл: export.php; Строка: '.__LINE__, 0);
					}
				}

				$def_plugin_date_arr = new YFYM_Data_Arr();
				$yfym_settings_arr[$feed_id] = $def_plugin_date_arr->get_opts_name_and_def_date('all');

				yfym_optionUPD('yfym_settings_arr', $yfym_settings_arr);
		
				yfym_optionADD('yfym_status_sborki', '-1', $feed_id);
				yfym_optionADD('yfym_last_element', '-1', $feed_id);
				print '<div class="updated notice notice-success is-dismissible"><p>'. __('Feed added', 'yfym').'. ID = '.$feed_id.'.</p></div>';
			}
		}

		$status_sborki = (int)yfym_optionGET('yfym_status_sborki', $feed_id);

		if (isset($_REQUEST['yfym_submit_action'])) {
			if (!empty($_POST) && check_admin_referer('yfym_nonce_action', 'yfym_nonce_field')) {
				do_action('yfym_prepend_submit_action', $feed_id);
			
				$feed_id = sanitize_text_field($_POST['yfym_num_feed_for_save']);
			
				$unixtime = current_time('timestamp', 1); // 1335808087 - временная зона GMT (Unix формат)
				yfym_optionUPD('yfym_date_save_set', $unixtime, $feed_id, 'yes', 'set_arr');
				
				if (isset($_POST['yfym_run_cron'])) {
					$arr_maybe = array("off", "five_min", "hourly", "six_hours", "twicedaily", "daily", "week");
					$yfym_run_cron = sanitize_text_field($_POST['yfym_run_cron']);
				
					if (in_array($yfym_run_cron, $arr_maybe)) {		
						yfym_optionUPD('yfym_status_cron', $yfym_run_cron, $feed_id, 'yes', 'set_arr');
						if ($yfym_run_cron === 'off') {
							// отключаем крон
							wp_clear_scheduled_hook('yfym_cron_period', array($feed_id));
							yfym_optionUPD('yfym_status_cron', 'off', $feed_id, 'yes', 'set_arr');
						
							wp_clear_scheduled_hook('yfym_cron_sborki', array($feed_id));
							yfym_optionUPD('yfym_status_sborki', '-1', $feed_id);
						} else {
							$recurrence = $yfym_run_cron;
							wp_clear_scheduled_hook('yfym_cron_period', array($feed_id));
							wp_schedule_event(time(), $recurrence, 'yfym_cron_period', array($feed_id));
							new YFYM_Error_Log('FEED № '.$feed_id.'; yfym_cron_period внесен в список заданий; Файл: export.php; Строка: '.__LINE__);
						}
					} else {
						new YFYM_Error_Log('Крон '.$yfym_run_cron.' не зарегистрирован. Файл: export.php; Строка: '.__LINE__);
					}
				}

				if (isset($_GET['tab']) && $_GET['tab'] === 'tags') {
					if (isset($_POST['yfym_params_arr'])) {
						yfym_optionUPD('yfym_params_arr', serialize($_POST['yfym_params_arr']), $feed_id);
					} else {yfym_optionUPD('yfym_params_arr', serialize(array()), $feed_id);}

					if (isset($_POST['yfym_add_in_name_arr'])) {
						yfym_optionUPD('yfym_add_in_name_arr', serialize($_POST['yfym_add_in_name_arr']), $feed_id);
					} else {yfym_optionUPD('yfym_add_in_name_arr', serialize(array()), $feed_id);}

					if (isset($_POST['yfym_no_group_id_arr'])) {
						yfym_optionUPD('yfym_no_group_id_arr', serialize($_POST['yfym_no_group_id_arr']), $feed_id);
					} else {yfym_optionUPD('yfym_no_group_id_arr', serialize(array()), $feed_id);}
				}

				$def_plugin_date_arr = new YFYM_Data_Arr();
				$opts_name_and_def_date_arr = $def_plugin_date_arr->get_opts_name_and_def_date('public');
				foreach ($opts_name_and_def_date_arr as $key => $value) {
					$save_if_empty = false;
					switch ($key) {
						case 'yfym_status_cron': 
						case 'yfymp_exclude_cat_arr': // селект категорий в прошке
							continue 2;
							break;
						case 'yfym_var_desc_priority':
						case 'yfym_one_variable':
						case 'yfym_skip_missing_products':
						case 'yfym_skip_backorders_products':
						case 'yfym_no_default_png_products':
						/* И галки в прошке */
						case 'yfymp_use_del_vc':
						case 'yfymp_excl_thumb':
						case 'yfymp_use_utm':
							if (!isset($_GET['tab']) || ($_GET['tab'] !== 'filtration')) {
								continue 2;
							} else {
								$save_if_empty = true;
							}
							break;
						case 'yfym_pickup_options':
						case 'yfym_delivery_options':
						case 'yfym_delivery_options2':
							if (!isset($_GET['tab']) || ($_GET['tab'] !== 'tags')) {
								continue 2;
							} else {
								$save_if_empty = true;
							}
							break;							
						case 'yfym_ufup':
							if (isset($_GET['tab']) && ($_GET['tab'] !== 'main_tab')) {
								continue 2;
							} else {
								$save_if_empty = true;
							}
							break;
					}
					$this->save_plugin_set($key, $feed_id, $save_if_empty);
				}

			}
		} 

		$this->feed_id = $feed_id;
		return;
	}
}