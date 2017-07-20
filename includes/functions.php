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
