<?php
/**
 * Create the one page navigation
 *
 * @package Premise
 * @subpackage One Page Nav
 */


/**
* One Page Nav Class
*/
class PremiseOnePageNavClass extends Walker_Nav_Menu {

	public $nav;

	protected $menu;

	protected $menu_id;

	protected $menu_name = 'header-menu';

	protected $menu_args = array(
		'menu-item-title'     => '',
		'menu-item-url'       => '',
		'menu-item-object-id' => '',
		'menu-item-object'    => '',
		'menu-item-type'      => 'post_type',
		'menu-item-status'    => 'publish', );

	protected $message = array(
		'success-add-post'    => array(
			'message'         => 'This Page/Post is now in your home page and header navigation.',
			'type'            => 'success', ),
		'failure-add-post'    => array(
			'message'         => 'Seems like this Page/Post is already in your navigation. Make sure to save your changes to add to your home page as well.',
			'type'            => 'warning', ),
		'success-delete-post' => array(
			'message'         => 'This Page/Post is no longer in your home page or header navigation.',
			'type'            => 'success', ),
		'failure-delete-post' => array(
			'message'         => 'Seems like this Page/Post was already removed from your navigation, make sure to save your changes to remove it from your home page as well.',
			'type'            => 'warning', ),
		'no-menu'             => array(
			'message'         => 'Error: This will only add this Page/Post to your home page. To add it to your Navigation as well you must create a Wordpress menu (with at least one item) and set the theme location to <em>Header Menu</em>.',
			'type'            => 'warning', ), );


