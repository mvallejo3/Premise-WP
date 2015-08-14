<?php
/**
 * Premise Library
 *
 * @package Premise
 * @subpackage Premise Library
 */

/**
 * Premise Print
 * 
 * @param var $var variable to print
 * @return string will print $var or 'Empty $var' wrapped in <pre> tags.
 */
function premise_print( $var ) {
	$var = ( is_array( $var ) && empty( $var ) ) ? 'Empty array()' : $var;
	$var = !empty($var) ? $var : 'Empty $var';
	echo '<pre style="display:block;margin:40px auto;width:90%;overflow:auto;"><code style="display:block;padding:20px;">';
	print_r( $var );
	echo '</code></pre>';
}





/**
 * Premise Field
 *
 * display or return a form field.
 *
 * @since 1.2 Parametters order changed, new parametter added:
 *        Old params: (array) arguments, (boolean) echo
 *        New params: (string) type, (array) arguments, (boolean) echo
 * 
 * @see class PremiseField in premise-forms-class.php
 *
 * @param string $type  the type of field to print or return. i.e. text, textarea, checkbox, wp_media, video
 * @param  array  $args array of arguments to buid a field
 * @param boolean $echo true outputs the html on to the page. false returns it as a string
 * @return string         html markup for a form field
 */
function premise_field( $type = 'text', $args = array(), $echo = true ) {

	/**
	 * Backward compatibility with version < 1.2
	 *
	 * Allows you to skipt the first param and pass it as part of args i.e. ( 'type' => 'text' )
	 * 
	 * @since 1.2 If the first param passed is an array, the function was called the old way. Fix it.
	 */
	if ( is_array( $type ) ) {
		$args = $type;
		premise_field_deprecated( $args, $echo );
	}

	$type  = ! empty( $type ) && is_string( $type ) ? $type : 'text';
	$field = new PremiseField( $type, $args );
	$html  = $field->get_field();

	if( !$echo )
		return $html;
	else
		echo $html;
}





/**
 * Old premise field
 *
 * @since 1.2 Used to be premise_field() but whole class got replaced. offered for backward compatibility.
 *
 * @deprecated 1.2 Replaced with premise_field()
 * 
 * @param  array   $args array of array for aguments to build field
 * @param  boolean $echo wheteher to putput the field or return as string
 * @return string        html for string. echoed or returned
 */
function premise_field_deprecated( $args = array(), $echo = true ) {

	if( array_key_exists( 'options', $args ) || (count($args) == count($args, COUNT_RECURSIVE) ) ) {
		$field = new PremiseField_Deprecated( $args );
		$html .= $field->get_field();
	}
	else{
		foreach ( $args as $arg ) {
			$field = new PremiseField_Deprecated( $arg );
			$html .= $field->get_field();
		}
	}
	
	if( !$echo )
		return $html;
	else
		echo $html;
}





function premise_field_section( $args = array(), $echo = true ) {
	$defaults = array(
		'container'             => true,
		'container_title'       => '',
		'container_desc'        => '',
		'container_class'       => '',
		'container_inner_class' => '',
		'fields' 				=> array(),
	);

	$field_section = wp_parse_args( $args, $defaults );

	$html  = ( true === $field_section['container'] )                                                ? '<div class="field-section'                                     : '';
	$html .= ( true === $field_section['container'] && !empty( $field_section['container_class'] ) ) ? ' ' . $field_section['container_class'] . '">'                  : '">';
	$html .= !empty( $field_section['container_title'] )                                             ? '<h3>' . $field_section['container_title'] . '</h3>'            : '';
	$html .= !empty( $field_section['container_desc'] )                                             ? '<p>' . $field_section['container_desc'] . '</p>'               : '';
	$html .= !empty( $field_section['container_inner_class'] )                                       ? '<div class="' . $field_section['container_inner_class'] . '">' : '';
	$html .= premise_field( $field_section['fields'], false ); // This gets the actual field
	$html .= !empty( $field_section['container_inner_class'] )                                       ? '</div>'                                                        : '';
	$html .= ( true === $field_section['container'] )                                                ? '</div>'                                                        : '';

	if( !$echo )
		return $html;
	else
		echo (string) $html;
}







