<?php

	/*
	*
	*	Meta Box Functions
	*	------------------------------------------------
	*	Swift Framework
	* 	Copyright Swift Ideas 2013 - http://www.swiftideas.net
	*
	*/
	
	function sf_register_meta_boxes() {
		$prefix = 'sf_';
		
		global $meta_boxes;
		
		$meta_boxes = array();
			
		$options = get_option('sf_neighborhood_options');
		$default_page_heading_bg_alt = $options['default_page_heading_bg_alt'];
		$default_show_page_heading = $options['default_show_page_heading'];
		$default_sidebar_config = $options['default_sidebar_config'];
		$default_left_sidebar = $options['default_left_sidebar'];
		$default_right_sidebar = $options['default_right_sidebar'];
		
		if (!$default_page_heading_bg_alt || $default_page_heading_bg_alt == "") {
			$default_page_heading_bg_alt = "none";
		}
		if ($default_show_page_heading == "") {
			$default_show_page_heading = 1;
		}
		if ($default_sidebar_config == "") {
			$default_sidebar_config = "no-sidebars";
		}		
		if ($default_left_sidebar == "") {
			$default_left_sidebar = "Sidebar-1";
		}		
		if ($default_right_sidebar == "") {
			$default_right_sidebar = "Sidebar-1";
		}
		
		$default_product_sidebar_config = $default_product_left_sidebar = $default_product_right_sidebar = "";
		
		if (isset($options['default_product_sidebar_config'])) {
		$default_product_sidebar_config = $options['default_product_sidebar_config'];
		}
		if (isset($options['default_product_left_sidebar'])) {
		$default_product_left_sidebar = $options['default_product_left_sidebar'];
		}
		if (isset($options['default_product_right_sidebar'])) {
		$default_product_right_sidebar = $options['default_product_right_sidebar'];
		}	
		
		if ($default_product_sidebar_config == "") {
			$default_product_sidebar_config = "no-sidebars";
		}
		if ($default_product_left_sidebar == "") {
			$default_product_left_sidebar = "Sidebar-1";
		}		
		if ($default_product_right_sidebar == "") {
			$default_product_right_sidebar = "Sidebar-1";
		}
			
		/* Thumbnail Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'thumbnail_meta_box',
			'title' => __('Thumbnail Options', 'neighborhood'),
			'pages' => apply_filters('thumbnail_meta_box_post_types', array( 'post', 'portfolio' ) ),
			'context' => 'normal',
			'fields' => array(
		
				// THUMBNAIL TYPE
				array(
					'name' => __('Thumbnail type', 'neighborhood'),
					'id'   => "{$prefix}thumbnail_type",
					'type' => 'select',
					'options' => array(
						'none'		=> __('None', 'neighborhood'),
						'image'		=> __('Image', 'neighborhood'),
						'video'		=> __('Video', 'neighborhood'),
						'slider'	=> __('Slider', 'neighborhood')
					),
					'multiple' => false,
					'std'  => 'image',
					'desc' => __('Choose what will be used for the item thumbnail.', 'neighborhood'),
				),
				
				// THUMBNAIL IMAGE
				array(
					'name'	=> __('Thumbnail image', 'neighborhood'),
					'desc'  => __('The image that will be used as the thumbnail image.', 'neighborhood'),
					'id'    => "{$prefix}thumbnail_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
				
				// THUMBNAIL VIDEO
				array(
					'name' => __('Thumbnail video URL', 'neighborhood'),
					'id' => $prefix . 'thumbnail_video_url',
					'desc' => __('Enter the video url for the thumbnail. Only links from Vimeo & YouTube are supported.', 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// THUMBNAIL GALLERY
				array(
					'name'             => __('Thumbnail gallery', 'neighborhood'),
					'desc'             => __('The images that will be used in the thumbnail gallery.', 'neighborhood'),
					'id'               => "{$prefix}thumbnail_gallery",
					'type'             => 'image_advanced',
					'max_file_uploads' => 50,
				),
				
				// THUMBNAIL LINK TYPE
				array(
					'name' => __('Thumbnail link type', 'neighborhood'),
					'id'   => "{$prefix}thumbnail_link_type",
					'type' => 'select',
					'options' => array(
						'link_to_post'		=> __('Link to item', 'neighborhood'),
						'link_to_url'		=> __('Link to URL', 'neighborhood'),
						'link_to_url_nw'	=> __('Link to URL (New Window)', 'neighborhood'),
						'lightbox_thumb'	=> __('Lightbox to the thumbnail image', 'neighborhood'),
						'lightbox_image'	=> __('Lightbox to image (select below)', 'neighborhood'),
						'lightbox_video'	=> __('Lightbox to video (input below)', 'neighborhood')
					),
					'multiple' => false,
					'std'  => 'link-to-post',
					'desc' => __('Choose what link will be used for the image(s) and title of the item.', 'neighborhood'),
				),
				
				// THUMBNAIL LINK URL
				array(
					'name' => __('Thumbnail link URL', 'neighborhood'),
					'id' => $prefix . 'thumbnail_link_url',
					'desc' => __('Enter the url for the thumbnail link.', 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// THUMBNAIL LINK LIGHTBOX IMAGE
				array(
					'name'	=> __('Thumbnail link lightbox image', 'neighborhood'),
					'desc'  => __('The image that will be used as the lightbox image.', 'neighborhood'),
					'id'    => "{$prefix}thumbnail_link_image",
					'type'  => 'thickbox_image'
				),
				
				// THUMBNAIL LINK LIGHTBOX VIDEO
				array(
					'name' => __('Thumbnail link lightbox video URL', 'neighborhood'),
					'id' => $prefix . 'thumbnail_link_video_url',
					'desc' => __('Enter the video url for the thumbnail lightbox. Only links from Vimeo & YouTube are supported.', 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				)
			)
		);
		
		
		/* Detail Media Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'detail_media_meta_box',
			'title' => __('Detail Media Options', 'neighborhood'),
			'pages' => apply_filters('detail_media_meta_box_post_types', array( 'post', 'portfolio' ) ),
			'context' => 'normal',
			'fields' => array(
			
				// USE THUMBNAIL CONTENT FOR THE MAIN DETAIL DISPLAY
				array(
					'name' => __('Use the thumbnail content', 'neighborhood'),    // File type: checkbox
					'id'   => "{$prefix}thumbnail_content_main_detail",
					'type' => 'checkbox',
					'desc' => __('Uncheck this box if you wish to select different media for the main detail display.', 'neighborhood'),
					'std' => 0,
				),
				
				// DETAIL TYPE
				array(
					'name' => __('Post detail type', 'neighborhood'),
					'id'   => "{$prefix}detail_type",
					'type' => 'select',
					'options' => array(
						'none'		=> __('None', 'neighborhood'),
						'image'		=> __('Image', 'neighborhood'),
						'video'		=> __('Video', 'neighborhood'),
						'slider'	=> __('Standard Slider', 'neighborhood'),
						'layer-slider' => __('Layer Slider', 'neighborhood'),
						'custom' => __('Custom', 'neighborhood')
					),
					'multiple' => false,
					'std'  => 'image',
					'desc' => __('Choose what will be used for the post item detail.', 'neighborhood'),
				),
				
				// DETAIL IMAGE
				array(
					'name'	=> __('Post detail image', 'neighborhood'),
					'desc'  => __('The image that will be used as the post detail image.', 'neighborhood'),
					'id'    => "{$prefix}detail_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
				
				// DETAIL VIDEO
				array(
					'name' => __('Post detail video URL', 'neighborhood'),
					'id' => $prefix . 'detail_video_url',
					'desc' => __('Enter the video url for the post thumbnail. Only links from Vimeo & YouTube are supported.', 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// DETAIL GALLERY
				array(
					'name'             => __('Post detail gallery', 'neighborhood'),
					'desc'             => __('The images that will be used in the post detail gallery.', 'neighborhood'),
					'id'               => "{$prefix}detail_gallery",
					'type'             => 'image_advanced',
					'max_file_uploads' => 50,
				),
				
				// DETAIL REV SLIDER
				array(
					'name' => __('Revolution slider alias', 'neighborhood'),
					'id' => $prefix . 'detail_rev_slider_alias',
					'desc' => __("Enter the revolution slider alias for the slider that you want to show.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// DETAIL CUSTOM
				array(
					'name' => __('Custom detail display', 'neighborhood'),
					'desc' => __("If you'd like to provide your own detail media, please add it here", 'neighborhood'),
					'id'   => "{$prefix}custom_media",
					'type' => 'textarea',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
			)
		);
		
		
		/* Portfolio Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'portfolio_meta_box',
			'title' => __('Portfolio Meta', 'neighborhood'),
			'pages' => apply_filters('portfolio_meta_box_post_types', array(  'portfolio' ) ),
			'context' => 'normal',
			'fields' => array(
			
				// ITEM DETAILS OPTIONS SECTION
				array (
					'name' 	=> '',
					'title' => __('Portfolio Item Details', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_item_details",
				    'type' 	=> 'section'
				),
				
				// Sub Text
				array(
					'name' => __('Subtitle', 'neighborhood'),
					'id' => $prefix . 'portfolio_subtitle',
					'desc' => __("Enter a subtitle for use within the portfolio item index (optional).", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// Client
				array(
					'name' => __('Client', 'neighborhood'),
					'id' => $prefix . 'portfolio_client',
					'desc' => __("Enter the client's name (optional).", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// External Link
				array(
					'name' => __('External Link', 'neighborhood'),
					'id' => $prefix . 'portfolio_external_link',
					'desc' => __("Enter an external link for the item  (optional) (NOTE: INCLUDE HTTP://).", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
							
				// CUSTOM EXCERPT SECTION
				array (
					'name' 	=> '',
					'title' => __('Custom Excerpt', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_custom_excerpt",
				    'type' 	=> 'section'
				),
				
				// CUSTOM EXCERPT
				array(
					'name' => __('Custom excerpt', 'neighborhood'),
					'desc' => __("You can optionally write a custom excerpt here to display instead of the excerpt that is automatically generated.", 'neighborhood'),
					'id'   => "{$prefix}custom_excerpt",
					'type' => 'textarea',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
				
				// MAIN DETAIL SECTION
				array (
					'name' 	=> '',
					'title' => __('Main Detail Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_detail",
				    'type' 	=> 'section'
				),
				
				// SHOW PAGE TITLE
				array(
					'name' => __('Show page title', 'neighborhood'),    // File type: checkbox
					'id'   => "{$prefix}page_title",
					'type' => 'checkbox',
					'desc' => __('Show the page title at the top of the page.', 'neighborhood'),
					'std' => $default_show_page_heading,
				),
				
				// PAGE TITLE LINE 1
				array(
					'name' => __('Page Title', 'neighborhood'),
					'id' => $prefix . 'page_title_one',
					'desc' => __("Enter a custom page title if you'd like.", 'neighborhood'),
					'type'  => 'text',
					'std' => '',
				),
				
				// PAGE TITLE BACKGROUND
				array(
					'name' => __('Page Title Background', 'neighborhood'),
					'id'   => "{$prefix}page_title_bg",
					'type' => 'select',
					'options' => array(
						'none'			=> __('None', 'neighborhood'),
						'alt-one'		=> __('Alt 1', 'neighborhood'),
						'alt-two'		=> __('Alt 2', 'neighborhood'),
						'alt-three'		=> __('Alt 3', 'neighborhood'),
						'alt-four'		=> __('Alt 4', 'neighborhood'),
						'alt-five'		=> __('Alt 5', 'neighborhood'),
						'alt-six'		=> __('Alt 6', 'neighborhood'),
						'alt-seven'		=> __('Alt 7', 'neighborhood'),
						'alt-eight'		=> __('Alt 8', 'neighborhood'),
						'alt-nine'		=> __('Alt 9', 'neighborhood'),
						'alt-ten'		=> __('Alt 10', 'neighborhood')
					),
					'multiple' => false,
					'std'  => $default_page_heading_bg_alt,
					'desc' => __('Choose the background for the page title (configured in the Neighborhood Options panel).', 'neighborhood'),
				),
				
				// ALT BG PREVIEW
				array (
					'name' 	=> '',
				    'id' 	=> "{$prefix}altbg-preview",
				    'type' 	=> 'altbgpreview'
				),
				
				// HIDE DETAILS BAR
				array(
					'name' => __('Hide item details bar', 'neighborhood'),
					'id'   => "{$prefix}hide_details",
					'type' => 'checkbox',
					'desc' => __('Check this box to hide the item details on the detail page.', 'neighborhood'),
					'std' => 0,
				),
				
				// INCLUDE SOCIAL SHARING
				array(
					'name' => __('Include social sharing', 'neighborhood'),
					'id'   => "{$prefix}social_sharing",
					'type' => 'checkbox',
					'desc' => __('Check this box to show social sharing icons on the detail page.', 'neighborhood'),
					'std' => 1,
				),
							
				// SWIFT SLIDER ENTRY SECTION
				array (
					'name' 	=> '',
					'title' => __('Swift Slider Entry Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_detail",
				    'type' 	=> 'section'
				),
				
				// SWIFT SLIDER BACKGROUND IMAGE
				array(
					'name'	=> __('Slide background image', 'neighborhood'),
					'desc'  => __('The image that will be used as the slide image in the Swift Slider.', 'neighborhood'),
					'id'    => "{$prefix}posts_slider_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
				
				// SWIFT SLIDER CAPTION POSITION
				array(
					'name' => __('Caption Position', 'neighborhood'),
					'id'   => "{$prefix}caption_position",
					'type' => 'select',
					'options' => array(
						'caption-left'		=> __('Left', 'neighborhood'),
						'caption-right'		=> __('Right', 'neighborhood')
					),
					'multiple' => false,
					'std'  => 'caption-right',
					'desc' => __('Choose which side you would like to display the caption over the slide.', 'neighborhood'),
				),
				
				// MISC
				array (
					'name' 	=> '',
					'title' => __('Misc. Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_detail",
				    'type' 	=> 'section'
				),
				
				// Extra Page Class
				array(
					'name' => __('Extra page class', 'neighborhood'),
					'id' => $prefix . 'extra_page_class',
					'desc' => __("If you wish to add extra classes to the body class of the page (for custom css use), then please add the class(es) here.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				)
			)
		);
		
		
		/* Page Background Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'page_background_meta_box',
			'title' => __('Page Background Options', 'neighborhood'),
			'pages' => apply_filters('page_background_meta_box_post_types', array( 'post', 'portfolio', 'page' ) ),
			'context' => 'normal',
			'fields' => array(
	
				// BACKGROUND IMAGE
				array(
					'name'	=> __('Background Image', 'neighborhood'),
					'desc'  => __('The image that will be used as the page background image.', 'neighborhood'),
					'id'    => "{$prefix}background_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
				
				// BACKGROUND SIZE
				array(
					'name' => __('Background Image Size', 'neighborhood'),
					'desc' => __('For fullscreen images, choose Cover. For repeating patterns, choose Auto.', 'neighborhood'),
					'id'   => "{$prefix}background_image_size",
					'type' => 'select',
					'options' => array(
						'cover'		=> __('Cover', 'neighborhood'),
						'auto'		=> __('Auto', 'neighborhood')
					),
					'multiple' => false,
					'std'  => 'cover',
				)
			)
		);
		
		
		/* Post Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'post_meta_box',
			'title' => __('Post Meta', 'neighborhood'),
			'pages' => apply_filters('post_meta_box_post_types', array( 'post' ) ),
			'context' => 'normal',
			'fields' => array(
							
				// CUSTOM EXCERPT SECTION
				array (
					'name' 	=> '',
					'title' => __('Custom Excerpt', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_custom_excerpt",
				    'type' 	=> 'section'
				),
				
				// CUSTOM EXCERPT
				array(
					'name' => __('Custom excerpt', 'neighborhood'),
					'desc' => __("You can optionally write a custom excerpt here to display instead of the excerpt that is automatically generated.", 'neighborhood'),
					'id'   => "{$prefix}custom_excerpt",
					'type' => 'textarea',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
				
				// MAIN DETAIL SECTION
				array (
					'name' 	=> '',
					'title' => __('Main Detail Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_detail",
				    'type' 	=> 'section'
				),
				
				// SHOW PAGE TITLE
				array(
					'name' => __('Show page title', 'neighborhood'),    // File type: checkbox
					'id'   => "{$prefix}page_title",
					'type' => 'checkbox',
					'desc' => __('Show the page title at the top of the page.', 'neighborhood'),
					'std' => $default_show_page_heading,
				),
				
				// REMOVE BREADCRUMBS
				array(
					'name' => __('Remove breadcrumbs', 'neighborhood'),    // File type: checkbox
					'id'   => "{$prefix}no_breadcrumbs",
					'type' => 'checkbox',
					'desc' => __('Remove the breadcrumbs on the page.', 'neighborhood'),
					'std' => 0,
				),
				
				// PAGE TITLE LINE 1
				array(
					'name' => __('Page Title', 'neighborhood'),
					'id' => $prefix . 'page_title_one',
					'desc' => __("Enter a custom page title if you'd like.", 'neighborhood'),
					'type'  => 'text',
					'std' => '',
				),
				
				// PAGE TITLE BACKGROUND
				array(
					'name' => __('Page Title Background', 'neighborhood'),
					'id'   => "{$prefix}page_title_bg",
					'type' => 'select',
					'options' => array(
						'none'			=> __('None', 'neighborhood'),
						'alt-one'		=> __('Alt 1', 'neighborhood'),
						'alt-two'		=> __('Alt 2', 'neighborhood'),
						'alt-three'		=> __('Alt 3', 'neighborhood'),
						'alt-four'		=> __('Alt 4', 'neighborhood'),
						'alt-five'		=> __('Alt 5', 'neighborhood'),
						'alt-six'		=> __('Alt 6', 'neighborhood'),
						'alt-seven'		=> __('Alt 7', 'neighborhood'),
						'alt-eight'		=> __('Alt 8', 'neighborhood'),
						'alt-nine'		=> __('Alt 9', 'neighborhood'),
						'alt-ten'		=> __('Alt 10', 'neighborhood')
					),
					'multiple' => false,
					'std'  => $default_page_heading_bg_alt,
					'desc' => __('Choose the background for the page title (configured in the Neighborhood Options panel).', 'neighborhood'),
				),
				
				// ALT BG PREVIEW
				array (
					'name' 	=> '',
				    'id' 	=> "{$prefix}altbg-preview",
				    'type' 	=> 'altbgpreview'
				),
				
				// FULL WIDTH MEDIA
				array(
					'name' => __('Full Width Media Display', 'neighborhood'),
					'id'   => "{$prefix}full_width_display",
					'type' => 'checkbox',
					'desc' => __('Check this box to show the detail media above the page content / sidebar config, rather than inside the page content.', 'neighborhood'),
					'std' => 1,
				),
				
				// INCLUDE AUTHOR INFO
				array(
					'name' => __('Include author info', 'neighborhood'),
					'id'   => "{$prefix}author_info",
					'type' => 'checkbox',
					'desc' => __('Check this box to show the author info box on the detail page.', 'neighborhood'),
					'std' => 1,
				),
				
				// INCLUDE SOCIAL SHARING
				array(
					'name' => __('Include social sharing', 'neighborhood'),
					'id'   => "{$prefix}social_sharing",
					'type' => 'checkbox',
					'desc' => __('Check this box to show social sharing icons on the detail page.', 'neighborhood'),
					'std' => 1,
				),
				
				// INCLUDE RELATED ARTICLES
				array(
					'name' => __('Include related articles', 'neighborhood'),
					'id'   => "{$prefix}related_articles",
					'type' => 'checkbox',
					'desc' => __('Check this box to show related articles on the detail page.', 'neighborhood'),
					'std' => 1,
				),
				
				// SIDEBAR OPTIONS SECTION
				array (
					'name' 	=> '',
					'title' => __('Sidebar Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_sidebar",
				    'type' 	=> 'section'
				),
				
				// SIDEBAR CONFIG
				array(
					'name' => __('Sidebar configuration', 'neighborhood'),
					'id'   => "{$prefix}sidebar_config",
					'type' => 'select',
					// Array of 'key' => 'value' pairs for select box
					'options' => array(
						'no-sidebars'		=> __('No Sidebars', 'neighborhood'),
						'left-sidebar'		=> __('Left Sidebar', 'neighborhood'),
						'right-sidebar'		=> __('Right Sidebar', 'neighborhood'),
						'both-sidebars'		=> __('Both Sidebars', 'neighborhood')
					),
					// Select multiple values, optional. Default is false.
					'multiple' => false,
					// Default value, can be string (single value) or array (for both single and multiple values)
					'std'  => $default_sidebar_config,
					'desc' => __('Choose the sidebar configuration for the detail page of this portfolio item.', 'neighborhood'),
				),
				
				// LEFT SIDEBAR
				array (
					'name' 	=> __('Left Sidebar', 'neighborhood'),
				    'id' 	=> "{$prefix}left_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_left_sidebar
				),
				
				// RIGHT SIDEBAR
				array (
					'name' 	=> __('Right Sidebar', 'neighborhood'),
				    'id' 	=> "{$prefix}right_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_right_sidebar
				),
							
				// SWIFT SLIDER ENTRY SECTION
				array (
					'name' 	=> '',
					'title' => __('Swift Slider Entry Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_detail",
				    'type' 	=> 'section'
				),
				
				// SWIFT SLIDER BACKGROUND IMAGE
				array(
					'name'	=> __('Slide background image', 'neighborhood'),
					'desc'  => __('The image that will be used as the slide image in the Swift Slider.', 'neighborhood'),
					'id'    => "{$prefix}posts_slider_image",
					'type'  => 'image_advanced',
					'max_file_uploads' => 1
				),
				
				// SWIFT SLIDER CAPTION POSITION
				array(
					'name' => __('Caption Position', 'neighborhood'),
					'id'   => "{$prefix}caption_position",
					'type' => 'select',
					'options' => array(
						'caption-left'		=> __('Left', 'neighborhood'),
						'caption-right'		=> __('Right', 'neighborhood')
					),
					'multiple' => false,
					'std'  => 'caption-right',
					'desc' => __('Choose which side you would like to display the caption over the slide.', 'neighborhood'),
				),
				
				// MISC
				array (
					'name' 	=> '',
					'title' => __('Misc. Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_detail",
				    'type' 	=> 'section'
				),
				
				// Extra Page Class
				array(
					'name' => __('Extra page class', 'neighborhood'),
					'id' => $prefix . 'extra_page_class',
					'desc' => __("If you wish to add extra classes to the body class of the page (for custom css use), then please add the class(es) here.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				)
				
			)
		);
		
		
		/* Product Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id' => 'product_meta_box',
			'title' => __('Product Meta', 'neighborhood'),
			'pages' => array( 'product' ),
			'pages' => apply_filters('product_meta_box_post_types', array( 'product' ) ),
			'context' => 'normal',
			'fields' => array(
							
				// PRODUCT DESCRIPTION SECTION
				array (
					'name' 	=> '',
					'title' => __('Product Description', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_custom_excerpt",
				    'type' 	=> 'section'
				),
				
				// PRODUCT DESCRIPTION
				array(
					'name' => __('Product Short Description', 'neighborhood'),
					'desc' => __("You can optionally write a short description here, which shows above the variations/shopping bag options.", 'neighborhood'),
					'id'   => "{$prefix}product_short_description",
					'type' => 'wysiwyg',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
				
				// PRODUCT DESCRIPTION
				array(
					'name' => __('Product Description', 'neighborhood'),
					'desc' => __("You can optionally write a product description here, which shows under the description accordion heading if you have the page builder enabled for product pages.", 'neighborhood'),
					'id'   => "{$prefix}product_description",
					'type' => 'wysiwyg',
					'std'  => "",
					'cols' => '40',
					'rows' => '8',
				),
				
				// MAIN DETAIL SECTION
				array (
					'name' 	=> '',
					'title' => __('Main Detail Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_detail",
				    'type' 	=> 'section'
				),
				
				// SHOW PAGE TITLE
				array(
					'name' => __('Show page title', 'neighborhood'),    // File type: checkbox
					'id'   => "{$prefix}page_title",
					'type' => 'checkbox',
					'desc' => __('Show the page title at the top of the page.', 'neighborhood'),
					'std' => $default_show_page_heading,
				),
				
				// REMOVE BREADCRUMBS
				array(
					'name' => __('Remove breadcrumbs', 'neighborhood'),    // File type: checkbox
					'id'   => "{$prefix}no_breadcrumbs",
					'type' => 'checkbox',
					'desc' => __('Remove the breadcrumbs on the page.', 'neighborhood'),
					'std' => 0,
				),
				
				// PAGE TITLE LINE 1
				array(
					'name' => __('Page Title', 'neighborhood'),
					'id' => $prefix . 'page_title_one',
					'desc' => __("Enter a custom page title if you'd like.", 'neighborhood'),
					'type'  => 'text',
					'std' => '',
				),
				
				// PAGE TITLE BACKGROUND
				array(
					'name' => __('Page Title Background', 'neighborhood'),
					'id'   => "{$prefix}page_title_bg",
					'type' => 'select',
					'options' => array(
						'none'			=> __('None', 'neighborhood'),
						'alt-one'		=> __('Alt 1', 'neighborhood'),
						'alt-two'		=> __('Alt 2', 'neighborhood'),
						'alt-three'		=> __('Alt 3', 'neighborhood'),
						'alt-four'		=> __('Alt 4', 'neighborhood'),
						'alt-five'		=> __('Alt 5', 'neighborhood'),
						'alt-six'		=> __('Alt 6', 'neighborhood'),
						'alt-seven'		=> __('Alt 7', 'neighborhood'),
						'alt-eight'		=> __('Alt 8', 'neighborhood'),
						'alt-nine'		=> __('Alt 9', 'neighborhood'),
						'alt-ten'		=> __('Alt 10', 'neighborhood')
					),
					'multiple' => false,
					'std'  => $default_page_heading_bg_alt,
					'desc' => __('Choose the background for the page title (configured in the Neighborhood Options panel).', 'neighborhood'),
				),
				
				// ALT BG PREVIEW
				array (
					'name' 	=> '',
				    'id' 	=> "{$prefix}altbg-preview",
				    'type' 	=> 'altbgpreview'
				),
							
				// SIDEBAR OPTIONS SECTION
				array (
					'name' 	=> '',
					'title' => __('Sidebar Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_sidebar",
				    'type' 	=> 'section'
				),
							
				// SIDEBAR CONFIG
				array(
					'name' => __('Sidebar configuration', 'neighborhood'),
					'id'   => "{$prefix}sidebar_config",
					'type' => 'select',
					'options' => array(
						'no-sidebars'		=> __('No Sidebars', 'neighborhood'),
						'left-sidebar'		=> __('Left Sidebar', 'neighborhood'),
						'right-sidebar'		=> __('Right Sidebar', 'neighborhood'),
						'both-sidebars'		=> __('Both Sidebars', 'neighborhood')
					),
					'multiple' => false,
					'std'  => $default_product_sidebar_config,
					'desc' => __('Choose the sidebar configuration for the detail page of this portfolio item.', 'neighborhood'),
				),
				
				// LEFT SIDEBAR
				array (
					'name' 	=> __('Left Sidebar', 'neighborhood'),
				    'id' 	=> "{$prefix}left_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_product_left_sidebar
				),
				
				// RIGHT SIDEBAR
				array (
					'name' 	=> __('Right Sidebar', 'neighborhood'),
				    'id' 	=> "{$prefix}right_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_product_right_sidebar
				),
										
				// MISC
				array (
					'name' 	=> '',
					'title' => __('Misc. Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_detail",
				    'type' 	=> 'section'
				),
				
				// Extra Page Class
				array(
					'name' => __('Extra page class', 'neighborhood'),
					'id' => $prefix . 'extra_page_class',
					'desc' => __("If you wish to add extra classes to the body class of the page (for custom css use), then please add the class(es) here.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				)
				
			)
		);
		
		
		/* Team Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id'    => 'team_meta_box',
			'title' => __('Team Member Meta', 'neighborhood'),
			'pages' => apply_filters('team_meta_box_post_types', array( 'team' ) ),
			'fields' => array(
			
				// TEAM MEMBER DETAILS SECTION
				array (
					'name' 	=> '',
					'title' => __('Team Member Details', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_team_member_details",
				    'type' 	=> 'section'
				),
				
				// TEAM MEMBER POSITION
				array(
					'name' => __('Position', 'neighborhood'),
					'id' => $prefix . 'team_member_position',
					'desc' => __("Enter the team member's position within the team.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER EMAIL
				array(
					'name' => __('Email Address', 'neighborhood'),
					'id' => $prefix . 'team_member_email',
					'desc' => __("Enter the team member's email address.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER PHONE NUMBER
				array(
					'name' => __('Phone Number', 'neighborhood'),
					'id' => $prefix . 'team_member_phone_number',
					'desc' => __("Enter the team member's phone number.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER TWITTER
				array(
					'name' => __('Twitter', 'neighborhood'),
					'id' => $prefix . 'team_member_twitter',
					'desc' => __("Enter the team member's Twitter username.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER FACEBOOK
				array(
					'name' => __('Facebook', 'neighborhood'),
					'id' => $prefix . 'team_member_facebook',
					'desc' => __("Enter the team member's Facebook URL.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER LINKEDIN
				array(
					'name' => __('LinkedIn', 'neighborhood'),
					'id' => $prefix . 'team_member_linkedin',
					'desc' => __("Enter the team member's LinkedIn URL.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER GOOGLE+
				array(
					'name' => __('Google+', 'neighborhood'),
					'id' => $prefix . 'team_member_google_plus',
					'desc' => __("Enter the team member's Google+ URL.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER SKYPE
				array(
					'name' => __('Skype', 'neighborhood'),
					'id' => $prefix . 'team_member_skype',
					'desc' => __("Enter the team member's Skype username.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER INSTAGRAM
				array(
					'name' => __('Instagram', 'neighborhood'),
					'id' => $prefix . 'team_member_instagram',
					'desc' => __("Enter the team member's Instragram URL (e.g. http://hashgr.am/).", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// TEAM MEMBER DRIBBBLE
				array(
					'name' => __('Dribbble', 'neighborhood'),
					'id' => $prefix . 'team_member_dribbble',
					'desc' => __("Enter the team member's Dribbble username.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				)
			)
		);
		
		
		/* Clients Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id'    => 'client_meta_box',
			'title' => __('Client Meta', 'neighborhood'),
			'pages' => apply_filters('clients_meta_box_post_types', array( 'clients' ) ),
			'fields' => array(
				
				// CLIENT IMAGE LINK
				array(
					'name' => __('Client Link', 'neighborhood'),
					'id' => $prefix . 'client_link',
					'desc' => __("Enter the link for the client if you want the image to be clickable.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => ''
				)
			)	
		);
		
		
		/* Testimonials Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id'    => 'testimonials_meta_box',
			'title' => __('Testimonial Meta', 'neighborhood'),
			'pages' => apply_filters('testimonials_meta_box_post_types', array( 'testimonials' ) ),
			'fields' => array(
				
				// TESTIMONAIL CITE
				array(
					'name' => __('Testimonial Cite', 'neighborhood'),
					'id' => $prefix . 'testimonial_cite',
					'desc' => __("Enter the cite for the testimonial.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => ''
				)
			)	
		);
		
		
		/* Slider Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id'    => 'slider_meta_box',
			'title' => __('Page Slider Options', 'neighborhood'),
			'pages' => apply_filters('page_slider_meta_box_post_types', array( 'page' ) ),
			'fields' => array(
				
				// SHOW SWIFT SLIDER
				array(
					'name' => __('Show Swift Slider', 'neighborhood'),    // File type: checkbox
					'id'   => "{$prefix}posts_slider",
					'type' => 'checkbox',
					'desc' => __('Show the Swift Slider at the top of the page.', 'neighborhood'),
					'std' => 0,
				),
				
				// SWIFT SLIDER TYPE
				array(
					'name' => __('Swift Slider Type', 'neighborhood'),
					'id'   => "{$prefix}posts_slider_type",
					'type' => 'select',
					'options' => array(
						'post'		=> __('Posts', 'neighborhood'),
						'portfolio'	=> __('Portfolio', 'neighborhood'),
						'hybrid'	=> __('Hybrid', 'neighborhood')
					),
					'multiple' => false,
					'std'  => 'post',
					'desc' => __('Choose the post type to display in the Swift Slider.', 'neighborhood'),
				),
				
				// SWIFT SLIDER CATEGORY
				array(
					'name' => __('Swift Slider category', 'neighborhood'),
					'id'   => "{$prefix}posts_slider_category",
					'type' => 'select',
					'desc' => __('Select the category for which the Swift Slider should show posts from.', 'neighborhood'),
					'options' => get_category_list_key_array('category'),
					'std' => '',
				),
				
				// SWIFT SLIDER PORTFOLIO CATEGORY
				array(
					'name' => __('Swift Slider portfolio category', 'neighborhood'),
					'id'   => "{$prefix}posts_slider_portfolio_category",
					'type' => 'select',
					'desc' => __('Select the category for which the Swift Slider should show portfolio items from.', 'neighborhood'),
					'options' => get_category_list_key_array('portfolio-category'),
					'std' => '',
				),
				
				// SWIFT SLIDER COUNT
				array(
					'name' => __('Swift Slider count', 'neighborhood'),
					'id' => $prefix . 'posts_slider_count',
					'desc' => __("The number of posts to show in the Swift Slider.", 'neighborhood'),
					'type'  => 'text',
					'std' => '5',
				),
				
				// SHOW FULL WIDTH REV SLIDER
				array(
					'name' => __('Revolution slider alias', 'neighborhood'),
					'id' => $prefix . 'rev_slider_alias',
					'desc' => __("Enter the revolution slider alias for the slider that you want to show. NOTE: If you have the Swift Slider enabled above, then this will be ignored.", 'neighborhood'),
					'type'  => 'text',
					'std' => '',
				)
			)	
		);
		
			
		/* Page Meta Box
		================================================== */ 
		$meta_boxes[] = array(
			'id'    => 'page_meta_box',
			'title' => __('Page Meta', 'neighborhood'),
			'pages' => apply_filters('page_meta_box_post_types', array( 'page' ) ),
			'fields' => array(
			
				// PAGE OPTIONS SECTION
				array (
					'name' 	=> '',
					'title' => __('Page Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_page",
				    'type' 	=> 'section'
				),
				
				// SHOW PAGE TITLE
				array(
					'name' => __('Show page title', 'neighborhood'),
					'id'   => "{$prefix}page_title",
					'type' => 'checkbox',
					'desc' => __('Show the page title at the top of the page.', 'neighborhood'),
					'std' => $default_show_page_heading,
				),
				
				// PAGE TITLE LINE 1
				array(
					'name' => __('Page Title', 'neighborhood'),
					'id' => $prefix . 'page_title_one',
					'desc' => __("Enter the a custom page title if you'd like.", 'neighborhood'),
					'type'  => 'text',
					'std' => '',
				),
				
				// PAGE TITLE BACKGROUND
				array(
					'name' => __('Page Title Background', 'neighborhood'),
					'id'   => "{$prefix}page_title_bg",
					'type' => 'select',
					'options' => array(
						'none'			=> __('None', 'neighborhood'),
						'alt-one'		=> __('Alt 1', 'neighborhood'),
						'alt-two'		=> __('Alt 2', 'neighborhood'),
						'alt-three'		=> __('Alt 3', 'neighborhood'),
						'alt-four'		=> __('Alt 4', 'neighborhood'),
						'alt-five'		=> __('Alt 5', 'neighborhood'),
						'alt-six'		=> __('Alt 6', 'neighborhood'),
						'alt-seven'		=> __('Alt 7', 'neighborhood'),
						'alt-eight'		=> __('Alt 8', 'neighborhood'),
						'alt-nine'		=> __('Alt 9', 'neighborhood'),
						'alt-ten'		=> __('Alt 10', 'neighborhood')
					),
					'multiple' => false,
					'std'  => $default_page_heading_bg_alt,
					'desc' => __('Choose the background for the page title (configured in the Neighborhood Options panel).', 'neighborhood'),
				),
				
				// ALT BG PREVIEW
				array (
					'name' 	=> '',
				    'id' 	=> "{$prefix}altbg-preview",
				    'type' 	=> 'altbgpreview'
				),
				
				// SIDEBAR OPTIONS SECTION
				array (
					'name' 	=> '',
					'title' => __('Sidebar Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_sidebar",
				    'type' 	=> 'section'
				),
				
				// SIDEBAR CONFIG
				array(
					'name' => __('Sidebar configuration', 'neighborhood'),
					'id'   => "{$prefix}sidebar_config",
					'type' => 'select',
					// Array of 'key' => 'value' pairs for select box
					'options' => array(
						'no-sidebars'		=> __('No Sidebars', 'neighborhood'),
						'left-sidebar'		=> __('Left Sidebar', 'neighborhood'),
						'right-sidebar'		=> __('Right Sidebar', 'neighborhood'),
						'both-sidebars'		=> __('Both Sidebars', 'neighborhood')
					),
					// Select multiple values, optional. Default is false.
					'multiple' => false,
					// Default value, can be string (single value) or array (for both single and multiple values)
					'std'  => $default_sidebar_config,
					'desc' => __('Choose the sidebar configuration for the detail page of this portfolio item.', 'neighborhood'),
				),
				
				// LEFT SIDEBAR
				array (
					'name' 	=> __('Left Sidebar', 'neighborhood'),
				    'id' 	=> "{$prefix}left_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_left_sidebar
				),
				
				// RIGHT SIDEBAR
				array (
					'name' 	=> __('Right Sidebar', 'neighborhood'),
				    'id' 	=> "{$prefix}right_sidebar",
				    'type' 	=> 'sidebars',
				    'std' 	=> $default_right_sidebar
				),
				
				// MISC OPTIONS SECTION
				array (
					'name' 	=> '',
					'title' => __('Misc. Options', 'neighborhood'),
				    'id' 	=> "{$prefix}heading_sidebar",
				    'type' 	=> 'section'
				),
				
				// REMOVE BREADCRUMBS
				array(
					'name' => __('Remove breadcrumbs', 'neighborhood'),    // File type: checkbox
					'id'   => "{$prefix}no_breadcrumbs",
					'type' => 'checkbox',
					'desc' => __('Remove the breadcrumbs on the page.', 'neighborhood'),
					'std' => 0,
				),
				
				// Extra Page Class
				array(
					'name' => __('Extra page class', 'neighborhood'),
					'id' => $prefix . 'extra_page_class',
					'desc' => __("If you wish to add extra classes to the body class of the page (for custom css use), then please add the class(es) here.", 'neighborhood'),
					'clone' => false,
					'type'  => 'text',
					'std' => '',
				),
				
				// REMOVE TOP SPACING
				array(
					'name' => __('Remove top spacing', 'neighborhood'),
					'id'   => "{$prefix}no_top_spacing",
					'type' => 'checkbox',
					'desc' => __('Remove the spacing at the top of the page.', 'neighborhood'),
					'std' => 0,
				),
				
				// REMOVE BOTTOM SPACING
				array(
					'name' => __('Remove bottom spacing', 'neighborhood'),
					'id'   => "{$prefix}no_bottom_spacing",
					'type' => 'checkbox',
					'desc' => __('Remove the spacing at the bottom of the page.', 'neighborhood'),
					'std' => 0,
				)
			)
		);
		
		return $meta_boxes;
	}
	add_filter( 'rwmb_meta_boxes', 'sf_register_meta_boxes' );
