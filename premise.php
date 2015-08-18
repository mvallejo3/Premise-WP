<?php

/**
 * Plugin Name: Premise WP Framework
 * Description: A Wordpress Framework for developers.
 * Plugin URI:	https://github.com/vallgroup/Premise-WP
 * Version:     1.1
 * Author:      Vallgroup LLC
 * Author URI:  http://vallgroup.com
 * License:     GPL
 * Text Domain: premise-text-domain
 *
 * @package Premise WP
 */



define( 'PREMISE_PATH', plugin_dir_path(__FILE__) );

/**
 * Define our PREMISE_URL constant
 *
 * Check whether Premise WP is being used from a theme or a plugin
 * then, set the right URL accordingly. Makes sure everything is loaded properly
 *
 * @since 1.2 
 */
if ( '' !== locate_template('Premise-WP/premise.php') ) 
	define( 'PREMISE_URL', get_stylesheet_directory_uri() . '/Premise-WP/' );
else 
	define( 'PREMISE_URL', plugin_dir_url(__FILE__) );



/**
 * Intantiate and setup Premise
 */
$init = new Premise_WP_FW_Class;

$init->premise_setup();


/**
 * The Premise Class
 *
 * This class starts premise instantiates the needed classes
 * and loads the premise css and js frameworks in the front-end 
 * and some parts of the backend.
 */
class Premise_WP_FW_Class {
	

	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 * @type object
	 */
	protected static $instance = NULL;


	

	/**
	 * plugin url
	 * 
	 * @var string
	 */
	public $plugin_url = '';




	/**
	 * plugin path
	 * 
	 * @var strin
	 */
	public $plugin_path = PREMISE_PATH;
	
	



	/**
	 * Constructor. Intentionally left empty and public.
	 *
	 * @see 	premise_setup()
	 * @since 	1.0
	 */
	public function __construct() {}

	



	/**
	 * Access this plugin’s working instance
	 *
	 * @since   1.0
	 * @return  object of this class
	 */
	public static function get_instance() {
		NULL === self::$instance and self::$instance = new self;
		
		return self::$instance;
	}

	


	
	/**
	 * Premise Hooks
	 */
	public function premise_hooks() {
		

		/**
		 * Enqueue scripts
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'premise_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'premise_scripts' ) );


		

		/**
		 * Add classes to body
		 */
		add_filter( 'body_class', array( $this, 'body_class' ) );




		/**
		 * Add classes to body
		 */
		add_filter( 'admin_body_class', array( $this, 'body_class' ) );
	}





	/**
	 * Setup Premise
	 *
	 * @since   1.0
	 */
	public function premise_setup() {
		$this->do_includes();
		$this->premise_hooks();
	}






	/**
	 * Set Premise paths
	 *
	 * @since 1.0
	 */
	protected function do_includes() {
		require( 'includes/includes.php' );
	}




	/**
	 * Add premise classes to body of document in the front-end
	 * 
	 * @param  array $classes  array of classes being passed to the body
	 * @return string          classes output into body element
	 */
	public function body_class( $classes ) {
		if ( is_admin() )
			return $classes . 'premise-wp-framewrok Premise-WP';
		
		$classes[] = 'premise-wp-framewrok';
		$classes[] = 'Premise-WP';
		return $classes;
	}






	/**
	 * Premise CSS & JS
	 */
	public function premise_scripts() {
		//register styles
		wp_register_style( 'premise_font_awesome', PREMISE_URL . 'includes/font-awesome-4.2.0/css/font-awesome.min.css' );
		wp_register_style( 'premise_style_css'   , PREMISE_URL . 'css/Premise-Wp.min.css', array( 'premise_font_awesome' ) );
		
		//register scripts
		wp_register_script( 'premise_script_js'  , PREMISE_URL . 'js/Premise-Wp.min.js', array( 'jquery', 'wp-color-picker' ) );

		//if is admin, register & enqueue jquery.minicolors + ms-Dropdown + premise_admin
		if ( is_admin() ) {
			wp_register_script( 'minicolors_js'      , PREMISE_URL . 'includes/minicolors/jquery.minicolors.min.js');
			wp_register_style( 'minicolors_css'      , PREMISE_URL . 'includes/minicolors/jquery.minicolors.css');

			wp_enqueue_style( 'minicolors_css' );
			wp_enqueue_script( 'minicolors_js' );

			wp_register_script( 'msdropdown_js'      , PREMISE_URL . 'includes/msdropdown/jquery.dd.min.js');
			wp_register_style( 'msdropdown_css'      , PREMISE_URL . 'includes/msdropdown/dd.css');

			wp_enqueue_script( 'msdropdown_js' );
			wp_enqueue_style( 'msdropdown_css' );
		}
		
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( 'premise_style_css' );
		wp_enqueue_script( 'premise_script_js' );
	}





	/**
	 * Loads translation file.
	 *
	 * Accessible to other classes to load different language files (admin and
	 * front-end for example).
	 *
	 * @wp-hook init
	 * @param   string $domain
	 * @since   1.0
	 * @return  void
	 */
	public function load_language( $domain ) {
		load_theme_textdomain(
			$domain,
			false,
			$this->plugin_path . 'languages'
		);
	}
}