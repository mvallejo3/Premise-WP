<?php
/**
 * Premise Forms Class
 *
 * This class allows us to easily build form elements using parameters within a PHP Array.
 *
 * @package Premise
 * @subpackage Forms Class
 */


class PremiseField {


	/**
	 * Holds type attribute for field
	 * 
	 * @var string
	 */
	protected $type = 'text';


	

	/**
	 * Defaults for each field
	 *
	 * Special Parameters: This parameters are special because they alter the
	 * HTML markup of a field or add functionality such as filters.
	 *
	 * Normal Parameters: This parameters (and every other paramaters passed) act as
	 * additional attributes added the the field. We add some defaults
	 * and some additional params to make your life easier like 'default'
	 * or 'options'
	 * 
	 * @var array
	 *
	 * @since 1.2 Moved type oustide of arguments and other changes
	 */
	protected $defaults = array(
		/**
		 * Special Parameters
		 */
		'label'      => '',      // Wraps label element around field. uses id for for attribute if id not empty
		'tooltip'    => '',      // Adds a tooltip and tooltip functionality to field
		'add_filter' => '',      // Add a filter to this field. Read documentation for list of filters
		'template'   => '',      // May not be needed after filters. add classes to outer html to control styling.
		/**
		 * Normal Parameters
		 */
		'name'       => '',      // name attribute. if empty fills from id
		'id'         => '',      // id attribute. is empty fills from name
		'value'      => '',      // value attribute. by default tries to get_option(name)
		'value_att'  => '',     // value attribute. Used for checkboxes and radio
		'default'    => '',      // if value is empty and get_option() return false
		'options'    => array(), // options for select fields in this format ( Text => Value )
		'attribute'  => '',      // html attributes to add to element i.e. onchange="doSomethingCool()"
	);




	/**
	 * holds initial agrumnets passed to the class
	 * 
	 * @var array
	 */
	protected $args = array();




	/**
	 * Parsed arguments for field
	 * 
	 * @var array
	 */
	protected $field = array();




	/**
	 * Holds the html for this field
	 * 
	 * @var string
	 */
	public $html = '';




	/**
	 * Holds the field label including tooltip
	 * 
	 * @var string
	 */
	public $label = '';




	/**
	 * Holds the field raw html
	 * 
	 * @var string
	 */
	public $field_html = '';




	/**
	 * will hold our button markup to upload wp media
	 * 
	 * @var string
	 */
	protected $btn_upload_file = '<a 
		class="premise-btn-upload" 
		href="javascript:void(0);" 
		onclick="premiseUploadFile(this)"
		><i class="fa fa-fw fa-upload"></i></a>';




	/**
	 * Holds the button for removing wp media uploaded
	 * 
	 * @var string
	 */
	protected $btn_remove_file = '<a 
		class="premise-btn-remove" 
		href="javascript:void(0);" 
		onclick="premiseRemoveFile(this)"
		><i class="fa fa-fw fa-times"></i></a>';




	/**
	 * Holds our fa_icon insert btn
	 * 
	 * @var string
	 */
	protected $btn_insert_icon = '<a 
		href="javascript:;" 
		class="premise-choose-icon" 
		><i class="fa fa-fw fa-th"></i></a>';





	/**
	 * holds our fa_icon remove btn
	 * 
	 * @var string
	 */
	protected $btn_remove_icon = '<a 
		href="javascript:;" 
		class="premise-remove-icon" 
		><i class="fa fa-fw fa-times"></i></a>';




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
	function __construct( $type = '', $args ) {

		if ( ! empty( $type ) && is_string( $type ) )
			$this->type = $type;

		if( ! empty( $args ) && is_array( $args ) )
			$this->args = $args;

		/**
		 * Initiate the field
		 */
		$this->field_init();

	}




	/**
	 * begin processing the field
	 */
	protected function field_init() {

		/**
		 * parse defaults and arguments
		 */
		$this->set_defaults();

		/**
		 * get everything ready to build the field
		 */
		$this->prepare_field();

		/**
		 * build the field
		 */
		$this->build_field();
				
	}






