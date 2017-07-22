<?php

namespace cio;

function process_events( $entry, $form ) {

	$args = array(
		'form' => $form['id']
	);

	$events = get_events( $args );


	foreach ( $events as $event ) {

		$customer   = array();
		$event_data = array();

		foreach ( $event['field_map'] as $gf_id => $map ) {

			if ( $gf_id != $event['id_field'] && $gf_id != $event['email_field'] ) {
				
				switch ( $map['type'] ) {
					
					case 'customer':
						$customer[ $map['property'] ] = $entry[ $gf_id ];
						break;
						
					case 'event':
						$event_data[ $map['property'] ] = $entry[ $gf_id ];
						break;
					
				}

			}

		}

		// Update customer info with fields mapped to this form
		if ( update_customer( $entry[ $event['id_field'] ], $entry[ $event['email_field'] ], $customer ) ) {

			// Create an event with mapped fields
			customer_event( $entry[ $event['id_field'] ], $event['event_name'], $event_data );

		}

	}

}

add_action( 'gform_after_submission', 'cio\process_events', 10, 2 );
