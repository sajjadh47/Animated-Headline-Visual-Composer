<?php

/**
 * Plugin Name: Animated Headline - Visual Composer
 * Description: Add a nice animated headline text effect with various animation effects.
 * Plugin URI: https://wordpress.org/plugins/animated-headline-visual-composer/
 * Author: Sajjad Hossain Sagor
 * Author URI: https://sajjadhsagor.com/
 * Version: 1.0.4
 * Text Domain: animated-headline-visual-composer
 * License: GPL2
 */

/*
	Copyright (C) Year  Author  Email

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*************************************************************************
	Define Plugin Constants
*************************************************************************/

define( "AHVC_PLUGIN_ROOT_PATH", plugin_dir_path( __FILE__ ) );
define( "AHVC_PLUGIN_ROOT_URL", plugin_dir_url( __FILE__ ) );

/*************************************************************************
	Checking if WPBakery Visual Composer is either installed or active
*************************************************************************/
register_activation_hook( __FILE__, 'ahvc_check_if_vc_active' );

add_action( 'admin_init', 'ahvc_check_if_vc_active' );

function ahvc_check_if_vc_active()
{
	if ( !in_array( 'js_composer/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	{
		// Deactivate the plugin
		deactivate_plugins( __FILE__ );

		add_action( 'admin_notices', function()
		{ ?>
			<div class="error">
				<p>
					<?php _e( 'Animated Headline - Visual Composer requires WPBakery Visual Composer Plugin installed. Please install it first.', 'wpahvc' ); ?>
				</p>
			</div>
			<?php
		} );
	}
}

/*************************************************************************
	Register Shortcode CSS & Javascript Files
*************************************************************************/
add_action( 'wp_enqueue_scripts', 'ahvc_register_scripts' );

function ahvc_register_scripts()
{	
	wp_register_style( 'ahvc_animation_css', plugins_url( '/assets/public/css/heading_animation.css', __FILE__ ), array(), false, false );
	
	wp_register_script( 'ahvc_animation_js', plugins_url( '/assets/public/js/animated_heading_vc.js', __FILE__ ), array( 'jquery' ), false, true );
}

/*************************************************************************
	Add Shortcode animated_headline_vc
*************************************************************************/

add_shortcode( 'animated_headline_vc', 'ahvc_animated_headline_vc_shortcode_render' );
		
function ahvc_animated_headline_vc_shortcode_render( $atts, $content = null )
{
	// Load on demand css & js files
	wp_enqueue_style( 'ahvc_animation_css' );
	wp_enqueue_script( 'ahvc_animation_js' );
		
	extract( shortcode_atts(
		array(
			'title' => 'Animated Heading Title',
			'animation_type' => 'rotate-1',
			'animation_texts' => 'Hello,World,Animated',
			'animation_speed' => 2500
		), $atts )
	);

	// Localize the script with animation_speed data
	wp_localize_script( 'ahvc_animation_js', 'Animated_Heading', array(
		'animation_speed' => intval( $animation_speed )
	) );

	// add letters class in $animation_type variable.
	switch ( $animation_type )
	{	
		case 'rotate-2':
		case 'rotate-3':
		case 'type':
		case 'scale':
			$animation_type .= " letters";
		break;
	}

	$html = '<h1 class="cd-headline '.$animation_type.'"><span> '. $title .' </span>';
	
	$html .= '<span class="cd-words-wrapper">';
	
	$html .=  apply_filters( 'ahvc_animation_texts', $animation_texts );
	
	$html .= '</span></h1>';
	
	return $html;
}

/*************************************************************************
	Some Shortcode Related Filters
*************************************************************************/

add_filter( 'ahvc_animation_texts', 'ahvc_prepare_animated_texts' );

function ahvc_prepare_animated_texts( $animation_texts )
{
	// First convert ',' separeted texts into array
	$animation_texts = explode( ',', $animation_texts );

	// Second is-visible class to the first element
	$html = '';
	
	for( $x = 0; $x < count( $animation_texts ); $x++ )
	{	
		$addclass = ( $x == 0 ) ? "is-visible" : "";
		
		$html .= '<b class="'.$addclass.'">'.$animation_texts[$x].'</b>'."\n";
	}
	
	return $html;
}

/*************************************************************************
	Add the shortcode to WPBakery Visual Composer Builder
*************************************************************************/
add_action( 'vc_before_init', 'ahvc_animated_headline_vc_shortcode' );

function ahvc_animated_headline_vc_shortcode()
{	
	vc_add_shortcode_param( 'raw_html', 'ahvc_add_custom_param_type' );

	vc_map( array(
		"name" => __( "Animated Headline", "ahvc" ),
		"base" => "animated_headline_vc",
		'description' => '10+ animation texts effects',
		"class" => "animated_headline_vc",
		"icon" => plugins_url('/assets/admin/images/animation.svg', __FILE__),
		"category" => __( "Content", "ahvc"),
		'front_enqueue_js' => array( plugins_url( '/assets/admin/js/jquery.tagsinput-revisited.js', __FILE__ ), plugins_url( '/assets/admin/js/animated_heading_vc.js', __FILE__ ), plugins_url( '/assets/admin/js/script.js', __FILE__ )),
		'front_enqueue_css' => array( plugins_url( '/assets/admin/css/jquery.tagsinput-revisited.css', __FILE__ ),plugins_url( '/assets/admin/css/heading_animations.css', __FILE__ ), plugins_url( '/assets/admin/css/style.css', __FILE__ )),
		'admin_enqueue_js' => array( plugins_url( '/assets/admin/js/jquery.tagsinput-revisited.js', __FILE__ ), plugins_url( '/assets/admin/js/animated_heading_vc.js', __FILE__ ), plugins_url( '/assets/admin/js/script.js', __FILE__ )),
		'admin_enqueue_css' => array( plugins_url( '/assets/admin/css/jquery.tagsinput-revisited.css', __FILE__ ), plugins_url( '/assets/admin/css/heading_animations.css', __FILE__ ), plugins_url( '/assets/admin/css/style.css', __FILE__ )),
		"params" => array(
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "animation_texts_title",
				"heading" => __( "Title", "ahvc" ),
				"param_name" => "title",
				"value" => __( "", "ahvc" ),
				"description" => __( "Enter title (Goes Before Animated Texts)", "ahvc" )
			),
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "animation_texts_options",
				"heading" => __( "Animated Texts", "ahvc" ),
				"param_name" => "animation_texts",
				"value" => __( "", "ahvc" ),
				"description" => __( "Enter Texts You Want to Animate", "ahvc" )
			),
			array(
				"type" => "dropdown",
				"holder" => "div",
				"class" => "animation_type",
				"heading" => __( "Animation Type", "ahvc" ),
				"param_name" => "animation_type",
				'value'       => array(
					'Choose Type' => '',
					'Rotate 1' => 'rotate-1',
					'Rotate 2' => 'rotate-2',
					'Rotate 3' => 'rotate-3',
					'Type' => 'type',
					'Loading Bar' => 'loading-bar',
					'Slide' => 'slide',
					'Clip' => 'clip',
					'Zoom' => 'zoom',
					'Scale' => 'scale',
					'Push' => 'push',
				),
				"description" => __( "Select Animation Type", "ahvc" )
			),
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "animation_texts_speed",
				"heading" => __( "Animation Speed", "ahvc" ),
				"param_name" => "animation_speed",
				"value" => __( "", "ahvc" ),
				"description" => __( "Enter Animation Speed (Default 2500ms). Note : [1000ms = 1 second]. Enter only number without ms text.", "ahvc" )
			),
			array(
				"type" => "raw_html",
				"holder" => "div",
				"class" => "animation_preview",
				"heading" => __( "Animation Preview", "ahvc" ),
				"param_name" => "animation_preview",
				"value" => __( "", "ahvc" ),
				"description" => __( "Choose Different Animation Type to Preview it here", "ahvc" )
			),
			array(
				"type" => "raw_html",
				"holder" => "div",
				"class" => "animation_preview",
				"heading" => __( "Animation Preview", "ahvc" ),
				"param_name" => "animation_preview",
				"value" => __( "", "ahvc" ),
				"description" => __( "Choose Different Animation Type to Preview it here", "ahvc" )
			),
		)
	) );
}

/*************************************************************************
		Adding Custom vc_map Fields
*************************************************************************/
function ahvc_add_custom_param_type( $settings, $value )
{
	return '<div class="raw_html_container"></div>';
}
