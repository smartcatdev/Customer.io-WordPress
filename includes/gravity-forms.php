<?php

namespace cio;

function gf_form_submission( $entry, $form ) {

	$args = array(
		'form' => $form['id']
	);

	$events = get_events( $args );

	foreach ( $events as $event ) {



	}

}

add_action( 'gform_after_submission', 'cio\gf_form_submission', 10, 2 );
