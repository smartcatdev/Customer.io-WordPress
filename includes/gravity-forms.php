<?php

namespace cio;

function process_events( $entry, $form ) {

    $args = array(
        'form' => $form['id']
    );

    $events = get_events( $args );


    if ( ! empty( $events ) ) {

        $event = $events[0];

        $customer   = array();
        $event_data = array();


        foreach ( $event['field_map'] as $gf_id => $map ) {

	        switch ( $map['type'] ) {

		        case 'customer':

			        if ( is_array( $map['prop'] ) ) {

				        foreach ( $map['prop'] as $id => $prop ) {

					        if ( ! empty( $prop ) ) {

						        $customer[ $prop ] = $entry[ $id ];

					        }

				        }

			        } else {

				        $customer[ $map['prop'] ] = $entry[ $gf_id ];

			        }

			        break;

		        case 'event':

			        if ( is_array( $map['prop'] ) ) {

				        foreach ( $map['prop'] as $id => $prop ) {

					        if ( ! empty( $prop ) ) {

						        $event_data[ $prop ] = $entry[ $id ];

					        }

				        }

			        } else {

				        $event_data[ $map['prop'] ] = $entry[ $gf_id ];

			        }

			        break;

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
