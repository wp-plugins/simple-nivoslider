<?php
/**
 * Simple NivoSlider
 * 
 * @package    Simple NivoSlider
 * @subpackage SimpleNivoSlider Main Functions
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

class SimpleNivoSlider {

	public $footer_js_s;
	public $attachments;

	/* ==================================================
	* @param	none
	* @since	3.0
	*/
	function filter_select(){

		$simplenivoslider_apply = get_post_meta( get_the_ID(), 'simplenivoslider_apply' );
		if ( !empty($simplenivoslider_apply) && $simplenivoslider_apply[0] === 'true' ) {

			$pattern_gallerylink = '/\[' . preg_quote('gallerylink') . '[^\]]*\]/im';
			$pattern_medialink = '/\[' . preg_quote('medialink') . '[^\]]*\]/im';

			$post_text = get_post( get_the_ID() ); 
			if (!empty($post_text->post_content)) {
				$contents = $post_text->post_content;
			}

			if ( !empty($contents) && preg_match($pattern_gallerylink, $contents) ) {
				// for GalleryLink http://wordpress.org/plugins/gallerylink/
				add_filter( 'slideshow_gallerylink', array($this, 'add_img_tag'), 17);
				add_filter( 'slideshow_gallerylink', array($this, 'add_div_tag'), 18 );
			} else if ( !empty($contents) && preg_match($pattern_medialink, $contents) ) {
				// for MediaLink http://wordpress.org/plugins/medialink/
				add_filter( 'slideshow_medialink', array($this, 'add_img_tag'), 17);
				add_filter( 'slideshow_medialink', array($this, 'add_div_tag'), 18 );
			} else {
				// for post or page
				add_filter( 'the_content', array($this, 'add_img_tag'), 9 );
				remove_shortcode('gallery', 'gallery_shortcode');
				add_shortcode('gallery', array($this, 'simplenivoslider_gallery_shortcode'));
				$this->footer_js_s[get_the_ID()] = $this->add_js();
				add_filter( 'the_content', array($this, 'add_div_tag'), 16 );
			}

		}

	}

	/* ==================================================
	* @param	string	$link
	* @return	string	$links
	* @since	3.0
	*/
	function add_div_tag($link) {

		$simplenivoslider_apply = get_post_meta( get_the_ID(), 'simplenivoslider_apply' );
		$settings_tbl = get_option('simplenivoslider_settings');
		if ( !empty($simplenivoslider_apply) && $simplenivoslider_apply[0] === 'true' ) {
			$link = strip_tags($link, '<img>');
			$links = '<div class="slider-wrapper theme-'.$settings_tbl['theme'].'"><div id="simplenivoslider'.get_the_ID().'" class="nivoSlider">'.$link.'</div></div>';
		} else {
			$links = $link;
		}

		return $links;

	}

	/* ==================================================
	* @param	string	$link
	* @return	string	$links
	* @since	1.0
	*/
	function add_img_tag($link) {

		$simplenivoslider_apply = get_post_meta( get_the_ID(), 'simplenivoslider_apply' );
		$settings_tbl = get_option('simplenivoslider_settings');

		if ( !empty($simplenivoslider_apply) && $simplenivoslider_apply[0] === 'true' ) {

			$gallery_shortcode = NULL;
			$pattern_gallery = '/\[' . preg_quote('gallery ') . '[^\]]*\]/im';
			if ( !empty($link) && preg_match($pattern_gallery, $link) ) {
				preg_match($pattern_gallery, $link, $retgallery);
				$gallery_shortcode = $retgallery[0];
			}

			$gallerylink_shortcode = NULL;
			$pattern_gallerylink = '/\[' . preg_quote('gallerylink') . '[^\]]*\]/im';
			if ( !empty($link) && preg_match($pattern_gallerylink, $link) ) {
				preg_match($pattern_gallerylink, $link, $retgallerylink);
				$gallerylink_shortcode = $retgallerylink[0];
			}

			$medialink_shortcode = NULL;
			$pattern_medialink = '/\[' . preg_quote('medialink') . '[^\]]*\]/im';
			if ( !empty($link) && preg_match($pattern_medialink, $link) ) {
				preg_match($pattern_medialink, $link, $retmedialink);
				$medialink_shortcode = $retmedialink[0];
			}

			if(preg_match_all("/<img(.+?)>/i", $link, $result) !== false){
				$link=implode( "\n", $result[0] );
		    	foreach ($result[1] as $value){
					preg_match('/src=\"(.[^\"]*)\"/',$value,$src);
					$explode = explode("/" , $src[1]);
					$file_name = $explode[count($explode) - 1];
					$title_name = preg_replace("/(.+)(\.[^.]+$)/", "$1", $file_name);
					$title_name = preg_replace('(-[0-9]*x[0-9]*)', '', $title_name);
					$image_thumb = NULL;
					foreach ( $this->attachments as $attachment ) {
						if( strpos($attachment->guid, $title_name) ){
							$title_name = $attachment->post_title;
							$image_thumb = wp_get_attachment_image_src( $attachment->ID, 'thumbnail', false );
						}
					}
					if( !strpos($value, 'title=') ) {
						$title_name = ' title="'.$title_name.'" ';
						$link = str_replace($value, $title_name.$value, $link);
					}
					if( !strpos($value, 'data-thumb=') && $settings_tbl['controlNavThumbs'] === 'true' ) {
						$thumb_data = ' data-thumb="'.$image_thumb[0].'" ';
						$link = str_replace($value, $thumb_data.$value, $link);
					}
				}
				$links = $link;
			} else {
				$links = $link;
			}
			$links = $links."\n".$gallery_shortcode."\n".$gallerylink_shortcode."\n".$medialink_shortcode;
			$this->footer_js_s[get_the_ID()] = $this->add_js();
		} else {
			$links = $link;
		}

		return $links;

	}

	/* ==================================================
	* @param	array	$attr
	* @param	array	$attachment
	* @return	array	$attr
	* @since	1.0
	*/
	function add_title_to_attachment_image( $attr, $attachment ) {

		$attr['title'] = esc_attr( $attachment->post_title );

		return $attr;

	}

	/* ==================================================
	* @param	none
	* @since	1.0
	*/
	function add_footer(){

		foreach ( $this->footer_js_s as $footer_js ){
			echo $footer_js;
		}

		$settings_tbl = get_option('simplenivoslider_settings');
		$theme = $settings_tbl['theme'];
		$thumbswidth = $settings_tbl['thumbswidth'];

		wp_enqueue_style( 'nivo-slider-themes',  SIMPLENIVOSLIDER_PLUGIN_URL.'/nivo-slider/themes/'.$theme.'/'.$theme.'.css' );
		wp_enqueue_style( 'nivo-slider',  SIMPLENIVOSLIDER_PLUGIN_URL.'/nivo-slider/nivo-slider.css' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'nivo-slider', SIMPLENIVOSLIDER_PLUGIN_URL.'/nivo-slider/jquery.nivo.slider.pack.js', null, '3.2');

	// CSS
$simplenivoslider_add_css = <<<SIMPLENIVOSLIDERADDCSS

<!-- Start Simple NivoSlider CSS -->
<style type="text/css">
.theme-{$theme} .nivo-controlNav.nivo-thumbs-enabled img {
	display: block;
	width: {$thumbswidth}px;
	height: auto;
}
</style>
<!-- End Simple NivoSlider CSS -->

SIMPLENIVOSLIDERADDCSS;

		echo $simplenivoslider_add_css;

	}

	/* ==================================================
	* @param	none
	* @since	1.0
	*/
	function add_js(){

		$settings_tbl = get_option('simplenivoslider_settings');
		$theme = $settings_tbl['theme'];
		$thumbswidth = $settings_tbl['thumbswidth'];

// JS
$simplenivoslider_add_js = <<<SIMPLENIVOSLIDER1

<!-- BEGIN: Simple Nivo Slider -->
<script type="text/javascript">
jQuery(window).load(function() {
	jQuery('#simplenivoslider
SIMPLENIVOSLIDER1;
$simplenivoslider_add_js .= get_the_ID()."').nivoSlider({
";

		foreach( $settings_tbl as $key => $value ) {
			if ( $key <> 'theme' && $key <> 'thumbswidth' ) {
				if ( is_string($value) && $value <> 'true' && $value<> 'false' ) {
					$simplenivoslider_add_js .= str_repeat(' ', 8).$key.": '".$value."',"."\n";
				} else {
					$simplenivoslider_add_js .= str_repeat(' ', 8).$key.': '.$value.','."\n";
				}
			}
		}
		$simplenivoslider_add_js = rtrim($simplenivoslider_add_js);
		$simplenivoslider_add_js = rtrim($simplenivoslider_add_js, ",");

$simplenivoslider_add_js .= <<<SIMPLENIVOSLIDER2

	});
});
</script>
<!-- END: Simple Nivo Slider -->

