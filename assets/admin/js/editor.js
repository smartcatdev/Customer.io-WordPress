jQuery( document ).ready( function ( $ ) {

    $( '.gf-select' ).change( function () {

        $( '#load-fields' ).val( $( this ).val().length > 0 ? 1 : 0);
        $( '#submit' ).trigger( 'click' );

    } );

} );