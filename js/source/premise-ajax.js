/**
 * Premise AJAX object
 */

var PremiseAjax = {


	overlay: '<div id="premise-ajax-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background-color:#FFFFFF;opacity:.6;z-index:9990;"></div>',

	icon: '<div id="premise-ajax-loading" class="absolute center" style="display:none;position:fixed;width:60px;top:40%;left:50%;margin-left:-30px;z-index:9991;"><i class="fa fa-3x fa-spinner fa-spin"></i></div>',

	dialog: '<div id="premise-ajax-dialog" style="display:none;position:fixed;top:10%;left:10%;width:80%;height:80%;background-color:#FFFFFF;z-index:9992;overflow:auto;box-shadow: 0 0 5px #333333;-webkit-box-shadow: 0 0 5px #333333;-moz-box-shadow: 0 0 5px #333333;-ms-box-shadow: 0 0 5px #333333;-o-box-shadow: 0 0 5px #333333;padding:20px;" class="round-corners25"></div>',

	control: '<a id="premise-ajax-close" style="display:none;position: fixed;padding: 2px 12px;top: 60px;right: 40px;background: #FFFFFF;z-index: 9995;line-height: 150%;font-size: 20px;color: #AAAAAA;border-radius: 24px;-webkit-border-radius: 24px;-moz-border-radius: 24px;-ms-border-radius: 24px;-o-border-radius: 24px;box-shadow: 0 0 5px #333333;-webkit-box-shadow: 0 0 5px #333333;-moz-box-shadow: 0 0 5px #333333;-ms-box-shadow: 0 0 5px #333333;-o-box-shadow: 0 0 5px #333333;" class="row" href="javascript:;" onclick="premiseAjaxClose();">x</a>',


	ajaxExists: false,


	init: function() {
		
		this.ajaxExists = ( jQuery('#premise-ajax-overlay').length > 0 ) ? true: false;

		this.bindEvents();
	},

	

	bindEvents: function() {

		jQuery(document).on('premiseAjaxOpenDialog', this.insertMarkup);
	},




	insertMarkup: function() {

		// shorten our object name for ease of use
		var PA = PremiseAjax;

		jQuery('body').append(PA.overlay);
		jQuery('body').append(PA.icon);
		jQuery('body').append(PA.dialog);
		jQuery('body').append(PA.control);

		PA.overlay = jQuery('#premise-ajax-overlay');
		PA.icon = jQuery('#premise-ajax-loading');
		PA.dialog = jQuery('#premise-ajax-dialog');
		PA.close = jQuery('#premise-ajax-close');
	},
	

	/**
	 * Ajax popup
	 * 
	 * @param  {object} el object or selector for anchor tag with ajax link
	 * @return {AJAX}    loads page in anchor tag's href attribute
	 */
	openDialog: function() {

		// shorten our object name for ease of use
		var PA = PremiseAjax;

		// use event to trigger this insted of the opening of the box
		if ( ! PA.ajaxExists ) {
			PA.insertMarkup();
		}

		var url = '#';

		jQuery(PA.overlay).fadeIn( 'fast' );
		jQuery(PA.icon).fadeIn( 'fast' );
		jQuery(PA.close).fadeIn( 'fast' );

		// PA.url     = url + ' ' + context;

		jQuery(PA.dialog).load( url, function( resp ) {
			jQuery(PA.dialog).fadeIn('fast');
			jQuery(PA.icon).fadeOut( 'fast' );
		});

	},



	/**
	 * Close Ajax dialog and empty it.
	 * 	
	 * @return {bool} false. This function does not return anything
	 */
	premiseAjaxClose: function() {
		// shorten our object name for ease of use
		var PA = PremiseAjax;

		// PA.icon.fadeOut(    ' fast' );
		// PA.overlay.fadeOut( ' fast' );
		// PA.dialog.fadeOut(  ' fast' );
		// PA.close.fadeOut(   ' fast' );

		// PA.dialog.empty();

		return false;
	}
}