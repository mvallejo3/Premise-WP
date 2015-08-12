
# Premise WP Framework  

Premise WP was built by developers for developers. At VallGroup, we build themes and plugins for our clients and the public
all the time. We developed Premise to help expedite our own process of how we build plugins and themes. Then we decided to 
share it with anyonew who's interested.

Premise is easy to use. Simply download Premise WP and copy the folder `Premise-WP` into your project. To make sure that 
there are no conflicts with any other projects using Premise WP ALWAYS make sure to include the main Premise file 
(`premise.php`) this way..   

```php

/**
 * Load Premise WP Framework 
 * only if it does not exist already
 *
 * This prevents any other plugins already running Premise WP code
 * from creating conflicts. You should always use the most current version
 * Premise WP.
 *
 * We use the class Premise_WP_FW_Class because it is the first class called
 * by Premise WP. 
 *
 * ALWAYS include Premise WP in your projects this way
 *
 * Note: We are calling this as our first line of code on our functions.php file
 */
if ( ! class_exists( 'Premise_WP_FW_Class' ) )
	require( 'Premise-WP/premise.php' );

```

-----------------------------------

## PremiseField Class

The `PremiseField` class makes it very easy to insert form fields into your project. Calling the functions `premise_field()` and `premise_field_section()` is the easiest way to take advantage of this functionlity. 
Here are some examples on how to use this class.

```php



```

```php

$defaults = array(
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
	'value_att'  => '',      // value attribute. Used for checkboxes and radio
	'default'    => '',      // if value is empty and get_option() return false
	'options'    => array(), // options for select fields in this format ( Text => Value )
	'attribute'  => '',      // html attributes to add to element i.e. onchange="doSomethingCool()"
);

```


## CSS Framework  

Premise has a small CSS framework within it. It basically consists of a variety of classes that you can apply 
through out your markup which will make it easy to structure and in some cases style your content.

### Grid System

A grid basically allows you to structure content using columns and rows. That exactly the way this grid system works. 
You asign a class `row` to a parent element and a class to each child element with the desired column size.

Here is an example:  

```html

<div class="row">
	
	<div class="col3">
		One Column
	</div>

	<div class="col3">
		Another Column
	</div>

	<div class="col3">
		Last Column
	</div>

</div>

```  

The code above will out put one row with 3 columns of the same width (33%). Premise's column layout supports up to 6 
columns per row.

Here are all column classes:  

* Class * | * Number of Columns *
---       | ---
`col2`    | 2 Columns
`col3`    | 3 Columns
`col4`    | 4 Columns
`col5`    | 5 Columns
`col6`    | 6 Columns


Another to create a grid strusture in your markup is by using the fluid grid system in Premise. The difference between 
the column layout and the fluid layout is that in the column layout all columns are of the same width. On the fluid 
layout columns can have different widths. The fluid layout works under a 12 column grid system in which you asign the 
element a class that determines how many columns that element should accoupy out of the 12 columns available. So 
applying a `span3` class to an element will span that element across 3 columns.

Consider the following code:

```html

<div class="row">
	
	<div class="span2">
		This spans over 2 columns
	</div>

	<div class="span10">
		This spans over 10 columns
	</div>
</div>

```

The code above will output two columns, one small and one bigger. You could not add any more columns to that row because 
the total amount or rows available is already reached.  

### Global Classes

These classes are commonly used for structuring and styling content quickly.

* Class * | * CSS Applied *       | * Common Cases *
---       | ---                   | ---
`block`   | `display:block;`      | div,section, article,etc.




*****************************


## Build Forms Quickly

Premise allows you to build forms quickly to display on both Admin side and front-end of your site. With Premise's built 
in function `premise_field( $args )`, you can build pretty much any field by passing an array arguments. Let's take a look.

