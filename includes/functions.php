<?php

namespace cio;


function update_customer( $id, $email, $data = array() ) {

	$url = ltrim(API_ENDPOINT . urlencode( $id ), '/' );

	$site_id = get_option( Options::SITE_ID );
	$api_key = get_option( Options::API_KEY );

	$user_data = array(
		'email'      => $email,
		'created_at' => current_time( 'timestamp', 1 )
	);

	$args = array(
		'method'  => 'PUT',
		'body'    => array_merge( $data, $user_data ),
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( "$site_id:$api_key" )
		)
	);

	$res = wp_remote_request( $url, $args );

	return $res['response']['code'] === 200;

}


function customer_event( $customer_id, $name, $data = array() ) {

	$url = trim( API_ENDPOINT . urlencode( $customer_id ), '/' ) . '/events';

	$site_id = get_option( Options::SITE_ID );
	$api_key = get_option( Options::API_KEY );

	$body = array(
		'name' => $name,
		'data' => $data
	);

	$args = array(
		'method'  => 'POST',
		'body'    => $body,
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( "$site_id:$api_key" )
		)
	);


	$res = wp_remote_request( $url, $args );

	return $res['response']['code'] === 200;

}

function get_forms() {

	$forms   = \GFAPI::get_forms();
	$results = array();

	foreach ( $forms as $form ) {

		$results[ $form['id'] ] = $form['title'];

	}

	return $results;

}


function get_forms_without_events( $args = array() ) {

	$defaults = array(
		'include' => null
	);

	$args = wp_parse_args( $args, $defaults );


	$events = get_events();
	$forms  = \GFAPI::get_forms();

	$results = array();


	foreach ( $forms as $form ) {

		$has_event = false;

		foreach ( $events as $event ) {

			if ( $event['form_id'] == $form['id'] && $form['id'] != $args['include'] ) {

				$has_event = true;
				break;

			}

		}

		if ( !$has_event ) {

			$results[ $form['id'] ] = $form['title'];

		}

	}

	return $results;

}


function get_events( array $args = array() ) {

	global $wpdb;

	$defaults = array(
		'id'   => false,
		'form' => false
	);

	$args = wp_parse_args( $args, $defaults );

	$q = "SELECT * FROM {$wpdb->prefix}cio_events WHERE true = true ";
	$v = array();

	if ( $args['id'] ) {

		$q  .= " AND id = %d ";
		$v[] = $args['id'];
	}

	if ( $args['form'] ) {

		$q  .= " AND form_id = %d ";
		$v[] = $args['form'];

	}


	if ( $args['id'] || $args['form'] ) {

		$q = $wpdb->prepare( $q, $v );

	}

	$results = $wpdb->get_results( $q, ARRAY_A );


	if ( !empty( $results ) ) {

		foreach ( $results as &$event ) {

			$event['field_map'] = json_decode( $event['field_map'], true );

		}

		return $results;

	}


	return false;

}
