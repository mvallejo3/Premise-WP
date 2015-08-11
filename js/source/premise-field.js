/**
 * Premise Field Object JS
 *
 * @author Mario Vallejo <mario@vallgroup.com>
 * @since 1.2
 */




/**
 * PremiseField
 *
 * Holds functionality for all premise fields.
 *
 * @package PremiseField
 * 
 * @type {Object}
 */
var PremiseField = {


	/**
	 * Holds jquery object for the button that toggles
	 * fa icons.
	 * 
	 * @type {object}
	 */
	faShowIconsBtn: null,




	/**
	 * Holds jquery object for the button that toggles
	 * fa icons.
	 * 
	 * @type {object}
	 */
	faHideIconsBtn: null,




	/**
	 * Construct our object
	 * 
	 * @return {void} actas as a __contruct
	 */
	init: function() {

		// The show icons button
		this.faShowIconsBtn = jQuery('.premise-field-fa_icon .premise-choose-icon');

		// The hide icons button
		this.faHideIconsBtn = jQuery('.premise-field-fa_icon .premise-remove-icon');

		// bind events
		this.bindEvents();

		/**
		 * premiseFieldAfterInit
		 * 
		 * @premise-hook premiseFieldAfterInit do hook after PremiseField object inits
		 *
		 * @since 1.2
		 *
		 * @param {object} this passes the object as parameter
		 */
		jQuery(document).trigger('premiseFieldAfterInit', this);
	},




	/**
	 * Bind Evenets needed for Fields to work properly
	 * 
	 * @return {void} Binds events
	 */
	bindEvents: function() {

		/**
		 * Bind event for fa_icons to show
		 */
		this.faShowIconsBtn.click(this.showIcons);


		/**
		 * Bind event for fa_icons to hide and delete
		 */
		this.faHideIconsBtn.click(function(){
			/**
			 * Delete value from Icon field only when 'x' is clicked
			 */
			jQuery(this).parents('.premise-field').find('input.premise-fa_icon').val('');
			
			/**
			 * hide Icon box
			 */
			PremiseField.hideIcons
		});
	},




	/**
	 * display fa_icons
	 * 
	 * @return {void} displays fa icons and binds event to close
	 */
	showIcons: function() {

		var parent = PremiseField.faShowIconsBtn.parents('.premise-field');
		var icons = parent.find('.premise-field-fa-icons-container');

		jQuery(icons).show('fast');

		/**
		 * premiseFieldAfterFaIconsOpen
		 * 
		 * @premise-hook premiseFieldAfterFaIconsOpen do hook after icons box opens
		 *
		 * @since  1.2
		 *
		 * @param {object} icons jQuery object for element holding all icons
		 * @param {object} parent jQuery object for field main element
		 */
		jQuery(document).trigger('premiseFieldAfterFaIconsOpen', icons, parent );

		// also add event for icons slection
		jQuery(document).on('click', '.premise-field-fa-icon-anchor', PremiseField.insertIcon);

		// bind body to close icons box
		jQuery(document).on('click', 'body', PremiseField.hideIcons);

		return false;
	},




	/**
	 * Hide Icons
	 *
	 * This hides icons and deletes the field's value
	 * If the user simply click somehwere else in the screen
	 * the icons window will close. This event will additionally
	 * dellet the icon field.
	 * 
	 * @return {void} close window and deletes field's vlaue
	 */
	hideIcons: function() {
		
		var parent = PremiseField.faHideIconsBtn.parents('.premise-field');
		var icons = parent.find('.premise-field-fa-icons-container');

		/**
		 * Hide icons box
		 */
		jQuery(icons).hide('fast');

		/**
		 * premiseFieldAfterFaIconsClose
		 * 
		 * @premise-hook premiseFieldAfterFaIconsClose do hook after icons close
		 *
		 * @since  1.2
		 *
		 * @param {object} icons jQuery object for element holding all icons
		 * @param {object} parent jQuery object for field main element
		 */
		jQuery(document).trigger('premiseFieldAfterFaIconsClose', icons, parent );

		// Just in case
		PremiseField.bindEvents();

		return false;
	},



	/**
	 * insert selected icon into our icon field
	 * 
	 * @return {string} icon class to use
	 */
	insertIcon: function() {
		// get icon
		var icon = jQuery(this).attr('data-icon');
		// place it in field
		jQuery(this).parents('.premise-field').find('input.premise-fa_icon').val(icon);
		// close icons
		jQuery('body').trigger('click');
	}
}