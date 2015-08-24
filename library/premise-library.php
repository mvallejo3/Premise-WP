<?php 
/**
 * Premise Ajax Library
 *
 * All Ajaxa related functions go here
 *
 * @package Premise WP
 * @subpackage Library
 *
 * @since  1.2 
 */



/**
 * Output ajax dialog markup
 * 
 * @return string html for premise ajax dialog
 */
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



?><?php 
/**
 * Premise Field Library
 *
 * This file holds function helpers for our
 * field class and general form functionality.
 * Usually AJAX related functions may be located
 * under @see premise-ajax-library.php
 *
 * @package Premise WP
 * @subpackage Library
 *
 * @author Mario Vallejo <mario@vallgroup.com>
 * @since 1.2
 */




/**
 * Premise Field
 *
 * display or return a form field.
 *
 * @since 1.2 Parametters order changed, new parametter added:
 *        Old params: (array) arguments, (boolean) echo
 *        New params: (string) type, (array) arguments, (boolean) echo
 * 
 * @see class PremiseField in model-premise-field.php
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
		$field = new PremiseField( '', $args );
		$html .= $field->get_field();
	}
	else{
		foreach ( $args as $arg ) {
			$field = new PremiseField( '', $arg );
			$html .= $field->get_field();
		}
	}
	
	if( !$echo )
		return $html;
	else
		echo $html;
}




/**
 * Premise field section
 * @param  array   $args array of arrays. each array contains 
 * @param  boolean $echo [description]
 * @return [type]        [description]
 */
function premise_field_section( $args = array(), $echo = true ) {
	if ( array_key_exists( 'fields', $args ) ) {
		return premise_field_section_deprecated( $args, $echo );
	}

	$html = ''; // Start with a clean section

	foreach( $args as $k => $v ) {
		$html .= is_array( $v ) ? premise_field( $k, $v, $echo ) : premise_field( $v, '', $echo );
	}

	if( ! $echo )
		return $html;
	else
		echo (string) $html;
}





function premise_field_section_deprecated( $args = array(), $echo = true ) {
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
	$html .= premise_field_deprecated( $field_section['fields'], false ); // This gets the actual field
	$html .= !empty( $field_section['container_inner_class'] )                                       ? '</div>'                                                        : '';
	$html .= ( true === $field_section['container'] )                                                ? '</div>'                                                        : '';

	if( !$echo )
		return $html;
	else
		echo (string) $html;
}




/**
 * Get font awesome icons
 * 
 * @return array array of icons
 */
