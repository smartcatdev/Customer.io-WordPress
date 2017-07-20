<?php

namespace cio;

function update_customer( $id, $data = array() ) {

	$url = API_ENDPOINT . $id;

	$args = array(
		'method' => 'put',
		'body'   => $data
	);

	return wp_remote_request( $url, $args );

}

function customer_event( $customer_id, $name, $data = array() ) {

}