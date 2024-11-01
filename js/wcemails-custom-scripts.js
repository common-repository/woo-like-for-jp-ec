var wcemails_admin;
( function( $ ){
	wcemails_admin = {
		init: function() {
			$( '.status-clone-wrapper' ).cloneya();
		},
	}
	$( document ).ready( function() { wcemails_admin.init(); } );
})( jQuery );
