<?php

	/*
	*
	*	Swift Page Builder - Products Function Class
	*	------------------------------------------------
	*	Swift Framework
	* 	Copyright Swift Ideas 2013 - http://www.swiftideas.net
	*
	*/

	function sf_mini_product_items($asset_type, $category, $item_count, $sidebar_config, $width) {

		global $woocommerce, $catalog_mode;

		$product_list_output = $image = "";
		$args = array();

		// ARRAY ARGUMENTS
		if ($asset_type == "latest-products") {
			$args = array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'product_cat'         => $category,
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => $item_count,
				'meta_query'          => WC()->query->get_meta_query(),
				'tax_query'           => WC()->query->get_tax_query(),
			);
		} else if ($asset_type == "featured-products") {
			$meta_query  = WC()->query->get_meta_query();
			$tax_query   = WC()->query->get_tax_query();
			$tax_query[] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
				'operator' => 'IN',
			);
			
			$args = array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'product_cat'         => $category,
				'posts_per_page'      => $item_count,
				'meta_query'          => $meta_query,
				'tax_query'           => $tax_query,
			);
		} else if ($asset_type == "top-rated") {
			$args = array(
				'posts_per_page' => $item_count,
				'product_cat' => $category,
				'no_found_rows'  => 1,
				'post_status'    => 'publish',
				'post_type'      => 'product',
				'meta_key'       => '_wc_average_rating',
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
				'meta_query'     => WC()->query->get_meta_query(),
				'tax_query'      => WC()->query->get_tax_query(),
			);
		} else if ($asset_type == "recently-viewed") {

			// Get recently viewed product cookies data
			$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
			$viewed_products = array_filter( array_map( 'absint', $viewed_products ) );

			// If no data, quit
			if ( empty( $viewed_products ) )
				return '<p class="no-products">'.__( "You haven't viewed any products yet.", 'neighborhood').'</p>';

			// Create query arguments array
		    $args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'product_cat' => $category,
					'ignore_sticky_posts'   => 1,
    				'posts_per_page' => $item_count,
    				'no_found_rows'  => 1,
    				'post__in'       => $viewed_products,
    				'orderby'        => 'rand'
    			);

			// Add meta_query to query args
			//$args['meta_query'] = array();

		    // Check products stock status
		    //$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();

		} else if ($asset_type == "sale-products") {
			$args = array(
				'posts_per_page' => $item_count,
				'no_found_rows'  => 1,
				'post_status'    => 'publish',
				'post_type'      => 'product',
				'product_cat'    => $category,
				'meta_query'     => WC()->query->get_meta_query(),
				'tax_query'      => WC()->query->get_tax_query(),
				'post__in'       => array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
			);
		} else {
			$args = array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => $item_count,
				'meta_key'            => 'total_sales',
				'orderby'             => 'meta_value_num',
				'meta_query'          => WC()->query->get_meta_query(),
				'tax_query'           => WC()->query->get_tax_query(),
				'product_cat' 		  => $category,
			);
		}

		// OUTPUT PRODUCTS
	    $products = new WP_Query( $args );

	    if ( $products->have_posts() ) {

	       $product_list_output .= '<ul class="mini-list mini-'.$asset_type.'">';

	       while ( $products->have_posts() ) : $products->the_post();

	            $product_output = $rating_output = "";

	            global $product, $post, $wpdb, $woocommerce_loop;

	            if ( has_post_thumbnail() ) {
	    			$image_title 		= esc_attr( get_the_title( get_post_thumbnail_id() ) );
	    			$image 		= wp_get_attachment_image( get_post_thumbnail_id(), 'woocommerce_gallery_thumbnail' );

	    			if ($image) {
	    				$image_html = $image;
	    			}
	           	}

	           	if ( comments_open() ) {

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
	           	        $rating_output = '<div class="star-rating" title="'.sprintf(__('Rated %s out of 5', 'neighborhood'), $average).'" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"><span style="width:'.($average*16).'px"><span itemprop="ratingValue" class="rating">'.$average.'</span> '.__('out of 5', 'neighborhood').'</span></div>';

	           	    }
	           	}

	            $product_output .= '<li class="clearfix" itemscope itemtype="http://schema.org/Product">';

	           	if ($image) {
		            $product_output .= '<figure>';
		            $product_output .= '<a href="'.get_permalink($post->ID).'">';
		            $product_output .= $image_html;
		            $product_output .= '</a>';
		            $product_output .= '</figure>';
	            } else {
	            	$product_output .= '<figure>';
		            $product_output .= '<a href="'.get_permalink($post->ID).'">';
					$product_output .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'neighborhood' ) );
		            $product_output .= '</a>';
		            $product_output .= '</figure>';
	            }
	            $product_output .= '<div class="product-details">';
	            $product_output .= '<h5 itemprop="name"><a href="'.get_permalink($post->ID).'">'.get_the_title().'</a></h5>';

	       		if ($asset_type == "top-rated") {

	       			$product_output .= $rating_output;

	       		} else {

            		$size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
            		if ( function_exists('wc_get_product_category_list') ) {
            			$product_output .= wc_get_product_category_list( ', ', '<span class="product-cats">', '</span>' );
            		} else {
            			$product_output .= $product->get_categories( ', ', '<span class="product-cats">', '</span>' );
            		}

            	}
            	if (!$catalog_mode) {
	            $product_output .= '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span class="price">'.$product->get_price_html().'</span></div>';
	            }
	            $product_output .= '</div>';
	            $product_output .= '</li>';

	            $product_list_output .= $product_output;

	       endwhile;

	       wp_reset_query();
	       wp_reset_postdata();
	       woocommerce_reset_loop();

	       $product_list_output .= '</ul>';

	       return $product_list_output;
	    }

	}

	if ( ! function_exists( 'sf_product_items' ) ) {
		function sf_product_items($asset_type, $category, $products, $columns, $carousel, $product_size, $item_count, $width) {

			global $woocommerce, $woocommerce_loop, $sf_carouselID;

			$woocommerce_loop['loop'] = 0;

			$args = array();

			global $sidebars;
			
			if ( $sf_carouselID == "" ) {
			    $sf_carouselID = 1;
			} else {
			    $sf_carouselID ++;
			}

			if ($columns) {
				$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', $columns );
			}
			add_filter('nhood_shop_columns', $columns);

			// CATEGORY ASSET OUTPUT
			if ($asset_type == "categories") {

				ob_start();

				$hide_empty = 1;

				$args = array(
					'hide_empty' => $hide_empty,
					'pad_counts' => true,
					//'child_of'   => $parent
				);

				$product_categories = array();
				if ($category != '') {
					$product_category_slugs = explode( ',', $category );
					foreach($product_category_slugs as $cat) {
						$category_obj = get_term_by( 'slug', $cat, 'product_cat' );
						array_push($product_categories, $category_obj);
					}
				} else {
					$product_categories = get_terms( 'product_cat', $args );
					if ( $hide_empty ) {
						foreach ( $product_categories as $key => $category ) {
							if ( $category->count == 0 ) {
								unset( $product_categories[ $key ] );
							}
						}
					}

					if ( $item_count ) {
						$product_categories = array_slice( $product_categories, 0, $item_count );
					}
				}

				ob_start();

				if ( $product_categories ) {

					if ($carousel == "yes") { ?>

						<div class="product-carousel carousel-wrap">
	
							<ul class="products list-<?php echo $asset_type; ?> carousel-items"
							     id="carousel-<?php echo $sf_carouselID; ?>" data-columns="<?php echo $columns; ?>">
	
								<?php foreach ( $product_categories as $category ) {
									wc_get_template( 'content-product_cat.php', array(
										'category' => $category
									) );
								} ?>
	
							</ul>
	
							<a href="#" class="carousel-prev"><i class="fas fa-chevron-left"></i></a><a href="#" class="carousel-next"><i class="fas fa-chevron-right"></i></a>
	
						</div>

					<?php } else {
					
						woocommerce_product_loop_start();

						foreach ( $product_categories as $category ) {

							wc_get_template( 'content-product_cat.php', array(
								'category' => $category
							) );

						}

						woocommerce_product_loop_end();
						
					}

				}

				woocommerce_reset_loop();
				$product_list_output = ob_get_contents();
				ob_end_clean();

				wp_reset_query();
				wp_reset_postdata();

				return $product_list_output;
			}

			// ARRAY ARGUMENTS
			if ($asset_type == "latest-products") {
				$args = array(
					'post_type' => 'product',
					'post_status' => 'publish',
					'product_cat' => $category,
					'ignore_sticky_posts'   => 1,
					'posts_per_page' => $item_count
				);
				$args['meta_query'] = array();
				$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
				$args['meta_query'][] = $woocommerce->query->visibility_meta_query();
			} else if ($asset_type == "featured-products") {
			
				$meta_query  = WC()->query->get_meta_query();
				$tax_query   = WC()->query->get_tax_query();
				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
					'operator' => 'IN',
				);
				
				$args = array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'product_cat'         => $category,
					'posts_per_page'      => $item_count,
					'meta_query'          => $meta_query,
					'tax_query'           => $tax_query,
				);
				
			} else if ($asset_type == "top-rated") {
				$args = array(
					'posts_per_page' => $item_count,
					'product_cat' => $category,
					'no_found_rows'  => 1,
					'post_status'    => 'publish',
					'post_type'      => 'product',
					'meta_key'       => '_wc_average_rating',
					'orderby'        => 'meta_value_num',
					'order'          => 'DESC',
					'meta_query'     => WC()->query->get_meta_query(),
					'tax_query'      => WC()->query->get_tax_query(),
				);
			} else if ($asset_type == "recently-viewed") {

				// Get recently viewed product cookies data
				$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
				$viewed_products = array_filter( array_map( 'absint', $viewed_products ) );

				// If no data, quit
				if ( empty( $viewed_products ) )
					return '<p class="no-products">'.__( "You haven't viewed any products yet.", 'neighborhood').'</p>';

				// Create query arguments array
			    $args = array(
						'post_type'      => 'product',
						'post_status'    => 'publish',
						'product_cat' => $category,
						'ignore_sticky_posts'   => 1,
	    				'posts_per_page' => $item_count,
	    				'no_found_rows'  => 1,
	    				'post__in'       => $viewed_products,
	    				'orderby'        => 'rand'
	    			);

				// Add meta_query to query args
				//$args['meta_query'] = array();

			    // Check products stock status
			    //$args['meta_query'][] = $woocommerce->query->stock_status_meta_query();

			} else if ($asset_type == "sale-products") {
				$args = array(
					'posts_per_page' => $item_count,
					'no_found_rows'  => 1,
					'post_status'    => 'publish',
					'post_type'      => 'product',
					'product_cat'    => $category,
					'meta_query'     => WC()->query->get_meta_query(),
					'tax_query'      => WC()->query->get_tax_query(),
					'post__in'       => array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
				);
			} else if ( $asset_type == "selected-products" ) {
	            $meta_query   = array();
	            $meta_query[] = WC()->query->visibility_meta_query();
	            $meta_query[] = WC()->query->stock_status_meta_query();
	            $meta_query   = array_filter( $meta_query );

				$product_ids = explode(',', $products);
	            $args = array(
	                'posts_per_page' => -1,
	                'no_found_rows'  => 1,
	                'post_status'    => 'publish',
	                'post_type'      => 'product',
	                'meta_query'     => $meta_query,
	                'post__in'       => array_merge( array( 0 ), $product_ids )
	            );
			} else {

				$args = array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'posts_per_page'      => $item_count,
					'meta_key'            => 'total_sales',
					'orderby'             => 'meta_value_num',
					'meta_query'          => WC()->query->get_meta_query(),
					'tax_query'           => WC()->query->get_tax_query(),
					'product_cat' 		  => $category,
				);

			}

			ob_start();

			// OUTPUT PRODUCTS
		    $products = new WP_Query( $args );

			if ( $products->have_posts() ) { ?>

				<?php if ($carousel == "yes") { ?>

					<div class="product-carousel carousel-wrap">

						<ul class="products list-<?php echo $asset_type; ?> carousel-items"
						     id="carousel-<?php echo $sf_carouselID; ?>" data-columns="<?php echo $columns; ?>">

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>

						</ul>

						<a href="#" class="carousel-prev"><i class="fas fa-chevron-left"></i></a><a href="#" class="carousel-next"><i class="fas fa-chevron-right"></i></a>

					</div>

				<?php } else {  ?>

				<ul class="products list-<?php echo $asset_type; ?> columns-<?php echo $columns; ?>" data-columns="<?php echo $columns; ?>">

					<?php while ( $products->have_posts() ) : $products->the_post(); ?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>

				</ul>

				<?php } ?>

			<?php }

	       $product_list_output = ob_get_contents();
	       ob_end_clean();

	       wp_reset_query();
	       wp_reset_postdata();
	       woocommerce_reset_loop();

	       return $product_list_output;

		}
	}


	/**
	 * Track recently viewed products and set cookie.
	 *
	 * @since Neighborhood 3.4.32
	 * @link https://github.com/woocommerce/woocommerce/issues/9724#issuecomment-160618200
	 */
	if ( !function_exists('sf_custom_track_product_view') ) {
		function sf_custom_track_product_view() {
		    if ( ! is_singular( 'product' ) ) {
		        return;
		    }

		    global $post;

		    if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) )
		        $viewed_products = array();
		    else
		        $viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );

		    if ( ! in_array( $post->ID, $viewed_products ) ) {
		        $viewed_products[] = $post->ID;
		    }

		    if ( sizeof( $viewed_products ) > 15 ) {
		        array_shift( $viewed_products );
		    }

		    // Store for session only
		    wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
		}
		add_action( 'template_redirect', 'sf_custom_track_product_view', 20 );
	}

?>