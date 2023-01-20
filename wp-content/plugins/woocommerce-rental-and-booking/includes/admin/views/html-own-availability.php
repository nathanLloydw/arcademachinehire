<?php

	if ( ! isset( $availability['type'] ) )
		$availability['type'] = 'custom_date';

	$parent_id = wp_get_post_parent_id( get_the_ID() );
	$conditions = reddq_rental_get_settings( $parent_id, 'conditions' );
	$conditional_data = $conditions['conditions'];
	$output_date_format = $conditional_data['date_format'];
	$euro_date_format  = $conditional_data['euro_format'];
?>

<tr>
	<td class="sort">&nbsp;</td>
	<td>
		<div class="select rental_availability_type">
			<select name="redq_rental_availability_type[]">
				<option value="custom_date" selected="selected"><?php _e( 'Custom date range', 'redq-rental' ); ?></option>
			</select>
		</div>
	</td>
	<td>
		<div class="from_date inventory-form-to-input">
			<input type="text" style="border: 1px solid #ddd;" class="date-picker" name="redq_rental_availability_from[]" value="<?php if ( !empty( $availability['from'] ) ) echo convert_to_output_format($availability['from'], $output_date_format); ?>"/>
		</div>
	</td>
	<td>
		<div class="to_date inventory-form-to-input">
			<input type="text" style="border: 1px solid #ddd;" class="date-picker" name="redq_rental_availability_to[]" value="<?php if ( !empty( $availability['to'] ) ) echo convert_to_output_format($availability['to'], $output_date_format); ?>" />
		</div>
	</td>
	<td>
		<div class="select">
			<select name="redq_availability_rentable[]">
				<option value="no" <?php selected( isset( $availability['rentable'] ) && $availability['rentable'] == 'no', true ) ?>><?php _e( 'Not', 'redq-rental' ) ;?></option>
				<!-- <option value="yes" <?php selected( isset( $availability['bookable'] ) && $availability['bookable'] == 'yes', true ) ?>><?php _e( 'Yes', 'redq-rental' ) ;?></option> -->
			</select>
		</div>
	</td>
	<td class="remove"><button type="btn" class=""><?php _e('delete','redq-rental'); ?></button></td>
</tr>