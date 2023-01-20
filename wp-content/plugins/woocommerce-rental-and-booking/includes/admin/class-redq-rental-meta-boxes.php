<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* woo-commmercerce meta boxes for redq rental
*/
class Redq_Rental_Meta_Boxes{

	function __construct(){
		add_filter( 'product_type_selector' , array( $this, 'redq_rental_product_type' ) );
		add_filter( 'woocommerce_product_data_tabs' , array( $this, 'redq_rental_additional_tabs' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'redq_rental_additional_tabs_panel' ) );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'redq_rental_general_tab_info' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'redq_rental_save_meta' ) );
		add_action( 'save_post', array($this, 'redq_save_post'));
	}

	public function redq_rental_product_type($product_types){
		$product_types['redq_rental'] = __( 'Rental Product', 'redq-rental' );
		return $product_types;
	}


	public function redq_rental_additional_tabs($product_tabs){

		$product_tabs['rental_inventory'] = array(
			'label'  => __( 'Inventory', 'redq-rental' ),
			'target' => 'rental_inventory_product_data',
			'class'  => array( 'hide_if_grouped','show_if_redq_rental','hide_if_simple','hide_if_external','hide_if_variable' ),
			'priority'=> '80',
		);

		$product_tabs['price_calculation'] = array(
			'label'  => __( 'Price Calculation', 'redq-rental' ),
			'target' => 'price_calculation_product_data',
			'class'  => array( 'hide_if_grouped','show_if_redq_rental','hide_if_simple','hide_if_external','hide_if_variable' ),
			'priority'=> '90',
		);

		$product_tabs['price_discount'] = array(
			'label'  => __( 'Price Discount', 'redq-rental' ),
			'target' => 'price_discount_product_data',
			'class'  => array( 'hide_if_grouped','show_if_redq_rental','hide_if_simple','hide_if_external','hide_if_variable' ),
			'priority'=> '100'
		);

		$product_tabs['settings'] = array(
			'label'  => __( 'Settings', 'redq-rental' ),
			'target' => 'product_settings_data',
			'class'  => array( 'hide_if_grouped','show_if_redq_rental','hide_if_simple','hide_if_external','hide_if_variable'   ),
			'priority'=> '110'
		);

		return $product_tabs;
	}


	public function redq_rental_general_tab_info(){
		global $post;
		$post_id = $post->ID;
		include( 'views/redq-rental-general-tab.php' );
	}


	public function redq_rental_additional_tabs_panel() {
		global $post;
		$post_id = $post->ID;
		include( 'views/redq-rental-additional-tabs-panel.php' );
	}



	public function redq_save_post($post_id){

		$pricing_type = get_post_meta(get_the_ID(),'pricing_type',true);
		$gproduct = wc_get_product($post_id);
		if(isset($gproduct) && !empty($gproduct)){
			$product_type = wc_get_product($post_id)->get_type();
		}

		if(isset($product_type) && $product_type === 'redq_rental'){
			if($pricing_type == 'general_pricing'){
				$general_pricing = get_post_meta($post_id,'general_price',true);
				update_post_meta($post_id,'_price',$general_pricing);
			}

			if($pricing_type === 'daily_pricing'){
				$daily_pricing = get_post_meta($post_id,'redq_daily_pricing',true);
				$today = date('N');
				switch ($today) {
					case '7':
						update_post_meta($post_id, '_price' , $daily_pricing['sunday']);
						break;
					case '1':
						update_post_meta($post_id, '_price' , $daily_pricing['monday']);
						break;
					case '2':
						update_post_meta($post_id, '_price' , $daily_pricing['tuesday']);
						break;
					case '3':
						update_post_meta($post_id, '_price' , $daily_pricing['wednesday']);
						break;
					case '4':
						update_post_meta($post_id, '_price' , $daily_pricing['thursday']);
						break;
					case '5':
						update_post_meta($post_id, '_price' , $daily_pricing['friday']);
						break;
					case '6':
						update_post_meta($post_id, '_price' , $daily_pricing['saturday']);
						break;
					default:
						update_post_meta($post_id, '_price' , 'Daily price not set');
						break;
				}
			}

			if($pricing_type === 'monthly_pricing'){
				$monthly_pricing = get_post_meta($post_id,'redq_monthly_pricing',true);
				$current_month = date('m');
				switch ($current_month) {
					case '1':
						update_post_meta($post_id, '_price' , $monthly_pricing['january']);
						break;
					case '2':
						update_post_meta($post_id, '_price' , $monthly_pricing['february']);
						break;
					case '3':
						update_post_meta($post_id, '_price' , $monthly_pricing['march']);
						break;
					case '4':
						update_post_meta($post_id, '_price' , $monthly_pricing['april']);
						break;
					case '5':
						update_post_meta($post_id, '_price' , $monthly_pricing['may']);
						break;
					case '6':
						update_post_meta($post_id, '_price' , $monthly_pricing['june']);
						break;
					case '7':
						update_post_meta($post_id, '_price' , $monthly_pricing['july']);
						break;
					case '8':
						update_post_meta($post_id, '_price' , $monthly_pricing['august']);
						break;
					case '9':
						update_post_meta($post_id, '_price' , $monthly_pricing['september']);
						break;
					case '10':
						update_post_meta($post_id, '_price' , $monthly_pricing['october']);
						break;
					case '11':
						update_post_meta($post_id, '_price' , $monthly_pricing['november']);
						break;
					case '12':
						update_post_meta($post_id, '_price' , $monthly_pricing['december']);
						break;
					default:
						update_post_meta($post_id, '_price' , 'Daily price not set');
						break;
				}
			}

			if($pricing_type === 'days_range'){
				$day_ranges_cost = get_post_meta($post_id,'redq_day_ranges_cost',true);
				update_post_meta($post_id, '_price' , $day_ranges_cost[0]['range_cost']);
			}
		}

	}


	public function redq_rental_save_meta($post_id){


		if(isset($_POST['pricing_type'])){
			update_post_meta( $post_id, 'pricing_type', $_POST['pricing_type'] );
		}

		if(isset($_POST['general_price'])){
			update_post_meta( $post_id, 'general_price', $_POST['general_price'] );
		}

		if(isset($_POST['perkilo_price'])){
			update_post_meta( $post_id, 'perkilo_price', $_POST['perkilo_price'] );
		}

		if(isset($_POST['hourly_price'])){
			update_post_meta( $post_id, 'hourly_price', $_POST['hourly_price'] );
		}

		$daily_prices = array();

		if(isset($_POST['friday_price'])){
			$daily_prices['friday'] = $_POST['friday_price'];
		}

		if(isset($_POST['saturday_price'])){
			$daily_prices['saturday'] = $_POST['saturday_price'];
		}

		if(isset($_POST['sunday_price'])){
			$daily_prices['sunday'] = $_POST['sunday_price'];
		}

		if(isset($_POST['monday_price'])){
			$daily_prices['monday'] = $_POST['monday_price'];
		}

		if(isset($_POST['tuesday_price'])){
			$daily_prices['tuesday'] = $_POST['tuesday_price'];
		}

		if(isset($_POST['wednesday_price'])){
			$daily_prices['wednesday'] = $_POST['wednesday_price'];
		}

		if(isset($_POST['thursday_price'])){
			$daily_prices['thursday'] = $_POST['thursday_price'];
		}

		update_post_meta( $post_id, 'redq_daily_pricing', $daily_prices );


		// Monthly pricing meta

		$monthly_price = array();

		if(isset($_POST['january_price'])){
			$monthly_price['january'] = $_POST['january_price'];
		}

		if(isset($_POST['february_price'])){
			$monthly_price['february'] = $_POST['february_price'];
		}

		if(isset($_POST['march_price'])){
			$monthly_price['march'] = $_POST['march_price'];
		}

		if(isset($_POST['april_price'])){
			$monthly_price['april'] = $_POST['april_price'];
		}

		if(isset($_POST['may_price'])){
			$monthly_price['may'] = $_POST['may_price'];
		}

		if(isset($_POST['june_price'])){
			$monthly_price['june'] = $_POST['june_price'];
		}

		if(isset($_POST['july_price'])){
			$monthly_price['july'] = $_POST['july_price'];
		}

		if(isset($_POST['august_price'])){
			$monthly_price['august'] = $_POST['august_price'];
		}

		if(isset($_POST['september_price'])){
			$monthly_price['september'] = $_POST['september_price'];
		}

		if(isset($_POST['october_price'])){
			$monthly_price['october'] = $_POST['october_price'];
		}

		if(isset($_POST['november_price'])){
			$monthly_price['november'] = $_POST['november_price'];
		}

		if(isset($_POST['december_price'])){
			$monthly_price['december'] = $_POST['december_price'];
		}

		update_post_meta( $post_id, 'redq_monthly_pricing', $monthly_price );


		// Day ranges data save
		$days_range_cost = array();
		if(isset($_POST['redq_min_days']) && isset($_POST['redq_max_days']) && isset($_POST['redq_days_range_cost'])){
			$min_days = $_POST['redq_min_days'];
			$max_days = $_POST['redq_max_days'];
			$range_cost = $_POST['redq_days_range_cost'];
			$cost_applicable = $_POST['redq_day_range_cost_applicable'];
			for($i=0; $i<sizeof($min_days); $i++){
				$days_range_cost[$i]['min_days'] = $min_days[$i];
				$days_range_cost[$i]['max_days'] = $max_days[$i];
				$days_range_cost[$i]['range_cost'] = $range_cost[$i];
				$days_range_cost[$i]['cost_applicable'] = $cost_applicable[$i];
			}
		}
		if(isset($days_range_cost)){
			update_post_meta( $post_id, 'redq_day_ranges_cost', $days_range_cost );
		}

		// Price discount data
		$price_discount_cost = array();
		if(isset($_POST['redq_price_discount_min_days']) && isset($_POST['redq_price_discount_max_days'])){
			$price_discount_min_days = $_POST['redq_price_discount_min_days'];
			$price_discount_max_days = $_POST['redq_price_discount_max_days'];
			$price_discount_type = $_POST['redq_price_discount_type'];
			$price_discount_amount = $_POST['redq_price_discount'];
			for($i=0; $i<sizeof($price_discount_min_days); $i++){
				$price_discount_cost[$i]['min_days'] = $price_discount_min_days[$i];
				$price_discount_cost[$i]['max_days'] = $price_discount_max_days[$i];
				$price_discount_cost[$i]['discount_type'] = $price_discount_type[$i];
				$price_discount_cost[$i]['discount_amount'] = $price_discount_amount[$i];
			}
		}
		if(isset($price_discount_cost)){
			update_post_meta( $post_id, 'redq_price_discount_cost', $price_discount_cost );
		}



		//inventory management data

		// $arr = get_post_meta($post_id, 'redq_block_dates_and_times', true);
		// $allowed = get_post_meta($post_id, 'inventory_child_posts', true);
		// $updated_dates_times = array_intersect_key($arr, array_flip($allowed));
		// update_post_meta($post_id, 'redq_block_dates_and_times', $updated_dates_times);

		if(isset($_POST['rental_inventory_count'])){
			update_post_meta( $post_id, 'redq_rental_inventory_count', $_POST['rental_inventory_count'] );
		}



		// Choose settings

		if(isset($_POST['rnb_settings_for_display'])){
			update_post_meta( $post_id, 'rnb_settings_for_display', $_POST['rnb_settings_for_display'] );
		}
		if(isset($_POST['rnb_settings_for_labels'])){
			update_post_meta( $post_id, 'rnb_settings_for_labels', $_POST['rnb_settings_for_labels'] );
		}
		if(isset($_POST['rnb_settings_for_conditions'])){
			update_post_meta( $post_id, 'rnb_settings_for_conditions', $_POST['rnb_settings_for_conditions'] );
		}
		if(isset($_POST['rnb_settings_for_validations'])){
			update_post_meta( $post_id, 'rnb_settings_for_validations', $_POST['rnb_settings_for_validations'] );
		}



		$settings_data = array();

		if(isset($_POST['block_rental_dates'])){
			update_post_meta( $post_id, 'redq_block_general_dates', $_POST['block_rental_dates'] );
		}

		if(isset($_POST['choose_date_format'])){
			update_post_meta( $post_id, 'redq_calendar_date_format', $_POST['choose_date_format'] );
        }
        
        if(isset($_POST['choose_time_format'])){
			update_post_meta( $post_id, 'redq_calendar_time_format', $_POST['choose_time_format'] );
		}

		if($_POST['choose_date_format'] === 'd/m/Y'){
			update_post_meta( $post_id, 'redq_choose_european_date_format', 'yes');
		}else{
			update_post_meta( $post_id, 'redq_choose_european_date_format', 'no');
		}

		$choose_euro_format = get_post_meta($post_id, 'redq_choose_european_date_format', true);

		if(isset($_POST['redq_max_time_late'])){
			update_post_meta( $post_id, 'redq_max_time_late', $_POST['redq_max_time_late'] );
		}

		if(isset($_POST['redq_max_rental_days'])){
			update_post_meta( $post_id, 'redq_max_rental_days', $_POST['redq_max_rental_days'] );
		}

		if(isset($_POST['redq_min_rental_days'])){
			update_post_meta( $post_id, 'redq_min_rental_days', $_POST['redq_min_rental_days'] );
		}

		if(isset($_POST['redq_rental_starting_block_dates'])){
			update_post_meta( $post_id, 'redq_rental_starting_block_dates', $_POST['redq_rental_starting_block_dates'] );
		}

		if(isset($_POST['redq_rental_before_booking_block_dates'])){
			update_post_meta( $post_id, 'redq_rental_before_booking_block_dates', $_POST['redq_rental_before_booking_block_dates'] );
		}

		if(isset($_POST['redq_rental_post_booking_block_dates'])){
			update_post_meta( $post_id, 'redq_rental_post_booking_block_dates', $_POST['redq_rental_post_booking_block_dates'] );
		}

		if(isset($_POST['redq_time_interval'])){
			update_post_meta( $post_id, 'redq_time_interval', $_POST['redq_time_interval'] );
		}

		if(isset($_POST['redq_allowed_times'])){
			update_post_meta( $post_id, 'redq_allowed_times', $_POST['redq_allowed_times'] );
		}

		if(isset($_POST['rnb_booking_layout'])){
			update_post_meta( $post_id, 'rnb_booking_layout', $_POST['rnb_booking_layout'] );
		}

		if(isset($_POST['redq_rental_local_enable_single_day_time_based_booking'])){
			update_post_meta( $post_id, 'redq_rental_local_enable_single_day_time_based_booking', $_POST['redq_rental_local_enable_single_day_time_based_booking'] );
		}else{
			update_post_meta( $post_id, 'redq_rental_local_enable_single_day_time_based_booking', 'closed' );
		}


		if(isset($_POST['redq_rental_local_quantity_on_days'])){
			update_post_meta( $post_id, 'redq_rental_local_quantity_on_days', $_POST['redq_rental_local_quantity_on_days'] );
		}else{
			update_post_meta( $post_id, 'redq_rental_local_quantity_on_days', 'closed' );
		}


		if(isset($_POST['redq_rental_off_days'])){
			update_post_meta( $post_id, 'redq_rental_off_days', $_POST['redq_rental_off_days'] );
		}else{
			$em = array();
			update_post_meta( $post_id, 'redq_rental_off_days', $em );
		}


		if(isset($_POST['redq_show_pricing_flipbox_text'])){
			update_post_meta( $post_id, 'redq_show_pricing_flipbox_text', $_POST['redq_show_pricing_flipbox_text'] );
		}

		if(isset($_POST['redq_flip_pricing_plan_text'])){
			update_post_meta( $post_id, 'redq_flip_pricing_plan_text', $_POST['redq_flip_pricing_plan_text'] );
		}

		if(isset($_POST['redq_pickup_location_heading_title'])){
			update_post_meta( $post_id, 'redq_pickup_location_heading_title', $_POST['redq_pickup_location_heading_title'] );
		}
		if(isset($_POST['redq_pickup_loc_placeholder'])){
			update_post_meta( $post_id, 'redq_pickup_loc_placeholder', $_POST['redq_pickup_loc_placeholder'] );
		}
		if(isset($_POST['redq_dropoff_location_heading_title'])){
			update_post_meta( $post_id, 'redq_dropoff_location_heading_title', $_POST['redq_dropoff_location_heading_title'] );
		}
		if(isset($_POST['redq_return_loc_placeholder'])){
			update_post_meta( $post_id, 'redq_return_loc_placeholder', $_POST['redq_return_loc_placeholder'] );
		}

		if(isset($_POST['redq_pickup_date_heading_title'])){
			update_post_meta( $post_id, 'redq_pickup_date_heading_title', $_POST['redq_pickup_date_heading_title'] );
		}

		if(isset($_POST['redq_pickup_date_placeholder'])){
			update_post_meta( $post_id, 'redq_pickup_date_placeholder', $_POST['redq_pickup_date_placeholder'] );
		}

		if(isset($_POST['redq_pickup_time_placeholder'])){
			update_post_meta( $post_id, 'redq_pickup_time_placeholder', $_POST['redq_pickup_time_placeholder'] );
		}


		if(isset($_POST['redq_dropoff_date_heading_title'])){
			update_post_meta( $post_id, 'redq_dropoff_date_heading_title', $_POST['redq_dropoff_date_heading_title'] );
		}


		if(isset($_POST['redq_dropoff_date_placeholder'])){
			update_post_meta( $post_id, 'redq_dropoff_date_placeholder', $_POST['redq_dropoff_date_placeholder'] );
		}

		if(isset($_POST['redq_dropoff_time_placeholder'])){
			update_post_meta( $post_id, 'redq_dropoff_time_placeholder', $_POST['redq_dropoff_time_placeholder'] );
		}

		if(isset($_POST['rnb_quantity_label'])){
			update_post_meta( $post_id, 'rnb_quantity_label', $_POST['rnb_quantity_label'] );
		}

		if(isset($_POST['redq_rnb_cat_heading'])){
			update_post_meta( $post_id, 'redq_rnb_cat_heading', $_POST['redq_rnb_cat_heading'] );
		}

		if(isset($_POST['redq_resources_heading_title'])){
			update_post_meta( $post_id, 'redq_resources_heading_title', $_POST['redq_resources_heading_title'] );
		}
		if(isset($_POST['redq_adults_heading_title'])){
			update_post_meta( $post_id, 'redq_adults_heading_title', $_POST['redq_adults_heading_title'] );
		}

		if(isset($_POST['redq_adults_placeholder'])){
			update_post_meta( $post_id, 'redq_adults_placeholder', $_POST['redq_adults_placeholder'] );
		}

		if(isset($_POST['redq_childs_heading_title'])){
			update_post_meta( $post_id, 'redq_childs_heading_title', $_POST['redq_childs_heading_title'] );
		}

		if(isset($_POST['redq_childs_placeholder'])){
			update_post_meta( $post_id, 'redq_childs_placeholder', $_POST['redq_childs_placeholder'] );
		}

		if(isset($_POST['redq_discount_text_title'])){
			update_post_meta( $post_id, 'redq_discount_text_title', $_POST['redq_discount_text_title'] );
		}

		if(isset($_POST['redq_instance_pay_text_title'])){
			update_post_meta( $post_id, 'redq_instance_pay_text_title', $_POST['redq_instance_pay_text_title'] );
		}

		if(isset($_POST['redq_total_cost_text_title'])){
			update_post_meta( $post_id, 'redq_total_cost_text_title', $_POST['redq_total_cost_text_title'] );
		}

		if(isset($_POST['redq_book_now_button_text'])){
			update_post_meta( $post_id, 'redq_book_now_button_text', $_POST['redq_book_now_button_text'] );
		}

		if(isset($_POST['redq_rfq_button_text'])){
			update_post_meta( $post_id, 'redq_rfq_button_text', $_POST['redq_rfq_button_text'] );
		}

		if(isset($_POST['redq_security_deposite_heading_title'])){
			update_post_meta( $post_id, 'redq_security_deposite_heading_title', $_POST['redq_security_deposite_heading_title'] );
		}

		if(isset($_POST['redq_rental_local_show_pickup_date']) && !empty($_POST['redq_rental_local_show_pickup_date'])){
			update_post_meta( $post_id, 'redq_rental_local_show_pickup_date', $_POST['redq_rental_local_show_pickup_date'] );
		}else{
			update_post_meta( $post_id, 'redq_rental_local_show_pickup_date', 'closed' );
		}

		if(isset($_POST['redq_rental_local_show_pickup_time']) && !empty($_POST['redq_rental_local_show_pickup_time'])){
			update_post_meta( $post_id, 'redq_rental_local_show_pickup_time', $_POST['redq_rental_local_show_pickup_time'] );
		}else{
			update_post_meta( $post_id, 'redq_rental_local_show_pickup_time', 'closed' );
		}

		if(isset($_POST['redq_rental_local_show_dropoff_date']) && !empty($_POST['redq_rental_local_show_dropoff_date'])){
			update_post_meta( $post_id, 'redq_rental_local_show_dropoff_date', $_POST['redq_rental_local_show_dropoff_date'] );
		}else{
			update_post_meta( $post_id, 'redq_rental_local_show_dropoff_date', 'closed' );
		}

		if(isset($_POST['redq_rental_local_show_dropoff_time']) && !empty($_POST['redq_rental_local_show_dropoff_time'])){
			update_post_meta( $post_id, 'redq_rental_local_show_dropoff_time', $_POST['redq_rental_local_show_dropoff_time'] );
		}else{
			update_post_meta( $post_id, 'redq_rental_local_show_dropoff_time', 'closed' );
		}

		if(isset($_POST['rnb_enable_quantity']) && !empty($_POST['rnb_enable_quantity'])){
			update_post_meta( $post_id, 'rnb_enable_quantity', $_POST['rnb_enable_quantity'] );
		}else{
			update_post_meta( $post_id, 'rnb_enable_quantity', 'closed' );
		}

		if(isset($_POST['redq_rental_local_show_pricing_flip_box']) && !empty($_POST['redq_rental_local_show_pricing_flip_box'])){
			update_post_meta( $post_id, 'redq_rental_local_show_pricing_flip_box', $_POST['redq_rental_local_show_pricing_flip_box'] );
		}else{
			update_post_meta( $post_id, 'redq_rental_local_show_pricing_flip_box', 'closed' );
		}

		if(isset($_POST['redq_rental_local_show_price_discount_on_days']) && !empty($_POST['redq_rental_local_show_price_discount_on_days'])){
			update_post_meta( $post_id, 'redq_rental_local_show_price_discount_on_days', $_POST['redq_rental_local_show_price_discount_on_days'] );
		}else{
			update_post_meta( $post_id, 'redq_rental_local_show_price_discount_on_days', 'closed' );
		}


		if(isset($_POST['redq_rental_local_show_price_instance_payment']) && !empty($_POST['redq_rental_local_show_price_instance_payment'])){
			update_post_meta( $post_id, 'redq_rental_local_show_price_instance_payment', $_POST['redq_rental_local_show_price_instance_payment'] );
		}else{
			update_post_meta( $post_id, 'redq_rental_local_show_price_instance_payment', 'closed' );
		}

	    if(isset($_POST['redq_rental_local_show_request_quote']) && !empty($_POST['redq_rental_local_show_request_quote'])){
	      	update_post_meta( $post_id, 'redq_rental_local_show_request_quote', $_POST['redq_rental_local_show_request_quote'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_local_show_request_quote', 'closed' );
	    }

	    if(isset($_POST['redq_rental_local_show_book_now']) && !empty($_POST['redq_rental_local_show_book_now'])){
	      	update_post_meta( $post_id, 'redq_rental_local_show_book_now', $_POST['redq_rental_local_show_book_now'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_local_show_book_now', 'closed' );
	    }


	    // Required section
	    if(isset($_POST['redq_rental_local_required_pickup_location']) && !empty($_POST['redq_rental_local_required_pickup_location'])){
	      	update_post_meta( $post_id, 'redq_rental_local_required_pickup_location', $_POST['redq_rental_local_required_pickup_location'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_local_required_pickup_location', 'closed' );
	    }

	    if(isset($_POST['redq_rental_local_required_return_location']) && !empty($_POST['redq_rental_local_required_return_location'])){
	      	update_post_meta( $post_id, 'redq_rental_local_required_return_location', $_POST['redq_rental_local_required_return_location'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_local_required_return_location', 'closed' );
	    }

	    if(isset($_POST['redq_rental_local_required_person']) && !empty($_POST['redq_rental_local_required_person'])){
	      	update_post_meta( $post_id, 'redq_rental_local_required_person', $_POST['redq_rental_local_required_person'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_local_required_person', 'closed' );
	    }

	    if(isset($_POST['redq_rental_required_local_pickup_time']) && !empty($_POST['redq_rental_required_local_pickup_time'])){
	      	update_post_meta( $post_id, 'redq_rental_required_local_pickup_time', $_POST['redq_rental_required_local_pickup_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_required_local_pickup_time', 'closed' );
	    }


	    if(isset($_POST['redq_rental_required_local_return_time']) && !empty($_POST['redq_rental_required_local_return_time'])){
	      	update_post_meta( $post_id, 'redq_rental_required_local_return_time', $_POST['redq_rental_required_local_return_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_required_local_return_time', 'closed' );
	    }

	    // Daily basis openning and closing time

	    if(isset($_POST['redq_rental_fri_min_time']) && !empty($_POST['redq_rental_fri_min_time'])){
	      	update_post_meta( $post_id, 'redq_rental_fri_min_time', $_POST['redq_rental_fri_min_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_fri_min_time', '00:00' );
	    }

	    if(isset($_POST['redq_rental_fri_max_time']) && !empty($_POST['redq_rental_fri_max_time'])){
	      	update_post_meta( $post_id, 'redq_rental_fri_max_time', $_POST['redq_rental_fri_max_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_fri_max_time', '24:00' );
	    }

	    if(isset($_POST['redq_rental_sat_min_time']) && !empty($_POST['redq_rental_sat_min_time'])){
	      	update_post_meta( $post_id, 'redq_rental_sat_min_time', $_POST['redq_rental_sat_min_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_sat_min_time', '00:00' );
	    }

	    if(isset($_POST['redq_rental_sat_max_time']) && !empty($_POST['redq_rental_sat_max_time'])){
	      	update_post_meta( $post_id, 'redq_rental_sat_max_time', $_POST['redq_rental_sat_max_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_sat_max_time', '24:00' );
	    }

	    if(isset($_POST['redq_rental_sun_min_time']) && !empty($_POST['redq_rental_sun_min_time'])){
	      	update_post_meta( $post_id, 'redq_rental_sun_min_time', $_POST['redq_rental_sun_min_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_sun_min_time', '00:00' );
	    }

	    if(isset($_POST['redq_rental_sun_max_time']) && !empty($_POST['redq_rental_sun_max_time'])){
	      	update_post_meta( $post_id, 'redq_rental_sun_max_time', $_POST['redq_rental_sun_max_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_sun_max_time', '24:00' );
	    }

	    if(isset($_POST['redq_rental_mon_min_time']) && !empty($_POST['redq_rental_mon_min_time'])){
	      	update_post_meta( $post_id, 'redq_rental_mon_min_time', $_POST['redq_rental_mon_min_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_mon_min_time', '00:00' );
	    }

	    if(isset($_POST['redq_rental_mon_max_time']) && !empty($_POST['redq_rental_mon_max_time'])){
	      	update_post_meta( $post_id, 'redq_rental_mon_max_time', $_POST['redq_rental_mon_max_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_mon_max_time', '24:00' );
	    }

	    if(isset($_POST['redq_rental_thu_min_time']) && !empty($_POST['redq_rental_thu_min_time'])){
	      	update_post_meta( $post_id, 'redq_rental_thu_min_time', $_POST['redq_rental_thu_min_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_thu_min_time', '00:00' );
	    }

	    if(isset($_POST['redq_rental_thu_max_time']) && !empty($_POST['redq_rental_thu_max_time'])){
	      	update_post_meta( $post_id, 'redq_rental_thu_max_time', $_POST['redq_rental_thu_max_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_thu_max_time', '24:00' );
	    }

	    if(isset($_POST['redq_rental_wed_min_time']) && !empty($_POST['redq_rental_wed_min_time'])){
	      	update_post_meta( $post_id, 'redq_rental_wed_min_time', $_POST['redq_rental_wed_min_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_wed_min_time', '00:00' );
	    }

	    if(isset($_POST['redq_rental_wed_max_time']) && !empty($_POST['redq_rental_wed_max_time'])){
	      	update_post_meta( $post_id, 'redq_rental_wed_max_time', $_POST['redq_rental_wed_max_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_wed_max_time', '24:00' );
	    }

	    if(isset($_POST['redq_rental_thur_min_time']) && !empty($_POST['redq_rental_thur_min_time'])){
	      	update_post_meta( $post_id, 'redq_rental_thur_min_time', $_POST['redq_rental_thur_min_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_thur_min_time', '00:00' );
	    }

	    if(isset($_POST['redq_rental_thur_max_time']) && !empty($_POST['redq_rental_thur_max_time'])){
	      	update_post_meta( $post_id, 'redq_rental_thur_max_time', $_POST['redq_rental_thur_max_time'] );
	    }else{
	      	update_post_meta( $post_id, 'redq_rental_thur_max_time', '24:00' );
	    }

	}


}

new Redq_Rental_Meta_Boxes();
