// For compatibility purposes we add this
if ( 'undefined' === typeof tribe ) {
	tribe = {};
}

if ( 'undefined' === typeof tribe.tickets ) {
	tribe.tickets = {};
}

tribe.tickets.registration = {};

( function( $, obj ) {
	'use strict';

	obj.selector = {
		container : '.tribe-block__tickets__registration',
		toggler   : '.toggle-handler',
	};

	var $tribe_registration = $( obj.selector.container );

	// Bail if there are no tickets on the current event/page/post
	if ( 0 === $tribe_registration.length ) {
		return;
	}

	/**
	 * Handle the toggle for each event
	 *
	 * @since TBD
	 *
	 * @return void
	*/
	$( obj.selector.container ).on( 'click',
		obj.selector.toggler,
		function( e ) {
			e.preventDefault();

			var $this      = $( this );
			var $event     = $this.closest( obj.selector.container );

			$event.find( '.tribe-block__tickets__item__attendee__fields' ).toggle();
			$this.toggleClass( 'open' );

	} );

	/**
	 * Init the page, set a flag for those events that need to fill inputs
	 * Toggle down those who are ready
	 *
	 * @since TBD
	 *
	 * @return void
	*/
	obj.initPage = function() {

		$( obj.selector.container ).each( function() {
			var $event = $( this );

			var required    = $event.find( 'input, textarea, select' ).filter( '[required]:visible' );
			var allRequired = true;
			var upToDate    = $event.data( 'is-meta-up-to-date' );
			console.log( upToDate );
			required.each( function() {
				var $field = $( this );

				if ( '' == $field.val() ) {
					allRequired = false;
				}
			});

			if ( ! allRequired || ! upToDate ) {
				$event.find( '.registration-status' ).css( 'background-color', '#5c0120' );
			} else {
				$event.find( '.registration-status' ).css( 'background-color', '#444' );
				$event.find( '.registration-status i' ).removeClass( 'dashicons-no-alt' );
				$event.find( '.registration-status i' ).addClass( 'dashicons-yes' );
				$event.find( '.tribe-block__tickets__item__attendee__fields' ).toggle();
			}

		});

	}

	/**
	 * Init the tickets registration script
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	obj.init = function() {
		obj.initPage();
	}

	obj.init();


})( jQuery, tribe.tickets.registration );