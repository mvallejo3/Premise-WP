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
 * 	   'context'   => 'post',
 *     'id'     => '78',
 *     'single' => false
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
		 * @since 1.2 new RegExp that saves each level of array into array
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




?>