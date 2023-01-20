<?php
if (!defined('ABSPATH'))
    exit;
/**
 * Hande cart page
 *
 * @version 5.0.0
 * @since 1.0.0
 */
class WC_Redq_Rental_Cart
{

    public function __construct()
    {
        add_filter('woocommerce_add_cart_item_data', array($this, 'redq_rental_add_cart_item_data'), 20, 2);
        add_filter('woocommerce_add_cart_item', array($this, 'redq_rental_add_cart_item'), 20, 1);
        add_filter('woocommerce_get_cart_item_from_session', array($this, 'redq_rental_get_cart_item_from_session'), 20, 2);
        add_filter('woocommerce_cart_item_quantity', array($this, 'redq_cart_item_quantity'), 20, 2);
        add_filter('woocommerce_add_to_cart_validation', array($this, 'redq_add_to_cart_validation'), 20, 3);
        add_action('woocommerce_checkout_process', array($this, 'redq_rental_checkout_order_process'), 20, 3);
        add_filter('woocommerce_get_item_data', array($this, 'redq_rental_get_item_data'), 20, 2);
        add_action('woocommerce_new_order_item', array($this, 'redq_rental_order_item_meta'), 20, 3);
        add_action('woocommerce_thankyou', array($this, 'woocommerce_thankyou'), 20, 1);
        add_action('wp_ajax_quote_booking_data', array($this, 'quote_booking_data'));
    }


    /**
     * If checkout failed during an AJAX call, send failure response.
     */
    protected function send_ajax_failure_response()
    {
        if (is_ajax()) {
            // only print notices if not reloading the checkout, otherwise they're lost in the page reload
            if (!isset(WC()->session->reload_checkout)) {
                ob_start();
                wc_print_notices();
                $messages = ob_get_clean();
            }

            $response = array(
                'result' => 'failure',
                'messages' => isset($messages) ? $messages : '',
                'refresh' => isset(WC()->session->refresh_totals),
                'reload' => isset(WC()->session->reload_checkout),
            );

            unset(WC()->session->refresh_totals, WC()->session->reload_checkout);

            wp_send_json($response);
        }
    }


    public function woocommerce_thankyou($order_id)
    {
        $order = new WC_Order($order_id);
        $items = $order->get_items();

        foreach ($items as $item) {
            foreach ($item['item_meta'] as $key => $value) {
                if ($key === 'Quote Request') {
                    wp_update_post(array(
                        'ID' => $value[0],
                        'post_status' => 'quote-completed'
                    ));
                }
            }
        }
    }


    /**
     * Insert posted data into cart item meta
     *
     * @param  string $product_id , array $cart_item_meta
     * @return array
     */
    public function redq_rental_add_cart_item_data($cart_item_meta, $product_id)
    {
        $product_type = wc_get_product($product_id)->get_type();
        if (isset($product_type) && $product_type === 'redq_rental' && !isset($cart_item_meta['rental_data']['quote_id'])) {
            $posted_data = $this->get_posted_data($product_id, $_POST);
            $cart_item_meta['rental_data'] = $posted_data;
        }
        return $cart_item_meta;
    }


    /**
     * Add cart item meta
     *
     * @param  array $cart_item
     * @return array
     */
    public function redq_rental_add_cart_item($cart_item)
    {

        $product_id = $cart_item['data']->get_id();
        $product_type = wc_get_product($product_id)->get_type();
        if (isset($cart_item['rental_data']['quote_id']) && $product_type === 'redq_rental') {
            $cart_item['data']->set_price(get_post_meta($cart_item['rental_data']['quote_id'], '_quote_price', true));
        } else {
            if (isset($cart_item['rental_data']['rental_days_and_costs']['cost']) && $product_type === 'redq_rental') {
                $cart_item['data']->set_price($cart_item['rental_data']['rental_days_and_costs']['cost']);
            }

            if (isset($cart_item['quantity']) && $product_type === 'redq_rental') {
                $cart_item['quantity'] = isset($cart_item['rental_data']['quantity']) ? $cart_item['rental_data']['quantity'] : 1;
            }
        }

        return $cart_item;
    }


    /**
     * Get item data from session
     *
     * @param  array $cart_item
     * @return array
     */
    public function redq_rental_get_cart_item_from_session($cart_item, $values)
    {

        if (!empty($values['rental_data'])) {
            $cart_item = $this->redq_rental_add_cart_item($cart_item);
        }
        return $cart_item;
    }


    /**
     * Set quanlity always 1
     *
     * @param  array $cart_item_key , int $product_quantity
     * @return int
     */
    public function redq_cart_item_quantity($product_quantity, $cart_item_key)
    {
        global $woocommerce;
        $cart_details = $woocommerce->cart->cart_contents;

        if (isset($cart_details)) {
            foreach ($cart_details as $key => $value) {
                if ($key === $cart_item_key) {
                    $product_id = $value['product_id'];
                    $product_type = wc_get_product($product_id)->get_type();
                    if ($product_type === 'redq_rental') {
                        return $value['quantity'] ? $value['quantity'] : 1;
                    } else {
                        return $product_quantity;
                    }
                }
            }
        }
    }


    /**
     * Set Validation
     *
     * @param  array $valid , int $product_id, int $quantity
     * @return boolean
     */
    public function redq_add_to_cart_validation($valid, $product_id, $quantity)
    {
        return $valid;
    }


