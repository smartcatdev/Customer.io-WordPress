<?php

namespace cio;


function update_customer( $id, $data = array() ) {

	$url = ltrim(API_ENDPOINT . $id, '/' );

	$site_id = get_option( Options::SITE_ID );
	$api_key = get_option( Options::SITE_ID );

	$args = array(
		'method'  => 'PUT',
		'body'    => $data,
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( "$site_id:$api_key" )
		)
	);

	$res = wp_remote_request( $url, $args );

	return $res['response']['code'] === 200;

}


function customer_event( $customer_id, $name, $data = array() ) {

	$url = trailingslashit( API_ENDPOINT . $customer_id ) . trim( $name, '/' );

	$site_id = get_option( Options::SITE_ID );
	$api_key = get_option( Options::SITE_ID );

	$args = array(
		'method'  => 'POST',
		'body'    => $data,
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


	$results = $wpdb->get_results( $wpdb->prepare( $q, $v ), ARRAY_A );


	if ( !empty( $results ) ) {

		foreach ( $results as &$event ) {

			$event['field_map'] = unserialize( $event['field_map'] );

		}

		return $results;

	}


	return false;

}