function premise_get_fa_icons() { 
	return array('fa-adjust','fa-adn','fa-align-center','fa-align-justify','fa-align-left','fa-align-right','fa-ambulance','fa-anchor','fa-android','fa-angellist','fa-angle-double-down','fa-angle-double-left','fa-angle-double-right','fa-angle-double-up','fa-angle-down','fa-angle-left','fa-angle-right','fa-angle-up','fa-apple','fa-archive','fa-area-chart','fa-arrow-circle-down','fa-arrow-circle-left','fa-arrow-circle-o-down','fa-arrow-circle-o-left','fa-arrow-circle-o-right','fa-arrow-circle-o-up','fa-arrow-circle-right','fa-arrow-circle-up','fa-arrow-down','fa-arrow-left','fa-arrow-right','fa-arrow-up','fa-arrows','fa-arrows-alt','fa-arrows-h','fa-arrows-v','fa-asterisk','fa-at','fa-automobile','fa-backward','fa-ban','fa-bank','fa-bar-chart','fa-bar-chart-o','fa-barcode','fa-bars','fa-beer','fa-behance','fa-behance-square','fa-bell','fa-bell-o','fa-bell-slash','fa-bell-slash-o','fa-bicycle','fa-binoculars','fa-birthday-cake','fa-bitbucket','fa-bitbucket-square','fa-bitcoin','fa-bold','fa-bolt','fa-bomb','fa-book','fa-bookmark','fa-bookmark-o','fa-briefcase','fa-btc','fa-bug','fa-building','fa-building-o','fa-bullhorn','fa-bullseye','fa-bus','fa-cab','fa-calculator','fa-calendar','fa-calendar-o','fa-camera','fa-camera-retro','fa-car','fa-caret-down','fa-caret-left','fa-caret-right','fa-caret-square-o-down','fa-caret-square-o-left','fa-caret-square-o-right','fa-caret-square-o-up','fa-caret-up','fa-cc','fa-cc-amex','fa-cc-discover','fa-cc-mastercard','fa-cc-paypal','fa-cc-stripe','fa-cc-visa','fa-certificate','fa-chain','fa-chain-broken','fa-check','fa-check-circle','fa-check-circle-o','fa-check-square','fa-check-square-o','fa-chevron-circle-down','fa-chevron-circle-left','fa-chevron-circle-right','fa-chevron-circle-up','fa-chevron-down','fa-chevron-left','fa-chevron-right','fa-chevron-up','fa-child','fa-circle','fa-circle-o','fa-circle-o-notch','fa-circle-thin','fa-clipboard','fa-clock-o','fa-close','fa-cloud','fa-cloud-download','fa-cloud-upload','fa-cny','fa-code','fa-code-fork','fa-codepen','fa-coffee','fa-cog','fa-cogs','fa-columns','fa-comment','fa-comment-o','fa-comments','fa-comments-o','fa-compass','fa-compress','fa-copy','fa-copyright','fa-credit-card','fa-crop','fa-crosshairs','fa-css3','fa-cube','fa-cubes','fa-cut','fa-cutlery','fa-dashboard','fa-database','fa-dedent','fa-delicious','fa-desktop','fa-deviantart','fa-digg','fa-dollar','fa-dot-circle-o','fa-download','fa-dribbble','fa-dropbox','fa-drupal','fa-edit','fa-eject','fa-ellipsis-h','fa-ellipsis-v','fa-empire','fa-envelope','fa-envelope-o','fa-envelope-square','fa-eraser','fa-eur','fa-euro','fa-exchange','fa-exclamation','fa-exclamation-circle','fa-exclamation-triangle','fa-expand','fa-external-link','fa-external-link-square','fa-eye','fa-eye-slash','fa-eyedropper','fa-facebook','fa-facebook-square','fa-fast-backward','fa-fast-forward','fa-fax','fa-female','fa-fighter-jet','fa-file','fa-file-archive-o','fa-file-audio-o','fa-file-code-o','fa-file-excel-o','fa-file-image-o','fa-file-movie-o','fa-file-o','fa-file-pdf-o','fa-file-photo-o','fa-file-picture-o','fa-file-powerpoint-o','fa-file-sound-o','fa-file-text','fa-file-text-o','fa-file-video-o','fa-file-word-o','fa-file-zip-o','fa-files-o','fa-film','fa-filter','fa-fire','fa-fire-extinguisher','fa-flag','fa-flag-checkered','fa-flag-o','fa-flash','fa-flask','fa-flickr','fa-floppy-o','fa-folder','fa-folder-o','fa-folder-open','fa-folder-open-o','fa-font','fa-forward','fa-foursquare','fa-frown-o','fa-futbol-o','fa-gamepad','fa-gavel','fa-gbp','fa-ge','fa-gear','fa-gears','fa-gift','fa-git','fa-git-square','fa-github','fa-github-alt','fa-github-square','fa-gittip','fa-glass','fa-globe','fa-google','fa-google-plus','fa-google-plus-square','fa-google-wallet','fa-graduation-cap','fa-group','fa-h-square','fa-hacker-news','fa-hand-o-down','fa-hand-o-left','fa-hand-o-right','fa-hand-o-up','fa-hdd-o','fa-header','fa-headphones','fa-heart','fa-heart-o','fa-history','fa-home','fa-hospital-o','fa-html5','fa-ils','fa-image','fa-inbox','fa-indent','fa-info','fa-info-circle','fa-inr','fa-instagram','fa-institution','fa-ioxhost','fa-italic','fa-joomla','fa-jpy','fa-jsfiddle','fa-key','fa-keyboard-o','fa-krw','fa-language','fa-laptop','fa-lastfm','fa-lastfm-square','fa-leaf','fa-legal','fa-lemon-o','fa-level-down','fa-level-up','fa-life-bouy','fa-life-buoy','fa-life-ring','fa-life-saver','fa-lightbulb-o','fa-line-chart','fa-link','fa-linkedin','fa-linkedin-square','fa-linux','fa-list','fa-list-alt','fa-list-ol','fa-list-ul','fa-location-arrow','fa-lock','fa-long-arrow-down','fa-long-arrow-left','fa-long-arrow-right','fa-long-arrow-up','fa-magic','fa-magnet','fa-mail-forward','fa-mail-reply','fa-mail-reply-all','fa-male','fa-map-marker','fa-maxcdn','fa-meanpath','fa-medkit','fa-meh-o','fa-microphone','fa-microphone-slash','fa-minus','fa-minus-circle','fa-minus-square','fa-minus-square-o','fa-mobile','fa-mobile-phone','fa-money','fa-moon-o','fa-mortar-board','fa-music','fa-navicon','fa-newspaper-o','fa-openid','fa-outdent','fa-pagelines','fa-paint-brush','fa-paper-plane','fa-paper-plane-o','fa-paperclip','fa-paragraph','fa-paste','fa-pause','fa-paw','fa-paypal','fa-pencil','fa-pencil-square','fa-pencil-square-o','fa-phone','fa-phone-square','fa-photo','fa-picture-o','fa-pie-chart','fa-pied-piper','fa-pied-piper-alt','fa-pinterest','fa-pinterest-square','fa-plane','fa-play','fa-play-circle','fa-play-circle-o','fa-plug','fa-plus','fa-plus-circle','fa-plus-square','fa-plus-square-o','fa-power-off','fa-print','fa-puzzle-piece','fa-qq','fa-qrcode','fa-question','fa-question-circle','fa-quote-left','fa-quote-right','fa-ra','fa-random','fa-rebel','fa-recycle','fa-reddit','fa-reddit-square','fa-refresh','fa-remove','fa-renren','fa-reorder','fa-repeat','fa-reply','fa-reply-all','fa-retweet','fa-rmb','fa-road','fa-rocket','fa-rotate-left','fa-rotate-right','fa-rouble','fa-rss','fa-rss-square','fa-rub','fa-ruble','fa-rupee','fa-save','fa-scissors','fa-search','fa-search-minus','fa-search-plus','fa-send','fa-send-o','fa-share','fa-share-alt','fa-share-alt-square','fa-share-square','fa-share-square-o','fa-shekel','fa-sheqel','fa-shield','fa-shopping-cart','fa-sign-in','fa-sign-out','fa-signal','fa-sitemap','fa-skype','fa-slack','fa-sliders','fa-slideshare','fa-smile-o','fa-soccer-ball-o','fa-sort','fa-sort-alpha-asc','fa-sort-alpha-desc','fa-sort-amount-asc','fa-sort-amount-desc','fa-sort-asc','fa-sort-desc','fa-sort-down','fa-sort-numeric-asc','fa-sort-numeric-desc','fa-sort-up','fa-soundcloud','fa-space-shuttle','fa-spinner','fa-spoon','fa-spotify','fa-square','fa-square-o','fa-stack-exchange','fa-stack-overflow','fa-star','fa-star-half','fa-star-half-empty','fa-star-half-full','fa-star-half-o','fa-star-o','fa-steam','fa-steam-square','fa-step-backward','fa-step-forward','fa-stethoscope','fa-stop','fa-strikethrough','fa-stumbleupon','fa-stumbleupon-circle','fa-subscript','fa-suitcase','fa-sun-o','fa-superscript','fa-support','fa-table','fa-tablet','fa-tachometer','fa-tag','fa-tags','fa-tasks','fa-taxi','fa-tencent-weibo','fa-terminal','fa-text-height','fa-text-width','fa-th','fa-th-large','fa-th-list','fa-thumb-tack','fa-thumbs-down','fa-thumbs-o-down','fa-thumbs-o-up','fa-thumbs-up','fa-ticket','fa-times','fa-times-circle','fa-times-circle-o','fa-tint','fa-toggle-down','fa-toggle-left','fa-toggle-off','fa-toggle-on','fa-toggle-right','fa-toggle-up','fa-trash','fa-trash-o','fa-tree','fa-trello','fa-trophy','fa-truck','fa-try','fa-tty','fa-tumblr','fa-tumblr-square','fa-turkish-lira','fa-twitch','fa-twitter','fa-twitter-square','fa-umbrella','fa-underline','fa-undo','fa-university','fa-unlink','fa-unlock','fa-unlock-alt','fa-unsorted','fa-upload','fa-usd','fa-user','fa-user-md','fa-users','fa-video-camera','fa-vimeo-square','fa-vine','fa-vk','fa-volume-down','fa-volume-off','fa-volume-up','fa-warning','fa-wechat','fa-weibo','fa-weixin','fa-wheelchair','fa-wifi','fa-windows','fa-won','fa-wordpress','fa-wrench','fa-xing','fa-xing-square','fa-yahoo','fa-yelp','fa-yen','fa-youtube','fa-youtube-play','fa-youtube-square');
}