	function __construct( $nav = '' ) {
		$this->nav = 'splash' == $nav ? 'splash' : '';
		$this->get_menu();
		$this->set_new_menu_params();
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ) );
		add_action( 'wp_ajax_premise_update_this_post', array( $this, 'add_to_one_page_nav' ) );
	}


	/**
	 * Get the main menu from theme
	 * @return array array of menu item objects
	 */
	protected function get_menu() {
		
		if ( ( $locations = get_nav_menu_locations() )
			&& isset( $locations[ $this->menu_name ] ) ){
			$this->menu_id = $locations[ $this->menu_name ];
		}else{
			$this->menu_id = null;
		}

		$this->menu = wp_get_nav_menu_items( $this->menu_id );
	}


	/**
	 * Set Menu Item Args
	 */
	protected function set_new_menu_params() {
		$this->menu_args['menu-item-title']     = !empty( $_POST['premise_post_title'] ) ? $_POST['premise_post_title'] : '';
		$this->menu_args['menu-item-url']       = !empty( $_POST['premise_post_url'] ) ? $_POST['premise_post_url'] : '';
		$this->menu_args['menu-item-object-id'] = !empty( $_POST['premise_post_object_id'] ) ? $_POST['premise_post_object_id'] : '';
		$this->menu_args['menu-item-object']    = !empty( $_POST['premise_post_object'] ) ? $_POST['premise_post_object'] : '';
	}


	/**
	 * Add Metabox
	 */
	public function add_metabox() {
		$post_types = get_post_types( array( 'public' => true ), 'names' );
		foreach ($post_types as $post_type) {
			add_meta_box( 'premise-one-page-nav', 'One Page Home', array( $this, 'render_metabox' ), $post_type, 'side', 'high' );
		}
	}


	/**
	 * Save Meta Box
	 * @param  int $post_id The Post ID
	 */
	public function save_metabox( $post_id ) {
		if ( !isset( $_POST['premise_one_page_nav_nonce'] ) ) return;
		if ( !wp_verify_nonce( $_POST['premise_one_page_nav_nonce'], 'premise_one_page_nav' ) ) return;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( isset( $_POST['post_type'] ) && get_post_type() == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) ) return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) ) return;
		}

		$premise_add_post_to_one_page = null;

		if ( isset( $_POST['premise_add_post_to_one_page'] ) ) {

			$premise_add_post_to_one_page = $_POST['premise_add_post_to_one_page'];
		}

		update_post_meta( $post_id, 'premise_add_post_to_one_page', $premise_add_post_to_one_page );
	}


	/**
	 * Render Metabox
	 * @param  object $post The post to be included in our one page navigation
	 * @return string       HTML for one page nav metabox
	 */
	public function render_metabox( $post ) {
		wp_nonce_field( 'premise_one_page_nav', 'premise_one_page_nav_nonce' );
		$check = get_post_meta( $post->ID, 'premise_add_post_to_one_page', true );

		echo '<span class="ajax-response"></span>';
		$form_builder = array(
			array(
				'type' => 'checkbox',
				'label' => 'Add to One Page Home',
				'tooltip' => 'When this box gets checked, this post or page automatically gets added to our home screen and header nav. You must have the One Page Home option enabled in the theme for this to work though.',
				'name' => 'premise_add_post_to_one_page',
				'value_att' => '1',
				'value' => $check,
				'attribute' => 'onchange="jQuery(function($){premiseAddToOPN(this);})"',
			 ),
			array(
				'type' => 'hidden',
				'name' => 'premise_post_title',
				'value' => get_the_title(),
				'template' => 'raw',
			),
			array(
				'type' => 'hidden',
				'name' => 'premise_post_url',
				'value' => get_permalink(),
				'template' => 'raw',
			),
			array(
				'type' => 'hidden',
				'name' => 'premise_post_object_id',
				'value' => $post->ID,
				'template' => 'raw',
			),
			array(
				'type' => 'hidden',
				'name' => 'premise_post_object',
				'value' => $post->post_type,
				'template' => 'raw',
			),
		);

		premise_field( $form_builder );
	}


	/**
	 * Add Post to One Page Nav AJAX Function
	 * @return mixed confirmation message $this->message, false if function fails
	 */
	public function add_to_one_page_nav() {

		// Check if menu even exists
		if( empty( $this->menu_id ) ) {

			echo json_encode( $this->message['no-menu'] );
			die();
		}

		// update premise_add_post_to_one_page post meta
		if ( isset( $_POST['premise_add_post_to_one_page'] )
			&& isset( $_POST['premise_post_object_id'] ) ) {

			update_post_meta(
				$_POST['premise_post_object_id'],
				'premise_add_post_to_one_page',
				$_POST['premise_add_post_to_one_page']
			);
		}


		// if adding post to OPN
		if ( isset( $_POST['premise_add_post_to_one_page'] )
			&& $_POST['premise_add_post_to_one_page'] == '1' ) {

			$r = true;

			if( !$this->premise_nav_item_exists() )
				$r = wp_update_nav_menu_item( $this->menu_id, 0, $this->menu_args );

			echo $r ? json_encode( $this->message['success-add-post'] ) : json_encode( $this->message['failure-add-post'] );
			die();
		}

		// If deleting post from OPN
		$item_id = "";
		foreach( $this->menu as $item ){

			if( $item->object_id == $this->menu_args['menu-item-object-id'] )
				$item_id = $item->ID;
		}

		$menuObject = wp_get_nav_menu_object( $this->menu_id );
		$menu_objects = get_objects_in_term( $menuObject->term_id, 'nav_menu' );

		if ( !empty( $item_id ) && !empty( $menu_objects ) ) {

			foreach ( $menu_objects as $item ) {

				if( $item == $item_id ){

					$r = wp_delete_post( $item );

					echo $r ? json_encode( $this->message['success-delete-post'] ) : json_encode( $this->message['failure-delete-post'] );
					die();
				}
			}
		}

		echo json_encode( $this->message['success-delete-post'] );

		die();
	}


	/**
	 * Check nav item exists
	 * @param int $nav_id nav item id to check for. if none $this->menu_args['menu-item-object-id'] will be used
	 * @return mixed if nav item exists returns int ID. false if nav item does not exist
	 */
	public function premise_nav_item_exists( $nav_id = '' ) {
		$nav_id = empty( $nav_id ) ? $this->menu_args['menu-item-object-id'] : $nav_id;
		foreach( $this->menu as $item ){
			if( $item->object_id == $nav_id )
				return $item->ID;
		}
		return false;
	}


	/**
	 * Customization to Wordpress Menu Walker
	 *
	 * Changes URL to {site_url}/#page-name when page is One Page Nav
	 *
	 * DO NOT EDIT THIS FUNCTION
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filter the CSS class(es) applied to a menu item's <li>.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's <li>.
		 *
		 * @since 3.0.1
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $menu_id The ID that is applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';

		$onepage = (array) get_post_meta( $item->object_id, 'premise_add_post', true );

		$post = get_post( $item->object_id );
		$in_one_page = get_post_meta( $item->object_id, 'premise_add_post_to_one_page', true );

		if ( ! empty( $item->url ) && !$in_one_page )
			$href = $item->url;
		elseif ( is_front_page() )
			$href = '#' . $post->post_name;
		else
			$href = get_site_url() . '/#' . $post->post_name;

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = $href;

		/**
		 * Filter the HTML attributes applied to a menu item's <a>.
		 *
		 * @since 3.6.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item The current menu item.
		 * @param array  $args An array of wp_nav_menu() arguments.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;


		$item_output .= '<a'. $attributes .'>';

		/*if( $this->nav == 'splash' ){
			$premise_nav_icon = '<span class="splash-nav-icon" style="background:'.$onepage['page-color'].';color:'.$onepage['title-color'].';">
				<i class="fa fa-fw '.$onepage['nav-icon'].'"></i>
				</span>';
			//insert nav icon
			$item_output .= $premise_nav_icon;
		}*/

		/** This filter is documented in wp-includes/post-template.php */
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes $args->before, the opening <a>,
		 * the menu item's title, the closing </a>, and $args->after. Currently, there is
		 * no filter for modifying the opening and closing <li> for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}


	/**
	 * Get the One Page Home WP_Query object
	 * For the loop
	 * @example  PremiseOnePageNavClass::premise_one_page_loop_query()
	 *
	 * @param  boolean $ignore_sticky Ignore Sticky posts
	 *
	 * @return object WP_Query
	 */
	static function premise_one_page_loop_query( $ignore_sticky = true ) {

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		$menu_items = PremiseOnePageNavClass::premise_grab_menu_items_ids();

		$args = array(
			'post_type' => $post_types,
			'meta_key' => 'premise_add_post_to_one_page',
			'meta_value' => '1',
			'orderby' => 'post__in',
			'order' => 'ASC',
			'post__in' => $menu_items,
			'posts_per_page' => -1, //FJ no limit for One Page
			'ignore_sticky_posts' => (bool)$ignore_sticky,
		);

		return new WP_Query( $args );
	}


	/**
	 * Get menu items ID
	 *
	 * @example  PremiseOnePageNavClass::premise_grab_menu_items_ids()
	 *
	 * @return array Menu items ID
	 */
	static function premise_grab_menu_items_ids() {

		$locations = get_nav_menu_locations();

		$menu_id = $locations['header-menu'];

		$menu = wp_get_nav_menu_items( $menu_id );

		$menu_ids = array();

		foreach ( $menu as $menu_item ) {
			array_push( $menu_ids, $menu_item->object_id );
		}

		if( $menu_ids )
			return $menu_ids;

		return false;
	}
}
