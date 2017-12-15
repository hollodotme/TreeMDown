/**
 * TreeMDown additional javscript
 * author: hollodotme
 * url: https://github.com/hollootme/TreeMDown
 */

$( document ).ready( function () {
	$( '.tmd-folder-link' ).click( function ( event ) {
		var toggle_id = $( this ).data( 'subtree-id' );
		$( '#' + toggle_id ).toggle();
	} );

	$( '#reset-main-search' ).click( function ( event ) {
		$( '#main-search' ).val( '' );
		this.form.submit();
	} );
	$( 'img' ).addClass( 'img-responsive' );
} );
