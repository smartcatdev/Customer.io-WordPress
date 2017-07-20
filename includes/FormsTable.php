<?php

namespace cio;


class FormsTable extends ListTable {

	public function __construct() {

		$args = array(
			'singular' => __( 'Form', 'cio' ),
			'plural'   => __( 'Forms', 'cio' )
		);

		parent::__construct( $args );

	}

	public function get_columns() {

		$columns = array(
			'cio_mapping_id' => __( 'Mapping', 'cio' ),
			'cio_form_name'  => __( 'Form', 'cio' )
		);

		return $columns;

	}

	public function get_sortable_columns() {

		$columns = array(
			'cio_mapping_id' => array( 'cio_mapping_id', false ),
			'cio_form_name'  => array( 'cio_form_name', false )
		);

		return $columns;

	}

	public function no_items() {

		_e( 'No forms found', 'cio' );

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
				id AS cio_mapping_id,
		        form_id AS cio_form_id
		      FROM {$wpdb->prefix}cio_field_mappings";

		$forms    = \GFAPI::get_forms();
		$mappings = $wpdb->get_results( $q, ARRAY_A );

		foreach ( $mappings as &$mapping ) {

			$form = array_filter( $forms, function ( $form ) use ( $mapping ) {

				return $mapping['cio_form_id'] == $form['id'];

			} );

			$mapping['cio_form_name'] = $form[0]['title'];

		}



		return $mappings;

	}

}
