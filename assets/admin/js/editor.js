jQuery( document ).ready( function ( $ ) {

    $( '.gf-select' ).change( function () {

        $( 'form.cio-edit-map' ).append( '<input type="hidden" name="load_fields" />' );

        $( '#submit' ).attr( 'formnovalidate', true )
            .trigger( 'click' );

    } );

} );