	/**
	 * Merge defaults with arguments passed to our object
	 *
	 * Saves all arguments into one array of arrays held by $field property.
	 *
	 * @return array all arguments. array of arrays.
	 */
	protected function set_defaults() {
		/**
		 * parse defaults and arguments
		 * 
		 * @var array
		 */
		$field = $this->field = wp_parse_args( $this->args, $this->defaults );

		/**
		 * Make sure our field has its necessary values
		 *
		 * Get the name field first since it is needed for the value field to be retreived.
		 */
		$field['name']  = ! empty( $field['name'] )  ? esc_attr( $field['name'] )  : $this->get_name( $field['id'] );
		$field['value'] = ! empty( $field['value'] ) ? $field['value'] : $this->get_db_value( $field['name'] );
		$field['id']    = ! empty( $field['id'] )    ? esc_attr( $field['id'] )    : $this->get_id_att( $field['name'] );

		$this->field = $field;
	}






	/**
	 * Prepare our field. This function assigns the values to the 
	 * class properties needed to build a particular field
	 */
	protected function prepare_field() {

		/**
		 * add filters before we do anything else
		 */
		$this->add_filters();

		/**
		 * prep the label element
		 */
		$this->the_label();

		/**
		 * prep the field element
		 */
		$this->the_field();
	}





	/**
	 * Add filters first
	 *
	 * This has to run first to make sure that our filters get hooked before they are called
	 * Unsets the filter argument at the end to avoid conflicts when printing attributes on field
	 *
	 * @since 1.2 
	 */
	protected function add_filters() {
		
		if ( ! empty( $this->field['add_filter'] ) && strpos( $this->field['add_filter'], ':' ) ) {

			$filter = explode( ':', $this->field['add_filter'] );

			add_filter( $filter[0], $filter[1] );
		}

		if ( 'fa_icon' == $this->type ) 
			add_filter( 'premise_field_html_after_wrapper', array( $this, 'fa_icons' ) );
		
		unset( $this->field['add_filter'] );
	}





	/**
	 * Saves and returns the label html element
	 *
	 * @since 1.2 
	 * 
	 * @return string HTML for label element
	 */
	protected function the_label() {
		$label = '';
		if ( ! empty( $this->field['label'] ) ) {
			$label .= '<label';
			$label .= ! empty( $this->field['id'] ) ? ' for="'.esc_attr( $this->field['id'] ).'">' : '>';
			$label .= esc_attr( $this->field['label'] );
			$label .= ! empty( $this->field['tooltip'] ) ? ' <span class="premise-tooltip"><i>'.esc_attr( $this->field['tooltip'] ).'</i></span>' : '';
			$label .= '</label>';
		}

		/**
		 * Alter the label html
		 *
		 * this filter allows you to change the html of the label element of a field
		 * passes the generated html to the function. additionaly paramters are all
		 * the field arguments and the type of field being called.
		 *
		 * @since 1.2 Added with new premise field class
		 * 
		 * @var string
		 */
		$this->label = apply_filters( 'premise_field_label_html', $label, $this->field, $this->type );
	}




	/**
	 * The field's html
	 * 
	 * @return string html for the raw field
	 */
	protected function the_field() {
		
		$html =''; // Start with a clean HTML string
		
		/**
		 * Build field depending on the type passed
		 */
		switch( $this->type ) {
			case 'select':
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

			case 'wp_media':
				$html .= $this->wp_media();
				break;

			case 'fa_icon':
				$html .= $this->fa_icon();
				break;

			default:
				$html .= $this->input_field();
				break;
		}

		/**
		 * filter the field html
		 *
		 * Allow you to change the html passed to the field element
		 *
		 * @since 1.2 
		 * 
		 * @var string
		 */
		$this->field_html = apply_filters( 'premise_field_raw_html', $html, $this->field, $this->type );
	}






	/**
	 * This function builds our field and saves the html markup for it
	 */
	protected function build_field() {

		/**
		 * html for actual field
		 * 
		 * @var string
		 */
		$html = '<div class="premise-field">';
		
			$html .= $this->label;

			$html .= '<div class="premise-field-'.$this->type.'">';

				$html .= $this->field_html;

			$html .= '</div>';

			/**
			 * Insert your own markup after the field
			 *
			 * @since 1.2 
			 *
			 * @var string has to return html string
			 */
			$html .= apply_filters( 'premise_field_html_after_wrapper', '', $this->field, $this->type );

		$html .= '</div>';

		/**
		 * filter the entire html
		 *
		 * Allow you to change the html passed to the field element
		 *
		 * @since 1.2 
		 * 
		 * @var string
		 */
		$this->html = apply_filters( 'premise_field_html', $html, $this->field, $this->type );
	}




