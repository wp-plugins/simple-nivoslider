<?php
/**
 * Simple Nivo Slider
 * 
 * @package    Simple Nivo Slider
 * @subpackage SimpleNivoSliderRegist registered in the database
    Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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

class SimpleNivoSliderRegist {

	/* ==================================================
	 * Settings register
	 * @since	1.0
	 */
	function register_settings(){

		if ( !get_option('simplenivoslider_mgsettings') ) {
			$mgsettings_tbl = array(
								'pagemax' => 20
							);
			update_option( 'simplenivoslider_mgsettings', $mgsettings_tbl );
		}

		if ( !get_option('simplenivoslider_settings') ) {
			$settings_tbl = array(
								'theme' => 'default',
								'effect' => 'random',
								'slices' => 15,
								'boxCols' => 8,
								'boxRows' => 4,
								'animSpeed' => 500,
								'pauseTime' => 3000,
								'startSlide' => 0,
								'directionNav' => 'true',
								'controlNav' => 'true',
								'controlNavThumbs' => 'false',
								'thumbswidth' => 40,
								'pauseOnHover' => 'true',
								'manualAdvance' => 'false',
								'prevText' => 'Prev',
								'nextText' => 'Next',
								'randomStart' => 'false'
							);
			update_option( 'simplenivoslider_settings', $settings_tbl );
		}

	}

}

?>