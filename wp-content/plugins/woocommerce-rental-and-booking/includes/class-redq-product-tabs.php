<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Rental Product Tab Class.
 *
 * The WooCommerce product class handles individual product data.
 *
 * @class 		WC_Product_Redq_Rental
 * @version 	1.0.0
 * @since 		1.0.0
 * @package		WooCommerce-rental-and-booking/includes
 * @category	Class
 * @author 		RedQTeam
 */

class WC_Redq_Rental_Tabs {

	/**
	 * Constructor.
	 *
	 * @param null
	 */
	public function __construct(){
		add_action( 'woocommerce_product_tabs', array( $this, 'redq_rental_product_tabs' ));
	}


	/**
	 * Initialize attribute and feature tabs
	 *
	 * @version		1.7.0
	 * @access public
	 * @param array $taxonomy
	 * @return WC_Product or WC_Product_Rental_product
	 */
	public function redq_rental_product_tabs($tabs){

		$product_type = wc_get_product(get_the_ID())->get_type();
		$get_general = reddq_rental_get_settings( get_the_ID(), 'general' );
		$general_data = $get_general['general'];

		if(isset($product_type) && $product_type === 'redq_rental'){
			$item_attributes = WC_Product_Redq_Rental::redq_get_rental_non_payable_attributes('attributes');
			if(isset($item_attributes) && !empty($item_attributes)){
				$tabs['attributes'] = array(
			        'title'     => $general_data['attribute_tab'],
			        'priority'  => 5,
			        'callback'  => 'WC_Redq_Rental_Tabs::redq_attributes'
			    );
			}

			$additional_features = WC_Product_Redq_Rental::redq_get_rental_non_payable_attributes('features');
			if(isset($additional_features) && !empty($additional_features)){
				$tabs['features'] = array(
			        'title'     => $general_data['feature_tab'],
			        'priority'  => 8,
			        'callback'  => 'WC_Redq_Rental_Tabs::redq_features'
			    );
			}
		}

	    return $tabs;
	}


	/**
	 * Attributes tab callback function
	 *
	 * @version		1.7.0
	 * @access public
	 * @param null
	 * @return WC_Product or WC_Product_Rental_product
	 */
	public static function redq_attributes(){ ?>

		<?php $item_attributes = WC_Product_Redq_Rental::redq_get_rental_non_payable_attributes('attributes'); ?>
		<?php if(isset($item_attributes) && !empty($item_attributes)): ?>
		<div class="item-arrtributes">
			<ul class="attributes">
				<?php foreach ($item_attributes as $key => $value) { ?>
					<li>
						<?php if(isset($value['selected_icon']) && $value['selected_icon'] === 'image') : ?>
							<?php $img = wp_get_attachment_image_src( $value['image'] ); ?>
							<span class="rnb-attribute-image"><img src="<?php echo esc_url($img[0]); ?>" alt="img"></i></span>
						<?php else: ?>
							<span class="attribute-icon"><i class="fa <?php echo esc_attr($value['icon']); ?>"></i></span>
						<?php endif; ?>
						<span class="attribute-name"><?php echo esc_attr($value['name']); ?></span>
						<span class="attribute-vaue"> : <?php echo esc_attr($value['value']); ?></span>
					</li>
				<?php } ?>
			</ul>
		</div>
		<?php endif; ?>
	<?php }


	/**
	 * Features tab callback function
	 *
	 * @version		1.7.0
	 * @access public
	 * @param null
	 * @return WC_Product or WC_Product_Rental_product
	 */
	public static function redq_features(){ ?>

		<?php $additional_features = WC_Product_Redq_Rental::redq_get_rental_non_payable_attributes('features'); ?>

		<?php if(isset($additional_features) && !empty($additional_features)): ?>
		<div class="item-extras">
			<ul class="attributes">
				<?php foreach ($additional_features as $key => $value) { ?>
					<?php
						if($value['availability'] === 'yes'){
							$checked = 'checked';
						}else{
							$checked = 'unchecked';
						}
					 ?>
					<li class="<?php echo esc_attr($checked); ?>">
						<?php echo esc_attr($value['name']); ?>
					</li>
				<?php } ?>
			</ul>
		</div>
		<?php endif; ?>

	<?php }


}

new WC_Redq_Rental_Tabs();





