<?php
/*
Plugin Name: Simple NivoSlider
Plugin URI: http://wordpress.org/plugins/simple-nivoslider/
Version: 2.3
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
	define("SIMPLENIVOSLIDER_PLUGIN_URL", plugins_url($path='',$scheme=null).'/simple-nivoslider');

	require_once( SIMPLENIVOSLIDER_PLUGIN_BASE_DIR . '/req/SimpleNivoSliderRegist.php' );
	$simplenivosliderregistandheader = new SimpleNivoSliderRegist();
	add_action('admin_init', array($simplenivosliderregistandheader, 'register_settings'));
	unset($simplenivosliderregistandheader);

	require_once( SIMPLENIVOSLIDER_PLUGIN_BASE_DIR . '/req/SimpleNivoSliderAdmin.php' );
	$simplenivoslideradmin = new SimpleNivoSliderAdmin();
	add_action( 'admin_menu', array($simplenivoslideradmin, 'plugin_menu'));
	add_action( 'admin_enqueue_scripts', array($simplenivoslideradmin, 'load_custom_wp_admin_style') );
	add_action( 'admin_menu', array($simplenivoslideradmin, 'add_apply_simplenivoslider_custom_box'));
	add_action( 'save_post', array($simplenivoslideradmin, 'save_apply_simplenivoslider_postdata'));
	add_filter( 'plugin_action_links', array($simplenivoslideradmin, 'settings_link'), 10, 2 );
	add_filter('manage_posts_columns', array($simplenivoslideradmin, 'posts_columns_simplenivoslider'));
	add_action('manage_posts_custom_column', array($simplenivoslideradmin, 'posts_custom_columns_simplenivoslider'), 10, 2);
	add_filter('manage_pages_columns', array($simplenivoslideradmin, 'pages_columns_simplenivoslider'));
	add_action('manage_pages_custom_column', array($simplenivoslideradmin, 'pages_custom_columns_simplenivoslider'), 10, 2);
	add_action( 'admin_footer', array($simplenivoslideradmin, 'load_custom_wp_admin_style2') );
	unset($simplenivoslideradmin);

	include_once( SIMPLENIVOSLIDER_PLUGIN_BASE_DIR.'/inc/SimpleNivoSlider.php' );
	$simplenivoslider = new SimpleNivoSlider();
	$footer_js_s = array();
	$simplenivoslider->footer_js_s = $footer_js_s;
	$simplenivoslider_attachment_args = array(
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'numberposts' => -1
		);
	$simplenivoslider->attachments = get_posts($simplenivoslider_attachment_args);

	add_filter( 'wp_get_attachment_image_attributes', array($simplenivoslider, 'add_title_to_attachment_image'), 12, 2 );

	// for gallery
	add_filter( 'post_gallery', array($simplenivoslider, 'add_gallery'), 13 );

	add_filter( 'the_content', array($simplenivoslider, 'add_img_tag'), 14 );

	// for GalleryLink http://wordpress.org/plugins/gallerylink/
	add_filter( 'post_gallerylink', array($simplenivoslider, 'add_img_tag'), 15 );
	// for MediaLink http://wordpress.org/plugins/medialink/
	add_filter( 'post_medialink', array($simplenivoslider, 'add_img_tag'), 16 );

	add_action( 'wp_footer', array($simplenivoslider, 'add_footer'), 17 );

	unset($simplenivoslider);

?>