SIMPLENIVOSLIDER2;

		return $simplenivoslider_add_js;

	}

	/**
	 * The Gallery shortcode.
	 *
	 * This implements the functionality of the Gallery Shortcode for displaying
	 * WordPress images on a post.
	 *
	 * @since 2.5.0
	 *
	 * @param array $attr {
	 *     Attributes of the gallery shortcode.
	 *
	 *     @type string $order      Order of the images in the gallery. Default 'ASC'. Accepts 'ASC', 'DESC'.
	 *     @type string $orderby    The field to use when ordering the images. Default 'menu_order ID'.
	 *                              Accepts any valid SQL ORDERBY statement.
	 *     @type int    $id         Post ID.
	 *     @type string $itemtag    HTML tag to use for each image in the gallery.
	 *                              Default 'dl', or 'figure' when the theme registers HTML5 gallery support.
	 *     @type string $icontag    HTML tag to use for each image's icon.
	 *                              Default 'dt', or 'div' when the theme registers HTML5 gallery support.
	 *     @type string $captiontag HTML tag to use for each image's caption.
	 *                              Default 'dd', or 'figcaption' when the theme registers HTML5 gallery support.
	 *     @type int    $columns    Number of columns of images to display. Default 3.
	 *     @type string $size       Size of the images to display. Default 'thumbnail'.
	 *     @type string $ids        A comma-separated list of IDs of attachments to display. Default empty.
	 *     @type string $include    A comma-separated list of IDs of attachments to include. Default empty.
	 *     @type string $exclude    A comma-separated list of IDs of attachments to exclude. Default empty.
	 *     @type string $link       What to link each image to. Default empty (links to the attachment page).
	 *                              Accepts 'file', 'none'.
	 * }
	 * @return string HTML content to display gallery.
	 */
	function simplenivoslider_gallery_shortcode( $attr ) {

		$post = get_post();

		static $instance = 0;
		$instance++;

		if ( ! empty( $attr['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) )
				$attr['orderby'] = 'post__in';
			$attr['include'] = $attr['ids'];
		}

		/**
		 * Filter the default gallery shortcode output.
		 *
		 * If the filtered output isn't empty, it will be used instead of generating
		 * the default gallery template.
		 *
		 * @since 2.5.0
		 *
		 * @see gallery_shortcode()
		 *
		 * @param string $output The gallery output. Default empty.
		 * @param array  $attr   Attributes of the gallery shortcode.
		 */
		$output = apply_filters( 'post_gallery', '', $attr );

		if ( $output != '' )
			return $output;

		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] )
				unset( $attr['orderby'] );
		}

		$html5 = current_theme_supports( 'html5', 'gallery' );

		extract(shortcode_atts(array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post ? $post->ID : 0,
			'itemtag'    => $html5 ? 'figure'     : 'dl',
			'icontag'    => $html5 ? 'div'        : 'dt',
			'captiontag' => $html5 ? 'figcaption' : 'dd',
			'columns'    => 3,
			'size'       => 'full',
			'include'    => '',
			'exclude'    => '',
			'link'       => 'none'
		), $attr, 'gallery'));

		$id = intval($id);
		if ( 'RAND' == $order )
			$orderby = 'none';

		if ( !empty($include) ) {
			$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( !empty($exclude) ) {
			$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		} else {
			$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		}

		if ( empty($attachments) )
			return '';

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment )
				$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
			return $output;
		}

		$i = 0;
		foreach ( $attachments as $id => $attachment ) {
			if ( ! empty( $link ) && 'file' === $link )
				$image_output = wp_get_attachment_link( $id, $size, false, false );
			elseif ( ! empty( $link ) && 'none' === $link )
				$image_output = wp_get_attachment_image( $id, $size, false );
			else
				$image_output = wp_get_attachment_link( $id, $size, true, false );

			$image_meta  = wp_get_attachment_metadata( $id );

			$orientation = '';
			if ( isset( $image_meta['height'], $image_meta['width'] ) )
				$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';

			$settings_tbl = get_option('simplenivoslider_settings');
			if ($settings_tbl['controlNavThumbs'] === 'true'){
				$image_thumb = wp_get_attachment_image_src( $id, 'thumbnail', false );
				if(preg_match_all("/<img(.+?)>/i", $image_output, $result) !== false){
			    	foreach ($result[1] as $value){
						if( !strpos($value, 'data-thumb=') ) {
							$thumb_data = ' data-thumb="'.$image_thumb[0].'" ';
							$image_output = str_replace($value, $thumb_data.$value, $image_output);
						}
					}
				}
			}
			$output .= $image_output;
		}

		return $output;

	}

}

?>