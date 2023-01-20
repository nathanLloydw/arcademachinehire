<?php

    /*
    *
    *	Swift Page Builder - Parallax Shortcode
    *	------------------------------------------------
    *	Swift Framework
    * 	Copyright Swift Ideas 2015 - http://www.swiftideas.com
    *
    */

    class SwiftPageBuilderShortcode_spb_parallax extends SwiftPageBuilderShortcode {

        protected function content( $atts, $content = null ) {

            $title = $el_position = $width = $el_class = '';
            extract( shortcode_atts( array(
                'title'                   => '',
                'parallax_type'           => '',
                'bg_image'                => '',
                'bg_video_mp4'            => '',
                'bg_video_webm'           => '',
                'bg_video_ogg'            => '',
                'parallax_video_height'   => 'window-height',
                'parallax_image_height'   => 'content-height',
                'parallax_video_overlay'  => 'none',
                'parallax_image_movement' => 'fixed',
                'parallax_image_speed'    => '0.5',
                'bg_type'                 => '',
                'padding_horizontal'      => '',
                'el_position'             => '',
                'width'                   => '1/1',
                'el_class'                => ''
            ), $atts ) );
            $output = '';

            /* SIDEBAR CONFIG
            ================================================== */
            global $sf_sidebar_config;

            $sidebars = '';
            if ( ( $sf_sidebar_config == "left-sidebar" ) || ( $sf_sidebar_config == "right-sidebar" ) ) {
                $sidebars = 'one-sidebar';
            } else if ( $sf_sidebar_config == "both-sidebars" ) {
                $sidebars = 'both-sidebars';
            } else {
                $sidebars = 'no-sidebars';
            }

            $el_class = $this->getExtraClass( $el_class );
            $width    = spb_translateColumnWidthToSpan( $width );

            $img_url = wp_get_attachment_image_src( $bg_image, 'full' );

            $inline_style = "";
            if ( $padding_horizontal != "" ) {
                $inline_style .= 'padding-left:' . $padding_horizontal . '%;padding-right:' . $padding_horizontal . '%;';
            }

            if ( $parallax_type == "video" ) {
                if ( $img_url[0] != "" ) {
                    $output .= "\n\t" . '<div class="spb_parallax_asset sf-parallax sf-parallax-video parallax-' . $parallax_video_height . ' spb_content_element bg-type-' . $bg_type . ' ' . $width . $el_class . '" data-v-center="true" style="background-image: url(' . $img_url[0] . ');">';
                } else {
                    $output .= "\n\t" . '<div class="spb_parallax_asset sf-parallax sf-parallax-video parallax-' . $parallax_video_height . ' spb_content_element bg-type-' . $bg_type . ' ' . $width . $el_class . '" data-v-center="true">';
                }
            } else {
                if ( $img_url[0] != "" ) {
                    if ( $parallax_image_movement == "stellar" ) {
                        $output .= "\n\t" . '<div class="spb_parallax_asset sf-parallax parallax-' . $parallax_image_height . ' parallax-' . $parallax_image_movement . ' spb_content_element bg-type-' . $bg_type . ' ' . $width . $el_class . '" style="background-image: url(' . $img_url[0] . ');" data-stellar-background-ratio="' . $parallax_image_speed . '" data-v-center="true">';
                    } else {
                        $output .= "\n\t" . '<div class="spb_parallax_asset sf-parallax parallax-' . $parallax_image_height . ' parallax-' . $parallax_image_movement . ' spb_content_element bg-type-' . $bg_type . ' ' . $width . $el_class . '" style="background-image: url(' . $img_url[0] . ');" data-v-center="true">';
                    }
                } else {
                    $output .= "\n\t" . '<div class="spb_parallax_asset sf-parallax parallax-' . $parallax_image_height . ' spb_content_element bg-type-' . $bg_type . ' ' . $width . $el_class . '" data-v-center="true">';
                }
            }
            $output .= "\n\t\t" . '<div class="spb_content_wrapper container" style="' . $inline_style . '">';
            $output .= ( $title != '' ) ? "\n\t\t\t" . '<div class="heading-wrap"><h3 class="spb-heading spb-center-heading"><span>' . $title . '</span></h3></div>' : '';
            $output .= "\n\t\t\t" . do_shortcode( $content );
            $output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.spb_content_wrapper' );

            if ( $parallax_type == "video" ) {
                $output .= '<video class="parallax-video" poster="' . $img_url[0] . '" preload="auto" autoplay loop="loop" muted="muted">';
                if ( $bg_video_mp4 != "" ) {
                    $output .= '<source src="' . $bg_video_mp4 . '" type="video/mp4">';
                }
                if ( $bg_video_webm != "" ) {
                    $output .= '<source src="' . $bg_video_webm . '" type="video/webm">';
                }
                if ( $bg_video_ogg != "" ) {
                    $output .= '<source src="' . $bg_video_ogg . '" type="video/ogg">';
                }
                $output .= '</video>';
                $output .= '<div class="video-overlay overlay-' . $parallax_video_overlay . '"></div>';
            }
            $output .= "\n\t" . '</div> ' . $this->endBlockComment( $width );

            if ( $sidebars == 'no-sidebars' ) {
                $output = $this->startRow( $el_position, '', true, 'full-width' ) . $output . $this->endRow( $el_position, '', true, 'full-width' );
            } else {
                $output = $this->startRow( $el_position ) . $output . $this->endRow( $el_position );
            }

            global $sf_include_parallax;
            $sf_include_parallax = true;

            return $output;
        }
    }

    SPBMap::map( 'spb_parallax', array(
        "name"          => __( "Parallax", 'neighborhood' ),
        "base"          => "spb_parallax",
        "class"         => "",
        "icon"          => "spb-icon-parallax",
        "wrapper_class" => "clearfix",
        "controls"      => "full",
        "params"        => array(
            array(
                "type"        => "textfield",
                "heading"     => __( "Widget title", 'neighborhood' ),
                "param_name"  => "title",
                "value"       => "",
                "description" => __( "Heading text. Leave it empty if not needed.", 'neighborhood' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Parallax Type", 'neighborhood' ),
                "param_name"  => "parallax_type",
                "value"       => array(
                    __( "Image", 'neighborhood' ) => "image",
                    __( "Video", 'neighborhood' ) => "video"
                ),
                "description" => __( "Choose whether you want to use an image or video for the background of the parallax. This will decide what is used from the options below.", 'neighborhood' )
            ),
            array(
                "type"        => "attach_image",
                "heading"     => __( "Background Image", 'neighborhood' ),
                "param_name"  => "bg_image",
                "value"       => "",
                "description" => "Choose an image to use as the background for the parallax area."
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Background Type", 'neighborhood' ),
                "param_name"  => "bg_type",
                "value"       => array(
                    __( "Cover", 'neighborhood' )   => "cover",
                    __( "Pattern", 'neighborhood' ) => "pattern"
                ),
                "description" => __( "If you're uploading an image that you want to spread across the whole asset, then choose cover. Else choose pattern for an image you want to repeat.", 'neighborhood' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Background Video (MP4)", 'neighborhood' ),
                "param_name"  => "bg_video_mp4",
                "value"       => "",
                "required"       => array("parallax_type", "=", "video"),
                "description" => "Provide a video URL in MP4 format to use as the background for the parallax area. You can upload these videos through the WordPress media manager."
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Background Video (WebM)", 'neighborhood' ),
                "param_name"  => "bg_video_webm",
                "value"       => "",
                "required"       => array("parallax_type", "=", "video"),
                "description" => "Provide a video URL in WebM format to use as the background for the parallax area. You can upload these videos through the WordPress media manager."
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Background Video (Ogg)", 'neighborhood' ),
                "param_name"  => "bg_video_ogg",
                "value"       => "",
                "required"       => array("parallax_type", "=", "video"),
                "description" => "Provide a video URL in OGG format to use as the background for the parallax area. You can upload these videos through the WordPress media manager."
            ),
            array(
                "type"        => "textarea_html",
                "holder"      => "div",
                "class"       => "",
                "heading"     => __( "Parallax Content", 'neighborhood' ),
                "param_name"  => "content",
                "value"       => __( "<p>This is a parallax text block. Click the edit button to change this text.</p>", 'neighborhood' ),
                "description" => __( "Enter your content.", 'neighborhood' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Parallax Video Height", 'neighborhood' ),
                "param_name"  => "parallax_video_height",
                "value"       => array(
                    __( "Content Height", 'neighborhood' ) => "content-height",
                    __( "Window Height", 'neighborhood' )  => "window-height"
                ),
                "required"       => array("parallax_type", "=", "video"),
                "description" => __( "If you are using this as a video parallax asset, then please choose whether you'd like asset to sized based on the content height or the window height.", 'neighborhood' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Parallax Video Overlay", 'neighborhood' ),
                "param_name"  => "parallax_video_overlay",
                "value"       => array(
                    __( "None", 'neighborhood' )             => "none",
                    __( "Light Grid", 'neighborhood' )       => "lightgrid",
                    __( "Dark Grid", 'neighborhood' )        => "darkgrid",
                    __( "Light Grid (Fat)", 'neighborhood' ) => "lightgridfat",
                    __( "Dark Grid (Fat)", 'neighborhood' )  => "darkgridfat",
                    __( "Light Diagonal", 'neighborhood' )   => "diaglight",
                    __( "Dark Diagonal", 'neighborhood' )    => "diagdark",
                    __( "Light Vertical", 'neighborhood' )   => "vertlight",
                    __( "Dark Vertical", 'neighborhood' )    => "vertdark",
                    __( "Light Horizontal", 'neighborhood' ) => "horizlight",
                    __( "Dark Horizontal", 'neighborhood' )  => "horizdark",
                ),
                "required"       => array("parallax_type", "=", "video"),
                "description" => __( "If you would like an overlay to appear on top of the video, then you can select it here.", 'neighborhood' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Parallax Image Height", 'neighborhood' ),
                "param_name"  => "parallax_image_height",
                "value"       => array(
                    __( "Content Height", 'neighborhood' ) => "content-height",
                    __( "Window Height", 'neighborhood' )  => "window-height"
                ),
                "required"       => array("parallax_type", "=", "image"),
                "description" => __( "If you are using this as an image parallax asset, then please choose whether you'd like asset to sized based on the content height or the height of the viewport window.", 'neighborhood' )
            ),
            array(
                "type"        => "dropdown",
                "heading"     => __( "Parallax Image Movement", 'neighborhood' ),
                "param_name"  => "parallax_image_movement",
                "value"       => array(
                    __( "Fixed", 'neighborhood' )             => "fixed",
                    __( "Scroll", 'neighborhood' )            => "scroll",
                    __( "Stellar (dynamic)", 'neighborhood' ) => "stellar",
                ),
                "required"       => array("parallax_type", "=", "image"),
                "description" => __( "Choose the type of movement you would like the parallax image to have. Fixed means the background image is fixed on the page, Scroll means the image will scroll will the page, and stellar makes the image move at a seperate speed to the page, providing a layered effect.", 'neighborhood' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Parallax Image Speed (Stellar Only)", 'neighborhood' ),
                "param_name"  => "parallax_image_speed",
                "value"       => "0.5",
                "required"       => array("parallax_type", "=", "image"),
                "description" => "The speed at which the parallax image moves in relation to the page scrolling. For example, 0.5 would mean the image scrolls at half the speed of the standard page scroll. (Default 0.5)."
            ),
            array(
                "type"        => "uislider",
                "heading"     => __( "Padding - Horizontal", 'neighborhood' ),
                "param_name"  => "padding_horizontal",
                "value"       => "15",
                "step"        => "5",
                "min"         => "0",
                "max"         => "40",
                "description" => __( "Adjust the horizontal padding for the content (percentage).", 'neighborhood' )
            ),
            array(
                "type"        => "textfield",
                "heading"     => __( "Extra class", 'neighborhood' ),
                "param_name"  => "el_class",
                "value"       => "",
                "description" => __( "If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'neighborhood' )
            )
        )
    ) );