```php

<?php 

/**
 * Build a text field
 */
$args = array(
	'type' => 'text',
	'name' => 'name_field',
	'value' => get_option('name_field'),
);
premise_field( $args );




/**
 * Build a select dropdown field.
 * NOTE: I can also pass the arguments directly into the function
 */
premise_field( array(
	'type' => 'select',
	'name' => 'select_field',
	'label' => 'My Select Field', 
	'placeholder' => 'Select an option', //used as the first option with empty value
	'options' => array(
		'Option Name' => 'Option Value',
		'Option Name 2' => 'Option Value 2',
		'Option Name 3' => 'Option Value 3',
	),
) );




/**
 * Build a file uploader field, and a textarea. 
 * NOTE: You can pass a multidemensional array holding multiple fields
 */
$args[] = array(
	'type' => 'file',
	'name' => 'file_field',
	'value' => get_option('file_field'),
);
$args[] = array(
	'type' => 'textarea',
	'name' => 'textarea_field',
	'value' => get_option('textarea_field'),
);
premise_field( $args );




/**
 * Build a section of fields. This is helpful if you want to separate fields in groups
 * where each group can maybe have their own title and description. 
 * The following builds the same fields as above but within a contianer
 */
$args = array(
	'container' = true,
	'container_title' => 'This is the Title', 
	'container_desc' => 'This is the description',
	'fields' => array(
		array(
			'type' => 'file',
			'name' => 'file_field',
			'value' => get_option('file_field'),
		),
		array(
			'type' => 'textarea',
			'name' => 'textarea_field',
			'value' => get_option('textarea_field'),
		),
	),
);
premise_field_section( $args );

?>

```

## One Page Nav

Call the `PremiseOnePageNavClass` class in your `functions.php` file to activate the One Page Nav functionality on your theme's Front / Home page.

```php

<?php

/**
 * One Page Nav
 *
 * Calls the class on the post edit screen.
 */
function premise_call_one_page_nav_class() {
    new PremiseOnePageNavClass;
}

add_action( 'wp_loaded', 'premise_call_one_page_nav_class' );

?>

```

Then call it again in your `functions.php` file when generating your theme's main menu navigation.

```php

<?php

/**
 * Main navigation
 * 
 * @return void
 */
function main_nav()
{
	wp_nav_menu(
		array(
			'theme_location'  => 'header-menu', // DO NOT MODIFY
			//call custom Walker Nav menu if One Page Home checked
			'walker'          => ( IS_HOME_ONE_PAGE ?
									new PremiseOnePageNavClass : '' ),
		)
	);
}

?>

```  

===  

## Premise Hooks  

Premise includes a variety of hooks that you can use to change or customize the way Premise behaves in your project. Here is a list of hooks and a brief description of what each hook does.  

Hook                               | Type         | Description                             |
---------                          | :----------: | ------------------                      |
`premise_field_label_html`         | Filter       | do hook for label html string           |
`premise_field_raw_html`           | Filter       | filter the html for the field itself    |
`premise_field_html_after_wrapper` | Filter       | insert html after the field wrapper     |
`premise_field_html`               | Filter       | filter the html for the whole field     |
`premise_field_input`              | Filter       | filter the input field html             |
`premise_field_textarea`           | Filter       | filter the textarea field html          |
`premise_field_upload_btn`         | Filter       | filter the wp media upload button       |
`premise_field_remove_btn`         | Filter       | filter the wp media remove button       |
`premise_field_icon_insert_btn`    | Filter       | do filter for button to show fa icon    |
`premise_field_icon_remove_btn`    | Filter       | do filter for button to hide fa icon    |
`premise_field_fa_icon_html`       | Trigger      | do filter for fa_icon field             |
`premiseFieldAfterInit`            | Trigger      | do hook after PremiseField object inits |
`premiseFieldAfterFaIconsOpen`     | Trigger      | do hook after icons box opens           |
`premiseFieldAfterFaIconsClose`    | Trigger      | do hook after icons close               |

===