    /**
     * Show cart item data in cart and checkout page
     *
     * @param  blank array $custom_data , array $cart_item
     * @return array
     */
    public function redq_rental_get_item_data($custom_data, $cart_item)
    {

        $product_id = $cart_item['data']->get_id();
        $product_type = wc_get_product($product_id)->get_type();

        if (isset($product_type) && $product_type === 'redq_rental') {

            $rental_data = $cart_item['rental_data'];

            $options_data = array();
            $options_data['quote_id'] = '';

            $get_labels = reddq_rental_get_settings($product_id, 'labels', array('pickup_location', 'return_location', 'pickup_date', 'return_date', 'resources', 'categories', 'person', 'deposites'));
            $labels = $get_labels['labels'];
            $get_displays = reddq_rental_get_settings($product_id, 'display');
            $displays = $get_displays['display'];

            $get_conditions = reddq_rental_get_settings($product_id, 'conditions');
            $conditional_data = $get_conditions['conditions'];

            $get_general = reddq_rental_get_settings($product_id, 'general');
            $general_data = $get_general['general'];

            if (isset($rental_data) && !empty($rental_data)) {
                if (isset($rental_data['quote_id'])) {
                    $custom_data[] = array(
                        'name' => $options_data['quote_id'] ? $options_data['quote_id'] : __('Quote Request', 'redq-rental'),
                        'value' => '#' . $rental_data['quote_id'],
                        'display' => ''
                    );
                }

                if (isset($rental_data['pickup_location'])) {
                    $custom_data[] = array(
                        'name' => $labels['pickup_location'],
                        'value' => $rental_data['pickup_location']['address'],
                        'display' => ''
                    );
                }

                if (isset($rental_data['pickup_location']) && !empty($rental_data['pickup_location']['cost'])) {
                    $custom_data[] = array(
                        'name' => $labels['pickup_location'] . __(' Cost', 'redq-rental'),
                        'value' => wc_price($rental_data['pickup_location']['cost']),
                        'display' => ''
                    );
                }

                if (isset($rental_data['dropoff_location'])) {
                    $custom_data[] = array(
                        'name' => $labels['return_location'],
                        'value' => $rental_data['dropoff_location']['address'],
                        'display' => ''
                    );
                }

                if (isset($rental_data['dropoff_location']) && !empty($rental_data['dropoff_location']['cost'])) {
                    $custom_data[] = array(
                        'name' => $labels['return_location'] . __(' Cost', 'redq-rental'),
                        'value' => wc_price($rental_data['dropoff_location']['cost']),
                        'display' => ''
                    );
                }

                if (isset($rental_data['location_cost'])) {
                    $custom_data[] = array(
                        'name' => esc_html__('Location Cost', 'redq-rental'),
                        'value' => wc_price($rental_data['location_cost']),
                        'display' => ''
                    );
                }

                if (isset($rental_data['payable_cat'])) {
                    $cat_name = '';
                    foreach ($rental_data['payable_cat'] as $key => $value) {
                        if ($value['multiply'] === 'per_day') {
                            $cat_name .= $value['name'] . '×' . $value['quantity'] . ' ( ' . wc_price($value['cost']) . ' - ' . __('Per Day', 'redq-rental') . ' )' . ' , <br> ';
                        } else {
                            $cat_name .= $value['name'] . '×' . $value['quantity'] . ' ( ' . wc_price($value['cost']) . ' - ' . __('One Time', 'redq-rental') . ' )' . ' , <br> ';
                        }
                    }
                    $custom_data[] = array(
                        'name' => $labels['categories'],
                        'value' => $cat_name,
                        'display' => ''
                    );
                }

                if (isset($rental_data['payable_resource'])) {
                    $resource_name = '';
                    foreach ($rental_data['payable_resource'] as $key => $value) {
                        if ($value['cost_multiply'] === 'per_day') {
                            $resource_name .= $value['resource_name'] . ' ( ' . wc_price($value['resource_cost']) . ' - ' . __('Per Day', 'redq-rental') . ' )' . ' , <br> ';
                        } else {
                            $resource_name .= $value['resource_name'] . ' ( ' . wc_price($value['resource_cost']) . ' - ' . __('One Time', 'redq-rental') . ' )' . ' , <br> ';
                        }
                    }
                    $custom_data[] = array(
                        'name' => $labels['resource'],
                        'value' => $resource_name,
                        'display' => ''
                    );
                }

                if (isset($rental_data['payable_security_deposites'])) {
                    $security_deposite_name = '';
                    foreach ($rental_data['payable_security_deposites'] as $key => $value) {
                        if ($value['cost_multiply'] === 'per_day') {
                            $security_deposite_name .= $value['security_deposite_name'] . ' ( ' . wc_price($value['security_deposite_cost']) . ' - ' . __('Per Day', 'redq-rental') . ' )' . ' , <br> ';
                        } else {
                            $security_deposite_name .= $value['security_deposite_name'] . ' ( ' . wc_price($value['security_deposite_cost']) . ' - ' . __('One Time', 'redq-rental') . ' )' . ' , <br> ';
                        }
                    }
                    $custom_data[] = array(
                        'name' => $labels['deposite'],
                        'value' => $security_deposite_name,
                        'display' => ''
                    );
                }

                if (isset($rental_data['adults_info'])) {
                    $custom_data[] = array(
                        'name' => $labels['adults'],
                        'value' => $rental_data['adults_info']['person_count'],
                        'display' => ''
                    );
                }

                if (isset($rental_data['childs_info'])) {
                    $custom_data[] = array(
                        'name' => $labels['childs'],
                        'value' => $rental_data['childs_info']['person_count'],
                        'display' => ''
                    );
                }


                if (isset($rental_data['pickup_date']) && $displays['pickup_date'] === 'open') {

                    $pickup_date_time = convert_to_output_format($rental_data['pickup_date'], $conditional_data['date_format']);

                    if (isset($rental_data['pickup_time'])) {
                        $pickup_date_time = $pickup_date_time . ' ' . esc_html__('at', 'redq-rental') . ' ' . $rental_data['pickup_time'];
                    }
                    $custom_data[] = array(
                        'name' => $labels['pickup_datetime'],
                        'value' => $pickup_date_time,
                        'display' => ''
                    );
                }

                if (isset($rental_data['dropoff_date']) && $displays['return_date'] === 'open') {

                    $return_date_time = convert_to_output_format($rental_data['dropoff_date'], $conditional_data['date_format']);

                    if (isset($rental_data['dropoff_time'])) {
                        $return_date_time = $return_date_time . ' ' . esc_html__('at', 'redq-rental') . ' ' . $rental_data['dropoff_time'];
                    }

                    $custom_data[] = array(
                        'name' => $labels['return_datetime'],
                        'value' => $return_date_time,
                        'display' => ''
                    );
                }

                if (isset($rental_data['rental_days_and_costs'])) {
                    if ($rental_data['rental_days_and_costs']['days'] > 0) {
                        $custom_data[] = array(
                            'name' => $general_data['total_days'] ? $general_data['total_days'] : esc_html__('Total Days', 'redq-rental'),
                            'value' => $rental_data['rental_days_and_costs']['days'],
                            'display' => ''
                        );
                    } else {
                        $custom_data[] = array(
                            'name' => $general_data['total_hours'] ? $general_data['total_hours'] : esc_html__('Total Hours', 'redq-rental'),
                            'value' => $rental_data['rental_days_and_costs']['hours'],
                            'display' => ''
                        );
                    }

                    if (!empty($rental_data['rental_days_and_costs']['due_payment'])) {
                        $custom_data[] = array(
                            'name' => $general_data['payment_due'] ? $general_data['payment_due'] : esc_html__('Due Payment', 'redq-rental'),
                            'value' => wc_price($rental_data['rental_days_and_costs']['due_payment']),
                            'display' => ''
                        );
                    }
                }
            }
        }

        return $custom_data;
    }


