<?php
/*
Plugin Name: Simple NivoSlider
Plugin URI: http://wordpress.org/plugins/simple-nivoslider/
Version: 1.0
Description: Integrates NivoSlider into WordPress.
Author: Katsushi Kawamori
Author URI: http://gallerylink.nyanko.org/medialink/simple-nivoslider/
Domain Path: /languages
*/

/*  Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

	load_plugin_textdomain('simplenivoslider', false, basename( dirname( __FILE__ ) ) . '/languages' );

	define("SIMPLENIVOSLIDER_PLUGIN_BASE_FILE", plugin_basename(__FILE__));
	define("SIMPLENIVOSLIDER_PLUGIN_BASE_DIR", dirname(__FILE__));
	define("SIMPLENIVOSLIDER_PLUGIN_URL", plugins_url($path='',$scheme=null));

	require_once( dirname( __FILE__ ) . '/req/SimpleNivoSliderRegist.php' );
	$simplenivosliderregistandheader = new SimpleNivoSliderRegist();
	add_action('admin_init', array($simplenivosliderregistandheader, 'register_settings'));
	unset($simplenivosliderregistandheader);

	add_action( 'wp_head', wp_enqueue_script('jquery') );

	require_once( dirname( __FILE__ ) . '/req/SimpleNivoSliderAdmin.php' );
	$simplenivoslideradmin = new SimpleNivoSliderAdmin();
	add_action( 'admin_menu', array($simplenivoslideradmin, 'plugin_menu'));
	add_action( 'admin_menu', array($simplenivoslideradmin, 'add_apply_simplenivoslider_custom_box'));
	add_action( 'save_post', array($simplenivoslideradmin, 'save_apply_simplenivoslider_postdata'));
	add_filter( 'plugin_action_links', array($simplenivoslideradmin, 'settings_link'), 10, 2 );
	add_filter('manage_posts_columns', array($simplenivoslideradmin, 'posts_columns_simplenivoslider'));
	add_action('manage_posts_custom_column', array($simplenivoslideradmin, 'posts_custom_columns_simplenivoslider'), 10, 2);
	add_filter('manage_pages_columns', array($simplenivoslideradmin, 'pages_columns_simplenivoslider'));
	add_action('manage_pages_custom_column', array($simplenivoslideradmin, 'pages_custom_columns_simplenivoslider'), 10, 2);
	unset($simplenivoslideradmin);

	include_once( SIMPLENIVOSLIDER_PLUGIN_BASE_DIR.'/inc/SimpleNivoSlider.php' );
	$simplenivoslider = new SimpleNivoSlider();
	$footerjs = '';
	$simplenivoslider->footerjs = $footerjs;

	remove_shortcode('gallery', 'gallery_shortcode');
	add_shortcode('gallery', array($simplenivoslider, 'simplenivoslider_gallery_shortcode'), 12 );
	add_filter( 'wp_get_attachment_image_attributes', array($simplenivoslider, 'add_title_to_attachment_image'), 13, 2 );
	add_filter( 'the_content', array($simplenivoslider, 'add_img_tag'), 14 );
	add_action( 'wp_footer', array($simplenivoslider, 'add_footer'), 15 );

	unset($simplenivoslider);

?>