/**
 * A more flexible option to the default WP get_option()
 *
 * @param  string $option_keys [description]
 * @param  string $key         [description]
 * @return mixed               Returns the value of the key within the array of options searched for
 */
function premise_get_option( $option_keys = '', $key = '' ) {
	if ( empty( $option_keys ) ) 
		return false;

	if( is_array( $option_keys ) ) {
		$options = array();

		foreach( $option_keys as $option ) {
			array_push( $options, get_option( $option ) );
		}
	}
	else {
		$options = (array) get_option( $option_keys );

		if( !empty( $key ) ){
			if ( is_array( $key ) ) {
				$value = $options;
				foreach ( $key as $k => $v ) {

					if ( array_key_exists( $v, (array)$value ) ) {
						$value = $value[$v];
					} else {
						// like get_option, return FALSE if no value
						$value = FALSE;
					}

				}

				return $value;
			}
			else {
				if ( array_key_exists( $key, $options ) ) {
					return $options[$key];
				} else {
					// like get_option, return FALSE if no value
					return FALSE;
				}
			}
		}
	}
	return $options;
}





/**
 * Insert Background Fields
 * @param string $name required, the name attribute to assign to each field. Fields are saved in an array
 * @param string $title optional, label output for select dropdown
 * @param string $intro optional, a description for select dropdown
 * @return echo will echo upload fields to insert a background
 *
 * @see premise_the_background()
 */
function premise_save_background( $name ) {
	$field = get_option( $name );
	
	$background = array(
		'type'      => 'select',
		'label'     => 'Select Background Option',
		'tooltip'   => 'Set your Home Splash background.',
		'name'      => $name.'[bg]',
		'id'        => $name.'-bg',
		'value'     => $field['bg'],
		'placeholder' => 'Select Background',
		'attribute' => 'onchange="premiseSelectBackground(this);"',
		'options'   => array( 
			'Solid Background'    => 'color',
			'Gradient Background' => 'gradient',
			'Image Background'    => 'image', 
		),
	);

	$color = array(
		'type' => 'minicolors',
		'label' => 'Select a color',
		'name' => $name.'[color]',
		'id' => $name.'-color',
		'value' => $field['color'],
	);
	
	$gradient = array(
		array(
			'type' => 'minicolors',
			'label' => 'Start Gradient',
			'name' => $name.'[gradient][gradient-start]',
			'id' => $name.'-gradient-start',
			'value' => $field['gradient']['gradient-start'],
		),
		array(
			'type' => 'minicolors',
			'label' => 'Finish Gradient',
			'name' => $name.'[gradient][gradient-finish]',
			'id' => $name.'-gradient-finish',
			'value' => $field['gradient']['gradient-finish'],
		),
		array(
			'type' => 'radio',
			'name' => $name.'[gradient][gradient-dir]',
			'value' => $field['gradient']['gradient-dir'],
			'label' => 'Select Gradient Type',
			'options' => array(
				array(
					'label' => 'Linear',
					'id' => $name.'-gradient-linear',
					'value_att' => 'linear',
				),
				array(
					'label' => 'Radial',
					'id' => $name.'-gradient-radial',
					'value_att' => 'radial',
				),
			),
		),
		array(
			'type' => 'radio',
			'name' => $name.'[gradient][gradient-linear-dir]',
			'value' => $field['gradient']['gradient-linear-dir'],
			'label' => 'Select Gradient Type',
			'options' => array(
				array(
					'label' => 'Top to Bottom',
					'id' => $name.'-gradient-dir-ttb',
					'value_att' => 'ttb',
				),
				array(
					'label' => 'Left to Right',
					'id' => $name.'-gradient-dir-ltr',
					'value_att' => 'ltr',
				),
			),
		),
	);
	
	$image = array(
		array(
			'type' => 'file',
			'name' => $name.'[image][image]',
			'value' => $field['image']['image'],
			'label' => 'Upload Image',
			'tootltip' => 'You can also use a pattern background by simply uploading a pattern and choosing "Repeat" option next.',
		),
		array(
			'type' => 'select',
			'name' => $name.'[image][repeat]',
			'value' => $field['image']['repeat'],
			'label' => 'Repeat Background',
			'options' => array( 
				'Reapeat' => 'repeat',
				'Reapeat-X' => 'repeat-x',
				'Reapeat-Y' => 'repeat-y',
				'No Repeat' => 'no-repeat',
			),
		),
		array(
			'type' => 'select',
			'name' => $name.'[image][attach]',
			'value' => $field['image']['attach'],
			'label' => 'Background Attachment',
			'placeholder' => 'Select Attachment',
			'options' => array( 
				'Fixed' => 'fixed',
				'Scroll' => 'scroll',
			),
		),
		array(
			'type' => 'select',
			'name' => $name.'[image][position-x]',
			'value' => $field['image']['position-x'],
			'label' => 'Background Position-X',
			'options' => array( 
				'Right' => 'right',
				'Center' => 'center',
				'Left' => 'left',
			),
		),
		array(
			'type' => 'select',
			'name' => $name.'[image][position-y]',
			'value' => $field['image']['position-y'],
			'label' => 'Background Position-Y',
			'options' => array( 
				'Top' => 'top',
				'Center' => 'center',
				'Bottom' => 'bottom',
			),
		),
		array(
			'type' => 'select',
			'name' => $name.'[image][size]',
			'value' => $field['image']['size'],
			'label' => 'Background Size',
			'options' => array( 
				'Normal' => '',
				'Cover' => '/ cover',
				'Contain' => 'contain',
			),
		)
	);
	//ouput fields
	echo '<div class="premise-background-select row"><div class="col2">';
		premise_field( $background );
	echo '</div><div class="col2">';

	//color
	echo '<div class="block premise-background premise-color-background"', $field['bg'] !== 'color' ? 'style="display:none;"' : '', '>';
		premise_field( $color );
	echo '</div>';

	//gradient
	echo '<div class="block premise-background premise-gradient-background"', $field['bg'] !== 'gradient' ? 'style="display:none;"' : '', '>';
		premise_field( $gradient );
	echo '</div>';

	//image
	echo '<div class="block premise-background premise-image-background"', $field['bg'] !== 'image' ? 'style="display:none;"' : '', '>';
		premise_field( $image );
	echo '</div>';

	echo '</div></div>';
}

