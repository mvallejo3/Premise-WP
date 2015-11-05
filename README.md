# Premise WP Framework  

Premise WP is a Wordpress framework for developers who build themes and plugins. It allows you to quickly build options in the backend by doing the heavy lifting and repetitive tasks
for you. Premise WP aslo comes with a CSS framework readily available on both the backend and frontend that allows you to quickly build responsive markup.

To begin using Premise WP simply download and install the plugin, once you activate it you are all set! You can begin using it in your theme or plugin's code.

---

### Building options in the backend

To build options in the backend of Wordpress call `premise_field()` and pass it some arguments so it knows the type of form element you wish to use for your option. The function will
output the HTML for the field. The most common arguments you will use are listed in the examples below. The first argument, `text`, lets the function know what type of field you want to
create. *i.e.* _`text` will build an `input` field with attribute `type="text"`, `textarea` will build a `textarea` field._

The second argument is an array of options that builds your field and tells Premise how to treat it.

```php

/**
 * Build a text field
 * 
 * By assigning a 'name' attribute, Premise automatically fills in the id attribute
 * for the field and when the user saves the option, Premise automatically grabs the value
 * from the options table in the database.
 */
premise_field( 'text', array(
	'name' => 'my_option[in_an_array]'
) );

// The code above prints the following text field
<input type="text" name="my_option[in_an_array]" id="my_option-in_an_array">

```

If you want to add a custom field to a user profile or a post (supports pages and custom post types), simply pass a `context` parameter along with your array of options.

```php

/**
 * Build a text field for a post
 * 
 * The context parameter lets Premise know if the field is meant for a user, post, or option.
 * default is option, so the context para is only required when on a post or user profile.
 */
premise_field( 'text', array(
	'name' => 'my_option[in_an_array]',
	'context' => 'post' // Grab value for current post
) );

// The code above prints the following text field
<input type="text" name="my_option[in_an_array]" id="my_option-in_an_array">

```

Here is a list of arguments
that you can pass and what each does.

```php

$defaults = array(
	/**
	 * Special Parameters
	 */
	'label'      => '',      // Wraps label element around field. uses id for for attribute if id not empty
	'tooltip'    => '',      // Adds a tooltip to field
	'add_filter' => '',      // Add a filter to this field. Read documentation for list of filters
	'context'    => '',      // Used to let Premise know where to retrieve values from ( post, user )
	/**
	 * Normal Parameters
	 */
	'name'       => '',      // name attribute. if empty fills from id
	'id'         => '',      // id attribute. if empty fills from name (if name not empty)
	'value'      => '',      // value attribute. if empty tries to get value from get_option(name) unless 'context' is post|user
	'value_att'  => '',      // value attribute. Used for checkboxes and radio to display the default vale="" attribute
	'default'    => '',      // if value is empty displays a default value
	'options'    => array(), // options for select fields in this format ( Text => Value )
	'attribute'  => '',      // html attributes to add to element i.e. onchange="doSomethingCool()"
);

```

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