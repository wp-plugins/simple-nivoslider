<?php
/**
 * Simple Nivo Slider
 * 
 * @package    Simple Nivo Slider
 * @subpackage SimpleNivoSliderAdmin Management screen
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

class SimpleNivoSliderAdmin {

	/* ==================================================
	 * Add a "Settings" link to the plugins page
	 * @since	1.0
	 */
	function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty($this_plugin) ) {
			$this_plugin = SIMPLENIVOSLIDER_PLUGIN_BASE_FILE;
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="'.admin_url('options-general.php?page=simplenivoslider').'">'.__( 'Settings').'</a>';
		}
			return $links;
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	function plugin_menu() {
		add_options_page( 'Simple Nivo Slider Options', 'Simple Nivo Slider', 'manage_options', 'simplenivoslider', array($this, 'plugin_options') );
	}


	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	function plugin_options() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		wp_enqueue_style( 'jquery-ui-tabs', SIMPLENIVOSLIDER_PLUGIN_URL.'/simple-nivoslider/css/jquery-ui.css' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-tabs-in', SIMPLENIVOSLIDER_PLUGIN_URL.'/simple-nivoslider/js/jquery-ui-tabs-in.js' );
		wp_enqueue_script( 'jquery-check-selectall-in', SIMPLENIVOSLIDER_PLUGIN_URL.'/simple-nivoslider/js/jquery-check-selectall-in.js' );

		if( !empty($_POST) ) { 
			$this->options_updated();
			$this->post_meta_updated();
		}
		$scriptname = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH).'?page=simplenivoslider';

		$simplenivoslider_apply = get_option('simplenivoslider_apply');
		$simplenivoslider_settings = get_option('simplenivoslider_settings');
		$simplenivoslider_mgsettings = get_option('simplenivoslider_mgsettings');
		$pagemax =$simplenivoslider_mgsettings['pagemax'];

		?>

		<div class="wrap">
		<h2>Simple Nivo Slider</h2>

	<div id="tabs">
	  <ul>
	    <li><a href="#tabs-1"><?php _e('Settings'); ?></a></li>
		<li><a href="#tabs-2">Nivo Slider&nbsp<?php _e('Settings'); ?></a></li>
		<li><a href="#tabs-3"><?php _e('Caution:'); ?></a></li>
	<!--
		<li><a href="#tabs-4">FAQ</a></li>
	 -->
	  </ul>

	<form method="post" action="<?php echo $scriptname; ?>">

	  <div id="tabs-1">
		<div class="wrap">
		<h2><?php _e('Settings'); ?></h2>

			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			<p>
			<div><?php _e('Number of titles to show to this page', 'simplenivoslider'); ?>:<input type="text" name="simplenivoslider_mgsettings_pagemax" value="<?php echo $pagemax; ?>" size="3" /></div>

			<?php
			$args = array(
				'post_type' => 'any',
				'numberposts' => -1,
				'orderby' => 'date',
				'order' => 'DESC'
				); 

			$postpages = get_posts($args);

			// pagenation
			$pageallcount = 0;
			foreach ( $postpages as $postpage ) {
				++$pageallcount;
			}
			if (!empty($_GET['p'])){
				$page = $_GET['p'];
			} else {
				$page = 1;
			}
			$count = 0;
			$pagebegin = (($page - 1) * $pagemax) + 1;
			$pageend = $page * $pagemax;
			$pagelast = ceil($pageallcount / $pagemax);

			?>
			<table class="wp-list-table widefat">
			<tbody>
				<tr><td colspan="3">
				<td align="right">
				<?php $this->pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname);
				?>
				</td>
				</tr>
				<tr>
				<td align="left" valign="middle"><?php _e('Title'); ?></td>
				<td align="left" valign="middle"><?php _e('Type'); ?></td>
				<td align="left" valign="middle"><?php _e('Date/Time'); ?></td>
				<td align="left" valign="middle"><?php _e('Apply'); ?><div><input type="checkbox" id="group_simplenivoslider" class="checkAll"></div></td>
				</tr>
			<?php

			if ($postpages) {
				foreach ( $postpages as $postpage ) {
					++$count;
				    $apply = get_post_meta( $postpage->ID, 'simplenivoslider_apply', true );
					if ( $pagebegin <= $count && $count <= $pageend ) {
						$title = $postpage->post_title;
						$link = $postpage->guid;
						$posttype = $postpage->post_type;
						$date = $postpage->post_date;
					?>
						<tr>
							<td align="left" valign="middle"><a style="color: #4682b4;" title="<?php _e('View');?>" href="<?php echo $link; ?>" target="_blank"><?php echo $title; ?></a></td>
							<td align="left" valign="middle"><?php echo $posttype; ?></td>
							<td align="left" valign="middle"><?php echo $date; ?></td>
							<td align="left" valign="middle">
							    <input type="hidden" class="group_simplenivoslider" name="simplenivoslider_applys[<?php echo $postpage->ID; ?>]" value="false">
							    <input type="checkbox" class="group_simplenivoslider" name="simplenivoslider_applys[<?php echo $postpage->ID; ?>]" value="true" <?php if ( $apply == true ) { echo 'checked'; }?>>
							</td>
						</tr>
					<?php
					} else {
					?>
					    <input type="hidden" name="simplenivoslider_applys[<?php echo $postpage->ID; ?>]" value="<?php echo $apply; ?>">
					<?php
					}
				}
			}
			?>
				<tr><td colspan="3">
				<td align="right">
				<?php $this->pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname);
				?>
				</td>
				</tr>
			</tbody>
			</table>

			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

		</div>
	  </div>

	  <div id="tabs-2">
		<div class="wrap">
			<h2>Nivo Slider&nbsp<?php _e('Settings'); ?>(<a href="http://docs.dev7studios.com/jquery-plugins/nivo-slider" target="_blank"><font color="red"><?php _e('Documentation'); ?></font></a>)</h2>	

			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			</table>
			<table>
			<tbody>
				<tr>
					<td align="right" valign="middle">theme</td>
					<td align="left" valign="middle">
					<?php $target_settings_theme = $simplenivoslider_settings['theme']; ?>
					<select id="simplenivoslider_settings_theme" name="simplenivoslider_settings_theme">
						<option <?php if ('default' == $target_settings_theme)echo 'selected="selected"'; ?>>default</option>
						<option <?php if ('dark' == $target_settings_theme)echo 'selected="selected"'; ?>>dark</option>
						<option <?php if ('light' == $target_settings_theme)echo 'selected="selected"'; ?>>light</option>
						<option <?php if ('bar' == $target_settings_theme)echo 'selected="selected"'; ?>>bar</option>
					</select>
					</td>
					<td align="left" valign="middle"><li><?php _e('Using themes', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">effect</td>
					<td align="left" valign="middle">
					<?php $target_settings_effect = $simplenivoslider_settings['effect']; ?>
					<select id="simplenivoslider_settings_effect" name="simplenivoslider_settings_effect">
						<option <?php if ('sliceDown' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceDown</option>
						<option <?php if ('sliceDownLeft' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceDownLeft</option>
						<option <?php if ('sliceUp' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceUp</option>
						<option <?php if ('sliceUpLeft' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceUpLeft</option>
						<option <?php if ('sliceUpDown' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceUpDown</option>
						<option <?php if ('sliceUpDownLeft' == $target_settings_effect)echo 'selected="selected"'; ?>>sliceUpDownLeft</option>
						<option <?php if ('fold' == $target_settings_effect)echo 'selected="selected"'; ?>>fold</option>
						<option <?php if ('fade' == $target_settings_effect)echo 'selected="selected"'; ?>>fade</option>
						<option <?php if ('random' == $target_settings_effect)echo 'selected="selected"'; ?>>random</option>
						<option <?php if ('slideInRight' == $target_settings_effect)echo 'selected="selected"'; ?>>slideInRight</option>
						<option <?php if ('slideInLeft' == $target_settings_effect)echo 'selected="selected"'; ?>>slideInLeft</option>
						<option <?php if ('boxRandom' == $target_settings_effect)echo 'selected="selected"'; ?>>boxRandom</option>
						<option <?php if ('boxRain' == $target_settings_effect)echo 'selected="selected"'; ?>>boxRain</option>
						<option <?php if ('boxRainReverse' == $target_settings_effect)echo 'selected="selected"'; ?>>boxRainReverse</option>
						<option <?php if ('boxRainGrow' == $target_settings_effect)echo 'selected="selected"'; ?>>boxRainGrow</option>
						<option <?php if ('boxRainGrowReverse' == $target_settings_effect)echo 'selected="selected"'; ?>>boxRainGrowReverse</option>
					</select>
					</td>
					<td align="left" valign="middle"><li><?php _e('Specify sets like', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">slices</td>
					<td align="left" valign="middle">
						<input type="text" id="simplenivoslider_settings_slices" name="simplenivoslider_settings_slices" value="<?php echo $simplenivoslider_settings['slices'] ?>" style="width: 80px" />
					</td>
					<td align="left" valign="middle"><li><?php _e('For slice animations', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">boxCols</td>
					<td align="left" valign="middle">
						<input type="text" id="simplenivoslider_settings_boxCols" name="simplenivoslider_settings_boxCols" value="<?php echo $simplenivoslider_settings['boxCols'] ?>" style="width: 80px" />
					</td>
					<td align="left" valign="middle"><li><?php _e('For box animations cols', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">boxRows</td>
					<td align="left" valign="middle">
						<input type="text" id="simplenivoslider_settings_boxRows" name="simplenivoslider_settings_boxRows" value="<?php echo $simplenivoslider_settings['boxRows'] ?>" style="width: 80px" />
					</td>
					<td align="left" valign="middle"><li><?php _e('For box animations rows', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">animSpeed</td>
					<td align="left" valign="middle">
						<input type="text" id="simplenivoslider_settings_animSpeed" name="simplenivoslider_settings_animSpeed" value="<?php echo $simplenivoslider_settings['animSpeed'] ?>" style="width: 80px" />msec
					</td>
					<td align="left" valign="middle"><li><?php _e('Slide transition speed', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">pauseTime</td>
					<td align="left" valign="middle">
						<input type="text" id="simplenivoslider_settings_pauseTime" name="simplenivoslider_settings_pauseTime" value="<?php echo $simplenivoslider_settings['pauseTime'] ?>" style="width: 80px" />msec
					</td>
					<td align="left" valign="middle"><li><?php _e('How long each slide will show', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">startSlide</td>
					<td align="left" valign="middle">
						<input type="text" id="simplenivoslider_settings_startSlide" name="simplenivoslider_settings_startSlide" value="<?php echo $simplenivoslider_settings['startSlide'] ?>" style="width: 80px" />
					</td>
					<td align="left" valign="middle"><li><?php _e('Set starting Slide (0 index)', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">directionNav</td>
					<td align="left" valign="middle">
					<?php $target_settings_directionNav = $simplenivoslider_settings['directionNav']; ?>
					<select id="simplenivoslider_settings_directionNav" name="simplenivoslider_settings_directionNav">
						<option <?php if ('true' == $target_settings_directionNav)echo 'selected="selected"'; ?>>true</option>
						<option <?php if ('false' == $target_settings_directionNav)echo 'selected="selected"'; ?>>false</option>
					</select>
					</td>
					<td align="left" valign="middle"><li><?php _e('Next & Prev navigation', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">controlNav</td>
					<td align="left" valign="middle">
					<?php $target_settings_controlNav = $simplenivoslider_settings['controlNav']; ?>
					<select id="simplenivoslider_settings_controlNav" name="simplenivoslider_settings_controlNav">
						<option <?php if ('true' == $target_settings_controlNav)echo 'selected="selected"'; ?>>true</option>
						<option <?php if ('false' == $target_settings_controlNav)echo 'selected="selected"'; ?>>false</option>
					</select>
					</td>
					<td align="left" valign="middle"><li><?php _e('1,2,3... navigation', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">controlNavThumbs</td>
					<td align="left" valign="middle">
					<?php $target_settings_controlNavThumbs = $simplenivoslider_settings['controlNavThumbs']; ?>
					<select id="simplenivoslider_settings_controlNavThumbs" name="simplenivoslider_settings_controlNavThumbs">
						<option <?php if ('true' == $target_settings_controlNavThumbs)echo 'selected="selected"'; ?>>true</option>
						<option <?php if ('false' == $target_settings_controlNavThumbs)echo 'selected="selected"'; ?>>false</option>
					</select>
					</td>
					<td align="left" valign="middle"><li><?php _e('Use thumbnails for Control Nav', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">thumbswidth</td>
					<td align="left" valign="middle">
						<input type="text" id="simplenivoslider_settings_thumbswidth" name="simplenivoslider_settings_thumbswidth" value="<?php echo $simplenivoslider_settings['thumbswidth'] ?>" style="width: 80px" />px
					</td>
					<td align="left" valign="middle"><li><?php _e('Width of thumbnails', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">pauseOnHover</td>
					<td align="left" valign="middle">
					<?php $target_settings_pauseOnHover = $simplenivoslider_settings['pauseOnHover']; ?>
					<select id="simplenivoslider_settings_pauseOnHover" name="simplenivoslider_settings_pauseOnHover">
						<option <?php if ('true' == $target_settings_pauseOnHover)echo 'selected="selected"'; ?>>true</option>
						<option <?php if ('false' == $target_settings_pauseOnHover)echo 'selected="selected"'; ?>>false</option>
					</select>
					</td>
					<td align="left" valign="middle"><li><?php _e('Stop animation while hovering', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">manualAdvance</td>
					<td align="left" valign="middle">
					<?php $target_settings_manualAdvance = $simplenivoslider_settings['manualAdvance']; ?>
					<select id="simplenivoslider_settings_manualAdvance" name="simplenivoslider_settings_manualAdvance">
						<option <?php if ('true' == $target_settings_manualAdvance)echo 'selected="selected"'; ?>>true</option>
						<option <?php if ('false' == $target_settings_manualAdvance)echo 'selected="selected"'; ?>>false</option>
					</select>
					</td>
					<td align="left" valign="middle"><li><?php _e('Force manual transitions', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">prevText</td>
					<td align="left" valign="middle">
						<input type="text" id="simplenivoslider_settings_prevText" name="simplenivoslider_settings_prevText" value="<?php echo $simplenivoslider_settings['prevText'] ?>" />
					</td>
					<td align="left" valign="middle"><li><?php _e('Prev directionNav text', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">nextText</td>
					<td align="left" valign="middle">
						<input type="text" id="simplenivoslider_settings_nextText" name="simplenivoslider_settings_nextText" value="<?php echo $simplenivoslider_settings['nextText'] ?>" />
					</td>
					<td align="left" valign="middle"><li><?php _e('Next directionNav text', 'simplenivoslider'); ?></li></td>
				</tr>
				<tr>
					<td align="right" valign="middle">randomStart</td>
					<td align="left" valign="middle">
					<?php $target_settings_randomStart = $simplenivoslider_settings['randomStart']; ?>
					<select id="simplenivoslider_settings_randomStart" name="simplenivoslider_settings_randomStart">
						<option <?php if ('true' == $target_settings_randomStart)echo 'selected="selected"'; ?>>true</option>
						<option <?php if ('false' == $target_settings_randomStart)echo 'selected="selected"'; ?>>false</option>
					</select>
					</td>
					<td align="left" valign="middle"><li><?php _e('Start on a random slide', 'simplenivoslider'); ?></li></td>
				</tr>
			</tbody>
			</table>

			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

		</div>
	  </div>

	  <div id="tabs-3">
		<div class="wrap">
			<h2><?php _e('Caution:'); ?></h2>
			<li><h3><?php _e('Meta-box of Simple NivoSlider will be added to [Edit Post] and [Edit Page]. Please do apply it.', 'simplenivoslider'); ?></h3></li>
			<img src = "<?php echo SIMPLENIVOSLIDER_PLUGIN_URL.'/simple-nivoslider/png/apply.png'; ?>">
		</div>
	  </div>

	<!--
	  <div id="tabs-4">
		<div class="wrap">
		<h2>FAQ</h2>

		</div>
	  </div>
	-->

	</form>
	</div>

		</div>
		<?php
	}

	/* ==================================================
	 * Pagenation
	 * @since	1.0
	 * string	$page
	 * string	$pagebegin
	 * string	$pageend
	 * string	$pagelast
	 * string	$scriptname
	 * return	$html
	 */
	function pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname){

			$pageprev = $page - 1;
			$pagenext = $page + 1;
			?>
<div class='tablenav-pages'>
<span class='pagination-links'>
<?php if ( $page <> 1 ){
		?><a title='<?php _e('Go to the first page'); ?>' href='<?php echo $scriptname; ?>'>&laquo;</a>
		<a title='<?php _e('Go to the previous page'); ?>' href='<?php echo $scriptname.'&p='.$pageprev ; ?>'>&lsaquo;</a>
<?php }	?>
<?php echo $page; ?> / <?php echo $pagelast; ?>
<?php if ( $page <> $pagelast ){
		?><a title='<?php _e('Go to the next page'); ?>' href='<?php echo $scriptname.'&p='.$pagenext ; ?>'>&rsaquo;</a>
		<a title='<?php _e('Go to the last page'); ?>' href='<?php echo $scriptname.'&p='.$pagelast; ?>'>&raquo;</a>
<?php }	?>
</span>
</div>
			<?php

	}

	/* ==================================================
	 * Update wp_options table.
	 * @since	1.0
	 */
	function options_updated(){

		$mgsettings_tbl = array(
						'pagemax' => intval($_POST['simplenivoslider_mgsettings_pagemax'])
						);
		update_option( 'simplenivoslider_mgsettings', $mgsettings_tbl );
				
		$settings_tbl = array(
						'theme' => $_POST['simplenivoslider_settings_theme'],
						'effect' => $_POST['simplenivoslider_settings_effect'],
						'slices' => intval($_POST['simplenivoslider_settings_slices']),
						'boxCols' => intval($_POST['simplenivoslider_settings_boxCols']),
						'boxRows' => intval($_POST['simplenivoslider_settings_boxRows']),
						'animSpeed' => intval($_POST['simplenivoslider_settings_animSpeed']),
						'pauseTime' => intval($_POST['simplenivoslider_settings_pauseTime']),
						'startSlide' => intval($_POST['simplenivoslider_settings_startSlide']),
						'directionNav' => $_POST['simplenivoslider_settings_directionNav'],
						'controlNav' => $_POST['simplenivoslider_settings_controlNav'],
						'controlNavThumbs' => $_POST['simplenivoslider_settings_controlNavThumbs'],
						'thumbswidth' => $_POST['simplenivoslider_settings_thumbswidth'],
						'pauseOnHover' => $_POST['simplenivoslider_settings_pauseOnHover'],
						'manualAdvance' => $_POST['simplenivoslider_settings_manualAdvance'],
						'prevText' => $_POST['simplenivoslider_settings_prevText'],
						'nextText' => $_POST['simplenivoslider_settings_nextText'],
						'randomStart' => $_POST['simplenivoslider_settings_randomStart']
						);
		update_option( 'simplenivoslider_settings', $settings_tbl );

	}

	/* ==================================================
	 * Update wp_postmeta table for admin settings.
	 * @since	1.0
	 */
	function post_meta_updated() {

		$simplenivoslider_applys = $_POST['simplenivoslider_applys'];

		foreach ( $simplenivoslider_applys as $key => $value ) {
			if ( $value === 'true' ) {
		    	update_post_meta( $key, 'simplenivoslider_apply', $value );
			} else {
				delete_post_meta( $key, 'simplenivoslider_apply' );
			}
		}

	}

	/* ==================================================
	 * Add custom box.
	 * @since	1.0
	 */
	function add_apply_simplenivoslider_custom_box() {
	    add_meta_box('simplenivoslider_apply', 'Simple Nivoslider', array(&$this,'apply_simplenivoslider_custom_box'), 'page', 'side', 'high');
	    add_meta_box('simplenivoslider_apply', 'Simple Nivoslider', array(&$this,'apply_simplenivoslider_custom_box'), 'post', 'side', 'high');
	}
	 
	/* ==================================================
	 * Custom box.
	 * @since	1.0
	 */
	function apply_simplenivoslider_custom_box() {

		if ( isset($_GET['post']) ) {
			$get_post = $_GET['post'];
		} else {
			$get_post = NULL;
		}

		$simplenivoslider_apply = get_post_meta( $get_post, 'simplenivoslider_apply' );

		?>
		<table>
		<tbody>
			<tr>
				<td>
					<div>
						<?php
						if (!empty($simplenivoslider_apply)) {
						?>
							<input type="radio" name="simplenivoslider_apply" value="true" <?php if ($simplenivoslider_apply[0] === 'true') { echo 'checked'; }?>><?php _e('Apply'); ?>&nbsp;&nbsp;
							<input type="radio" name="simplenivoslider_apply" value="false" <?php if ($simplenivoslider_apply[0] <> 'true') { echo 'checked'; }?>><?php _e('None');
						} else {
						?>
							<input type="radio" name="simplenivoslider_apply" value="true"><?php _e('Apply'); ?>&nbsp;&nbsp;
							<input type="radio" name="simplenivoslider_apply" value="false" checked><?php _e('None');
						}
						?>
					</div>
				</td>
			</tr>
		</tbody>
		</table>
		<?php

	}

	/* ==================================================
	 * Update wp_postmeta table.
	 * @since	1.0
	 */
	function save_apply_simplenivoslider_postdata( $post_id ) {

		if ( isset($_POST['simplenivoslider_apply']) ) {
			$dataapply = $_POST['simplenivoslider_apply'];
			if ( $dataapply === 'true' ) {
				add_post_meta( $post_id, 'simplenivoslider_apply', $dataapply, true );
			} else if ( $dataapply === ''  || $dataapply === 'false' ) {
				delete_post_meta( $post_id, 'simplenivoslider_apply' );
			}
		}

	}

	/* ==================================================
	 * Posts columns menu
	 * @since	1.0
	 */
	function posts_columns_simplenivoslider($columns){
	    $columns['column_simplenivoslider_apply'] = __('Simple NivoSlider');
	    return $columns;
	}

	/* ==================================================
	 * Posts columns
	 * @since	1.0
	 */
	function posts_custom_columns_simplenivoslider($column_name, $post_id){
		if($column_name === 'column_simplenivoslider_apply'){
			$simplenivoslider_apply = get_post_meta( $post_id, 'simplenivoslider_apply' );
			if (!empty($simplenivoslider_apply)) {
				if ($simplenivoslider_apply[0]){
					_e('Apply');
				} else {
					_e('None');
				}
			} else {
				_e('None');
			}
	    }
	}

	/* ==================================================
	 * Pages columns menu
	 * @since	1.0
	 */
	function pages_columns_simplenivoslider($columns){
	    $columns['column_simplenivoslider_apply'] = __('Simple NivoSlider');
	    return $columns;
	}

	/* ==================================================
	 * Pages columns
	 * @since	1.0
	 */
	function pages_custom_columns_simplenivoslider($column_name, $post_id){
		if($column_name === 'column_simplenivoslider_apply'){
			$simplenivoslider_apply = get_post_meta( $post_id, 'simplenivoslider_apply' );
			if (!empty($simplenivoslider_apply)) {
				if ($simplenivoslider_apply[0]){
					_e('Apply');
				} else {
					_e('None');
				}
			} else {
				_e('None');
			}
	    }
	}

}

?>