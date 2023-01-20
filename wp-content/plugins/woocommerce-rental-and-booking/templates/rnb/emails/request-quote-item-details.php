  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center" valign="top">
        <center>

          <table width="100%" style="background-color:#ffffff;border-bottom:1px solid #e5e5e5;">
            <tr>
              <td align="center">
                <center style="padding:0 0 50px 0;">

                  <table width="70%" style="margin:0 auto;">
                    <tr>
                      <td>

                        <table>
                          <tr>
                            <td>
                              <h2 align="left" style="font-family:Georgia,Cambria,'Times New Roman',serif;font-size:32px;font-weight:300;line-height: normal;padding: 35px 0 0;color: #4d4d4d;">
                              <?php printf( __( 'Quote #%s Details', 'redq-rental' ), $quote['id'] ); ?>
                              </h2>
                            </td>
                          </tr>
                          <tr>
                            <td style="border-collapse: collapse;width: 70%;padding-top: 20px;text-align: left;vertical-align: top;">
                              <table cellspacing="0" cellpadding="0" width="100%">
                                <tbody><tr>
                                  <td style="border-collapse: collapse;text-align: left;vertical-align: top;width: 90%">
                                    <span style="color: #4d4d4d; font-weight:bold;"><?php echo wpautop( wptexturize( $product_title ) ) ?> <strong> x 1</strong></span>
                                    <dl class="variation">
                                      <?php
                                        $product_id = get_post_meta($quote_id, '_product_id', true);

                                        $get_labels = reddq_rental_get_settings( $product_id, 'labels', array('pickup_location', 'return_location', 'pickup_date', 'return_date', 'resources', 'categories', 'person', 'deposites') );
                                        $labels = $get_labels['labels'];

                                        foreach ($form_data as $meta) :
                                          if( isset( $meta['name'] ) ) {
                                            switch ($meta['name']) {
                                              case 'add-to-cart':
                                                # code...
                                                break;

                                              case 'currency-symbol':
                                                # code...
                                                break;

                                              case 'pickup_location':
                                                if(!empty($meta['value'])):
                                                  $pickup_location_title = $labels['pickup_location'];
                                                  $dval = explode('|', $meta['value'] );
                                                  $pickup_value = $dval[0].' ( '.wc_price($dval[2]). ' )'; ?>
                                                  <dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $pickup_location_title ) ?>:</dt>
                                                  <dd>
                                                    <p><strong><?php echo $pickup_value; ?></strong></p>
                                                  </dd>
                                                <?php endif; break;

                                              case 'dropoff_location':
                                                if(!empty($meta['value'])):
                                                  $return_location_title = $labels['return_location'];
                                                  $dval = explode('|', $meta['value'] );
                                                  $return_value = $dval[0].' ( '.wc_price($dval[2]). ' )'; ?>
                                                  <dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $return_location_title ) ?>:</dt>
                                                  <dd>
                                                    <p><strong><?php echo $return_value; ?></strong></p>
                                                  </dd>
                                                <?php endif; break;

                                              case 'pickup_date':
                                                if(!empty($meta['value'])):
                                                  $pickup_date_title = $labels['pickup_date'];
                                                  $pickup_date_value = $meta['value']; ?>
                                                  <dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $pickup_date_title ) ?>:</dt>
                                                  <dd>
                                                    <p><strong><?php echo $pickup_date_value; ?></strong></p>
                                                  </dd>
                                                <?php endif; break;

                                              case 'pickup_time':
                                                if(!empty($meta['value'])):
                                                  $pickup_time_title = $labels['pickup_time'];
                                                  $pickup_time_value = $meta['value'] ? $meta['value'] : '' ; ?>
                                                  <dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $pickup_time_title ) ?>:</dt>
                                                  <dd>
                                                    <p><strong><?php echo $pickup_time_value; ?></strong></p>
                                                  </dd>
                                                <?php endif; break;

                                              case 'dropoff_date':
                                                if(!empty($meta['value'])):
                                                  $return_date_title = $labels['return_date'];
                                                  $return_date_value = $meta['value'] ? $meta['value'] : '' ; ?>
                                                  <dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $return_date_title ) ?>:</dt>
                                                  <dd>
                                                    <p><strong><?php echo $return_date_value; ?></strong></p>
                                                  </dd>
                                                <?php endif; break;

                                              case 'dropoff_time':
                                                if(!empty($meta['value'])):
                                                $return_time_title = $labels['return_time'];
                                                $return_time_value = $meta['value'] ? $meta['value'] : '' ; ?>
                                                <dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $return_time_title ) ?>:</dt>
                                                <dd>
                                                  <p><strong><?php echo $return_time_value; ?></strong></p>
                                                </dd>
                                                <?php endif; break;

                                              case 'additional_adults_info':
                                                if(!empty($meta['value'])):
                                                  $person_title = $labels['adults'];
                                                  $dval = explode('|', $meta['value'] );
                                                  $person_value = $dval[0].' ( '.wc_price($dval[1]).' - '.$dval[2]. ' )'; ?>
                                                  <dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $person_title ) ?>:</dt>
                                                  <dd>
                                                    <p><strong><?php echo $person_value; ?></strong></p>
                                                  </dd>
                                                <?php endif; break;

                                              case 'extras': ?>
                                                <?php
                                                  $resources_title = $labels['resource'];

                                                  $resource_name = '';
                                                  $payable_resource = array();
                                                  foreach ($meta['value'] as $key => $value) {
                                                    $extras = explode('|', $value);
                                                    $payable_resource[$key]['resource_name'] = $extras[0];
                                                    $payable_resource[$key]['resource_cost'] = $extras[1];
                                                    $payable_resource[$key]['cost_multiply'] = $extras[2];
                                                    $payable_resource[$key]['resource_hourly_cost'] = $extras[3];
                                                  }
                                                  foreach ($payable_resource as $key => $value) {
                                                    if($value['cost_multiply'] === 'per_day'){
                                                      $resource_name .= $value['resource_name'].' ( '.wc_price($value['resource_cost']).' - '.__('Per Day','redq-rental').' )'.' , <br> ';
                                                    }else{
                                                      $resource_name .= $value['resource_name'].' ( '.wc_price($value['resource_cost']).' - '.__('One Time','redq-rental').' )'.' , <br> ';
                                                    }
                                                  }
                                                ?>
                                                <dt style="float: left;margin-right: 10px;"><?php echo esc_attr($resources_title);  ?></dt>
                                                <dd>
                                                  <p><strong><?php echo $resource_name; ?></strong></p>
                                                </dd>
                                               <?php break;
                                              case 'security_deposites': ?>
                                                <?php
                                                  $deposits_title = $labels['deposite'];
                                                  $deposite_name = '';
                                                  $payable_deposits = array();
                                                  foreach ($meta['value'] as $key => $value) {
                                                    $extras = explode('|', $value);
                                                    $payable_deposits[$key]['deposite_name'] = $extras[0];
                                                    $payable_deposits[$key]['deposite_cost'] = $extras[1];
                                                    $payable_deposits[$key]['cost_multiply'] = $extras[2];
                                                    $payable_deposits[$key]['deposite_hourly_cost'] = $extras[3];
                                                  }
                                                  foreach ($payable_deposits as $key => $value) {
                                                    if($value['cost_multiply'] === 'per_day'){
                                                      $deposite_name .= $value['deposite_name'].' ( '.wc_price($value['deposite_cost']).' - '.__('Per Day','redq-rental').' )'.' , <br> ';
                                                    }else{
                                                      $deposite_name .= $value['deposite_name'].' ( '.wc_price($value['deposite_cost']).' - '.__('One Time','redq-rental').' )'.' , <br> ';
                                                    }
                                                  }
                                                ?>
                                                <dt style="float: left;margin-right: 10px;">
                                                  <?php echo esc_attr($deposits_title); ?>
                                                </dt>
                                                <dd>
                                                  <p><strong><?php echo $deposite_name; ?></strong></p>
                                                </dd>
                                               <?php break;

                                              case 'inventory_quantity': ?>
                                                <dt style="float: left;margin-right: 10px;">
                                                  <?php echo esc_html__( 'Quantity', 'redq-rental' ); ?>
                                                </dt>
                                                <dd>
                                                  <p><strong><?php echo esc_attr( $meta['value'] ) ?></strong></p>
                                                </dd>
                                               <?php break;

                                              case 'quote_price': ?>
                                                <dt style="float: left;margin-right: 10px;">
                                                  <?php echo esc_html__( 'Quote Price', 'redq-rental' ); ?>
                                                </dt>
                                                <dd>
                                                  <p><strong><?php echo wc_price( $meta['value'] ) ?></strong></p>
                                                </dd>
                                               <?php break;

                                              default: ?>
                                                <dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $meta['name'] ) ?>:</dt>
                                                <dd>
                                                  <p><strong><?php echo esc_attr( $meta['value'] ) ?></strong></p>
                                                </dd>
                                                <?php break;
                                            }
                                          }
                                        endforeach

                                      ?>
                                    </dl>
                                  </td>
                                </tr>
                              </tbody></table>
                            </td>
                            <td style="border-collapse: collapse;padding-top: 20px;text-align: left;vertical-align: top; width: 10%;">
                              <?php echo wc_price(get_post_meta($quote_id, '_quote_price', true)); ?>
                            </td>
                          </tr>


                          <!-- Start customer details part -->
                          <tr>
                            <td>
                              <h2 align="left" style="font-family:Georgia,Cambria,'Times New Roman',serif;font-size:32px;font-weight:300;line-height: normal;padding: 35px 0 0;color: #4d4d4d;">
                              <?php echo esc_html__( 'Customer Details', 'redq-rental' ); ?>
                              </h2>
                            </td>
                          </tr>
                          <tr>
                            <td style="border-collapse: collapse;width: 70%;padding-top: 20px;text-align: left;vertical-align: top;">
                              <table cellspacing="0" cellpadding="0" width="100%">
                                <tbody>
                                  <tr>
                                    <td style="border-collapse: collapse;text-align: left;vertical-align: top;width: 90%">
                                      <dl class="variation">
                                        <?php
                                          //Retrieve customer data
                                          $customer_info = array_map(function ($data) {
                                            if(isset($data['forms'])){
                                              return $data['forms'];
                                            }
                                          }, $form_data);

                                          $customer_info = array_values( array_filter($customer_info, function($value) { return $value !== null; }) );
                                          $customer_data = isset($customer_info[0]) && !empty($customer_info[0]) ? $customer_info[0] : array();
                                          //End retrieve customer data
                                        ?>

                                        <?php if( isset($customer_data['quote_first_name']) && !empty($customer_data['quote_first_name'])) : ?>
                                          <dt style="float: left;margin-right: 10px;"><?php echo esc_html__( 'First Name', 'redq-rental' ); ?>:</dt>
                                          <dd><p><strong><?php echo esc_attr( $customer_data['quote_first_name'] ); ?></strong></p></dd>
                                        <?php endif; ?>

                                        <?php if( isset($customer_data['quote_last_name']) && !empty($customer_data['quote_last_name'])) : ?>
                                          <dt style="float: left;margin-right: 10px;"><?php echo esc_html__( 'Last Name', 'redq-rental' ); ?>:</dt>
                                          <dd><p><strong><?php echo esc_attr( $customer_data['quote_last_name'] ); ?></strong></p></dd>
                                        <?php endif; ?>

                                        <?php if( isset($customer_data['quote_email']) && !empty($customer_data['quote_email'])) : ?>
                                          <dt style="float: left;margin-right: 10px;"><?php echo esc_html__( 'Email', 'redq-rental' ); ?>:</dt>
                                          <dd><p><strong><?php echo esc_attr( $customer_data['quote_email'] ); ?></strong></p></dd>
                                        <?php endif; ?>

                                        <?php if( isset($customer_data['quote_phone']) && !empty($customer_data['quote_phone'])) : ?>
                                          <dt style="float: left;margin-right: 10px;"><?php echo esc_html__( 'Phone ', 'redq-rental' ); ?>:</dt>
                                          <dd><p><strong><?php echo esc_attr( $customer_data['quote_phone'] ); ?></strong></p></dd>
                                        <?php endif; ?>

                                        <?php if( isset($customer_data['quote_message']) && !empty($customer_data['quote_message'])) : ?>
                                          <dt style="float: left;margin-right: 10px;"><?php echo esc_html__( 'Message', 'redq-rental' ); ?>:</dt>
                                          <dd><p><strong><?php echo esc_attr( $customer_data['quote_message'] ); ?></strong></p></dd>
                                        <?php endif; ?>

                                      </dl>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                          <!-- End customer details part -->


                        </table>
                      </td>
                    </tr>
                  </table>

                </center>
              </td>
            </tr>
          </table>

        </center>
      </td>
    </tr>
  </table>