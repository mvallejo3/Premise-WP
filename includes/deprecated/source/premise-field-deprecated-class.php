<?php
/**
 * Premise Forms Class
 *
 * This class allows us to easily build form elements using parameters within a PHP Array.
 *
 * @deprecated
 *
 * @package Premise
 * @subpackage Deprecated
 */



/**
 * Old premise field class
 * 
 * @deprecated 1.2 Replaced with PremiseField Class
 */
class PremiseField_Deprecated {


	/**
	 * holds initial agrumnets passed to the class
	 * 
	 * @var array
	 */
	protected $args = array();


	
	

	/**
	 * Defaults for each field
	 * 
	 * @var array
	 */
	protected $defaults = array(
		'type' 	  		  => 'text',		//i.e. textarea, select, multiple, wp_dropdown_pages, wp_dropdown_categories, msdropdown, checkbox, file
		'name' 	  		  => '',
		'id' 	  		  => '',
		'label' 	  	  => '',
		'placeholder' 	  => '',  			//also used as select default option if not empty
		'tooltip' 	  	  => '',  			//displays balloon style tooltip
		'value' 	  	  => '',  			//value from database
		'value_att' 	  => '1',  			//Used for checkboxes and radio fields. if this is equal to 'value' the field will be checked
		'class' 	  	  => '',  			//custom class for easy styling
		'attribute' 	  => '',			//Additional html attributes to add to element i.e. onchange="premiseSelectBackground()"
		'options'		  => array(),		//holds different options depending on the type of field
		'template'		  => 'default',
		'default'         => '', 			//A default value to be assigned.
	);






	/**
	 * holds our field
	 * 
	 * @var array
	 */
	protected $field = array();




	

	/**
	 * will hold our button markup to our object assigned in prepare_field()
	 * 
	 * @var string
	 */
	protected $btn_upload_file;
	protected $btn_remove_file;
	protected $btn_choose_icon;
	protected $btn_remove_icon;






	/**
	 * Holds the html for this field(s)
	 * 
	 * @var string
	 */
	public $html = '';






	/**
	 * Holds the class that should be assigned to the field wrapper
	 * 
	 * @var string
	 */
	public $wrapper = 'text';







	/**
	 * Holds the field label including tooltip
	 * 
	 * @var string
	 */
	public $label = '';




	/**
	 * holds our fontawesome icons. assigned on prepare_field();
	 * 
	 * @var array
	 */
	public $fa_icons = array();






	/**
	 * construct our object
	 * 
	 * @param array $args array holding one or more fields
	 */
	function __construct( $args ) {

		if( ! empty( $args ) && is_array( $args ) && array_key_exists( 'type', $args ) )
			$this->args = $args;

		$this->set_defaults();

		$this->prepare_field();

		$this->field_init();

	}





	/**
	 * begin processing the field
	 */
	protected function field_init() {

		if( 'raw' !== $this->field['template'] )
			$this->build_field();
		else 
			$this->raw_field();
				
	}






	/**
	 * This function builds our field and saves the html markup for it
	 */
	protected function build_field() {

		$html = '<div class="premise-field';
		$html .= ! empty( $this->field['class'] ) ? ' ' . $this->field['class'] . '">' : '">';

		$html .= $this->label;

		$html .= '<div class="' . $this->wrapper . '">';

		$html .= $this->the_field();

		$html .= '</div></div>';

		$this->html .= $html;

	}







	/**
	 * Outputs only the necessary elements for any given field
	 * No wrappers, no label, nothing but the field.
	 */
	protected function raw_field() {
		$html = $this->the_field();

		$this->html .= $html;
	}









	protected function the_field() {
		$html ='';
		switch( $this->field['type'] ) {
			case 'select':
			case 'multiple':
			case 'wp_dropdown_pages':
			case 'wp_dropdown_categories':
			case 'msdropdown':
				$html .= $this->select_field();
				break;

			case 'textarea':
				$html .= $this->textarea();
				break;

			case 'checkbox':
				$html .= $this->checkbox();
				break;

			case 'radio':
				$html .= $this->radio();
				break;

			default:
				$html .= $this->input_field();
				break;
		}
		return $html;
	}







