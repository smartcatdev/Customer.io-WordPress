<?php

namespace cio;

function process_events( $entry, $form ) {

	$args = array(
		'form' => $form['id']
	);

	$events = get_events( $args );
	$event  = $events[0];

	$customer   = array();
	$event_data = array();

	foreach ( $event['field_map'] as $gf_id => $map ) {

		if ( $gf_id != $event['id_field'] && $gf_id != $event['email_field'] ) {

			// Variable variable for data variable
			$type = $map['type'] == 'customer' ? 'customer' : 'event_data';

			if ( is_array( $map['prop'] ) ) {

				foreach ( $map['prop'] as $id => $prop ) {

					if ( !empty( $prop ) ) {

						$$type[ $prop ] = $entry[ $id ];

					}

				}

			} else {

				$$type[ $map['prop'] ] = $entry[ $gf_id ];

			}

		}

	}


	// Update customer info with fields mapped to this form
	if ( update_customer( $entry[ $event['id_field'] ], $entry[ $event['email_field'] ], $customer ) ) {

		// Create an event with mapped fields
		customer_event( $entry[ $event['id_field'] ], $event['event_name'], $event_data );

	}

}

add_action( 'gform_after_submission', 'cio\process_events', 10, 2 );
