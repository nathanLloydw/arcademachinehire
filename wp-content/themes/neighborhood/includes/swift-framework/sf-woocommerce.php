<?php
	/*
	*
	*	WooCommerce Functions & Hooks
	*	------------------------------------------------
	*	Swift Framework
	* 	Copyright Swift Ideas 2013 - http://www.swiftideas.net
	*
	*/


	/* BASIC FILTER HOOKS
	================================================== */
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
	add_action('woocommerce_before_main_content', 'neighborhood_wrapper_start', 10);
	add_action('woocommerce_after_main_content', 'neighborhood_wrapper_end', 10);

	/* Remove default thumbnail output */
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

	/* Remove default sale flash output */
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

	/* Add Shipping Calculator to after cart action */
	add_action( 'woocommerce_after_cart_table', 'woocommerce_shipping_calculator', 10 );

	/* Remove totals from cart collaterals */
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
	
	/* Remove default product item link */
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
	remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
	
	/* Wrapper modifications */
	function neighborhood_wrapper_start() {
	  echo '<div class="page-content clearfix">';
	}

	function neighborhood_wrapper_end() {
	  echo '</div>';
	}
	
	
	/* WOOCOMMERCE PRODUCT ADD WISHLIST TO HOVER
	================================================== */
	if ( !function_exists('neighborhood_product_wishlist_button') ) {
		function neighborhood_product_wishlist_button() {
			echo sf_wishlist_button();
		}
		add_action('woocommerce_after_shop_loop_item', 'neighborhood_product_wishlist_button', 15);
		add_action('woocommerce_after_add_to_cart_button', 'neighborhood_product_wishlist_button', 15);
	}
	
	 
	/* WOOCOMMERCE PRODUCT DEFAULT ADD TO CART TEXT
	================================================== */
	function neighborhood_custom_cart_button_text( $button_text ) {
		return $button_text ? $button_text : __( 'Add to Shopping Bag', 'neighborhood' );	 
	}
	add_filter( 'woocommerce_product_single_add_to_cart_text', 'neighborhood_custom_cart_button_text' );	


	/* PRODUCT ITEMS TEXT
	================================================== */
	function sf_product_items_text($count) {

		$product_item_text = "";

    	if ( $count > 1 ) {
        	$product_item_text = str_replace('%', number_format_i18n($count), __('% items', 'neighborhood'));
        } elseif ( $count == 0 ) {
        	$product_item_text = __('0 items', 'neighborhood');
        } else {
        	$product_item_text = __('1 item', 'neighborhood');
        }

        return $product_item_text;
	}


	/* REMOVE WOOCOMMERCE PRETTYPHOTO STYLES/SCRIPTS
    ================================================== */
    function sf_remove_woo_lightbox_js() {
    	if ( ! class_exists( 'WC_Quick_View' ) ) {
	        wp_dequeue_script( 'prettyPhoto' );
	        wp_dequeue_script( 'prettyPhoto-init' );
	    }
    }

    add_action( 'wp_enqueue_scripts', 'sf_remove_woo_lightbox_js', 99 );

    function sf_remove_woo_lightbox_css() {
        wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
    }

    add_action( 'wp_enqueue_styles', 'sf_remove_woo_lightbox_css', 99 );
	
	
	
	/* WOOCOMMERCE PRODUCT IMAGE HTML
	================================================== */
	function sf_single_product_image_html( $html, $post_ID ) {
	
		if ( version_compare( WC_VERSION, '2.7', '>=' ) ) { 
			return $html;
		}
		
		$options = get_option('sf_neighborhood_options');
		$product_image_srcset = false;
		if ( isset($options['product_image_srcset']) ) {
			$product_image_srcset = $options['product_image_srcset'];
		}
		
		if ( !$product_image_srcset ) {
			return $html;
		}
		
		$video_url = get_post_meta( $post_ID, '_video_url', true );
		$image_caption = $image_alt = $image_title = $caption_html = "";
		$image_id			= get_post_thumbnail_id();
		$image_meta 		= sf_get_attachment_meta( $image_id );
		
		if ( isset($image_meta) ) {
			$image_caption 		= esc_attr( $image_meta['caption'] );
			$image_title 		= esc_attr( $image_meta['title'] );
			$image_alt 			= esc_attr( $image_meta['alt'] );
		}
		$image_link  		= wp_get_attachment_url( $image_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
		$image         		= get_the_post_thumbnail( $post_ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
			'title'	=> $image_title,
			'alt'	=> $image_title,
			'class' => 'product-slider-image',
			'data-zoom-image' => $image_link
		) );							
		$thumb_image = wp_get_attachment_url( $image_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
	
		if ( $image_caption != "" ) {
			$caption_html = '<div class="img-caption">' . $image_caption . '</div>';
		}
	
		if ( $video_url != '' ) {
			return '<div class="video-wrap" data-thumb="' . $thumb_image . '">' . $html . '</div>';
		} else {
			return sprintf( '<li itemprop="image" data-thumb="%s">%s%s<a href="%s" itemprop="image" class="woocommerce-main-image zoom lightbox" data-rel="ilightbox[product]" data-caption="%s" title="%s" alt="%s"><i class="fas fa-search-plus"></i></a></li>', $thumb_image, $caption_html, $image, $image_link, $image_caption, $image_title, $image_alt );
		}
	}
	add_filter('woocommerce_single_product_image_html', 'sf_single_product_image_html', 10, 2);
	
						
	/* WOOCOMMERCE PRODUCT IMAGE THUMBS HTML
	================================================== */
	function sf_single_product_image_thumbnail_html( $html, $attachment_id, $post_ID = '', $image_class = '' ) {
	
		if ( version_compare( WC_VERSION, '2.7', '>=' ) ) { 
			return $html;
		}
		
		$options = get_option('sf_neighborhood_options');
		$product_image_srcset = false;
		if ( isset($options['product_image_srcset']) ) {
			$product_image_srcset = $options['product_image_srcset'];
		}
		
		if ( !$product_image_srcset ) {
			//return $html;
		}
		 
		$image_caption = $image_alt = $image_title = $caption_html = "";
		$image_id = $attachment_id;
		$image_meta = sf_get_attachment_meta( $image_id );
		
		if ( isset($image_meta) ) {
			$image_caption 		= esc_attr( $image_meta['caption'] );
			$image_title 		= esc_attr( $image_meta['title'] );
			$image_alt 			= esc_attr( $image_meta['alt'] );
		}
		
		$image_link  = wp_get_attachment_url( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
		$thumb_image = wp_get_attachment_url( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
		$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), false, array(
			'title'	=> $image_title,
			'alt'	=> $image_title,
			'class' => 'product-slider-image',
			'data-zoom-image' => $image_link
		) );
	
		if ( $image_caption != "" ) {
			$caption_html = '<div class="img-caption">' . $image_caption . '</div>';
		}
		return '<li itemprop="image" data-thumb="'.$thumb_image.'">' . $image . '' . $caption_html . '<a href="'.$image_link.'" itemprop="image" class="woocommerce-main-image zoom lightbox" data-rel="ilightbox[product]" data-caption="'.$image_caption.'" title="'.$image_title.'" alt="'.$image_alt.'"><i class="fas fa-search-plus"></i></a></li>';
	}
	add_filter('woocommerce_single_product_image_thumbnail_html', 'sf_single_product_image_thumbnail_html', 10, 4);


	/* WOOCOMMERCE CONTENT FUNCTIONS
	================================================== */

	function sf_get_product_stars() {

		$stars_output = "";

	    global $wpdb;
	    global $post;
	    $count = $wpdb->get_var("
		    SELECT COUNT(meta_value) FROM $wpdb->commentmeta
		    LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
		    WHERE meta_key = 'rating'
		    AND comment_post_ID = $post->ID
		    AND comment_approved = '1'
		    AND meta_value > 0
		");

		$rating = $wpdb->get_var("
		    SELECT SUM(meta_value) FROM $wpdb->commentmeta
		    LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
		    WHERE meta_key = 'rating'
		    AND comment_post_ID = $post->ID
		    AND comment_approved = '1'
		");

		if ( $count > 0 ) {

		    $average = number_format($rating / $count, 2);

		    $stars_output .= '<div class="starwrapper" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';

		    $stars_output .= '<span class="star-rating" title="'.sprintf(__('Rated %s out of 5', 'neighborhood'), $average).'"><span style="width:'.($average*16).'px"><span itemprop="ratingValue" class="rating">'.$average.'</span> </span></span>';

		    $stars_output .= '</div>';
		}

		return $stars_output;
	}

	function is_out_of_stock() {
	    global $post;
	    $post_id = $post->ID;
	    $stock_status = sf_get_post_meta($post_id, '_stock_status',true);

	    if ($stock_status == 'outofstock') {
	    return true;
	    } else {
	    return false;
	    }
	}


	/* ADD TO CART HEADER RELOAD
	================================================== */
	if (!function_exists('sf_woo_header_add_to_cart_fragment')) {
		function sf_woo_header_add_to_cart_fragment( $fragments ) {
			global $woocommerce;

			ob_start();

			$cart_total = "";
			if ( WC()->cart->prices_include_tax ) { 
			$cart_total = WC()->cart->get_cart_total();
			} else {
			$cart_total = WC()->cart->get_cart_subtotal();
			}
			$cart_count = WC()->cart->get_cart_contents_count();
			$cart_count_text = sf_product_items_text($cart_count);
			$price_display_suffix  = get_option( 'woocommerce_price_display_suffix' );
			
			$options = get_option('sf_neighborhood_options');
			$show_cart_count = false;
			if (isset($options['show_cart_count'])) {
				$show_cart_count = $options['show_cart_count'];
			}
			?>

			<li class="parent shopping-bag-item">
				<?php if ($show_cart_count) { ?>
				<a class="cart-contents" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e('View your shopping bag', 'neighborhood'); ?>"><i class="sf-cart"></i><?php echo $cart_total; ?> (<?php echo $cart_count; ?>)</a>
				<?php } else { ?>
				<a class="cart-contents" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e('View your shopping bag', 'neighborhood'); ?>"><i class="sf-cart"></i><?php echo $cart_total; ?></a>
				<?php }  ?>

				<ul class="sub-menu">
					<li>
						<div class="shopping-bag">

							<?php if ( ! WC()->cart->is_empty() ) { ?>

								<div class="bag-header"><?php echo $cart_count_text; ?> <?php _e('in the shopping bag', 'neighborhood'); ?></div>

								<div class="bag-contents">

									<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) { ?>
									                                    
	                                    <?php
	                                    $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
	                                    $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
	                                    ?>
	
	                                    <?php  
										
										$variation_id_class = $variation_id = '';
										
                                        if ( $cart_item['variation_id'] > 0 ) {
                                             $variation_id_class = ' product-var-id-' .  $cart_item['variation_id']; 
                                        	 $variation_id = $cart_item['variation_id'];
                                        } 
										
                                        if ( $cart_item['variation_id'] > 0 )
                                             $variation_id_class = ' product-var-id-' .  $cart_item['variation_id']; 
										 
	                                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
	                                    	
	                                    	$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
	                						$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
	                						$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
	                						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
	                						$product_title       = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
	                						$product_short_title = ( strlen( $product_title ) > 25 ) ? substr( $product_title, 0, 22 ) . '...' : $product_title;
	                                    ?>

								        	<div class="bag-product clearfix">

								            	<figure>
								            		<a class="bag-product-img" href="<?php echo esc_url( $product_permalink ); ?>">
								            	    	<?php echo $_product->get_image(); ?>
								            	    </a>
								            	</figure>

									            <div class="bag-product-details">
									           		<div class="bag-product-title">
									           			<a href="<?php echo esc_url( $product_permalink ); ?>">
									           				<?php echo apply_filters( 'woocommerce_cart_widget_product_title', $product_title, $_product ); ?>
									           			</a>
									           		</div>
									            	<div
									            	    class="bag-product-price"><?php _e( "Unit Price:", 'neighborhood' ); ?> <?php echo $product_price; ?></div>
									            	<div
									            	    class="bag-product-quantity"><?php _e( 'Quantity:', 'neighborhood' ); ?> <?php echo $cart_item['quantity']; ?></div>
									            	<?php if ( $price_display_suffix ) { ?>
									            		<small class="woocommerce-price-suffix"><?php echo $price_display_suffix; ?></small>
								            		<?php } ?>
									            </div>

									            <?php
									            if (function_exists('wc_get_cart_remove_url')) {
													echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
	                            							'<a href="%s" class="remove remove-product" title="%s" data-ajaxurl="'.admin_url( 'admin-ajax.php' ).'" data-product-qty="'. $cart_item['quantity'] .'"  data-product-id="%s" data-product_sku="%s" data-variation-id="%s">&times;</a>',
	                            							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
	                            							__( 'Remove this item', 'neighborhood' ),
	                            							esc_attr( $product_id ),
	                            							esc_attr( $_product->get_sku() ),
	                            							esc_attr( $variation_id )
	                            						), $cart_item_key );
												} else {
													echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
	                            							'<a href="%s" class="remove remove-product" title="%s" data-ajaxurl="'.admin_url( 'admin-ajax.php' ).'" data-product-qty="'. $cart_item['quantity'] .'"  data-product-id="%s" data-product_sku="%s" data-variation-id="%s">&times;</a>',
	                            							esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
	                            							__( 'Remove this item', 'neighborhood' ),
	                            							esc_attr( $product_id ),
	                            							esc_attr( $_product->get_sku() ),
	                            							esc_attr( $variation_id )
	                            						), $cart_item_key );
												}
									            ?>

									    	</div>

								    	<?php } ?>

								    <?php } ?>

							    </div>

							    <div class="bag-buttons">

							    	<a class="sf-roll-button bag-button" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><span><?php _e('View shopping bag', 'neighborhood'); ?></span><span><?php _e('View shopping bag', 'neighborhood'); ?></span></a>

							    	<a class="sf-roll-button checkout-button" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><span><?php _e('Proceed to checkout', 'neighborhood'); ?></span><span><?php _e('Proceed to checkout', 'neighborhood'); ?></span></a>

								</div>

							<?php } else { ?>

								<div class="bag-header"><?php _e("0 items in the shopping bag", 'neighborhood'); ?></div>

								<div class="bag-empty"><?php _e('Unfortunately, your shopping bag is empty.','neighborhood'); ?></div>

								<div class="bag-buttons">

									<?php $shop_page_url = get_permalink( wc_get_page_id( 'shop' ) ); ?>

									<a class="sf-roll-button shop-button" href="<?php echo esc_url( $shop_page_url ); ?>"><span><?php _e('Go to the shop', 'neighborhood'); ?></span><span><?php _e('Go to the shop', 'neighborhood'); ?></span></a>

								</div>

							<?php } ?>

							</div>
						</li>
					</ul>
				</li>

			<?php

			$fragments['.shopping-bag-item'] = ob_get_clean();

			return $fragments;

		}
		add_filter('woocommerce_add_to_cart_fragments', 'sf_woo_header_add_to_cart_fragment');
	}

	/* WISHLIST BUTTON
	================================================== */
	if (!function_exists('sf_wishlist_button')) {
		function sf_wishlist_button() {

			global $product, $yith_wcwl;

			if ( class_exists( 'YITH_WCWL_UI' ) )  {
				$url = YITH_WCWL()->get_wishlist_url();
				$product_type = method_exists( $product, 'get_type' ) ? $product->get_type() : $product->product_type;		
	        	$default_wishlists = is_user_logged_in() ? YITH_WCWL()->get_wishlists( array( 'is_default' => true ) ) : false;
			
				if ( ! empty( $default_wishlists ) ) {
		        	$default_wishlist = $default_wishlists[0]['ID'];
	        	}
	        	else {
		        	$default_wishlist = false;
	        	}
			
				$exists = YITH_WCWL()->is_product_in_wishlist( $product->get_id() , $default_wishlist);			 

				$classes = get_option( 'yith_wcwl_use_button' ) == 'yes' ? 'class="add_to_wishlist single_add_to_wishlist button alt"' : 'class="add_to_wishlist"';

				$html  = '<div class="yith-wcwl-add-to-wishlist">';
				    $html .= '<div class="yith-wcwl-add-button';  // the class attribute is closed in the next row

				    $html .= $exists ? ' hide" style="display:none;"' : ' show"';
					$url = method_exists( $yith_wcwl, 'get_addtowishlist_url' ) ? htmlspecialchars( $yith_wcwl->get_addtowishlist_url() ) : esc_url( add_query_arg( 'add_to_wishlist', $product_id ) );
				    $html .= '><a href="' . $url . '" data-product-id="' . $product->get_id() . '" data-product-type="' . $product_type . '" ' . $classes . ' ><i class="fas fa-star"></i></a>';
				    $html .= '</div>';

				$html .= '<div class="yith-wcwl-wishlistaddedbrowse hide" style="display:none;"><span class="feedback">' . __( 'Product added to wishlist.', 'neighborhood' ) . '</span> <a href="' . $url . '"><i class="fas fa-check"></i></a></div>';
				$html .= '<div class="yith-wcwl-wishlistexistsbrowse ' . ( $exists ? 'show' : 'hide' ) . '" style="display:' . ( $exists ? 'block' : 'none' ) . '"><a href="' . $url . '"><i class="fas fa-check"></i></a></div>';
				$html .= '<div style="clear:both"></div><div class="yith-wcwl-wishlistaddresponse"></div>';

				$html .= '</div>';

				return $html;

			}

		}
	}


	/* SHOW PRODUCTS COUNT URL PARAMETER
	================================================== */
	if (isset($_GET['layout'])) {
		$page_layout = $_GET['layout'];
	}
	if ( !function_exists('sf_product_shop_count') ) {
		function sf_product_shop_count() {
			$default_count = 24;

			$count = isset($_GET['show_products']) ? $_GET['show_products'] : $default_count;

			if ($count === 'all') {
				$count = -1;
			}
			else if ( ! is_numeric($count)) {
				$count = $default_count;
			}

			return $count;
		}	
	}
	add_filter( 'loop_shop_per_page', 'sf_product_shop_count');


	/* SINGLE PRODUCT
	================================================== */
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
	remove_action( 'woocommerce_product_tabs', 'woocommerce_product_description_tab', 10 );
	remove_action( 'woocommerce_product_tab_panels', 'woocommerce_product_description_panel', 10 );

	add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 35);
	add_action( 'woocommerce_single_product_summary', 'sf_product_share', 45);

	if (!function_exists('sf_product_share')) {
		function sf_product_share() {
			global $post;
			$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), false, '' );
		?>
			<div class="product-share share-links clearfix">
				<span><?php _e("Share", 'neighborhood'); ?></span>
				<ul>
				    <li><a href="mailto:?subject=<?php the_title(); ?>&body=<?php echo strip_tags(apply_filters( 'woocommerce_short_description', $post->post_excerpt )); ?> <?php the_permalink(); ?>" class="product_share_email"><i class="fas fa-envelope"></i></a></li>
				    <li><a href="http://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>" onclick="javascript:window.open(this.href,
				      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="product_share_facebook"><i class="fab fa-facebook"></i></a></li>
				    <li><a href="https://twitter.com/share?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" onclick="javascript:window.open(this.href,
				      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="product_share_twitter"><i class="fab fa-twitter"></i></a></li>
				    <li><a href="https://plus.google.com/share?url=<?php the_permalink(); ?>&title=<?php the_title(); ?>" onclick="javascript:window.open(this.href,
				      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fab fa-google-plus"></i></a></li>
				    <li><a href="//pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php echo $src[0]; ?>&description=<?php the_title(); ?>" onclick="javascript:window.open(this.href,
				      '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="product_share_pinterest"><i class="fab fa-pinterest"></i></a></li>
				</ul>
			</div>
		<?php }
	}

	if (!function_exists('sf_woo_help_bar')) {
		function sf_woo_help_bar() {
			$options = get_option('sf_neighborhood_options');
			$help_bar_text = __($options['help_bar_text'], 'neighborhood');
			$email_modal = __($options['email_modal'], 'neighborhood');
			$shipping_modal = __($options['shipping_modal'], 'neighborhood');
			$returns_modal = __($options['returns_modal'], 'neighborhood');
			$faqs_modal = __($options['faqs_modal'], 'neighborhood');
		?>
			<div class="help-bar clearfix">
				<span><?php echo do_shortcode($help_bar_text); ?></span>
				<ul>
				    <li><a href="#email-form" class="inline" data-toggle="modal"><?php _e("Email Customer Care", 'neighborhood'); ?></a></li>
				    <li><a href="#shipping-information" class="inline" data-toggle="modal"><?php _e("Shipping Information", 'neighborhood'); ?></a></li>
				    <li><a href="#returns-exchange" class="inline" data-toggle="modal"><?php _e("Returns & Exchange", 'neighborhood'); ?></a></li>
				    <li><a href="#faqs" class="inline" data-toggle="modal"><?php _e("FAQs", 'neighborhood'); ?></a></li>
				</ul>
			</div>

			<div id="email-form" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="email-form-modal" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="email-form-modal"><?php _e("Email Customer Care", 'neighborhood'); ?></h3>
				</div>
				<div class="modal-body">

					<?php echo do_shortcode($email_modal); ?>

				</div>
			</div>

			<div id="shipping-information" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="shipping-modal" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="shipping-modal"><?php _e("Shipping Information", 'neighborhood'); ?></h3>
				</div>
				<div class="modal-body">

					<?php echo do_shortcode($shipping_modal); ?>

				</div>
			</div>

			<div id="returns-exchange" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="returns-modal" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="returns-modal"><?php _e("Returns & Exchange", 'neighborhood'); ?></h3>
				</div>
				<div class="modal-body">

					<?php echo do_shortcode($returns_modal); ?>

				</div>
			</div>

			<div id="faqs" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="faqs-modal" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="faqs-modal"><?php _e("FAQs", 'neighborhood'); ?></h3>
				</div>
				<div class="modal-body">

					<?php echo do_shortcode($faqs_modal); ?>

				</div>
			</div>

		<?php }
		add_action( 'woocommerce_before_account_navigation', 'sf_woo_help_bar' );
	}


	/* WOO SHIPPING CALC BEFORE
	================================================== */
	if ( ! function_exists('sf_cart_shipping_calc_before')){
		function sf_cart_shipping_calc_before() {
			echo '<div class="shipping-calc-wrap">';
			echo '<h4 class="lined-heading"><span>'.__( 'Shipping Calculator', 'neighborhood' ).'</span></h4>';
		}
		add_action( 'woocommerce_before_shipping_calculator', 'sf_cart_shipping_calc_before' );
	}


	/* WOO SHIPPING CALC AFTER
	================================================== */
	if ( ! function_exists('sf_cart_shipping_calc_after')){
		function sf_cart_shipping_calc_after() {
			echo '</div>';
		}
		add_action( 'woocommerce_after_shipping_calculator', 'sf_cart_shipping_calc_after' );
	}
		
	
	/* WOO ADD TO CART BUTTON FILTER
	================================================== */
	
	if ( !function_exists( 'neighborhood_add_to_cart_link' ) ) {
		function neighborhood_add_to_cart_link( $link, $product ) {	
			$product_type = method_exists( $product, 'get_type' ) ? $product->get_type() : $product->product_type;
			$class = implode( ' ', array_filter( array(
									'button',
									'product_type_' . $product_type,
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : ''
							) ) );
									
			return sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" data-added_text="%s" class="%s">%s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( isset( $quantity ) ? $quantity : 1 ),
				esc_attr( $product->get_id() ),
				esc_attr( $product->get_sku() ),
				esc_attr( __( 'Added to cart', 'neighborhood' ) ),
				esc_attr( isset( $class ) ? $class : 'button' ),
				esc_html( $product->add_to_cart_text() )
			);
		}
		add_filter('woocommerce_loop_add_to_cart_link', 'neighborhood_add_to_cart_link', 10, 2 );
	}