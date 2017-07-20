<?php

namespace cio;


class EventsTable extends ListTable {

	public function __construct() {

		$args = array(
			'singular' => __( 'Event', 'cio' ),
			'plural'   => __( 'Events', 'cio' )
		);

		parent::__construct( $args );

	}

	public function get_columns() {

		$columns = array(
			'cio_event'      => __( 'Event', 'cio' ),
			'cio_form_name'  => __( 'Form', 'cio' )
		);

		return $columns;

	}

	public function get_sortable_columns() {

		$columns = array(
			'cio_event'      => array( 'cio_event', false ),
			'cio_form_name'  => array( 'cio_form_name', false )
		);

		return $columns;

	}

	public function no_items() {

		_e( 'No events found', 'cio' );

	}

	public function column_default( $item, $column_name ) {

		return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '—';

	}

	public function prepare_items() {

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$per_page = 20;
		$data = $this->get_forms();
		$current_page = $this->get_pagenum();

		$this->set_pagination_args( array(
			'total_items' => count( $data ),
			'pre_page'    => $per_page
		) );

		$this->items = $this->get_items( $data, $per_page, $current_page );

	}

	private function get_items( $data, $per_page = 5, $page_number = 1 ) {

		$offset = ( $page_number - 1 ) * $per_page;

		return array_slice( $data, $offset, $per_page );

	}

	private function get_forms() {

		global $wpdb;

		$q = "SELECT DISTINCT 
				form
				event AS cio_event,
		      FROM {$wpdb->prefix}cio_events";

		$forms  = \GFAPI::get_forms();
		$events = $wpdb->get_results( $q, ARRAY_A );

		foreach ( $events as &$event ) {

			$form = array_filter( $forms, function ( $form ) use ( $event ) {

				return $event['form_id'] == $form['id'];

			} );

			$event['cio_form_name'] = $form[0]['title'];

		}



		return $events;

	}

}
