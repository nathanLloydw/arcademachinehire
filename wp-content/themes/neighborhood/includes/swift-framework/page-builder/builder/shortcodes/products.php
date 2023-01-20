<?php

class SwiftPageBuilderShortcode_spb_products_mini extends SwiftPageBuilderShortcode {

    protected function content($atts, $content = null) {

		    $title = $asset_type = $width = $el_class = $output = $items = $el_position = '';
		
	        extract(shortcode_atts(array(
		        'title' => '',
		        'asset_type' => 'best-sellers',
	        	'item_count' => '4',
	        	'category' => '',
	        	'el_position' => '',
	        	'width' => '1/4',
	        	'el_class' => ''
	        ), $atts));
	        
	        
			/* SIDEBAR CONFIG
			================================================== */			
			$sidebar_config = sf_get_post_meta(get_the_ID(), 'sf_sidebar_config', true);
				        
			$sidebars = '';
			if (($sidebar_config == "left-sidebar") || ($sidebar_config == "right-sidebar")) {
			$sidebars = 'one-sidebar';
			} else if ($sidebar_config == "both-sidebars") {
			$sidebars = 'both-sidebars';
			} else {
			$sidebars = 'no-sidebars';
			}
    		    		
			/* PRODUCT ITEMS
			================================================== */	
			$items = sf_mini_product_items($asset_type, $category, $item_count, $sidebars, $width);
    		
    		
    		$el_class = $this->getExtraClass($el_class);
            $width = spb_translateColumnWidthToSpan($width);
            
            $output .= "\n\t".'<div class="product_list_widget woocommerce spb_content_element '.$width.$el_class.'">';
            $output .= "\n\t\t".'<div class="spb_wrapper">';
            $output .= ($title != '' ) ? "\n\t\t\t".'<h4 class="spb_heading"><span>'.$title.'</span></h4>' : '';
            $output .= "\n\t\t\t\t".$items;
            $output .= "\n\t\t".'</div> '.$this->endBlockComment('.spb_wrapper');
            $output .= "\n\t".'</div> '.$this->endBlockComment($width);
    
            $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
            
            global $has_products;
            $has_products = true;
            
            return $output;
		
    }
}

SPBMap::map( 'spb_products_mini', array(
    "name"		=> __("Products (Mini)", 'neighborhood'),
    "base"		=> "spb_products_mini",
    "class"		=> "spb-products-mini",
    "icon"      => "spb-icon-products-mini",
    "params"	=> array(
	    array(
	        "type" => "textfield",
	        "heading" => __("Widget title", 'neighborhood'),
	        "param_name" => "title",
	        "value" => "",
	        "description" => __("Heading text. Leave it empty if not needed.", 'neighborhood')
	    ),
	    array(
	        "type" => "dropdown",
	        "heading" => __("Asset type", 'neighborhood'),
	        "param_name" => "asset_type",
	        "value" => array(
	        	__('Best Sellers', 'neighborhood') => "best-sellers",
	        	__('Latest Products', 'neighborhood') => "latest-products",
	        	__('Top Rated', 'neighborhood') => "top-rated",
	        	__('Sale Products', 'neighborhood') => "sale-products",
	        	__('Recently Viewed', 'neighborhood') => "recently-viewed",
	        	__('Featured Products', 'neighborhood') => "featured-products"
	        	),
	        "description" => __("Select the order of the products you'd like to show.", 'neighborhood')
	    ),
	    array(
	        "type" => "textfield",
	        "heading" => __("Product category", 'neighborhood'),
	        "param_name" => "category",
	        "value" => "",
	        "description" => __("Optionally, provide the category slugs for the products you want to show (comma seperated). i.e. trainer,dress,bag.", 'neighborhood')
	    ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Number of items", 'neighborhood'),
            "param_name" => "item_count",
            "value" => "4",
            "description" => __("The number of products to show.", 'neighborhood')
        ),
        array(
            "type" => "textfield",
            "heading" => __("Extra class name", 'neighborhood'),
            "param_name" => "el_class",
            "value" => "",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'neighborhood')
        )
    )
) );


class SwiftPageBuilderShortcode_spb_products extends SwiftPageBuilderShortcode {