/**
 * Home Splash Styles
 * @param  string $option_name name of option key used to save the background
 * @param  bool   $echo 	   default true will echo styles. False will return styles
 * @return string              inline styles on element
 *
 * @see premise_save_background()
 */
function premise_the_background( $option_name, $echo = true ) {
	$bg = get_option( $option_name );
	$styles = '';

	switch( $bg['bg'] ) {
		case 'color' :
			$styles .= 'background: '.$bg['color'].';';
		break;

		case 'pattern' :
			$styles .= 'background: url('.$bg['pattern'].') repeat scroll top left;';
		break;

		case 'image' :
			$styles .= 'background: url('.$bg['image']['image'].') '.$bg['image']['repeat'].' '.$bg['image']['attach'].' '.$bg['image']['position-x'].' '.$bg['image']['position-x'].' '.$bg['image']['cover'].';';
		break;

		case 'gradient' :
			if( 'radial' == $bg['gradient']['gradient-dir'] ){
				$styles .= "
					background-color: ".$bg['gradient']['gradient-start'].";
					background: -webkit-gradient(radial, center center, 0, center center, 460, from(".$bg['gradient']['gradient-start']."), to(".$bg['gradient']['gradient-finish']."));
					background: -webkit-radial-gradient(circle, ".$bg['gradient']['gradient-start'].", ".$bg['gradient']['gradient-finish'].");
					background: -moz-radial-gradient(circle, ".$bg['gradient']['gradient-start'].", ".$bg['gradient']['gradient-finish'].");
					background: -ms-radial-gradient(circle, ".$bg['gradient']['gradient-start'].", ".$bg['gradient']['gradient-finish'].");";
			}
			else{
				if( 'ttb' == $bg['gradient']['gradient-linear-dir'] ){
					$styles .= "
						background-color: ".$bg['gradient']['gradient-start'].";
						background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(".$bg['gradient']['gradient-start']."), to(".$bg['gradient']['gradient-finish']."));
						background: -webkit-linear-gradient(top, ".$bg['gradient']['gradient-start'].", ".$bg['gradient']['gradient-finish'].");
						background: -moz-linear-gradient(top, ".$bg['gradient']['gradient-start'].", ".$bg['gradient']['gradient-finish'].");
						background: -ms-linear-gradient(top, ".$bg['gradient']['gradient-start'].", ".$bg['gradient']['gradient-finish'].");
						background: -o-linear-gradient(top, ".$bg['gradient']['gradient-start'].", ".$bg['gradient']['gradient-finish'].");";
				}
				else{
					$styles .= "
						background-color: ".$bg['gradient']['gradient-start'].";
						background: -webkit-gradient(linear, left top, right top, from(".$bg['gradient']['gradient-start']."), to(".$bg['gradient']['gradient-finish']."));
						background: -webkit-linear-gradient(left, ".$bg['gradient']['gradient-start'].", ".$bg['gradient']['gradient-finish'].");
						background: -moz-linear-gradient(left, ".$bg['gradient']['gradient-start'].", ".$bg['gradient']['gradient-finish'].");
						background: -ms-linear-gradient(left, ".$bg['gradient']['gradient-start'].", ".$bg['gradient']['gradient-finish'].");
						background: -o-linear-gradient(left, ".$bg['gradient']['gradient-start'].", ".$bg['gradient']['gradient-finish'].");";
				}
			}
		break;
	}

	if( !$echo ) 
		return $styles;

	echo $styles;
}