    /**
     * Checking Processed Data
     *
     * @param  string order_id , array $posted_data, object order
     * @return array
     */
    public function redq_rental_checkout_order_process()
    {

        $cart_items = WC()->cart->get_cart();

        //Check available quantity from cart items
        if (isset($cart_items) && !empty($cart_items)) :
            $product_dates_count = [];
        $final_product_dates = [];
        $product_ids = [];
        $product_quantities = [];

        foreach ($cart_items as $cart_item) {

            $date_count = [];
            $product_id = $cart_item['product_id'];
            $product_type = wc_get_product($product_id)->get_type();

            if (isset($product_type) && $product_type !== 'redq_rental') return;

            $rental_data = $cart_item['rental_data'];
            $cart_days = $rental_data['rental_days_and_costs']['days'];

            if ($cart_days <= 0) return;

            $quantity = isset($cart_item['quantity']) ? $cart_item['quantity'] : 1;

            $dates = $rental_data['rental_days_and_costs']['booked_dates']['saved'];

            foreach ($dates as $key => $value) {
                for ($i = 0; $i < $quantity; $i++) {
                    $date_count[] = $value;
                }
            }
            $product_dates_count[][$product_id] = $date_count;
            $product_ids[] = $product_id;
            $product_quantities[$product_id] = $quantity;
        }

        foreach ($product_ids as $product_key => $product_id) {
            $final_product_dates[$product_id] = redq_rental_array_flatten(array_merge(array_column($product_dates_count, $product_id)));
        }

        if (isset($final_product_dates) && !empty($final_product_dates)) {
            foreach ($final_product_dates as $key => $value) {
                $inv_qty = $product_quantities[$key];
                $cart_qty = max(array_count_values($value));
                if ($cart_qty > $inv_qty) {
                    wc_add_notice(sprintf(__('Quantity is not available for <strong><a href="%1s">%2s</a><strong> in your selected dates ', 'redq-rental'), get_permalink($key), get_the_title($key)), 'error');
                    $this->send_ajax_failure_response();
                }
            }
        }
        endif;


        //Checking available quantity in both cart item and previously booked dates
        if (isset($cart_items) && !empty($cart_items)) :
            foreach ($cart_items as $cart_item) {

            $quantity_ara = array();
            $product_id = $cart_item['product_id'];
            $product_type = wc_get_product($product_id)->get_type();

            if (isset($product_type) && $product_type !== 'redq_rental') return;

            $quantity = isset($cart_item['quantity']) ? $cart_item['quantity'] : 1;
            $rental_data = $cart_item['rental_data'];
            $dates = $rental_data['rental_days_and_costs']['booked_dates']['saved'];
            $booked_dates = get_post_meta($product_id, 'redq_block_dates_and_times', true);

            foreach ($dates as $key => $date) {
                $count_qty = 0;
                foreach ($booked_dates as $bkey => $booked_date) {
                    if (!in_array($date, $booked_date)) {
                        $count_qty++;
                    }
                }
                $quantity_ara[] = $count_qty;
            }

            $available_qty = min($quantity_ara);

            if ($quantity > $available_qty) {
                wc_add_notice(sprintf(__('Quantity %s is not available', 'redq-rental'), $quantity), 'error');
                $this->send_ajax_failure_response();
            }
        }
        endif;
        //End checking available quantity


        //Checking for unavailable dates
        if (isset($cart_items) && !empty($cart_items)) :
            $flag = false;
        $matched = array();
        $reported_dates = array();
        foreach ($cart_items as $cart_item) {
            $count = 0;
            $first = 0;
            $product_id = $cart_item['product_id'];

            $rental_data = $cart_item['rental_data'];
            $dates = $rental_data['rental_days_and_costs']['booked_dates']['saved'];
            $dates = reddq_rental_booking_extra_block_days($product_id, $dates);

            $booked_dates = get_post_meta($product_id, 'redq_block_dates_and_times', true);
            $inventory_size = sizeof($booked_dates);

            if (isset($booked_dates) && !empty($booked_dates)) {
                foreach ($booked_dates as $key => $value) {
                    $matched = array_intersect($dates, $value['only_block_dates']);
                    if (!empty($matched)) {
                        $count++;
                    }

                    if ($first === 0) {
                        $first_matched = $matched;
                        $first++;
                    }

                    $reported_dates = array_intersect($first_matched, $matched);
                }
            }

            if ($count >= $inventory_size) {
                $flag = true;
                break;
            }
        }

        if ($flag) {
            $reported_dates = implode(',', $reported_dates);
            wc_add_notice(sprintf(__('This order can not be processed. As %s dates are not available', 'redq-rental'), $reported_dates), 'error');
            $this->send_ajax_failure_response();
        }
        endif;
    }



