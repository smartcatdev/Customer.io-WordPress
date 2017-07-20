<?php

namespace cio;


class FormsTable extends ListTable {

	public function __construct() {

		$args = array(
			'singular' => __( 'Form', 'cio' ),
			'plural'   => __( 'Forms', 'cio' ),
			'ajax'     => false
		);

		parent::__construct( $args );

	}

	public function get_columns() {

		$columns = array(
			'cio_form_id'   => __( 'ID', 'cio' ),
			'cio_form_name' => __( 'Name', 'cio' )
		);

		return $columns;

	}

	public function get_sortable_columns() {

		$columns = array(
			'cio_form_id'   => array( 'cio_form_id', false ),
			'cio_form_name' => array( 'cio_form_name', false )
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

		$forms = array();

		foreach ( \GFAPI::get_forms() as $form ) {

			$data = array();

			$data['cio_form_id']   = $form['id'];
			$data['cio_form_name'] = $form['title'];

			$forms[] = $data;

		}

		return $forms;

	}

}
