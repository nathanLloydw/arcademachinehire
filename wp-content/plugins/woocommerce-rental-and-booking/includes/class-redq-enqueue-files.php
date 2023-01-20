<?php

/**
*
*/
class Rnb_Enqueue_Files {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_styles_and_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles_and_scripts' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'rnb_screen_ids' ) );
	}


	/**
	 * Frontend enqueues front-end stylesheet and scripts
	 *
	 * @since 1.0.0
	 * @return null
	 */
	public function frontend_styles_and_scripts(){

		$post_id = get_the_ID();
		$get_product = wc_get_product( $post_id );

		if(isset($get_product) && !empty($get_product)){
			$product_type = $get_product->get_type();
		}

		wp_register_script( 'quote-handle', REDQ_ROOT_URL . '/assets/js/quote.js', array( 'jquery'), false, true );
		wp_enqueue_script('quote-handle');

		wp_localize_script('quote-handle' , 'REDQ_MYACCOUNT_API', array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
		));

		if(isset($product_type) && $product_type === 'redq_rental'){

			$rnb_data = array();
			$general_data 		= reddq_rental_get_settings(  $post_id, 'general' );
	        $labels 			= reddq_rental_get_settings(  $post_id, 'labels', array('pickup_location', 'return_location', 'pickup_date', 'return_date', 'resources', 'categories', 'person', 'deposites') );
      		$layout_two_labels 	= reddq_rental_get_settings(  $post_id, 'layout_two', array( 'datetime', 'location', 'resource', 'person', 'deposit', 'resources' ) );
      		$displays 			= reddq_rental_get_settings(  $post_id, 'display' );
      		$conditions 		= reddq_rental_get_settings(  $post_id, 'conditions' );
			$validations 		= reddq_rental_get_settings(  $post_id, 'validations' );

			$gmap_enable = get_option( 'rnb_enable_gmap' );
			$map_key = get_option( 'rnb_gmap_api_key' );
			$check_ssl = is_ssl() ? 'https' : 'http';

			wp_register_style('rental-global', REDQ_ROOT_URL. '/assets/css/rental-global.css', array(), $ver = false, $media = 'all');
	  		wp_enqueue_style('rental-global');

			wp_register_script( 'jquery.datetimepicker.full', REDQ_ROOT_URL . '/assets/js/jquery.datetimepicker.full.js', array( 'jquery'), true );
			wp_enqueue_script('jquery.datetimepicker.full');

			wp_register_style( 'jquery.datetimepicker', REDQ_ROOT_URL . '/assets/css/jquery.datetimepicker.css', array(), $ver = false, $media = 'all' );
			wp_enqueue_style('jquery.datetimepicker');

			wp_register_script( 'sweetalert.min', REDQ_ROOT_URL . '/assets/js/sweetalert.min.js', array( 'jquery'), true );
			wp_enqueue_script('sweetalert.min');

			wp_register_script( 'chosen.jquery', REDQ_ROOT_URL . '/assets/js/chosen.jquery.js', array( 'jquery'), true );
			wp_enqueue_script('chosen.jquery');

			wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
			wp_enqueue_style('ion-icon', ''.$check_ssl.'://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');

	  		wp_register_script( 'jquery.steps', REDQ_ROOT_URL . '/assets/js/jquery.steps.js', array( 'jquery'), true );
			wp_enqueue_script('jquery.steps');

			wp_register_style('jquery.steps', REDQ_ROOT_URL. '/assets/css/jquery.steps.css', array(), $ver = false, $media = 'all');
	  		wp_enqueue_style('jquery.steps');

			wp_register_style('sweetalert', REDQ_ROOT_URL. '/assets/css/sweetalert.css', array(), $ver = false, $media = 'all');
	  		wp_enqueue_style('sweetalert');

	        wp_register_script( 'sweetalert-forms', REDQ_ROOT_URL . '/assets/js/swal-forms.js', array( 'jquery'), false, true );
	        wp_enqueue_script('sweetalert-forms');

	        wp_register_style('sweetalert-forms', REDQ_ROOT_URL. '/assets/css/swal-forms.css', array(), $ver = false, $media = 'all');
	        wp_enqueue_style('sweetalert-forms');

	  		wp_register_style('chosen', REDQ_ROOT_URL. '/assets/css/chosen.css', array(), $ver = false, $media = 'all');
	  		wp_enqueue_style('chosen');

	  		wp_register_style('rental-style', REDQ_ROOT_URL. '/assets/css/rental-style.css', array(), $ver = false, $media = 'all');
	  		wp_enqueue_style('rental-style');

	        wp_register_style('magnific-popup', REDQ_ROOT_URL. '/assets/css/magnific-popup.css', array(), $ver = false, $media = 'all');
	        wp_enqueue_style('magnific-popup');

			wp_register_script( 'date', REDQ_ROOT_URL . '/assets/js/date.js', array( 'jquery'), true );
			wp_enqueue_script('date');

			wp_register_script( 'accounting', REDQ_ROOT_URL . '/assets/js/accounting.js', array( 'jquery'), true );
			wp_enqueue_script('accounting');

			wp_register_script( 'jquery.flip', REDQ_ROOT_URL . '/assets/js/jquery.flip.js', array( 'jquery'), true );
			wp_enqueue_script('jquery.flip');

	        wp_register_script( 'magnific-popup', REDQ_ROOT_URL . '/assets/js/jquery.magnific-popup.min.js', array( 'jquery'), true );
	        wp_enqueue_script('magnific-popup');

	        if(isset($gmap_enable) && $gmap_enable ==='yes' && isset($map_key) && !empty($map_key) && $conditions['conditions']['booking_layout'] !== 'layout_one' ){

		       	$markers = array(
							'pickup' 		=> REDQ_ROOT_URL. '/assets/img/marker-pickup.png',
							'destination' 	=> REDQ_ROOT_URL. '/assets/img/marker-destination.png'
						);

		        wp_register_script('google-map-api', '//maps.googleapis.com/maps/api/js?key='.$map_key.'&libraries=places,geometry&language=en-US' , true , false );
		        wp_enqueue_script('google-map-api');

		        wp_register_script( 'rnb-map', REDQ_ROOT_URL . '/assets/js/rnb-map.js', array( 'jquery'), true );
				wp_enqueue_script('rnb-map');

				wp_localize_script('rnb-map', 'RNB_MAP', array(
								'markers' => $markers,
								'pickup_title' => esc_html__('Pickup Point', 'redq-rental'),
								'dropoff_title' => esc_html__('DropOff Point', 'redq-rental'),
		        ));
		    }


			wp_register_script( 'preselected-cost-handle', REDQ_ROOT_URL . '/assets/js/preselected-cost-handle.js', array( 'jquery'), true );
			wp_enqueue_script('preselected-cost-handle');

			wp_register_script( 'cost-handle', REDQ_ROOT_URL . '/assets/js/cost-handle.js', array( 'jquery'), true );
			wp_enqueue_script('cost-handle');

			wp_register_script( 'front-end-scripts', REDQ_ROOT_URL . '/assets/js/main-script.js', array( 'jquery'), true );
			wp_enqueue_script('front-end-scripts');


			redq_update_prices();

      		$rnb_data['settings']['general'] 			= $general_data['general'];
			$rnb_data['settings']['labels'] 			= $labels['labels'];
			$rnb_data['settings']['displays'] 			= $displays['display'];
			$rnb_data['settings']['conditions'] 		= $conditions['conditions'];
			$rnb_data['settings']['validations'] 		= $validations['validations'];
			$rnb_data['settings']['layout_two_labels'] 	= $layout_two_labels;

			$pricing_data = redq_rental_get_pricing_data( $post_id );
			$rnb_data['pricings'] = $pricing_data;

			$block_dates 			= calculate_block_dates();
			$cart_dates 			= rental_product_in_cart( $post_id );
			$starting_block_days 	= reddq_rental_staring_block_days( $post_id );

			$holiday_list 				= $rnb_data['settings']['general']['holidays'];
			$holidays 				= reddq_rental_handle_holidays( $post_id, $holiday_list );

			$buffer_dates = array_merge( $starting_block_days, $cart_dates, $holidays );
			$availability = reqd_rental_availability_data($post_id);

			$woocommerce_info = array(
		        'symbol' => get_woocommerce_currency_symbol(),
		        'currency' => get_woocommerce_currency(),
		        'thousand' => wc_get_price_thousand_separator(),
		        'decimal' => wc_get_price_decimal_separator(),
		        'number' => wc_get_price_decimals(),
		        'position' => get_option('woocommerce_currency_pos'),
		    );

			$translated_strings = array(
		        'singular_max_booking_day_msg' 	=> __('Max booking day is ', 'redq-rental'),
		        'plural_max_booking_days_msg' 	=> __('Max booking days are ', 'redq-rental'),
		        'opps' 						=> __('Ooops', 'redq-rental'),
		        'unavailable_date_range' 	=> __('This date range is unavailable', 'redq-rental'),
		        'singular_min_booking_day_msg' 			=> __('Min rental day is ', 'redq-rental'),
		        'plural_min_booking_days_msg' 			=> __('Min rental days are ', 'redq-rental'),
		        'exceed_text' 				=> __(' exceed ', 'redq-rental'),
		        'max_booking_days' 			=> __('Max rental days ', 'redq-rental'),
		        'quote_user_name' 			=> __('User name field is required', 'redq-rental'),
		        'quote_password' 			=> __('Password field is required', 'redq-rental'),
		        'quote_first_name' 			=> __('First name field is required', 'redq-rental'),
		        'quote_last_name' 			=> __('Last name field is required', 'redq-rental'),
		        'quote_email' 				=> __('Quote email is required', 'redq-rental'),
		        'quote_phone' 				=> __('Phone is required', 'redq-rental'),
				'quote_message' 			=> __('Message is required', 'redq-rental'),
				'positive_days'				=> __('No of days must be greater than 1 day', 'redq-rental'),
				'positive_hours'			=> __('Total hours must be greater than 0 hours', 'redq-rental'),
				'pickup_loc_required'		=> __('Pickup location is required', 'redq-rental'),
				'dropoff_loc_required'		=> __('Drop-off location is required', 'redq-rental'),
				'pickup_time_required'		=> __('Pickup time is required', 'redq-rental'),
				'dorpoff_time_required'		=> __('Drop-off time is required', 'redq-rental'),
				'adult_required'			=> __('Adults is required', 'redq-rental'),
				'child_required'			=> __('Child is required', 'redq-rental'),
				'qty_plural_msg'			=> __('quantities are not avaialable', 'redq-rental'),
				'qty_singular_msg'			=> __('quantity is not avaialable', 'redq-rental'),
		    );

			$localize_info = array(
				'domain' => $general_data['general']['lang_domain'],
				'months'  => $general_data['general']['months'],
				'weekdays' => $general_data['general']['weekdays']
			);

			wp_localize_script('cost-handle', 'BOOKING_DATA', array(
				'rnb_data' 				=> $rnb_data,
				'block_dates' 			=> $block_dates,
				'woocommerce_info' 		=> $woocommerce_info,
				'translated_strings' 	=> $translated_strings,
				'availability'      	=> $availability,
				'buffer_days'			=> $buffer_dates,
			));

			wp_localize_script('front-end-scripts', 'CALENDAR_DATA', array(
				'availability'      	=> $availability,
				'calendar_props'		=> $rnb_data,
				'block_dates'   		=> $block_dates,
				'localize_info' 		=> $localize_info,
				'translated_strings' 	=> $translated_strings,
				'buffer_days'			=> $buffer_dates,
			));

	        wp_localize_script('front-end-scripts', 'REDQ_RENTAL_API', array(
	          	'ajax_url'  => admin_url( 'admin-ajax.php' ),
	        ));
		}

		wp_register_style('rental-quote', REDQ_ROOT_URL. '/assets/css/quote-front.css', array(), $ver = false, $media = 'all');
		wp_enqueue_style('rental-quote');
	}


	public function rnb_screen_ids($screen_ids){

		$screen_ids[] = 'toplevel_page_rnb_admin';
		$screen_ids[] = 'edit-request_quote';
		$screen_ids[] = 'edit-inventory';
		$screen_ids[] = 'inventory';
		$screen_ids[] = 'edit-resource';
		$screen_ids[] = 'edit-rnb_categories';
		$screen_ids[] = 'edit-resource';
		$screen_ids[] = 'edit-person';
		$screen_ids[] = 'edit-deposite';
		$screen_ids[] = 'edit-attributes';
		$screen_ids[] = 'edit-features';
		$screen_ids[] = 'edit-pickup_location';
		$screen_ids[] = 'edit-dropoff_location';

		return $screen_ids;
	}


	/**
	 * Plugin enqueues admin stylesheet and scripts
	 *
	 * @since 1.0.0
	 * @return null
	 */
	public function admin_styles_and_scripts($hook){

		global $woocommerce;
		$screen         = get_current_screen();
		$screen_id      = $screen ? $screen->id : '';

		wp_register_script( 'jquery-ui-js',REDQ_ROOT_URL . '/assets/js/jquery-ui.js', array('jquery'), $ver = true, true);
		wp_register_style('jquery-ui-css', REDQ_ROOT_URL. '/assets/css/jquery-ui.css', array(), $ver = false, $media = 'all');
		wp_register_script( 'select2.min',REDQ_ROOT_URL . '/assets/js/select2.min.js', array('jquery'), $ver = true, true);
		wp_register_style('redq-admin', REDQ_ROOT_URL. '/assets/css/redq-admin.css', array(), $ver = false, $media = 'all');
	  	wp_register_style('redq-quote', REDQ_ROOT_URL. '/assets/css/redq-quote.css', array(), $ver = false, $media = 'all');
	  	wp_register_script( 'icon-picker',REDQ_ROOT_URL . '/assets/js/icon-picker.js', array('jquery'), $ver = true, true);
	  	wp_register_script( 'redq_rental_writepanel_js', REDQ_ROOT_URL . '/assets/js/writepanel.js', array( 'jquery', 'jquery-ui-datepicker' ), true );

		// Admin styles for WC , Inventory & RFQ pages only
		if ( in_array( $screen_id, wc_get_screen_ids() ) && $screen_id !== 'shop_coupon' ) {

			$postid = get_the_ID();

			$params = array(
				'plugin_url'        => $woocommerce->plugin_url(),
				'ajax_url'          => admin_url( 'admin-ajax.php' ),
				'calendar_image'    => $woocommerce->plugin_url() . '/assets/images/calendar.png',
			);

			if(isset($postid) && !empty($postid)){
				$post_type = get_post_type($postid);
		  		$post_id = isset($post_type) && $post_type === 'inventory' ? wp_get_post_parent_id( $postid ) : $postid;
		  		$conditions = reddq_rental_get_settings( $post_id, 'conditions' );
		  		$admin_data = $conditions['conditions'];
		  		$params['calendar_data'] = $admin_data;
			}

			wp_enqueue_script( 'jquery-ui-js' );
			wp_enqueue_style('jquery-ui-css');
			wp_enqueue_script( 'select2.min' );
			wp_enqueue_style('redq-admin');
			wp_enqueue_style('redq-quote');
			wp_enqueue_script( 'icon-picker' );
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_media();
			wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
			wp_enqueue_script('redq_rental_writepanel_js');
			wp_localize_script( 'redq_rental_writepanel_js', 'RNB_ADMIN_DATA', $params );
		}
	}


}


new Rnb_Enqueue_Files();



