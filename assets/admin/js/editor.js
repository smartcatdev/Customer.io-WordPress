jQuery( document ).ready( function ( $ ) {

    $( '.gf-select' ).change( function () {

        $( 'form.cio-edit-map' ).append( '<input type="hidden" name="load_fields" />' );

        $( '#submit' ).attr( 'formnovalidate', true )
            .trigger( 'click' );

    } );

    $( '.id-field' ).change( function () {

        // Reset all
        $( '.field-name' ).prop( 'required', false );

        // Set only the corresponding field to required
        $( this ).parents( 'tr' )
            .find( '.field-name' )
            .prop( 'required', true );

    } );

    $( '.id-field' ).filter( function ( index, el ) {

        return $( el ).prop( 'checked' )

    } ).parents( 'tr' )
        .find( '.field-name' )
        .prop( 'required', true );

} );