	/**
	 * create an input field
	 * 
	 * @return string html for an input element
	 */
	protected function input_field() {

		$field  = '<input type="'. $this->type .'"';

		foreach ( $this->field as $k => $v ) {
			$field .= $this->gen_input_atts( $k, $v );
		}
		
		$field .= '>';

		/**
		 * Filter to alter html of input field after creating it
		 *
		 * @since 1.2 added to offer more control over markup
		 */
		return apply_filters( 'premise_field_input', $field, $this->field, $this->type );

	}







	/**
	 * Textarea element
	 * 
	 * @return string html for textarea
	 */
	protected function textarea() {
		
		$field = '<textarea ';

		foreach ( $this->field as $k => $v ) {
			$field .= ( ! empty( $v ) && 'value' !== $k ) ? ' '.esc_attr( $k ).'="'.esc_attr( $v ).'"' : '';
		}

		$field .= '>'.$this->field['value'].'</textarea>';

		return $field;
	}






	/**
	 * create a checkbox field
	 * 
	 * @return string html for checkbox field
	 */
	protected function checkbox() {
		
		$field  = '<input type="'. $this->type .'"';
		
		foreach ( $this->field as $k => $v ) {
			if ( 'value_att' == $k && ! empty( $v ) ) {
				$field .= checked( $this->field['value'], $v, false );
				continue;
			}
			$field .= $this->gen_input_atts( $k, $v );
		}

		/**
		 * Close the field
		 * Add label if needed and id has been passed
		 */
		if ( ! empty( $this->label ) ) 
			$field .= '><span class="premise-field-state"></span>';
		elseif ( !empty( $this->field['id'] ) )  
			$field .= '><label for="'.esc_attr( $this->field['id'] ).'" class="premise-field-state"></label>';
		else 
			$field .= '>';

		return $field;

	}






	/**
	 * create a radio field
	 * 
	 * @return string html for radio element
	 */
	protected function radio() {
		
		$field  .= '<input type="'.$this->type.'"';
		
		foreach ( $this->field as $k => $v ) {
			if ( 'value_att' == $k && ! empty( $v ) ) {
				$field .= checked( $this->field['value'], $v, false );
				continue;
			}
			$field .= $this->gen_input_atts( $k, $v );
		}

		/**
		 * Close the field
		 * Add label if needed and id has been passed
		 */
		if ( ! empty( $this->label ) ) 
			$field .= '><span class="premise-field-state"></span>';
		elseif ( !empty( $this->field['id'] ) )  
			$field .= '><label for="'.esc_attr( $this->field['id'] ).'" class="premise-field-state"></label>';
		else 
			$field .= '>';

		return $field;
	}





	/**
	 * create select field
	 * 
	 * @return string html for select field
	 */
	protected function select_field() {
		
		$field  = '<select';

		foreach ( $this->field as $k => $v ) {
			$field .= ( ! empty( $v ) && 'value' !== $k && 'options' !== $k ) ? ' '.esc_attr( $k ).'="'.esc_attr( $v ).'"' : '';
		}

		$field .= '>' . $this->select_options() . '</select>';

		return $field;
	}






