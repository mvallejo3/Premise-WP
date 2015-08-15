<?php 
/**
 * Premise Field Library
 *
 * This file holds function helpers for our
 * field class and general form functionality.
 * Usually AJAX related functions may be located
 * under @see premise-ajax-library.php
 *
 * @package Premise WP
 * @package Library
 *
 * @author Mario Vallejo <mario@vallgroup.com>
 * @since 1.2
 */





function premise_name_att_to_array( $name ) {

	$name_a = array(); // Name arry

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


// /**
// 		 * If values are stored in an array
// 		 */
// 		if ( preg_match( '/\[|\]/', $name ) ) {

// 			*
// 			 * Turn html attribute name into an array of keys
// 			 *
// 			 * From:
// 			 * name="sangreea[key1][key2][key3]"
// 			 *
// 			 * To:
// 			 * array (size=2)
// 			 * 0 => 
// 			 *   array (size=4)
// 			 *     0 => string 'sangreea[' (length=9)
// 			 *     1 => string 'key1]' (length=5)
// 			 *     2 => string 'key2]' (length=5)
// 			 *     3 => string 'key3]' (length=5)
// 			 * 1 => 
// 			 *   array (size=4)
// 			 *     0 => string 'sangreea' (length=8)
// 			 *     1 => string 'key1' (length=4)
// 			 *     2 => string 'key2' (length=4)
// 			 *     3 => string 'key3' (length=4)
// 			 *
// 			 * @var array $keys[1] has strings of keys for each level of the array
// 			 *
// 			 * @since 1.2 new RegExp that saves each level of array into array
// 			 *
// 			 * @var array
			 
// 			preg_match_all( "/([0-9a-zA-Z].*?)[\[\]]/", $name, $keys);

			
// 			/**
// 			 * Set the DB option name and unset it from the keys array
// 			 *
// 			 * @var string
// 			 */
// 			$option = $keys[1][0];
// 			unset( $keys[1][0] );

// 			$val = premise_get_option( $option, $keys[1] );
// 		}

?>