    /**
     * order_item_meta function
     *
     * @param  string $item_id , array $values
     * @return array
     */
    public function redq_rental_order_item_meta($item_id, $values, $order_id)
    {

        if (array_key_exists('legacy_values', $values)) {
            $product_id = $values->legacy_values['product_id'];
            $product_type = wc_get_product($product_id)->get_type();
        }

        if (isset($product_type) && $product_type === 'redq_rental') {

            $rental_data = $values->legacy_values['rental_data'];

            $options_data = array();
            $options_data['quote_id'] = '';
            $quantity = isset($rental_data['quantity']) ? $rental_data['quantity'] : 1;

            $get_labels = reddq_rental_get_settings($product_id, 'labels', array('pickup_location', 'return_location', 'pickup_date', 'return_date', 'resources', 'categories', 'person', 'deposites'));
            $labels = $get_labels['labels'];
            $get_displays = reddq_rental_get_settings($product_id, 'display');
            $displays = $get_displays['display'];
            $get_conditions = reddq_rental_get_settings($product_id, 'conditions');
            $conditional_data = $get_conditions['conditions'];
            $get_general = reddq_rental_get_settings($product_id, 'general');
            $general_data = $get_general['general'];

            if (isset($rental_data['quote_id'])) {
                wc_add_order_item_meta($item_id, $options_data['quote_id'] ? $options_data['quote_id'] : __('Quote Request', 'redq-rental'), $rental_data['quote_id']);
            }

            if (isset($rental_data['pickup_location'])) {
                wc_add_order_item_meta($item_id, $labels['pickup_location'], $rental_data['pickup_location']['address']);
            }

            if (isset($rental_data['pickup_location']) && !empty($rental_data['pickup_location']['cost'])) {
                wc_add_order_item_meta($item_id, $labels['pickup_location'] . __(' Cost', 'redq-rental'), wc_price($rental_data['pickup_location']['cost']));
            }

            if (isset($rental_data['dropoff_location'])) {
                wc_add_order_item_meta($item_id, $labels['return_location'], $rental_data['dropoff_location']['address']);
            }

            if (isset($rental_data['dropoff_location']) && !empty($rental_data['dropoff_location']['cost'])) {
                wc_add_order_item_meta($item_id, $labels['return_location'] . __(' Cost', 'redq-rental'), wc_price($rental_data['dropoff_location']['cost']));
            }

            if (isset($rental_data['location_cost']) && !empty($rental_data['location_cost'])) {
                wc_add_order_item_meta($item_id, esc_html__('Location Cost', 'redq-rental'), wc_price($rental_data['location_cost']));
            }

            if (isset($rental_data['payable_cat'])) {
                $rnb_cat = '';
                foreach ($rental_data['payable_cat'] as $key => $value) {
                    if ($value['multiply'] === 'per_day') {
                        $rnb_cat .= $value['name'] . '×' . $value['quantity'] . ' ( ' . wc_price($value['cost']) . ' - ' . __('Per Day', 'redq-rental') . ' )' . ' , <br> ';
                    } else {
                        $rnb_cat .= $value['name'] . '×' . $value['quantity'] . ' ( ' . wc_price($value['cost']) . ' - ' . __('One Time', 'redq-rental') . ' )' . ' , <br> ';
                    }
                }
                wc_add_order_item_meta($item_id, $labels['categories'], $rnb_cat);
            }

            if (isset($rental_data['payable_resource'])) {
                $resource_name = '';
                foreach ($rental_data['payable_resource'] as $key => $value) {
                    if ($value['cost_multiply'] === 'per_day') {
                        $resource_name .= $value['resource_name'] . ' ( ' . wc_price($value['resource_cost']) . ' - ' . __('Per Day', 'redq-rental') . ' )' . ' , <br> ';
                    } else {
                        $resource_name .= $value['resource_name'] . ' ( ' . wc_price($value['resource_cost']) . ' - ' . __('One Time', 'redq-rental') . ' )' . ' , <br> ';
                    }
                }
                wc_add_order_item_meta($item_id, $labels['resource'], $resource_name);
            }

            if (isset($rental_data['payable_security_deposites'])) {
                $security_deposite_name = '';
                foreach ($rental_data['payable_security_deposites'] as $key => $value) {
                    if ($value['cost_multiply'] === 'per_day') {
                        $security_deposite_name .= $value['security_deposite_name'] . ' ( ' . wc_price($value['security_deposite_cost']) . ' - ' . __('Per Day', 'redq-rental') . ' )' . ' , <br> ';
                    } else {
                        $security_deposite_name .= $value['security_deposite_name'] . ' ( ' . wc_price($value['security_deposite_cost']) . ' - ' . __('One Time', 'redq-rental') . ' )' . ' , <br> ';
                    }
                }
                wc_add_order_item_meta($item_id, $labels['deposite'], $security_deposite_name);
            }

            if (isset($rental_data['adults_info'])) {
                wc_add_order_item_meta($item_id, $labels['adults'], $rental_data['adults_info']['person_count']);
            }

            if (isset($rental_data['childs_info'])) {
                wc_add_order_item_meta($item_id, $labels['childs'], $rental_data['childs_info']['person_count']);
            }

            if (isset($rental_data['pickup_date']) && $displays['pickup_date'] === 'open') {

                $pickup_date_time = convert_to_output_format($rental_data['pickup_date'], $conditional_data['date_format']);

                $ptime = '';

                if (isset($rental_data['pickup_time'])) {
                    $pickup_date_time = $pickup_date_time . ' ' . esc_html__('at', 'redq-rental') . ' ' . $rental_data['pickup_time'];
                    $ptime = $rental_data['pickup_time'];
                }

                wc_add_order_item_meta($item_id, $labels['pickup_datetime'], $pickup_date_time);
                wc_add_order_item_meta($item_id, 'pickup_hidden_datetime', $rental_data['pickup_date'] . '|' . $ptime);
            }

            if (isset($rental_data['dropoff_date']) && $displays['return_date'] === 'open') {

                $return_date_time = convert_to_output_format($rental_data['dropoff_date'], $conditional_data['date_format']);
                $rtime = '';

                if (isset($rental_data['dropoff_time'])) {
                    $return_date_time = $return_date_time . ' ' . esc_html__('at', 'redq-rental') . ' ' . $rental_data['dropoff_time'];
                    $rtime = $rental_data['dropoff_time'];
                }

                wc_add_order_item_meta($item_id, $labels['return_datetime'], $return_date_time);
                wc_add_order_item_meta($item_id, 'return_hidden_datetime', $rental_data['dropoff_date'] . '|' . $rtime);
            }

            if (isset($rental_data['rental_days_and_costs'])) {
                if ($rental_data['rental_days_and_costs']['days'] > 0) {
                    wc_add_order_item_meta($item_id, $general_data['total_days'] ? $general_data['total_days'] : esc_html__('Total Days', 'redq-rental'), $rental_data['rental_days_and_costs']['days']);
                    wc_add_order_item_meta($item_id, 'return_hidden_days', $rental_data['rental_days_and_costs']['days']);
                } else {
                    wc_add_order_item_meta($item_id, $general_data['total_hours'] ? $general_data['total_hours'] : esc_html__('Total Hours', 'redq-rental'), $rental_data['rental_days_and_costs']['hours']);
                }

                if (!empty($rental_data['rental_days_and_costs']['due_payment'])) {
                    wc_add_order_item_meta($item_id, $general_data['payment_due'] ? $general_data['payment_due'] : esc_html__('Due Payment', 'redq-rental'), wc_price($rental_data['rental_days_and_costs']['due_payment']));
                }
            }

            // Start inventory post meta update from here
            $booked_dates_ara = isset($rental_data['rental_days_and_costs']['booked_dates']['saved']) ? $rental_data['rental_days_and_costs']['booked_dates']['saved'] : array();
            rnb_process_rental_order_data($product_id, $order_id, $item_id, $booked_dates_ara, $quantity);

        }
    }


    // AJAX ADD TO CART FROM QUOTE
    public function quote_booking_data()
    {

        $quote_id = $_POST['quote_id'];
        $product_id = $_POST['product_id'];
        $cart_data = array();
        $posted_data = array();

        $quote_meta = json_decode(get_post_meta($quote_id, 'order_quote_meta', true), true);

        if (isset($quote_meta) && is_array($quote_meta)) :
            foreach ($quote_meta as $key => $value) {
            if (isset($quote_meta[$key]['name'])) :
                $posted_data[$quote_meta[$key]['name']] = $quote_meta[$key]['value'];
            endif;
        }
        endif;


        $posted_data['quote_id'] = $quote_id;
        $ajax_data = $this->get_posted_data($product_id, $posted_data);
        $cart_data['rental_data'] = $ajax_data;
        if (WC()->cart->add_to_cart($product_id, $quantity = 1, $variation_id = '', $variation = '', $cart_data)) {
            echo json_encode(array(
                'success' => true,
            ));
        }

        wp_die();
    }


