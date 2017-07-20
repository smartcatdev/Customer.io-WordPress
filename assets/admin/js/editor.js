jQuery( document ).ready( function ( $ ) {

    $( '.gf-select' ).change( function () {

        $( '#load-fields' ).val( true );
        $( '#submit' ).trigger( 'click' );

    } );

} );