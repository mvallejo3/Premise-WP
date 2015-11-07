jQuery(function($){

	/**
	 * Initiate the Premise Field Object
	 */
	PremiseField.init();

	PremiseAjax.init();

});





/**
 * Premise Same Height
 * @param  {string} el the class of the elements to set same height
 * @return {mixed}     will set same min-height to elements. bool false if unsuccessful
 */
function premiseSameHeight( el ) {
	el = el || '.premise-same-height';
	
	var heightTallest = 0, setHeight;

	var setUp = jQuery( el ).each(function(){
		if( setHeight ){
			return false;
		}

		setHeight = jQuery(this).attr('data-height');

		if( setHeight ){
			heightTallest = setHeight;
			return false;
		}

		var h = jQuery(this).outerHeight();
		if( h > heightTallest ){
			heightTallest = h;
		}
	});

	var fixHeight = jQuery( el ).css( 'min-height', heightTallest );

	jQuery.when( setUp ).done( fixHeight );
	
	return false;
}