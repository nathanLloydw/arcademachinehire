<?php
global $product;
$product_id = $product->get_id();
$pricing_type = get_post_meta($product_id, 'pricing_type', true);
$displays = reddq_rental_get_settings($product_id, 'display');
$displays = $displays['display'];


if (isset($displays['flip_box']) && $displays['flip_box'] !== 'closed' && $pricing_type !== 'general_pricing') :
    global $product;
	$labels = reddq_rental_get_settings($product_id, 'labels', array('price_info'));
	$labels = $labels['labels'];
	$pricing_data = redq_rental_get_pricing_data($product_id);
	$price_info_top = get_option('rnb_flipbox_price_top_info', 'yes');
	$filpbox = $price_info_top && $price_info_top === 'yes' ? ['back', 'front'] : ['front', 'back'];
?>

	<div class="rnb-pricing-plan-button">
		<span class="rnb-pricing-plan">
			<a href="#" class="rnb-pricing-plan-link">
				<i class="fa fa-hand-pointer-o"></i>
				&nbsp;
				<?php echo esc_attr($labels['flipbox_info']); ?>
			</a>
		</span>
	</div>
	<div class="price-showing" style="margin-bottom: 100px;">
		<div class="<?php echo esc_attr($filpbox[1]); ?>">

			<div class="item-pricing">
				<h5><?php echo esc_attr($labels['flipbox_info']); ?></h5>
				<?php if ($pricing_data['pricing_type'] === 'days_range') : ?>
					<?php $pricing_plans = $pricing_data['days_range']; ?>
					<div class="rnb-pricing-wrap">
						<?php foreach ($pricing_plans as $key => $value) { ?>
							<?php $rate = $value['cost_applicable'] === 'fixed' ? esc_html__('Fixed', 'redq-rental') : esc_html__('/ Day', 'redq-rental'); ?>
							<div class="day-ranges-pricing-plan">
								<span class="range-days"><?php echo esc_attr($value['min_days']); ?> - <?php echo esc_attr($value['max_days']); ?> <?php _e('days :', 'redq-rental'); ?> </span>
								<span class="range-price"><strong><?php echo wc_price($value['range_cost']); ?></strong> <?php echo esc_attr($rate); ?></span>
							</div>
						<?php
    } ?>
					</div>
				<?php endif; ?>

				<?php if ($pricing_data['pricing_type'] === 'daily_pricing') : ?>
					<?php
    $daily_pricing = $pricing_data['daily_pricing'];
    $day_names = redq_rental_day_names();
    ?>
					<div class="rnb-pricing-wrap">
						<?php foreach ($daily_pricing as $key => $value) { ?>
							<div class="day-ranges-pricing-plan">
								<span class="day"><?php echo esc_attr(ucfirst($day_names[$key])); ?> </span>
								<span class="day-price"><strong> - <?php echo wc_price($value); ?></strong></span>
							</div>
						<?php
    } ?>
					</div>
				<?php endif; ?>

				<?php if ($pricing_data['pricing_type'] === 'monthly_pricing') : ?>
					<?php
    $monthly_pricing = $pricing_data['monthly_pricing'];
    $month_names = redq_rental_month_names();
    ?>
					<div class="rnb-pricing-wrap">
						<?php foreach ($monthly_pricing as $key => $value) { ?>
							<div class="day-ranges-pricing-plan">
								<span class="month"><?php echo ucfirst($month_names[$key]); ?> </span>
								<span class="month-price"><strong> - <?php echo wc_price($value); ?></strong></span>
							</div>
						<?php
    } ?>
					</div>
				<?php endif; ?>

			</div>

			<div class="rnb-discount-wrap">
				<div class="discount-portion">
					<?php $price_discounts = $pricing_data['price_discount']; ?>
					<?php if (isset($price_discounts) && !empty($price_discounts)) : ?>
						<h5><?php echo esc_html__('Discount Rates', 'redq-rental'); ?></h5>
						<?php foreach ($price_discounts as $key => $value) { ?>
                            <?php
                            if ($value['discount_type'] === 'percentage') {
                                $rate = esc_html__('%', 'redq-rental');
                                $amount = $value['discount_amount'];
                            } else {
                                $rate = esc_html__('Fixed', 'redq-rental');
                                $amount = wc_price($value['discount_amount']);
                            }
                            ?>
							<div class="discount-plan">
								<span class="range-days"><?php echo esc_attr($value['min_days']); ?> - <?php echo esc_attr($value['max_days']); ?> <?php _e('days :', 'redq-rental'); ?> </span>
								<span class="range-price"><strong><?php echo $amount; ?></strong> <?php echo esc_attr($rate); ?></span>
							</div>
						<?php
    } ?>
					<?php endif; ?>
				</div>
			</div>

		</div>
	</div>
<?php endif; ?>
