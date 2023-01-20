<?php
	global $product;
	$product_id = $product->get_id();
	$displays = reddq_rental_get_settings( $product_id, 'display' );
	$general = reddq_rental_get_settings( $product_id, 'general' );

	$show_quote = $displays['display']['rfq'];
	$user_pass = $general['general']['rfq_user_pass'];

	if( $show_quote === 'open' ) {
		$customer_first_name = '';
		$customer_last_name = '';
		$customer_phone = '';
		$customer_email = '';
		if( is_user_logged_in() ) {
			global $current_user;
			$customer_first_name = get_user_meta($current_user->ID, 'billing_first_name', true);
			$customer_last_name = get_user_meta($current_user->ID, 'billing_last_name', true);
			$customer_phone = get_user_meta($current_user->ID, 'billing_phone', true);
			$customer_email = get_user_meta($current_user->ID, 'billing_email', true);
		}
?>
	<button id="quote-content-confirm" class="redq_request_for_a_quote btn-book-now button"><?php echo esc_html( $product->single_request_for_quote_text() ); ?></button>

	<div id="quote-popup" class="rnb-popup mfp-hide">
		<?php if( !is_user_logged_in() && $user_pass === 'no' ) : ?>
			<p>
				<input type="text" name="quote-username" id="quote-username" placeholder="<?php esc_html_e('Username', 'redq-rental') ?>" value="" required="true" />
			</p>
			<p>
				<input type="password" name="quote-password" id="quote-password" placeholder="<?php esc_html_e('Password', 'redq-rental') ?>" value="" required="true" />
			</p>
		<?php endif ?>

		<p>
			<input type="text" name="quote-first-name" id="quote-first-name" placeholder="<?php esc_html_e('First Name', 'redq-rental') ?>" value="<?php echo esc_attr($customer_first_name) ?>" required="true" />
		</p>
		<p>
			<input type="text" name="quote-last-name" id="quote-last-name" placeholder="<?php esc_html_e('Last Name', 'redq-rental') ?>" value="<?php echo esc_attr($customer_last_name) ?>" required="true" />
		</p>
		<p>
			<input type="email" name="quote-email" id="quote-email" placeholder="<?php esc_html_e('Email', 'redq-rental') ?>" value="<?php echo esc_attr($customer_email) ?>" required="true" />
		</p>
		<p>
			<input type="text" name="quote-phone" id="quote-phone" placeholder="<?php esc_html_e('Phone', 'redq-rental') ?>" value="<?php echo esc_attr($customer_phone) ?>" required="true" />
		</p>
		<p>
			<textarea name="quote-message" id="quote-message" placeholder="<?php esc_html_e('Message', 'redq-rental') ?>"></textarea>
		</p>
		<p><button class="quote-submit"><?php esc_html_e('Submit', 'redq-rental') ?><i class="fa fa-spinner fa-pulse fa-fw"></i></button></p>
		<div class="quote-modal-message"></div>
	</div>

<?php } ?>