    protected function content($atts, $content = null) {

		    $title = $asset_type = $carousel = $product_size = $width = $sidebars = $el_class = $output = $items = $el_position = '';
		
	        extract(shortcode_atts(array(
		        'title' => '',
		        'asset_type' => 'best-sellers',
		        'carousel' => 'no',
		        'product_size' => 'standard',
	        	'item_count' => '8',
	        	'columns'		=> '4',
	        	'category' => '',
	        	'products' => '',
	        	'el_position' => '',
	        	'width' => '1/1',
	        	'el_class' => ''
	        ), $atts));
	        
	   	    		
			/* PRODUCT ITEMS
			================================================== */	
			$items = sf_product_items($asset_type, $category, $products, $columns, $carousel, $product_size, $item_count, $width);
    		
    		$el_class = $this->getExtraClass($el_class);
            $width = spb_translateColumnWidthToSpan($width);
            
            $output .= "\n\t".'<div class="product_list_widget products-'.$product_size.' woocommerce spb_content_element '.$width.$el_class.'">';
            $output .= "\n\t\t".'<div class="spb_wrapper">';
            $output .= ($title != '' ) ? "\n\t\t\t".'<h4 class="spb_heading"><span>'.$title.'</span></h4>' : '';
            $output .= "\n\t\t\t\t".$items;
            $output .= "\n\t\t".'</div> '.$this->endBlockComment('.spb_wrapper');
            $output .= "\n\t".'</div> '.$this->endBlockComment($width);
    
            $output = $this->startRow($el_position) . $output . $this->endRow($el_position);
            
            if ($carousel == "yes") {
            	global $include_carousel;
            	$include_carousel = true;
            	
            }
            global $include_isotope, $has_products;
            $include_isotope = true;
            $has_products = true;

            return $output;
		
    }
}

SPBMap::map( 'spb_products', array(
    "name"		=> __("Products", 'neighborhood'),
    "base"		=> "spb_products",
    "class"		=> "spb-products",
    "icon"      => "spb-icon-products",
    "params"	=> array(
	    array(
	        "type" => "textfield",
	        "heading" => __("Widget title", 'neighborhood'),
	        "param_name" => "title",
	        "value" => "",
	        "description" => __("Heading text. Leave it empty if not needed.", 'neighborhood')
	    ),
	    array(
	        "type" => "dropdown",
	        "heading" => __("Asset type", 'neighborhood'),
	        "param_name" => "asset_type",
	        "value" => array(
	        	__('Best Sellers', 'neighborhood') => "best-sellers",
	        	__('Latest Products', 'neighborhood') => "latest-products",
	        	__('Top Rated', 'neighborhood') => "top-rated",
	        	__('Sale Products', 'neighborhood') => "sale-products",
	        	__('Recently Viewed', 'neighborhood') => "recently-viewed",
	        	__('Featured Products', 'neighborhood') => "featured-products",
	        	__('Categories', 'neighborhood') => "categories",
	        	__('Selected Products', 'neighborhood')   => "selected-products"
	        	),
	        "description" => __("Select the display of products you'd like to show.", 'neighborhood')
	    ),
	    array(
	        "type" => "textfield",
	        "heading" => __("Product category", 'neighborhood'),
	        "param_name" => "category",
	        "value" => "",
	        "description" => __("Optionally, provide the category slugs for the products you want to show (comma seperated). i.e. trainer,dress,bag. NOTE: This is not used for categories asset type.", 'neighborhood')
	    ),
	    array(
            "type"        => "textfield",
            "heading"     => __( "Products", 'neighborhood' ),
            "param_name"  => "products",
            "value"       => "",
            "description" => __( "Select specific products to show here, providing the Product ID in comma delimited format. NOTE: Only used with selected products asset type.", 'neighborhood' )
        ),
        array(
            "type" => "dropdown",
            "heading" => __("Column count", 'neighborhood'),
            "param_name" => "columns",
            "value" => array("4", "3", "2", "1"),
            "description" => __("How many product columns to display.", 'neighborhood')
        ),
	    array(
	        "type" => "dropdown",
	        "heading" => __("Carousel", 'neighborhood'),
	        "param_name" => "carousel",
	        "value" => array(
	        	__('Yes', 'neighborhood') => "yes",
	        	__('No', 'neighborhood') => "no",
	        	),
	        "description" => __("Select if you'd like the asset to be a carousel.", 'neighborhood')
	    ),
	    array(
	        "type" => "dropdown",
	        "heading" => __("Product Size", 'neighborhood'),
	        "param_name" => "product_size",
	        "value" => array(
	        	__('Standard', 'neighborhood') => "standard",
	        	__('Mini', 'neighborhood') => "mini",
	        	),
	        "description" => __("Select whether you would like the product size to be standard, or mini. Mini shows 6 products in a row on a page with no sidebars.", 'neighborhood')
	    ),
        array(
            "type" => "textfield",
            "class" => "",
            "heading" => __("Number of items", 'neighborhood'),
            "param_name" => "item_count",
            "value" => "8",
            "description" => __("The number of products to show.", 'neighborhood')
        ),
        array(
            "type" => "textfield",
            "heading" => __("Extra class name", 'neighborhood'),
            "param_name" => "el_class",
            "value" => "",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'neighborhood')
        )
    )
) );

?>