	/**
	 * create select field options
	 * 
	 * @return string options elements for select dropdown
	 */
	protected function select_options() {
		
		$options = '';

		if( is_array( $this->field['value'] ) ) {

			foreach ( $this->field['options'] as $key => $value ) {
				$options .= '<option  value="'.$value.'"';
				$options .= (is_array( $this->field['value'] ) && in_array( $value, $this->field['value'] ) ) ? 'selected="selected"' : '';
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






	/**
	 * create wp media upload field
	 *
	 * this field allow you to upload files using wordpress's
	 * own media upload ui.
	 *
	 * @since 1.2 replace the file type since now you can use that independently
	 * 
	 * @return string html for wo media upload field
	 */
	protected function wp_media() {

		/**
		 * We our own filter to alter the html of our input field
		 */
		add_filter( 'premise_field_input', array( $this, 'wp_media_input' ) );

		/**
		 * call the input field. 
		 * 
		 * This will be alter due to our hook above
		 * 
		 * @var string
		 */
		$field = $this->input_field();

		/**
		 * Filter to alter the html on the media upload btn
		 */
		$field .= apply_filters( 'premise_field_upload_btn', $this->btn_upload_file, $this->field );

		/**
		 * Filter to alter the html on the media remove button
		 */
		$field .= apply_filters( 'premise_field_remove_btn', $this->btn_remove_file, $this->field );

		return apply_filters( 'premise_field_wp_media_html', $field, $this->field, $this->type );
	}




	/**
	 * build our wp media input field
	 *
	 * @since 1.2
	 * 
	 * @param  string $field the html for the field default
	 * @return string        the new html for the field
	 */
	public function wp_media_input( $field ) {
		return str_replace( 'type="wp_media"', 'type="text" class="premise-file-url"', $field );
	}





	/**
	 * build fa_icon field
	 *
	 * @since 1.2 
	 * 
	 * @return string html for fa_icon field
	 */
	protected function fa_icon() {

		/**
		 * We our own filter to alter the html of our input field
		 */
		add_filter( 'premise_field_input', array( $this, 'fa_icon_input' ) );

		/**
		 * call the input field. 
		 * 
		 * This will be alter due to our hook above
		 * 
		 * @var string
		 */
		$field = $this->input_field();

		/**
		 * Filter to alter the html on the icon select btn
		 */
		$field .= apply_filters( 'premise_field_icon_insert_btn', $this->btn_insert_icon, $this->field );

		/**
		 * Filter to alter the html on the icon remove button
		 */
		$field .= apply_filters( 'premise_field_icon_remove_btn', $this->btn_remove_icon, $this->field );

		return apply_filters( 'premise_field_fa_icon_html', $field, $this->field, $this->type );
	}



	


	/**
	 * build our fa_icon input
	 *
	 * @since 1.2
	 * 
	 * @param  string $field the html for the field default
	 * @return string        the new html for the field
	 */
	public function fa_icon_input( $field ) {
		return str_replace( 'type="fa_icon"', 'type="text" class="premise-fa_icon"', $field );
	}





	public function fa_icons() {
		$icons = '<div class="premise-fa-all-icons" style="display:none;"><ul>';
		
		foreach ( (array) premise_get_fa_icons() as $icon ) {
			
			$icons .= '<li class="premise-inline-block premise-float-left">
				<a href="javascript:;" onclick="" class="premise-block" data-icon="'.$icon.'">
					<i class="fa fa-fw '.$icon.'"></i>
				</a>
			</li>';

		}

		$icons .= '</ul></div>';

		return $icons;
	}








	/**
	 * try to get the option value for a field
	 *
	 * @since 1.2 added before but documented in this version
	 * 
	 * @param  string $name name attribute to know what option to look for
	 * @return mixed       returns the value found or an empty string if nothing was found
	 */
	protected function get_db_value( $name ) {

		if ( empty( $name ) )
			return '';

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
			return ! empty( $this->field['default'] ) ? esc_attr( $this->field['default'] ) : '';
	}






	/**
	 * Get id attribute for field from name
	 *
	 * @since 1.2 added before but documented in this version
	 * 
	 * @param  string $name string to get id from
	 * @return string       filtered string for id
	 */
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

			return esc_attr( $id_att );
		}
		else {
			return esc_attr( $name );
		}
	}






	/**
	 * Get the name attribute from the id
	 *
	 * @since 1.2 added before but documented in this version
	 * 
	 * @param  string $label string to get name attribute from
	 * @return string        filtered string for name
	 */
	protected function get_name( $label ) {
		$label = str_replace(' ', '-', strtolower($label));
		$label = preg_replace('/[^-_a-z0-9]/', '', $label);
		return esc_attr( $label );
	}




	/**
	 * Generate attributes for input field
	 *
	 * @since 1.2 
	 * 
	 * @param  string $k attribute name
	 * @param  string $v attribute value
	 * @return string    string of attributes
	 */
	protected function gen_input_atts( $k, $v ) {
		return ( ! empty( $v ) && 'label' !== $k ) ? ' '.esc_attr( $k ).'="'.esc_attr( $v ).'"' : '';
	}






	public function get_field() {
		return $this->html;
	}


}
?>