?><?php
/**
 * Premise Library
 *
 * @package Premise WP
 * @subpackage Library
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
 * premise_get_value()
 *
 * This function allows you to retrieve options saved in the Wordpress database whether they 
 * were saved within a post or a user profile or in the wp_options table. You can also retreive
 * options that were saved in an array e.g. 'name="option[array1][array2]' by simply passing the 
 * name like it is on the field i.e. premise_get_option( 'option[array1][array2]' ). This will 
 * return the value of array2.
 *
 * By passing a context param, you can tell the function where to look for the option. So if 
 * we pass the context 'post', the function will look for the value using Wordpress' built in
 * get_post_meta(), instead of get_option(). At the same time if you pass the context 'user'
 * the function will use get_user_meta() to retreive the data you are looking for.
 * 
 * NOTE: The context param can be an array containing the post or user id and the single param.
 * Here is an example:
 * 
 * premise_get_option( 'option[array1][array2]', array(
 * 	   'context' => 'post',
 *     'id'      => '78',
 *     'single'  => false
 * ) );
 * 
 * @see https://developer.wordpress.org/reference/functions/get_post_meta/ single param is the last param this function takes
 * @see https://developer.wordpress.org/reference/functions/get_user_meta/ single param is the last param this function takes
 *
 * @since 1.2 made it possible to search within posts and users as well as regilar options
 *        This function was created as a helper for the PremiseField class.
 *
 * @param  string $name     name attribute for the field
 * @param  mixed  $context  string with context. or array with context, id, and single params.
 * @return mixed            Returns the value of the option, or false if nothing was found
 */