	protected function input_field() {

		$field  = '<input type="'. $this->field['type'] .'"';

		$field .= !empty( $this->field['name'] ) 		? ' name="'. $this->field['name'] .'"' 	: '';
		$field .= !empty( $this->field['id'] ) 			? ' id="'. $this->field['id'] .'"' 		: '';
		$field .= (string) $this->field['value'] !== '' ? ' value="'. $this->field['value'] .'"' : '';
		$field .= !empty( $this->field_class )			? ' class="'. $this->field_class .'"'	: '';
		$field .= !empty( $this->field['placeholder'] )	? ' placeholder="'. esc_attr( $this->field['placeholder'] ) .'"'	: '';
		$field .= !empty( $this->field['attribute'] ) 	? ' ' . $this->field['attribute'] 			: '';
		
		$field .= '>';

		/**
		 * add buttons if file or fa-icon field
		 */
		switch( $this->wrapper ) {
			case 'file':
				$field .= $this->btn_upload_file;
				$field .= $this->btn_remove_file;
				break;

			case 'fa-icon':
				$field .= $this->btn_choose_icon;
				$field .= $this->btn_remove_icon;
				$field .= $this->fa_icons();
				break;
		}

		return $field;

	}








	protected function textarea() {
		
		$field = '<textarea ';

		$field .= !empty( $this->field['name'] ) ? ' name="'.$this->field['name'].'"' : '';
		$field .= !empty( $this->field['id'] ) ? ' id="'.$this->field['id'].'"' : '';
		$field .= !empty( $this->field['placeholder'] ) ? ' placeholder="'.$this->field['placeholder'].'"' : '';
		$field .= !empty( $this->field['attribute'] ) ? ' ' . $this->field['attribute'] : '';

		$field .= '>'.$this->field['value'].'</textarea>';

		return $field;
	}







	protected function checkbox() {
		
		$field  = '<input type="'. $this->field['type'] .'"';
		
		$field .= !empty( $this->field['name'] ) 		? ' name="'. $this->field['name'] .'"' 		: '';
		$field .= !empty( $this->field['id'] ) 			? ' id="'. $this->field['id'] .'"' 			: '';
		$field .= !empty( $this->field['value_att'] ) 	? ' value="'. $this->field['value_att'] .'"' : '';
		$field .= !empty( $this->field['class'] ) 		? ' class="'. $this->field['class'] .'"' 	: '';
		$field .= !empty( $this->field['attribute'] ) 	? ' ' . $this->field['attribute'] 				: '';

		$field .= checked( $this->field['value'], $this->field['value_att'], false );

		$field .= '>';

		$field .= '<label ';
		$field .= ! empty( $this->field['id'] ) 			? 'for="'. $this->field['id'] .'"' 		: '';
		$field .= '>';
		$field .= !empty( $this->field['options']['label'] ) ? $this->field['options']['label'] : '';
		$field .= '</label>';

		return $field;

	}







	protected function radio() {
		if( ! empty( $this->field['options'] ) && is_array( $this->field['options'] ) ) {
			
			$field = '';

			foreach ( $this->field['options'] as $radio ) {
				
				$field  .= '<input type="'.$this->field['type'].'"';
				
				$field .= !empty( $this->field['attribute'] ) 	? ' ' . $this->field['attribute'] 		: '';
				$field .= !empty( $this->field['name'] ) 		? ' name="'.$this->field['name'].'"' : '';
				$field .= !empty( $radio['id'] ) 				? ' id="'.$radio['id'].'"' 			: '';
				$field .= !empty( $radio['value_att'] ) 		? ' value="'.$radio['value_att'].'"' : '';
				
				$field .= checked( $this->field['value'], $radio['value_att'], false );

				$field .= '>';

				$field .= '<label ';
				$field .= ! empty( $radio['id'] ) ? 'for="'.$radio['id'].'">' : '';
				$field .= $radio['label'].'</label>';

			}

			return $field;

		}
	}






