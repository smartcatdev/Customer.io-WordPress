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
			'event_name'   => __( 'Event', 'cio' ),
			'form_name'    => __( 'Form', 'cio' ),
			'date_created' => __( 'Date', 'cio' )
		);

		return $columns;

	}

	public function get_sortable_columns() {

		$columns = array(
			'event_name'   => array( 'event_name', false ),
			'form_name'    => array( 'form_name', false ),
			'date_created' => array( 'date_created', false )
		);

		return $columns;

	}

	public function no_items() {

		_e( 'No events found', 'cio' );

	}

	public function column_default( $item, $column_name ) {

		return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '—';

	}

	public function column_date_created( $item ) {

		echo date_i18n( 'Y/m/d', strtotime( $item['date_created'] ) );

	}

	public function column_event_name( $item ) { ?>

		<?php $edit_url =  menu_page_url( 'cio-edit-event', false ) . '&event=' . $item['id']; ?>

		<strong>
			<a class="row-title" href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( $item['event_name'] ); ?></a>
		</strong>
		<div class="row-actions">
			<span class="edit">
				<a href="<?php echo esc_url( $edit_url ); ?>"><?php _e( 'Edit', 'cio' ); ?></a>
			</span> |
			<span class="trash">

                <?php

                    $args = array(
                        'action'           => 'trash',
                        'event'            => $item['id'],
                        'cio_delete_nonce' => wp_create_nonce( 'delete_event' )
                    );

                ?>

				<a class="submitdelete" href="<?php echo add_query_arg( $args ); ?>"><?php _e( 'Delete', 'cio' ); ?></a>
			</span>
		</div>

	<?php }

	public function prepare_items() {

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$per_page = 20;
		$data = $this->get_events();
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

	private function get_events() {

		global $wpdb;

		$q = "SELECT * FROM {$wpdb->prefix}cio_events";

		$forms  = \GFAPI::get_forms();
		$events = $wpdb->get_results( $q, ARRAY_A );

		foreach ( $events as &$event ) {

			$form = array_filter( $forms, function ( $form ) use ( $event ) {

				return $event['form_id'] == $form['id'];

			} );

			if ( !empty( $forms ) ) {
				$event['form_name'] = $form[0]['title'];
            }

		}

		return $events;

	}

}