function premise_load_ajax_markup() {
	$ajax_overlay = '<div id="premise-ajax-overlay" style="
		display:none;
		position:fixed;
		top:0;
		left:0;
		width:100%;
		height:100%;
		background-color:#FFFFFF;
		opacity:.6;
		z-index:9990;
		"></div>';

	$ajax_icon = '<div id="premise-ajax-loading" 
		class="absolute center" style="
		display:none;
		position:fixed;
		width:60px;
		top:40%;
		left:50%;
		margin-left:-30px;
		z-index:9991;
		"><i class="fa fa-3x fa-spinner fa-spin"></i></div>';

	$ajax_dialog = '<div id="premise-ajax-dialog" style="
		display:none;
		position:fixed;
		top:10%;
		left:10%;
		width:80%;
		height:80%;
		background-color:#FFFFFF;
		z-index:9992;
		overflow:auto;
		box-shadow: 0 0 5px #333333;
		-webkit-box-shadow: 0 0 5px #333333;
		-moz-box-shadow: 0 0 5px #333333;
		-ms-box-shadow: 0 0 5px #333333;
		-o-box-shadow: 0 0 5px #333333;
		padding:20px;
		" class="round-corners25"></div>';

	$ajax_control = '<a id="premise-ajax-close" style="
		display:none;
		position: fixed;
		padding: 2px 12px;
		top: 60px;
		right: 40px;
		background: #FFFFFF;
		z-index: 9995;
		line-height: 150%;
		font-size: 20px;
		color: #AAAAAA;
		border-radius: 24px;
		-webkit-border-radius: 24px;
		-moz-border-radius: 24px;
		-ms-border-radius: 24px;
		-o-border-radius: 24px;
		box-shadow: 0 0 5px #333333;
		-webkit-box-shadow: 0 0 5px #333333;
		-moz-box-shadow: 0 0 5px #333333;
		-ms-box-shadow: 0 0 5px #333333;
		-o-box-shadow: 0 0 5px #333333;
		" class="row" href="javascript:;" onclick="premiseAjaxClose();">x</a>';

	echo $ajax_overlay, $ajax_icon, $ajax_dialog, $ajax_control;
}



/**
 * Premise tab
 *
 * @see  http://www.onextrapixel.com/2013/07/31/creating-content-tabs-with-pure-css/
 *
 * @param  string $part        Tab part: 'header', 'footer', 'first'
 * @param  intege $number_tabs Number of tabs
 * @param  string $title       Tab title
 * @param  string $icon        font awesome icon or theme logo if 'first'
 *
 * @return outputs the Premise tab HTML
 */