	protected function select_field() {
		
		if( 'wp_dropdown_pages' == $this->field['type'] ) {
			$field = $this->do_wp_dropdown_pages();
		}
		elseif( 'wp_dropdown_categories' == $this->field['type'] ) {
			$field = $this->do_wp_dropdown_categories();
		}
		elseif( 'msdropdown' == $this->field['type'] ) {
			$field  = '<script>jQuery(document).ready(function(e) { jQuery("#' . $this->field['id'] . '").msDropdown({}); });</script>';
			$field .= '<select '.$this->field['attribute'].' name="'.$this->field['name'].'" id="'.$this->field['id'].'">';
			$field .= ! empty( $this->field['placeholder'] ) ? '<option value="">'.$this->field['placeholder'].'</option>' : '';
			$field .= $this->do_msdropdown();
			$field .= '</select>';
		}
		else {
			$multiple = ( 'multiple' == $this->field['type'] ? 'multiple' : '' );

			$field  = '<select '.$this->field['attribute'].' name="'.$this->field['name'].'" id="'.$this->field['id'].'" ' . $multiple . '>';
			$field .= ! empty( $this->field['placeholder'] ) ? '<option value="">'.$this->field['placeholder'].'</option>' : '';
			$field .= $this->select_options();
			$field .= '</select>';
		}

		return $field;
	}







	protected function select_options() {
		
		$options = '';

		if( is_array( $this->field['value'] ) ) {
			foreach ( $this->field['options'] as $key => $value ) {
				$options .= '<option  value="'.$value.'"';
				$options .= (is_array( $this->field['value'] ) && in_array( $value, $this->field['value'] ) ) ? 'selected' : '';
				$options .= '>'.$key.'</option>';
			}
		}
		else {
			foreach ($this->field['options'] as $key => $value) {
				$options .= '<option  value="'.$value.'"';
				$options .= selected( $this->field['value'], $value, false );
				$options .= '>'.$key.'</option>';
			}	
		}

		return $options;
	}








	protected function do_wp_dropdown_pages() {
		
		$new_defaults = array(  
			'depth' 				=> 0,
			'child_of' 				=> 0,
			'selected' 				=> $this->field['value'],
			'name'					=> $this->field['name'],
			'id' 					=> $this->field['id'],
			'show_option_none' 			=> $this->field['placeholder'],
			'show_option_no_change' 		=> '',
			'option_none_value' 			=> '',
		);

		$this->field = wp_parse_args( $this->field, $new_defaults );

		/**
		 * Make sure this never gets echoed.
		 */
		$this->field['echo'] = 0;

		return wp_dropdown_pages( $this->field );
	}





	/**
	 * Dropdown menu listing Wordpress categories
	 * http://codex.wordpress.org/Function_Reference/wp_dropdown_categories
	 *
	 * @return string HTML dropdown list
	 */
	protected function do_wp_dropdown_categories() {

		$taxonomy = 'category';

		if ( isset( $this->field['taxonomy'] ) ) {

			$taxonomy = $this->field['taxonomy'];
		}

		$new_defaults = array(
			'depth'            => 0,
			'child_of'         => 0,
			'selected'         => $this->field['value'],
			'name'             => $this->field['name'],
			'id'               => $this->field['id'],
			'show_option_none' => $this->field['placeholder'],
			'taxonomy'		   => $taxonomy,
		);

		$this->field = wp_parse_args( $this->field, $new_defaults );

		/**
		 * Make sure this never gets echoed.
		 */
		$this->field['echo'] = 0;

		return wp_dropdown_categories( $this->field );
	}





	/**
	 * Dropdown menu listing images
	 * http://www.marghoobsuleman.com/jquery-image-dropdown
	 *
	 * @return string HTML dropdown list
	 */
	protected function do_msdropdown() {

		$options = '';

		foreach ($this->field['options'] as $key => $value) {
			$options .= '<option  value="'.$value.'" data-image="' . $value . '"';
			$options .= selected( $this->field['value'], $value, false );
			$options .= '></option>';
		}

		return $options;
	}