    /**
     * Return all post data for rental
     *
     * @param  string $product_id , array $posted_data
     * @return array
     */
    public function get_posted_data($product_id, $posted_data)
    {

        $payable_cat = array();
        $payable_resource = array();
        $payable_security_deposites = array();
        $adults_info = array();
        $childs_info = array();
        $pickup_location = array();
        $dropoff_location = array();
        $data = array();

        $conditional_data = reddq_rental_get_settings($product_id, 'conditions');
        $pricing_data = redq_rental_get_pricing_data($product_id);
        $conditional_data = $conditional_data['conditions'];
        $euro_format = $conditional_data['euro_format'];


        if (isset($posted_data['quote_id']) && !empty($posted_data['quote_id'])) {
            $data['quote_id'] = $posted_data['quote_id'];
        }

        if (isset($posted_data['categories']) && !empty($posted_data['categories'])) {
            foreach ($posted_data['categories'] as $key => $value) {
                $categories = explode('|', $value);
                $payable_cat[$key]['name'] = $categories[0];
                $payable_cat[$key]['cost'] = $categories[1];
                $payable_cat[$key]['multiply'] = $categories[2];
                $payable_cat[$key]['hourly_cost'] = $categories[3];
                $payable_cat[$key]['quantity'] = $categories[4];
            }
            $data['payable_cat'] = $payable_cat;
        }

        if (isset($posted_data['extras']) && !empty($posted_data['extras'])) {
            foreach ($posted_data['extras'] as $key => $value) {
                $extras = explode('|', $value);
                $payable_resource[$key]['resource_name'] = $extras[0];
                $payable_resource[$key]['resource_cost'] = $extras[1];
                $payable_resource[$key]['cost_multiply'] = $extras[2];
                $payable_resource[$key]['resource_hourly_cost'] = $extras[3];
            }
            $data['payable_resource'] = $payable_resource;
        }

        if (isset($posted_data['security_deposites']) && !empty($posted_data['security_deposites'])) {
            foreach ($posted_data['security_deposites'] as $key => $value) {
                $extras = explode('|', $value);
                $payable_security_deposites[$key]['security_deposite_name'] = $extras[0];
                $payable_security_deposites[$key]['security_deposite_cost'] = $extras[1];
                $payable_security_deposites[$key]['cost_multiply'] = $extras[2];
                $payable_security_deposites[$key]['security_deposite_hourly_cost'] = $extras[3];
            }
            $data['payable_security_deposites'] = $payable_security_deposites;
        }

        if (isset($posted_data['additional_adults_info']) && !empty($posted_data['additional_adults_info'])) {

            $person = explode('|', $posted_data['additional_adults_info']);
            $adults_info['person_count'] = $person[0];
            $adults_info['person_cost'] = $person[1];
            $adults_info['cost_multiply'] = $person[2];
            $adults_info['person_hourly_cost'] = $person[3];

            $data['adults_info'] = $adults_info;
        }


        if (isset($posted_data['additional_childs_info']) && !empty($posted_data['additional_childs_info'])) {

            $person = explode('|', $posted_data['additional_childs_info']);
            $childs_info['person_count'] = $person[0];
            $childs_info['person_cost'] = $person[1];
            $childs_info['cost_multiply'] = $person[2];
            $childs_info['person_hourly_cost'] = $person[3];

            $data['childs_info'] = $childs_info;
        }


        if ($conditional_data['booking_layout'] === 'layout_one') {
            if (isset($posted_data['pickup_location']) && !empty($posted_data['pickup_location'])) {
                $pickup_location_split = explode('|', $posted_data['pickup_location']);
                $pickup_location['address'] = $pickup_location_split[0];
                $pickup_location['title'] = $pickup_location_split[1];
                $pickup_location['cost'] = $pickup_location_split[2];
                $data['pickup_location'] = $pickup_location;
            }

            if (isset($posted_data['dropoff_location']) && !empty($posted_data['dropoff_location'])) {

                $dropoff_location_split = explode('|', $posted_data['dropoff_location']);
                $dropoff_location['address'] = $dropoff_location_split[0];
                $dropoff_location['title'] = $dropoff_location_split[1];
                $dropoff_location['cost'] = $dropoff_location_split[2];

                $data['dropoff_location'] = $dropoff_location;
            }
        } else {
            if (isset($posted_data['pickup_location']) && !empty($posted_data['pickup_location'])) {
                $pickup_location['address'] = $posted_data['pickup_location'];
                $pickup_location['title'] = $posted_data['pickup_location'];
                $data['pickup_location'] = $pickup_location;
            }
            if (isset($posted_data['dropoff_location']) && !empty($posted_data['dropoff_location'])) {
                $dropoff_location['address'] = $posted_data['dropoff_location'];
                $dropoff_location['title'] = $posted_data['dropoff_location'];
                $data['dropoff_location'] = $dropoff_location;
            }

            if (isset($posted_data['total_distance']) && !empty($posted_data['total_distance'])) {
                $distance = explode('|', $posted_data['total_distance']);
                $total_kilos = $distance[0] ? $distance[0] : '';
                $location_cost = floatval($pricing_data['perkilo_price']) * $total_kilos;
                $data['location_cost'] = $location_cost;
            }
        }


        if (isset($posted_data['pickup_date']) && !empty($posted_data['pickup_date'])) {
            $data['pickup_date'] = convert_to_generalized_format($posted_data['pickup_date'], $euro_format);
        }

        if (isset($posted_data['pickup_time']) && !empty($posted_data['pickup_time'])) {
            $data['pickup_time'] = $posted_data['pickup_time'];
        }

        if (isset($posted_data['dropoff_date']) && !empty($posted_data['dropoff_date'])) {
            $data['dropoff_date'] = convert_to_generalized_format($posted_data['dropoff_date'], $euro_format);
        }

        if (isset($posted_data['dropoff_time']) && !empty($posted_data['dropoff_time'])) {
            $data['dropoff_time'] = $posted_data['dropoff_time'];
        }

        if (isset($posted_data['inventory_quantity']) && !empty($posted_data['inventory_quantity'])) {
            $data['quantity'] = $posted_data['inventory_quantity'];
        }


        if (isset($data['pickup_date']) && !empty($data['pickup_date']) && !isset($data['dropoff_date']) && empty($data['dropoff_date'])) {
            if (!isset($data['pickup_time']) || !isset($data['dropoff_time'])) {
                $data['dropoff_date'] = $data['pickup_date'];
            } else {
                $data['dropoff_date'] = $data['pickup_date'];
            }
        }

        if (isset($data['pickup_time']) && !empty($data['pickup_time']) && !isset($data['dropoff_time']) && empty($data['dropoff_time'])) {
            $data['dropoff_time'] = $data['pickup_time'];
        }

        if (isset($data['dropoff_date']) && !empty($data['dropoff_date']) && !isset($data['pickup_date']) && empty($data['pickup_date'])) {
            if (!isset($data['pickup_time']) || !isset($data['dropoff_time'])) {
                $data['pickup_date'] = $data['dropoff_date'];
            } else {
                $data['pickup_date'] = $data['dropoff_date'];
            }
        }

        if (isset($data['dropoff_time']) && !empty($data['dropoff_time']) && !isset($data['pickup_time']) && empty($data['pickup_time'])) {
            $data['pickup_time'] = $data['dropoff_time'];
        }

        $cost_calculation = $this->calculate_cost($product_id, $data, $conditional_data);

        $data['rental_days_and_costs'] = $cost_calculation;
        $data['max_hours_late'] = get_post_meta($product_id, 'redq_max_time_late', true);


        if ($conditional_data['euro_format'] == 'yes') {
            $data['pickup_date'] = str_replace('.', '/', $data['pickup_date']);
        } else {
            $data['pickup_date'] = $data['pickup_date'];
        }

        if ($conditional_data['euro_format'] == 'yes') {
            $data['dropoff_date'] = str_replace('.', '/', $data['dropoff_date']);
        } else {
            $data['dropoff_date'] = $data['dropoff_date'];
        }



        return $data;
    }


