<?php
	global $product;
	$pick_up_locations = $product->redq_get_rental_payable_attributes('pickup_location');
 ?>
<?php if(isset($pick_up_locations) && !empty($pick_up_locations)): ?>
	<div class="redq-pick-up-location rnb-select-wrapper rnb-component-wrapper">
		<?php
	        $labels = reddq_rental_get_settings( get_the_ID(), 'labels', array('pickup_location') );
	        $labels = $labels['labels'];
	    ?>
		<h5><?php echo esc_attr($labels['pickup_location']); ?></h5>
		<select class="redq-select-boxes pickup_location rnb-select-box" name="pickup_location" data-placeholder="<?php echo esc_attr($labels['pickup_loc_placeholder']); ?>">
			<option value=""><?php echo esc_attr($labels['pickup_loc_placeholder']); ?></option>
			<?php foreach ($pick_up_locations as $key => $value) { ?>
				<option value="<?php echo esc_attr($value['address']); ?>|<?php echo esc_attr($value['title']); ?>|<?php echo esc_attr($value['cost']); ?>" data-pickup-location-cost= "<?php echo esc_attr($value['cost']); ?>"><?php echo esc_attr($value['title']); ?></option>
			<?php } ?>
		</select>
	</div>
<?php endif; ?>