function premise_tab( $part, $number_tabs = 1, $title = '', $icon = '' ) {

	static $tab_number = 1;

	switch ( $part ) {

		case 'first':
			if ( $tab_number == 1 ) : ?>

			<ul class="premise-tabs clear">

			<?php endif; ?>
				<li style="width: <?php echo 100 / $number_tabs; ?>%">
					<label class="first">
						<img src="<?php echo $icon; ?>" alt="Logo" />
						<br />
						<span><?php echo $title; ?></span>
					</label>
				</li>
			<?php
			break;
		
		case 'header':

			$checked = '';

			if ( $tab_number == 1 )
				$checked = 'checked';
			?>
				<li style="width: <?php echo 100 / $number_tabs; ?>%">
					<input type="radio" <?php echo $checked; ?> name="premise-tabs" id="premise-tab<?php echo $tab_number; ?>">
					<label for="premise-tab<?php echo $tab_number; ?>">
						<i class="fa <?php echo $icon; ?>"></i>
						<?php echo $title; ?>
					</label>

					<div id="premise-tab-content<?php echo $tab_number; ?>" class="premise-tab-content">
			<?php

			$tab_number ++;

			break;
		
		case 'footer':
			?>
					</div><!-- /premise-tab-content -->
				</li>
			<?php if ( ($tab_number + 1) == $number_tabs ) : ?>

			</ul>

			<?php endif;
			break;

		default:

			break;
	}
}




/**
 * Get font awesome icons
 * 
 * @return array array of icons
 */