    /**
     * Return rental cost and days
     *
     * @param  string $key , array $data
     * @return array
     */
    public function calculate_cost($product_id, $data, $conditional_data)
    {

        $product_type = wc_get_product($product_id)->get_type();
        $payable_person = array();

        if (isset($product_type) && $product_type === 'redq_rental') {

            $pricing_data = redq_rental_get_pricing_data($product_id);
            $calculate_cost_and_day = array();

            if (isset($data['location_cost'])) {
                $location_cost = $data['location_cost'];
            } else {
                $location_cost = 0;
            }

            if (isset($data['pickup_location']['cost'])) {
                $pickup_cost = $data['pickup_location']['cost'];
            } else {
                $pickup_cost = 0;
            }

            if (isset($data['dropoff_location']['cost'])) {
                $dropoff_cost = $data['dropoff_location']['cost'];
            } else {
                $dropoff_cost = 0;
            }

            if (isset($data['payable_cat'])) {
                $payable_cat = $data['payable_cat'];
            } else {
                $payable_cat = array();
            }

            if (isset($data['payable_resource'])) {
                $payable_resource = $data['payable_resource'];
            } else {
                $payable_resource = array();
            }


            if (isset($data['payable_security_deposites'])) {
                $payable_security_deposites = $data['payable_security_deposites'];
            } else {
                $payable_security_deposites = array();
            }

            if (isset($data['adults_info'])) {
                $adults_info = $data['adults_info'];
            } else {
                $adults_info = array();
            }

            if (isset($data['childs_info'])) {
                $childs_info = $data['childs_info'];
            } else {
                $childs_info = array();
            }

            $payable_person['adults'] = $adults_info;
            $payable_person['childs'] = $childs_info;

            if (isset($data['pickup_date'])) {
                $pickup_date = $data['pickup_date'];
            } else {
                $pickup_date = '';
            }

            if (isset($data['pickup_time'])) {
                $pickup_time = $data['pickup_time'];
            } else {
                $pickup_time = '';
            }

            if (isset($data['dropoff_date'])) {
                $dropoff_date = $data['dropoff_date'];
            } else {
                $dropoff_date = '';
            }

            if (isset($data['dropoff_time'])) {
                $dropoff_time = $data['dropoff_time'];
            } else {
                $dropoff_time = '';
            }

            if (isset($pricing_data['price_discount']) && $pricing_data['price_discount']) {
                $price_discount = $pricing_data['price_discount'];
            } else {
                $price_discount = array();
            }

            $days = $this->calculate_rental_days($data, $conditional_data);

            $calculate_cost_and_day['days'] = $days['days'];
            $calculate_cost_and_day['hours'] = $days['hours'];
            $calculate_cost_and_day['booked_dates'] = $days['booked_dates'];

            $pricing_type = $pricing_data['pricing_type'];
            $hourly_pricing = $pricing_data['hourly_price'];

            if ($pricing_type === 'general_pricing') {
                $general_pricing = $pricing_data['general_pricing'];
                $cost = $this->calculate_general_pricing_plan_cost($general_pricing, $days, $payable_cat, $payable_resource, $payable_person, $hourly_pricing, $payable_security_deposites, $pickup_cost, $dropoff_cost, $price_discount, $location_cost);
            }

            if ($pricing_type === 'daily_pricing') {
                $daily_pricing_plan = $pricing_data['daily_pricing'];
                $cost = $this->calculate_daily_pricing_plan_cost($daily_pricing_plan, $pickup_date, $days, $payable_cat, $payable_resource, $payable_person, $hourly_pricing, $payable_security_deposites, $pickup_cost, $dropoff_cost, $price_discount, $location_cost);
            }

            if ($pricing_type === 'monthly_pricing') {
                $monthly_pricing_plan = $pricing_data['monthly_pricing'];
                $cost = $this->calculate_monthly_pricing_plan_cost($monthly_pricing_plan, $pickup_date, $days, $payable_cat, $payable_resource, $payable_person, $hourly_pricing, $payable_security_deposites, $pickup_cost, $dropoff_cost, $price_discount, $location_cost);
            }

            if ($pricing_type === 'days_range') {
                $day_ranges_pricing_plan = $pricing_data['days_range'];
                $cost = $this->calculate_day_ranges_pricing_plan_cost($day_ranges_pricing_plan, $pickup_date, $days, $payable_cat, $payable_resource, $payable_person, $hourly_pricing, $payable_security_deposites, $pickup_cost, $dropoff_cost, $price_discount, $location_cost);
            }

            $pre_payment_percentage = get_option('rnb_instance_payment');

            if (empty($pre_payment_percentage)) {
                $pre_payment_percentage = 100;
            }

            $instance_payment = ($cost * $pre_payment_percentage) / 100;
            $due_payment = $cost - $instance_payment;

            $calculate_cost_and_day['cost'] = $instance_payment;
            $calculate_cost_and_day['due_payment'] = $due_payment;

            return $calculate_cost_and_day;

        }

    }



    /**
     * Calculate total rental days
     *
     * @param  array $data
     * @return string
     */
    public function calculate_rental_days($data, $conditional_data)
    {

        $durations = array();
        $choose_euro_format = $conditional_data['euro_format'];
        $max_hours_late = $conditional_data['max_time_late'];
        $output_format = $conditional_data['date_format'];


        $pickup_date = isset($data['pickup_date']) ? $data['pickup_date'] : '';
        $dropoff_date = isset($data['dropoff_date']) ? $data['dropoff_date'] : '';
        $pickup_time = isset($data['pickup_time']) ? $data['pickup_time'] : '';
        $dropoff_time = isset($data['dropoff_time']) ? $data['dropoff_time'] : '';

        $formated_pickup_time = date("H:i", strtotime($pickup_time));
        $formated_dropoff_time = date("H:i", strtotime($dropoff_time));
        $pickup_date_time = strtotime("$pickup_date $formated_pickup_time");
        $dropoff_date_time = strtotime("$dropoff_date $formated_dropoff_time");

        $hours = abs($pickup_date_time - $dropoff_date_time) / (60 * 60);
        $total_hours = 0;

        $enable_single_day_time_booking = $conditional_data['single_day_booking'];

        if ($hours < 24) {
            if ($enable_single_day_time_booking == 'open') {
                $days = 1;
            } else {
                $days = 0;
            }
            $total_hours = ceil($hours);
        } else {
            $days = intval($hours / 24);
            $extra_hours = $hours % 24;

            if ($enable_single_day_time_booking == 'open') {
                if ($extra_hours >= floatval($max_hours_late)) {
                    $days = $days + 1;
                }
            } else {
                if ($extra_hours > floatval($max_hours_late)) {
                    $days = $days + 1;
                }
            }
        }

        $booked_dates = array();
        $current = strtotime($pickup_date);
        $count = 0;

        while ($count < $days) {
            $day = strtotime('+' . $count . ' day', $current);
            $booked_dates['formatted'][] = date($output_format, $day);
            $booked_dates['saved'][] = date('Y-m-d', $day);
            $booked_dates['iso'][] = $day;
            $count++;
        }

        $durations['days'] = $days;
        $durations['hours'] = $total_hours;
        $durations['booked_dates'] = $booked_dates;

        return $durations;
    }



