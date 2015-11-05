# Premise WP Framework  

Premise WP is a Wordpress framework for developers who build themes and plugins. It allows you to quickly build options in the backend by doing the heavy lifting and repetitive tasks
for you. Premise WP aslo comes with a CSS framework readily available on both the backend and frontend that allows you to quickly build responsive markup.

To begin using Premise WP simply download and install the plugin, once you activate it you are all set! You can begin using it in your theme or plugin.

### Creating options in the backend

```php

/**
 * Print a text field
 */
premise_field( 'text' );

/**
 * The code above prints the following field
 */
<input type="text">

```

To add more attributes to our field, we pass the attributes that we want to add in an array as our second param. Here is how we would add a `name` attribute to our field.

```php

/**
 * Print a text field with name attribute
 * 
 * The second param is an array
 */
premise_field( 'text', array( 'name' => 'my_field_name' ) );

/**
 * The code above prints the following field
 */
<input type="text" name="my_field_name">

```

You can create any field you want by simply passing the type of field as your first parameter. If you call `premise_field( 'textarea' )` it would create a textarea field. The same goes for 
`premise_field( 'select' )` or `premise_field( 'email' )`. 

Notice that for `input` fields you only pass the type of field, where as for `select` or `textarea` fields you have to pass the tag name. 

Premise WP also has some special types of fields built into it. So far, these types of fields include `wp_media`, `fa_icon`, and `video`. Here is what these special types of fields do:
* `wp_media`: Create a 'File Upload' field that utilizes Wordpress Media Uploader 
* `fa_icon`: Create Icon field (lets users choose/insert an icon) that utilizes Font Awesome icon library
* `video`: Create an embed video field

You can add a lot more to a field than just attributes. Simply by including some arguments as part of our second param array can add things like a tooltip or a label. Here is a list of arguments
that you can pass and what each does.

```php

$defaults = array(
	/**
	 * Special Parameters
	 */
	'label'      => '',      // Wraps label element around field. uses id for for attribute if id not empty
	'tooltip'    => '',      // Adds a tooltip and tooltip functionality to field
	'add_filter' => '',      // Add a filter to this field. Read documentation for list of filters
	'context'    => '',      // Used to let Premise know where to retrieve values from ( post, user )
	/**
	 * Normal Parameters
	 */
	'name'       => '',      // name attribute. if empty fills from id
	'id'         => '',      // id attribute. if empty fills from name
	'value'      => '',      // value attribute. by default tries to get_option(name)
	'value_att'  => '',      // value attribute. Used for checkboxes and radio
	'default'    => '',      // if value is empty and get_option() return false
	'options'    => array(), // options for select fields in this format ( Text => Value )
	'attribute'  => '',      // html attributes to add to element i.e. onchange="doSomethingCool()"
);

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

## Changelog  

#### 1.2  
* Updated `PremiseField` class. Made it simpler to create fields, added filters, added `video` field.
* Changed arameters for `premise_field()`. This function now takes a string as first param which makes it possible to create a field simpler e.g. `premise_field( 'text' )`.
* Added prefix `premise-` to all classes of Premise WP CSS Framework. Divided CSS into sectins each in its own file.
* Started moving Premise WP JS into objects. Still a lot of work left here.