	protected function fa_icons() {
		$icons = '<div class="fa-all-icons" style="display:none;"><ul>';
		foreach ($this->fa_icons as $icon)
			$icons .= '<li class="premise-field-fa-icon-li premise-inline-block premise-float-left">
			<a href="javascript:;" class="premise-field-fa-icon-anchor premise-block" '.$this->field['attribute'].' data-icon="'.$icon.'"><i class="fa fa-fw '.$icon.'"></i></a></li>';
		$icons .= '</ul></div>';
		return $icons;
	}







	protected function set_defaults() {
		$field = $this->field = wp_parse_args( $this->args, $this->defaults );

		/**
		 * Make sureour field has its necessary values
		 */
		$field['name']  = ! empty( $field['name'] )  ? $field['name']  : $this->get_name( $field['label'] );
		$field['value'] = $field['value'] !== '' ? $field['value'] : $this->get_db_value( $field['name'] );
		$field['id']    = ! empty( $field['id'] )    ? $field['id']    : $this->get_id_att( $field['name'] );

		$this->field = $field;
	}






	/**
	 * Prepare our field. This function assigns the values to the 
	 * class properties needed to build a particular field
	 */
	protected function prepare_field() {

		$this->label  = ! empty( $this->field['label'] )                                     ? '<label for="'.$this->field['id'].'">'.$this->field['label'].'</label>' : '';
		$this->label .= ( ! empty( $this->field['label'] ) && ! empty( $this->field['tooltip'] ) ) ? '<span class="premise-tooltip"><i>'.$this->field['tooltip'].'</i></span>'  : '';
		
		/**
		 * Set the field['type'] value
		 */
		switch( $this->field['type'] ) {
			case 'select':
			case 'multiple':
			case 'wp_dropdown_pages':
			case 'wp_dropdown_categories':
			case 'msdropdown':
				$this->wrapper = 'select';
				break;


			case 'color':
			case 'minicolors':
				$this->wrapper = 'color';
				$this->field['type'] = 'text';
				$this->field_class = 'premise-minicolors';
				$this->field['template'] = 'default';
				break;

			case 'file':
				$this->wrapper = 'file';
				$this->field['type'] = 'text';
				$this->field_class = 'premise-file-url';
				$this->btn_upload_file = '<a 
					class="premise-btn-upload" 
					href="javascript:void(0);" 
					onclick="premiseUploadFile(this)"
					><i class="fa fa-fw fa-upload"></i></a>';
				$this->btn_remove_file = '<a 
					class="premise-btn-remove" 
					href="javascript:void(0);" 
					onclick="premiseRemoveFile(this)"
					><i class="fa fa-fw fa-times"></i></a>';
				break;

			case 'fa-icon':
				$this->wrapper = 'fa-icon';
				$this->field['type'] = 'text';
				$this->field_class = 'premise-insert-icon';
				$this->btn_choose_icon = '<a 
					href="javascript:void(0);" 
					class="premise-choose-icon" 
					><i class="fa fa-fw fa-th"></i></a>';
				$this->btn_remove_icon = '<a 
					href="javascript:void(0);" 
					class="premise-remove-icon" 
					><i class="fa fa-fw fa-times"></i></a>';
				$this->fa_icons = array('fa-adjust','fa-adn','fa-align-center','fa-align-justify','fa-align-left','fa-align-right','fa-ambulance','fa-anchor','fa-android','fa-angellist','fa-angle-double-down','fa-angle-double-left','fa-angle-double-right','fa-angle-double-up','fa-angle-down','fa-angle-left','fa-angle-right','fa-angle-up','fa-apple','fa-archive','fa-area-chart','fa-arrow-circle-down','fa-arrow-circle-left','fa-arrow-circle-o-down','fa-arrow-circle-o-left','fa-arrow-circle-o-right','fa-arrow-circle-o-up','fa-arrow-circle-right','fa-arrow-circle-up','fa-arrow-down','fa-arrow-left','fa-arrow-right','fa-arrow-up','fa-arrows','fa-arrows-alt','fa-arrows-h','fa-arrows-v','fa-asterisk','fa-at','fa-automobile','fa-backward','fa-ban','fa-bank','fa-bar-chart','fa-bar-chart-o','fa-barcode','fa-bars','fa-beer','fa-behance','fa-behance-square','fa-bell','fa-bell-o','fa-bell-slash','fa-bell-slash-o','fa-bicycle','fa-binoculars','fa-birthday-cake','fa-bitbucket','fa-bitbucket-square','fa-bitcoin','fa-bold','fa-bolt','fa-bomb','fa-book','fa-bookmark','fa-bookmark-o','fa-briefcase','fa-btc','fa-bug','fa-building','fa-building-o','fa-bullhorn','fa-bullseye','fa-bus','fa-cab','fa-calculator','fa-calendar','fa-calendar-o','fa-camera','fa-camera-retro','fa-car','fa-caret-down','fa-caret-left','fa-caret-right','fa-caret-square-o-down','fa-caret-square-o-left','fa-caret-square-o-right','fa-caret-square-o-up','fa-caret-up','fa-cc','fa-cc-amex','fa-cc-discover','fa-cc-mastercard','fa-cc-paypal','fa-cc-stripe','fa-cc-visa','fa-certificate','fa-chain','fa-chain-broken','fa-check','fa-check-circle','fa-check-circle-o','fa-check-square','fa-check-square-o','fa-chevron-circle-down','fa-chevron-circle-left','fa-chevron-circle-right','fa-chevron-circle-up','fa-chevron-down','fa-chevron-left','fa-chevron-right','fa-chevron-up','fa-child','fa-circle','fa-circle-o','fa-circle-o-notch','fa-circle-thin','fa-clipboard','fa-clock-o','fa-close','fa-cloud','fa-cloud-download','fa-cloud-upload','fa-cny','fa-code','fa-code-fork','fa-codepen','fa-coffee','fa-cog','fa-cogs','fa-columns','fa-comment','fa-comment-o','fa-comments','fa-comments-o','fa-compass','fa-compress','fa-copy','fa-copyright','fa-credit-card','fa-crop','fa-crosshairs','fa-css3','fa-cube','fa-cubes','fa-cut','fa-cutlery','fa-dashboard','fa-database','fa-dedent','fa-delicious','fa-desktop','fa-deviantart','fa-digg','fa-dollar','fa-dot-circle-o','fa-download','fa-dribbble','fa-dropbox','fa-drupal','fa-edit','fa-eject','fa-ellipsis-h','fa-ellipsis-v','fa-empire','fa-envelope','fa-envelope-o','fa-envelope-square','fa-eraser','fa-eur','fa-euro','fa-exchange','fa-exclamation','fa-exclamation-circle','fa-exclamation-triangle','fa-expand','fa-external-link','fa-external-link-square','fa-eye','fa-eye-slash','fa-eyedropper','fa-facebook','fa-facebook-square','fa-fast-backward','fa-fast-forward','fa-fax','fa-female','fa-fighter-jet','fa-file','fa-file-archive-o','fa-file-audio-o','fa-file-code-o','fa-file-excel-o','fa-file-image-o','fa-file-movie-o','fa-file-o','fa-file-pdf-o','fa-file-photo-o','fa-file-picture-o','fa-file-powerpoint-o','fa-file-sound-o','fa-file-text','fa-file-text-o','fa-file-video-o','fa-file-word-o','fa-file-zip-o','fa-files-o','fa-film','fa-filter','fa-fire','fa-fire-extinguisher','fa-flag','fa-flag-checkered','fa-flag-o','fa-flash','fa-flask','fa-flickr','fa-floppy-o','fa-folder','fa-folder-o','fa-folder-open','fa-folder-open-o','fa-font','fa-forward','fa-foursquare','fa-frown-o','fa-futbol-o','fa-gamepad','fa-gavel','fa-gbp','fa-ge','fa-gear','fa-gears','fa-gift','fa-git','fa-git-square','fa-github','fa-github-alt','fa-github-square','fa-gittip','fa-glass','fa-globe','fa-google','fa-google-plus','fa-google-plus-square','fa-google-wallet','fa-graduation-cap','fa-group','fa-h-square','fa-hacker-news','fa-hand-o-down','fa-hand-o-left','fa-hand-o-right','fa-hand-o-up','fa-hdd-o','fa-header','fa-headphones','fa-heart','fa-heart-o','fa-history','fa-home','fa-hospital-o','fa-html5','fa-ils','fa-image','fa-inbox','fa-indent','fa-info','fa-info-circle','fa-inr','fa-instagram','fa-institution','fa-ioxhost','fa-italic','fa-joomla','fa-jpy','fa-jsfiddle','fa-key','fa-keyboard-o','fa-krw','fa-language','fa-laptop','fa-lastfm','fa-lastfm-square','fa-leaf','fa-legal','fa-lemon-o','fa-level-down','fa-level-up','fa-life-bouy','fa-life-buoy','fa-life-ring','fa-life-saver','fa-lightbulb-o','fa-line-chart','fa-link','fa-linkedin','fa-linkedin-square','fa-linux','fa-list','fa-list-alt','fa-list-ol','fa-list-ul','fa-location-arrow','fa-lock','fa-long-arrow-down','fa-long-arrow-left','fa-long-arrow-right','fa-long-arrow-up','fa-magic','fa-magnet','fa-mail-forward','fa-mail-reply','fa-mail-reply-all','fa-male','fa-map-marker','fa-maxcdn','fa-meanpath','fa-medkit','fa-meh-o','fa-microphone','fa-microphone-slash','fa-minus','fa-minus-circle','fa-minus-square','fa-minus-square-o','fa-mobile','fa-mobile-phone','fa-money','fa-moon-o','fa-mortar-board','fa-music','fa-navicon','fa-newspaper-o','fa-openid','fa-outdent','fa-pagelines','fa-paint-brush','fa-paper-plane','fa-paper-plane-o','fa-paperclip','fa-paragraph','fa-paste','fa-pause','fa-paw','fa-paypal','fa-pencil','fa-pencil-square','fa-pencil-square-o','fa-phone','fa-phone-square','fa-photo','fa-picture-o','fa-pie-chart','fa-pied-piper','fa-pied-piper-alt','fa-pinterest','fa-pinterest-square','fa-plane','fa-play','fa-play-circle','fa-play-circle-o','fa-plug','fa-plus','fa-plus-circle','fa-plus-square','fa-plus-square-o','fa-power-off','fa-print','fa-puzzle-piece','fa-qq','fa-qrcode','fa-question','fa-question-circle','fa-quote-left','fa-quote-right','fa-ra','fa-random','fa-rebel','fa-recycle','fa-reddit','fa-reddit-square','fa-refresh','fa-remove','fa-renren','fa-reorder','fa-repeat','fa-reply','fa-reply-all','fa-retweet','fa-rmb','fa-road','fa-rocket','fa-rotate-left','fa-rotate-right','fa-rouble','fa-rss','fa-rss-square','fa-rub','fa-ruble','fa-rupee','fa-save','fa-scissors','fa-search','fa-search-minus','fa-search-plus','fa-send','fa-send-o','fa-share','fa-share-alt','fa-share-alt-square','fa-share-square','fa-share-square-o','fa-shekel','fa-sheqel','fa-shield','fa-shopping-cart','fa-sign-in','fa-sign-out','fa-signal','fa-sitemap','fa-skype','fa-slack','fa-sliders','fa-slideshare','fa-smile-o','fa-soccer-ball-o','fa-sort','fa-sort-alpha-asc','fa-sort-alpha-desc','fa-sort-amount-asc','fa-sort-amount-desc','fa-sort-asc','fa-sort-desc','fa-sort-down','fa-sort-numeric-asc','fa-sort-numeric-desc','fa-sort-up','fa-soundcloud','fa-space-shuttle','fa-spinner','fa-spoon','fa-spotify','fa-square','fa-square-o','fa-stack-exchange','fa-stack-overflow','fa-star','fa-star-half','fa-star-half-empty','fa-star-half-full','fa-star-half-o','fa-star-o','fa-steam','fa-steam-square','fa-step-backward','fa-step-forward','fa-stethoscope','fa-stop','fa-strikethrough','fa-stumbleupon','fa-stumbleupon-circle','fa-subscript','fa-suitcase','fa-sun-o','fa-superscript','fa-support','fa-table','fa-tablet','fa-tachometer','fa-tag','fa-tags','fa-tasks','fa-taxi','fa-tencent-weibo','fa-terminal','fa-text-height','fa-text-width','fa-th','fa-th-large','fa-th-list','fa-thumb-tack','fa-thumbs-down','fa-thumbs-o-down','fa-thumbs-o-up','fa-thumbs-up','fa-ticket','fa-times','fa-times-circle','fa-times-circle-o','fa-tint','fa-toggle-down','fa-toggle-left','fa-toggle-off','fa-toggle-on','fa-toggle-right','fa-toggle-up','fa-trash','fa-trash-o','fa-tree','fa-trello','fa-trophy','fa-truck','fa-try','fa-tty','fa-tumblr','fa-tumblr-square','fa-turkish-lira','fa-twitch','fa-twitter','fa-twitter-square','fa-umbrella','fa-underline','fa-undo','fa-university','fa-unlink','fa-unlock','fa-unlock-alt','fa-unsorted','fa-upload','fa-usd','fa-user','fa-user-md','fa-users','fa-video-camera','fa-vimeo-square','fa-vine','fa-vk','fa-volume-down','fa-volume-off','fa-volume-up','fa-warning','fa-wechat','fa-weibo','fa-weixin','fa-wheelchair','fa-wifi','fa-windows','fa-won','fa-wordpress','fa-wrench','fa-xing','fa-xing-square','fa-yahoo','fa-yelp','fa-yen','fa-youtube','fa-youtube-play','fa-youtube-square');
				break;

			case 'checkbox':
			case 'radio':
				
				$this->wrapper = ( 'radio' == $this->field['type'] ) 											? 'radio'																	: 'checkbox';
				$this->label  = ! empty( $this->field['label'] ) 												? '<p class="premise-label">'.$this->field['label'].'</p>' 							: '';
				$this->label .= ( ! empty( $this->field['label'] ) && ! empty( $this->field['tooltip'] ) ) 		? '<span class="premise-tooltip"><i>'.$this->field['tooltip'].'</i></span>' 		: '';
				break;

			default :
				$this->label  = ! empty( $this->field['label'] ) 												? '<label for="'.$this->field['id'].'">'.$this->field['label'].'</label>' 	: '';
				$this->label .= ( ! empty( $this->field['label'] ) && ! empty( $this->field['tooltip'] ) ) 		? '<span class="premise-tooltip"><i>'.$this->field['tooltip'].'</i></span>' 		: '';
				break;
		}

	}