function premise_get_value( $name = '', $context = '' ) {

	$value = ''; // Start with a clean value

	/**
	 * Get the name
	 * 
	 * @var array
	 */
	$_name = premise_name_att_to_array( $name );
	
	/**
	 * prepare the context
	 */
	$context_type = is_array( $context ) ? $context['context'] : $context;
	$id           = is_array( $context ) ? $context['id']      : '';
	$single       = is_array( $context ) ? $context['single']  : '';

	/**
	 * Get the value based on the context_type
	 * if $context_type is not empty it can only be post or user
	 */
	if ( empty( $context_type ) )
		$value = get_option( $_name[0] );
	else
		$value = 'post' == $context_type ? premise_get_post_meta( $id, $_name[0] ) : premise_get_user_meta( $id, $_name[0] );

	/**
	 * If the second array from the name is not empty it means
	 * we have a multilevel value to retrieve. Check that value 
	 * is an array as well just to make sure no errors happen.
	 */
	if ( ! empty( $_name[1] ) && is_array( $value ) ) {
		foreach ( $_name[1] as $k => $v ) {
			if ( array_key_exists( $v, (array) $value ) ) {
				$value = $value[$v];
			} else {
				// like get_option(), return FALSE if no value
				$value = false;
			}
		}
	}

	/**
	 * if value is still in array but with only one value
	 * get tha value and return it.
	 */
	if ( is_array( $value ) && ( 1 == count( $value ) ) )
		$value = $value[0];

	// like get_option(), return FALSE if no value
	return ! empty( $value ) ? $value : false;
}




/**
 * premise_get_post_meta()
 *
 * get_post_meta helper.
 *
 * @since 1.2 added for context param on PremiseField class.
 *
 * @see https://developer.wordpress.org/reference/functions/get_post_meta/
 * 
 * @param  mixed   $post_id the post ID (int). if empty the current post ID will be used
 * @param  mixed   $name    the name of the option to retrieve
 * @param  boolean $single  whether to return a single value
 * @return mixed            Returns value from database
 */
function premise_get_post_meta( $post_id = '', $name = '', $single = true ) {
	
	if ( empty( $post_id ) ) {
		global $post;
		$post_id = $post->ID;
	}

	/**
	 * Get the results from the database
	 * 
	 * @var mixed
	 */
	return get_post_meta( $post_id, $name, $single );
}




/**
 * premise_get_user_meta()
 *
 * get_user_meta helper.
 *
 * @since 1.2 added for context param on PremiseField class.
 *
 * @see https://developer.wordpress.org/reference/functions/get_user_meta/
 * 
 * @param  mixed   $user_id the user ID (int). if empty the current user ID will be used
 * @param  mixed   $name    the name of the option to retrieve
 * @param  boolean $single  whether to return a single value
 * @return mixed            Returns value from database
 */