    /**
     * Calculate general pricing plan's cost
     *
     * @param  string $general_pricing, string $days, array $payable_resource, array $payable_person
     * @return string
     */
    public function calculate_general_pricing_plan_cost($general_pricing, $durations, $payable_cat, $payable_resource, $payable_person, $hourly_pricing, $payable_security_deposites, $pickup_cost, $dropoff_cost, $price_discount, $location_cost)
    {


        $days = $durations['days'];
        $hours = $durations['hours'];

        if ($days > 0) {
            $cost = intval($days) * floatval($general_pricing);
            $cost = $this->calculate_price_discount($cost, $price_discount, $days);
            $cost = $this->calculate_extras_cost($cost, $days, $payable_cat, $payable_resource, $payable_person, $payable_security_deposites, $pickup_cost, $dropoff_cost, $location_cost);
        } else {
            $cost = intval($hours) * floatval($hourly_pricing);
            $cost = $this->calculate_hourly_extras_cost($cost, $hours, $payable_cat, $payable_resource, $payable_person, $payable_security_deposites, $pickup_cost, $dropoff_cost, $location_cost);
        }
        return $cost;
    }



    /**
     * Calculate daily pricing plan's cost
     *
     * @param  array $daily_pricing_plan, string $pickup_date, string $days, array $payable_resource, array $payable_person
     * @return string
     */
    public function calculate_daily_pricing_plan_cost($daily_pricing_plan, $pickup_date, $durations, $payable_cat, $payable_resource, $payable_person, $hourly_pricing, $payable_security_deposites, $pickup_cost, $dropoff_cost, $price_discount, $location_cost)
    {

        $cost = 0;
        $days = $durations['days'];
        $hours = $durations['hours'];

        if ($days > 0) {
            for ($i = 0; $i < intval($days); $i++) {
                $day = date("N", strtotime($pickup_date . " +$i day"));
                switch ($day) {
                    case 1:
                        if ($daily_pricing_plan['monday'] != '') {
                            $cost = $cost + floatval($daily_pricing_plan['monday']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 2:
                        if ($daily_pricing_plan['tuesday'] != '') {
                            $cost = $cost + floatval($daily_pricing_plan['tuesday']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 3:
                        if ($daily_pricing_plan['wednesday'] != '') {
                            $cost = $cost + floatval($daily_pricing_plan['wednesday']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 4:
                        if ($daily_pricing_plan['thursday'] != '') {
                            $cost = $cost + floatval($daily_pricing_plan['thursday']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 5:
                        if ($daily_pricing_plan['friday'] != '') {
                            $cost = $cost + floatval($daily_pricing_plan['friday']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 6:
                        if ($daily_pricing_plan['saturday'] != '') {
                            $cost = $cost + floatval($daily_pricing_plan['saturday']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 7:
                        if ($daily_pricing_plan['sunday'] != '') {
                            $cost = $cost + floatval($daily_pricing_plan['sunday']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;

                    default:
                        break;
                }

            } //end for loop

            $cost = $this->calculate_price_discount($cost, $price_discount, $days);
            $cost = $this->calculate_extras_cost($cost, $days, $payable_cat, $payable_resource, $payable_person, $payable_security_deposites, $pickup_cost, $dropoff_cost, $location_cost);
        } else {
            $cost = intval($hours) * floatval($hourly_pricing);
            $cost = $this->calculate_hourly_extras_cost($cost, $hours, $payable_cat, $payable_resource, $payable_person, $payable_security_deposites, $pickup_cost, $dropoff_cost, $location_cost);
        }

        return $cost;
    }



    /**
     * Calculate monthly pricing plan's cost
     *
     * @param  array $monthly_pricing_plan, string $pickup_date, string $days, array $payable_resource, array $payable_person
     * @return string
     */
    public function calculate_monthly_pricing_plan_cost($monthly_pricing_plan, $pickup_date, $durations, $payable_cat, $payable_resource, $payable_person, $hourly_pricing, $payable_security_deposites, $pickup_cost, $dropoff_cost, $price_discount, $location_cost)
    {

        $cost = 0;
        $days = $durations['days'];
        $hours = $durations['hours'];

        if ($days > 0) {

            for ($i = 0; $i < intval($days); $i++) {
                $month = date("n", strtotime($pickup_date . " +$i day"));
                switch ($month) {
                    case 1:
                        if ($monthly_pricing_plan['january'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['january']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 2:
                        if ($monthly_pricing_plan['february'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['february']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 3:
                        if ($monthly_pricing_plan['march'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['march']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 4:
                        if ($monthly_pricing_plan['april'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['april']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 5:
                        if ($monthly_pricing_plan['may'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['may']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 6:
                        if ($monthly_pricing_plan['june'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['june']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 7:
                        if ($monthly_pricing_plan['july'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['july']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 8:
                        if ($monthly_pricing_plan['august'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['august']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 9:
                        if ($monthly_pricing_plan['september'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['september']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 10:
                        if ($monthly_pricing_plan['october'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['october']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 11:
                        if ($monthly_pricing_plan['november'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['november']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;
                    case 12:
                        if ($monthly_pricing_plan['december'] != '') {
                            $cost = $cost + floatval($monthly_pricing_plan['december']);
                        } else {
                            $cost = $cost + 0;
                        }
                        break;

                    default:
                        break;
                }
            }

            $cost = $this->calculate_price_discount($cost, $price_discount, $days);
            $cost = $this->calculate_extras_cost($cost, $days, $payable_cat, $payable_resource, $payable_person, $payable_security_deposites, $pickup_cost, $dropoff_cost, $location_cost);
        } else {
            $cost = intval($hours) * floatval($hourly_pricing);
            $cost = $this->calculate_hourly_extras_cost($cost, $hours, $payable_cat, $payable_resource, $payable_person, $payable_security_deposites, $pickup_cost, $dropoff_cost, $location_cost);
        }

        return $cost;
    }



    /**
     * Calculate day ranges plan's cost
     *
     * @param  array $day_ranges_pricing_plan, string $pickup_date, string $days, array $payable_resource, array $payable_person
     * @return string
     */
    public function calculate_day_ranges_pricing_plan_cost($day_ranges_pricing_plan, $pickup_date, $durations, $payable_cat, $payable_resource, $payable_person, $hourly_pricing, $payable_security_deposites, $pickup_cost, $dropoff_cost, $price_discount, $location_cost)
    {

        $cost = 0;
        $flag = 0;
        $days = $durations['days'];
        $hours = $durations['hours'];

        if ($days > 0) {
            foreach ($day_ranges_pricing_plan as $key => $value) {
                if ($flag == 0) {
                    if ($value['cost_applicable'] === 'per_day') {
                        if (intval($value['min_days']) <= intval($days) && intval($value['max_days']) >= intval($days)) {
                            $cost = floatval($value['range_cost']) * intval($days);
                            $flag = 1;
                        }
                    } else {
                        if (intval($value['min_days']) <= intval($days) && intval($value['max_days']) >= intval($days)) {
                            $cost = floatval($value['range_cost']);
                            $flag = 1;
                        }
                    }
                }
            }

            $cost = $this->calculate_price_discount($cost, $price_discount, $days);
            $cost = $this->calculate_extras_cost($cost, $days, $payable_cat, $payable_resource, $payable_person, $payable_security_deposites, $pickup_cost, $dropoff_cost, $location_cost);

        } else {
            $cost = intval($hours) * floatval($hourly_pricing);
            $cost = $this->calculate_hourly_extras_cost($cost, $hours, $payable_cat, $payable_resource, $payable_person, $payable_security_deposites, $pickup_cost, $dropoff_cost, $location_cost);
        }

        return $cost;
    }


    /**
     * Calculate price discount
     *
     * @param  string $cost, array $price_discount, string $days
     * @return string
     */
    public function calculate_price_discount($cost, $price_discount, $days)
    {

        $flag = 0;
        $discount_amount;
        $discount_type;

        foreach ($price_discount as $key => $value) {
            if ($flag == 0) {
                if (intval($value['min_days']) <= intval($days) && intval($value['max_days']) >= intval($days)) {
                    $discount_type = $value['discount_type'];
                    $discount_amount = $value['discount_amount'];
                    $flag = 1;
                }
            }
        }

        if (isset($discount_type) && !empty($discount_type) && isset($discount_amount) && !empty($discount_amount)) {
            if ($discount_type === 'percentage') {
                $cost = $cost - ($cost * $discount_amount) / 100;
            } else {
                $cost = $cost - $discount_amount;
            }
        }

        return $cost;
    }


    /**
     * Calculate resource and person cost
     *
     * @param  string $general_pricing, string $days, array $payable_resource, array $payable_person
     * @return string
     */
    public function calculate_extras_cost($cost, $days, $payable_cat, $payable_resource, $payable_person, $payable_security_deposites, $pickup_cost, $dropoff_cost, $location_cost)
    {

        if (isset($pickup_cost) && !empty($pickup_cost)) {
            $cost = floatval($cost) + floatval($pickup_cost);
        }

        if (isset($dropoff_cost) && !empty($dropoff_cost)) {
            $cost = floatval($cost) + floatval($dropoff_cost);
        }

        if (isset($location_cost) && !empty($location_cost)) {
            $cost = floatval($cost) + floatval($location_cost);
        }

        if (isset($payable_cat) && sizeof($payable_cat) != 0) {
            foreach ($payable_cat as $key => $value) {
                if ($value['multiply'] == 'per_day') {
                    $cost = floatval($cost) + intval($value['quantity']) * intval($days) * floatval($value['cost']);
                } else {
                    $cost = floatval($cost) + intval($value['quantity']) * floatval($value['cost']);
                }
            }
        }

        if (isset($payable_resource) && sizeof($payable_resource) != 0) {
            foreach ($payable_resource as $key => $value) {
                if ($value['cost_multiply'] == 'per_day') {
                    $cost = floatval($cost) + intval($days) * floatval($value['resource_cost']);
                } else {
                    $cost = floatval($cost) + floatval($value['resource_cost']);
                }
            }
        }

        if (isset($payable_security_deposites) && sizeof($payable_security_deposites) != 0) {
            foreach ($payable_security_deposites as $key => $value) {
                if ($value['cost_multiply'] == 'per_day') {
                    $cost = floatval($cost) + intval($days) * floatval($value['security_deposite_cost']);
                } else {
                    $cost = floatval($cost) + floatval($value['security_deposite_cost']);
                }
            }
        }

        $adults = $payable_person['adults'];
        $childs = $payable_person['childs'];

        if (isset($adults) && sizeof($adults) != 0) {
            if ($adults['cost_multiply'] == 'per_day') {
                $cost = floatval($cost) + intval($days) * floatval($adults['person_cost']);
            } else {
                $cost = floatval($cost) + floatval($adults['person_cost']);
            }
        }

        if (isset($childs) && sizeof($childs) != 0) {
            if ($childs['cost_multiply'] == 'per_day') {
                $cost = floatval($cost) + intval($days) * floatval($childs['person_cost']);
            } else {
                $cost = floatval($cost) + floatval($childs['person_cost']);
            }
        }

        return $cost;
    }



    /**
     * Calculate hourly resource and person cost
     *
     * @param  string $general_pricing, string $days, array $payable_resource, array $payable_person
     * @return string
     */
    public function calculate_hourly_extras_cost($cost, $hours, $payable_cat, $payable_resource, $payable_person, $payable_security_deposites, $pickup_cost, $dropoff_cost, $location_cost)
    {

        if (isset($pickup_cost) && !empty($pickup_cost)) {
            $cost = floatval($cost) + floatval($pickup_cost);
        }

        if (isset($dropoff_cost) && !empty($dropoff_cost)) {
            $cost = floatval($cost) + floatval($dropoff_cost);
        }

        if (isset($location_cost) && !empty($location_cost)) {
            $cost = floatval($cost) + floatval($location_cost);
        }

        if (isset($payable_cat) && sizeof($payable_cat) != 0) {
            foreach ($payable_cat as $key => $value) {
                if ($value['multiply'] == 'per_day') {
                    $cost = floatval($cost) + intval($value['quantity']) * intval($hours) * floatval($value['hourly_cost']);
                } else {
                    $cost = floatval($cost) + intval($value['quantity']) * floatval($value['hourly_cost']);
                }
            }
        }

        if (isset($payable_resource) && sizeof($payable_resource) != 0) {
            foreach ($payable_resource as $key => $value) {
                if ($value['cost_multiply'] == 'per_day') {
                    $cost = floatval($cost) + intval($hours) * floatval($value['resource_hourly_cost']);
                } else {
                    $cost = floatval($cost) + floatval($value['resource_cost']);
                }
            }
        }

        $adults = $payable_person['adults'];
        $childs = $payable_person['childs'];

        if (isset($adults) && sizeof($adults) != 0) {
            if ($adults['cost_multiply'] == 'per_day') {
                $cost = floatval($cost) + intval($hours) * floatval($adults['person_hourly_cost']);
            } else {
                $cost = floatval($cost) + floatval($adults['person_cost']);
            }
        }

        if (isset($childs) && sizeof($childs) != 0) {
            if ($childs['cost_multiply'] == 'per_day') {
                $cost = floatval($cost) + intval($hours) * floatval($childs['person_hourly_cost']);
            } else {
                $cost = floatval($cost) + floatval($childs['person_cost']);
            }
        }

        if (isset($payable_security_deposites) && sizeof($payable_security_deposites) != 0) {
            foreach ($payable_security_deposites as $key => $value) {
                if ($value['cost_multiply'] == 'per_day') {
                    $cost = floatval($cost) + intval($hours) * floatval($value['security_deposite_hourly_cost']);
                } else {
                    $cost = floatval($cost) + floatval($value['security_deposite_cost']);
                }
            }
        }

        return $cost;
    }


}

new WC_Redq_Rental_Cart();