	protected function get_db_value( $name ) {

		/**
		 * If values are stored in an array
		 */
		if ( preg_match( '/\[|\]/', $name ) ) {

			/**
			 * Turn html att name into an array of keys
			 *
			 * This will help us get the options from the database
			 *
			 * @var array
			 */
			$keys = str_replace( ']', '', $name );
			$keys = explode( '[', $keys );

			/**
			 * Set the DB option name and unset it from the keys array
			 *
			 * @var string
			 */
			$option = $keys[0];
			unset( $keys[0] );

			$val = premise_get_option( $option, $keys );
		}
		else {
			$val = get_option( $name );
		}

		if ( $val ) 
			return esc_attr( $val );
		else 
			return ! empty( $this->field['default'] ) ? esc_attr( $this->field['default'] ) : false;
	}







	protected function get_id_att( $name ) {
		
		/**
		 * If values are stored in an array
		 */
		if ( preg_match( '/\[|\]/', $name ) ) {

			/**
			 * Turn html att name into an array of keys
			 *
			 * This will help us get the options from the database
			 *
			 * @var array
			 */
			$id_att = preg_replace( array('/\[/', '/\]/'), array('-', ''), $name );

			return $id_att;
		}
		else {
			return $name;
		}
	}







	protected function get_name( $label ) {
		$label = str_replace(' ', '-', strtolower($label));
		$label = preg_replace('/[^-_a-z0-9]/', '', $label);
		return esc_attr( $label );
	}






	public function get_field() {
		return $this->html;
	}


}
?>