function premise_get_user_meta( $user_id = '', $name = '', $single = true ) {
	
	if ( empty( $user_id ) ) {
		global $user;
		$user_id = $user->ID;
	}

	/**
	 * Get the results from the database
	 * 
	 * @var mixed
	 */
	return get_user_meta( $user_id, $name, $single );
}




/**
 * premise get option
 *
 * get options from wordpress with a little more control. This function lets you get values that are
 * saved within arrays by simply passing the name field string. i.e. premise_get_option( 'option[array1][array2]' )
 * which would output the value for array2 of 'option'. The $key param used to be used for this purpose but
 * becuase we now use premise_get_value() within this function, we recommend not using $key anymore. 
 *
 * @since 1.2 uses premise_get_value. ability to submit an option string like option[array1][array2]
 *        to retrieve the value of array2. keys were used for this before but with little control.
 *        Do not use $key anymore. 
 * 
 * @param  mixed $option string of option or name field. array of options or name fields
 * @param  mixed $key    deprecated should not be used anymore.
 * @return mixed         array of value, or single value, or false if nothing found
 */
function premise_get_option( $option = '', $key = '' ) {
	if ( empty( $option ) ) 
		return false;
	
	if ( is_string( $option ) ) {
		
		if ( ! empty( $key ) ) {
			$_key = is_array( $key ) ? '[\'' . implode( '\'][\'', $key ) . '\']' : '[\'' . (string) $key . '\']';
			$option .= $_key;
		}

		return premise_get_value( $option );
	}
	
	if( is_array( $option ) ) {
		$options = array();

		foreach( $option as $opt ) {
			array_push( $options, premise_get_value( $opt ) );
		}

		return $options;
	}

	return false;
}




/**
 * Premise name to array
 *
 * Converts the name attribute of a field into an array. 
 *
 * @since 1.2 
 * 
 * @param  string $name the name attribute of a field to convert to an array
 * @return string       array with name attribute
 */
function premise_name_att_to_array( $name ) {

	$name_a = array(); // Name array

	/**
	 * If values are stored in an array
	 */
	if ( preg_match( '/\[|\]/', $name ) ) {

		/**
		 * Turn html attribute name into an array of _keys
		 *
		 * From:
		 * name="sangreea[key1][key2][key3]"
		 *
		 * To:
		 * array (size=2)
		 * 0 => 
		 *   array (size=4)
		 *     0 => string 'sangreea[' (length=9)
		 *     1 => string 'key1]' (length=5)
		 *     2 => string 'key2]' (length=5)
		 *     3 => string 'key3]' (length=5)
		 * 1 => 
		 *   array (size=4)
		 *     0 => string 'sangreea' (length=8)
		 *     1 => string 'key1' (length=4)
		 *     2 => string 'key2' (length=4)
		 *     3 => string 'key3' (length=4)
		 *
		 * @var array $_keys[1] has strings of _keys for each level of the array
		 *
		 * @since 1.2 RegExp that saves each level of array into array
		 *
		 * @var array
		 */
		preg_match_all( "/([0-9a-zA-Z].*?)[\[\]]/", $name, $_keys );

		
		/**
		 * Set the DB option name and unset it from the _keys array
		 *
		 * @var string
		 */
		$name_a[] = $_keys[1][0];
		unset( $_keys[1][0] );
		
		if ( ! empty( $_keys[1] ) )
			$name_a[] = $_keys[1];
	}
	else {
		$name_a[] = esc_attr( $name );
	}

	return $name_a;
}




/**
 * Generate a random string
 *
 * Returns a random string with a set number of characters. 
 * The string includes only letters and numbers at this point.
 *
 * @since 1.2 might use it for shortcodes, thought it might be useful for someone one day.
 * 
 * @param  int    $length number of characters to return in the string. defaults to 8
 * @return string         string with random characters
 */
function premise_rand_str( $length = '' ) {

	// set default le\ngth if length is empty or not a number
	$length = ! empty( $length ) && is_numeric( $length ) ? intval( $length ) : 8;
	
	//start with empty string
	$token = "";

	//define chars to use in random string
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	//count chars and deduct one because we are counting from zero later
	$num_chars = strlen($chars) - 1;
	
	//generate random string
	for($i=0; $i<$length; $i++) {
		$pick = mt_rand(0,$num_chars);
		$char = $chars[$pick];
		$token .= $char;
	}
	
	//return random string
	return esc_attr( $token );
}




?><?php 
/**
 * 
 */

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

?>