function premise_get_fa_icons() { 
	return array('fa-adjust','fa-adn','fa-align-center','fa-align-justify','fa-align-left','fa-align-right','fa-ambulance','fa-anchor','fa-android','fa-angellist','fa-angle-double-down','fa-angle-double-left','fa-angle-double-right','fa-angle-double-up','fa-angle-down','fa-angle-left','fa-angle-right','fa-angle-up','fa-apple','fa-archive','fa-area-chart','fa-arrow-circle-down','fa-arrow-circle-left','fa-arrow-circle-o-down','fa-arrow-circle-o-left','fa-arrow-circle-o-right','fa-arrow-circle-o-up','fa-arrow-circle-right','fa-arrow-circle-up','fa-arrow-down','fa-arrow-left','fa-arrow-right','fa-arrow-up','fa-arrows','fa-arrows-alt','fa-arrows-h','fa-arrows-v','fa-asterisk','fa-at','fa-automobile','fa-backward','fa-ban','fa-bank','fa-bar-chart','fa-bar-chart-o','fa-barcode','fa-bars','fa-beer','fa-behance','fa-behance-square','fa-bell','fa-bell-o','fa-bell-slash','fa-bell-slash-o','fa-bicycle','fa-binoculars','fa-birthday-cake','fa-bitbucket','fa-bitbucket-square','fa-bitcoin','fa-bold','fa-bolt','fa-bomb','fa-book','fa-bookmark','fa-bookmark-o','fa-briefcase','fa-btc','fa-bug','fa-building','fa-building-o','fa-bullhorn','fa-bullseye','fa-bus','fa-cab','fa-calculator','fa-calendar','fa-calendar-o','fa-camera','fa-camera-retro','fa-car','fa-caret-down','fa-caret-left','fa-caret-right','fa-caret-square-o-down','fa-caret-square-o-left','fa-caret-square-o-right','fa-caret-square-o-up','fa-caret-up','fa-cc','fa-cc-amex','fa-cc-discover','fa-cc-mastercard','fa-cc-paypal','fa-cc-stripe','fa-cc-visa','fa-certificate','fa-chain','fa-chain-broken','fa-check','fa-check-circle','fa-check-circle-o','fa-check-square','fa-check-square-o','fa-chevron-circle-down','fa-chevron-circle-left','fa-chevron-circle-right','fa-chevron-circle-up','fa-chevron-down','fa-chevron-left','fa-chevron-right','fa-chevron-up','fa-child','fa-circle','fa-circle-o','fa-circle-o-notch','fa-circle-thin','fa-clipboard','fa-clock-o','fa-close','fa-cloud','fa-cloud-download','fa-cloud-upload','fa-cny','fa-code','fa-code-fork','fa-codepen','fa-coffee','fa-cog','fa-cogs','fa-columns','fa-comment','fa-comment-o','fa-comments','fa-comments-o','fa-compass','fa-compress','fa-copy','fa-copyright','fa-credit-card','fa-crop','fa-crosshairs','fa-css3','fa-cube','fa-cubes','fa-cut','fa-cutlery','fa-dashboard','fa-database','fa-dedent','fa-delicious','fa-desktop','fa-deviantart','fa-digg','fa-dollar','fa-dot-circle-o','fa-download','fa-dribbble','fa-dropbox','fa-drupal','fa-edit','fa-eject','fa-ellipsis-h','fa-ellipsis-v','fa-empire','fa-envelope','fa-envelope-o','fa-envelope-square','fa-eraser','fa-eur','fa-euro','fa-exchange','fa-exclamation','fa-exclamation-circle','fa-exclamation-triangle','fa-expand','fa-external-link','fa-external-link-square','fa-eye','fa-eye-slash','fa-eyedropper','fa-facebook','fa-facebook-square','fa-fast-backward','fa-fast-forward','fa-fax','fa-female','fa-fighter-jet','fa-file','fa-file-archive-o','fa-file-audio-o','fa-file-code-o','fa-file-excel-o','fa-file-image-o','fa-file-movie-o','fa-file-o','fa-file-pdf-o','fa-file-photo-o','fa-file-picture-o','fa-file-powerpoint-o','fa-file-sound-o','fa-file-text','fa-file-text-o','fa-file-video-o','fa-file-word-o','fa-file-zip-o','fa-files-o','fa-film','fa-filter','fa-fire','fa-fire-extinguisher','fa-flag','fa-flag-checkered','fa-flag-o','fa-flash','fa-flask','fa-flickr','fa-floppy-o','fa-folder','fa-folder-o','fa-folder-open','fa-folder-open-o','fa-font','fa-forward','fa-foursquare','fa-frown-o','fa-futbol-o','fa-gamepad','fa-gavel','fa-gbp','fa-ge','fa-gear','fa-gears','fa-gift','fa-git','fa-git-square','fa-github','fa-github-alt','fa-github-square','fa-gittip','fa-glass','fa-globe','fa-google','fa-google-plus','fa-google-plus-square','fa-google-wallet','fa-graduation-cap','fa-group','fa-h-square','fa-hacker-news','fa-hand-o-down','fa-hand-o-left','fa-hand-o-right','fa-hand-o-up','fa-hdd-o','fa-header','fa-headphones','fa-heart','fa-heart-o','fa-history','fa-home','fa-hospital-o','fa-html5','fa-ils','fa-image','fa-inbox','fa-indent','fa-info','fa-info-circle','fa-inr','fa-instagram','fa-institution','fa-ioxhost','fa-italic','fa-joomla','fa-jpy','fa-jsfiddle','fa-key','fa-keyboard-o','fa-krw','fa-language','fa-laptop','fa-lastfm','fa-lastfm-square','fa-leaf','fa-legal','fa-lemon-o','fa-level-down','fa-level-up','fa-life-bouy','fa-life-buoy','fa-life-ring','fa-life-saver','fa-lightbulb-o','fa-line-chart','fa-link','fa-linkedin','fa-linkedin-square','fa-linux','fa-list','fa-list-alt','fa-list-ol','fa-list-ul','fa-location-arrow','fa-lock','fa-long-arrow-down','fa-long-arrow-left','fa-long-arrow-right','fa-long-arrow-up','fa-magic','fa-magnet','fa-mail-forward','fa-mail-reply','fa-mail-reply-all','fa-male','fa-map-marker','fa-maxcdn','fa-meanpath','fa-medkit','fa-meh-o','fa-microphone','fa-microphone-slash','fa-minus','fa-minus-circle','fa-minus-square','fa-minus-square-o','fa-mobile','fa-mobile-phone','fa-money','fa-moon-o','fa-mortar-board','fa-music','fa-navicon','fa-newspaper-o','fa-openid','fa-outdent','fa-pagelines','fa-paint-brush','fa-paper-plane','fa-paper-plane-o','fa-paperclip','fa-paragraph','fa-paste','fa-pause','fa-paw','fa-paypal','fa-pencil','fa-pencil-square','fa-pencil-square-o','fa-phone','fa-phone-square','fa-photo','fa-picture-o','fa-pie-chart','fa-pied-piper','fa-pied-piper-alt','fa-pinterest','fa-pinterest-square','fa-plane','fa-play','fa-play-circle','fa-play-circle-o','fa-plug','fa-plus','fa-plus-circle','fa-plus-square','fa-plus-square-o','fa-power-off','fa-print','fa-puzzle-piece','fa-qq','fa-qrcode','fa-question','fa-question-circle','fa-quote-left','fa-quote-right','fa-ra','fa-random','fa-rebel','fa-recycle','fa-reddit','fa-reddit-square','fa-refresh','fa-remove','fa-renren','fa-reorder','fa-repeat','fa-reply','fa-reply-all','fa-retweet','fa-rmb','fa-road','fa-rocket','fa-rotate-left','fa-rotate-right','fa-rouble','fa-rss','fa-rss-square','fa-rub','fa-ruble','fa-rupee','fa-save','fa-scissors','fa-search','fa-search-minus','fa-search-plus','fa-send','fa-send-o','fa-share','fa-share-alt','fa-share-alt-square','fa-share-square','fa-share-square-o','fa-shekel','fa-sheqel','fa-shield','fa-shopping-cart','fa-sign-in','fa-sign-out','fa-signal','fa-sitemap','fa-skype','fa-slack','fa-sliders','fa-slideshare','fa-smile-o','fa-soccer-ball-o','fa-sort','fa-sort-alpha-asc','fa-sort-alpha-desc','fa-sort-amount-asc','fa-sort-amount-desc','fa-sort-asc','fa-sort-desc','fa-sort-down','fa-sort-numeric-asc','fa-sort-numeric-desc','fa-sort-up','fa-soundcloud','fa-space-shuttle','fa-spinner','fa-spoon','fa-spotify','fa-square','fa-square-o','fa-stack-exchange','fa-stack-overflow','fa-star','fa-star-half','fa-star-half-empty','fa-star-half-full','fa-star-half-o','fa-star-o','fa-steam','fa-steam-square','fa-step-backward','fa-step-forward','fa-stethoscope','fa-stop','fa-strikethrough','fa-stumbleupon','fa-stumbleupon-circle','fa-subscript','fa-suitcase','fa-sun-o','fa-superscript','fa-support','fa-table','fa-tablet','fa-tachometer','fa-tag','fa-tags','fa-tasks','fa-taxi','fa-tencent-weibo','fa-terminal','fa-text-height','fa-text-width','fa-th','fa-th-large','fa-th-list','fa-thumb-tack','fa-thumbs-down','fa-thumbs-o-down','fa-thumbs-o-up','fa-thumbs-up','fa-ticket','fa-times','fa-times-circle','fa-times-circle-o','fa-tint','fa-toggle-down','fa-toggle-left','fa-toggle-off','fa-toggle-on','fa-toggle-right','fa-toggle-up','fa-trash','fa-trash-o','fa-tree','fa-trello','fa-trophy','fa-truck','fa-try','fa-tty','fa-tumblr','fa-tumblr-square','fa-turkish-lira','fa-twitch','fa-twitter','fa-twitter-square','fa-umbrella','fa-underline','fa-undo','fa-university','fa-unlink','fa-unlock','fa-unlock-alt','fa-unsorted','fa-upload','fa-usd','fa-user','fa-user-md','fa-users','fa-video-camera','fa-vimeo-square','fa-vine','fa-vk','fa-volume-down','fa-volume-off','fa-volume-up','fa-warning','fa-wechat','fa-weibo','fa-weixin','fa-wheelchair','fa-wifi','fa-windows','fa-won','fa-wordpress','fa-wrench','fa-xing','fa-xing-square','fa-yahoo','fa-yelp','fa-yen','fa-youtube','fa-youtube-play','fa-youtube-square');
}



?>