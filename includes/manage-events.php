<?php

namespace cio;


function enqueue_editor_scripts() {

	wp_enqueue_script( 'cio-editor-js', asset( 'admin/js/editor.js' ), array( 'jquery' ), VERSION );
	wp_enqueue_style( 'cio-editor-css', asset( 'admin/css/editor.css' ), null, VERSION );

}

add_action( 'admin_enqueue_scripts', 'cio\enqueue_editor_scripts' );


function do_form_events_tab( $tab ) {

	if ( $tab === 'cio-events' ) {

		echo '<h1 class="wp-heading-inline">' . __( 'Events', 'cio' ) . '</h1>';

		if ( apply_filters( 'cio_do_black_magic', false ) ) {

			echo '<a href="' . menu_page_url( 'cio-new-event', false ). '" class="page-title-action">' . __( 'Add New', 'cio' ) . '</a>';

		}

		echo '<hr class="wp-header-end">';

		$table = new EventsTable();

		$table->prepare_items();
		$table->display();

	}

}

add_action( 'cio_menu_page_tab', 'cio\do_form_events_tab' );


function add_edit_pages() {

    if ( apply_filters( 'cio_do_black_magic', false ) ) {

	    add_submenu_page( 'customer-io', __( 'Add New Event', 'cio' ), '', 'edit_posts', 'cio-new-event', 'cio\do_new_event_page' );

    }

	add_submenu_page( 'customer-io', __( 'Edit Event', 'cio' ), '', 'edit_posts', 'cio-edit-event', 'cio\do_event_edit_page' );

}

add_action( 'admin_menu', 'cio\add_edit_pages', 200 );


function save_event() {

    global $wpdb;

    if ( !isset( $_POST['load_fields'] ) && isset( $_POST['save_event_nonce'] ) &&
         wp_verify_nonce( $_POST['save_event_nonce'], 'save_event' ) ) {

        if ( isset( $_POST['id_field'] ) && isset( $_POST['email_field'] ) ) {

	        $time = current_time( 'mysql', 1 );
	        $map = array();

	        // Group mapped fields with properties and types
	        foreach ( $_POST['fields'] as $index => $prop ) {

	            if ( !empty( $prop ) ) {

		            $map[ $index ] = array(
			            'property' => $prop,
			            'type'     => $_POST['types'][ $index ]
		            );

                }

	        }

	        $data = array(
		        'form_id'      => intval( $_POST['form_id'] ),
		        'event_name'   => sanitize_text_field( $_POST['event_name'] ),
		        'status'       => 'active',
		        'id_field'     => intval( $_POST['id_field'] ),
		        'email_field'  => intval( $_POST['email_field'] ),
		        'field_map'    => serialize( $map ),
		        'date_updated' => $time
	        );

	        if ( isset( $_GET['event'] ) ) {

		        $data['id'] = intval( $_GET['event'] );

	        } else {

		        $data['date_created'] = $time;

	        }

	        if ( $wpdb->replace( "{$wpdb->prefix}cio_events", $data ) ) {

		        wp_redirect( '?page=cio-edit-event&event=' . $wpdb->insert_id );

	        } else {

		        add_settings_error( 'cio-events', 'save-event', __( 'An error occurred while saving this event' ) );

	        }

        } else {

	        add_settings_error( 'cio-events', 'save-event', __( 'Error saving this event. Missing required fields' ) );

        }

    }


}

add_action( 'admin_init', 'cio\save_event' );


function delete_event() {

    global $wpdb;

    if ( isset( $_GET['cio_delete_nonce'] ) &&
         isset( $_GET['action'] ) && $_GET['action'] === 'trash' &&
         wp_verify_nonce( $_GET['cio_delete_nonce'], 'delete_event' ) ) {

        $q = "DELETE 
              FROM {$wpdb->prefix}cio_events
              WHERE id = %d ";

        $q = $wpdb->prepare( $q, array( $_GET['event'] )  );

        if ( $wpdb->query( $q ) ) {

            add_settings_error( 'customer-io', 'event-deleted', __( 'Event was successfully deleted' ), 'updated' );
	        wp_safe_redirect( remove_query_arg( array( 'action', 'event', 'cio_delete_nonce' ) ) );

        }

    }

}

add_action( 'admin_init', 'cio\delete_event' );


function do_new_event_page() { ?>

	<div class="wrap">

        <?php settings_errors( 'cio-events' ); ?>

		<h2><?php _e( 'Add New Event', 'cio' ); ?></h2>

		<form method="post" class="cio-edit-map">
			<table class="form-table">

				<tbody>

					<tr class="regular-text">
						<th scope="row"><?php _e( 'Event', 'cio' ); ?></th>
						<td>
							<?php

								$args = array(
									'name'  => 'event_name',
									'class' => 'regular-text',
									'desc'  => __( '', 'cio' ),
									'value' => isset( $_POST['event_name'] ) ? $_POST['event_name'] : '',
                                    'attrs' => array(
                                        'required' => 'required'
                                    )
								);

								make_text_field( $args );

							?>
						</td>
					</tr>
					<tr class="regular-text">
						<th scope="row"><?php _e( 'Gravity Form', 'cio' ); ?></th>
						<td>
							<?php

								$args = array(
									'name'     => 'form_id',
									'class'    => array( 'regular-text', 'gf-select' ),
									'options'  => array( '' => __( 'Select a form', 'cio' ) ) + get_forms_without_events(),
									'desc'     => __( '', 'cio'),
									'selected' => isset( $_POST['form_id'] ) ? $_POST['form_id'] : '',
									'attrs' => array(
										'required' => 'required'
									)
								);

								make_select( $args );

							?>
						</td>
					</tr>
				</tbody>
			</table>

			<?php if ( isset( $_POST['load_fields'] ) && !empty( $_POST['form_id'] ) ) : ?>

				<h2><?php _e( 'Map Fields', 'cio' ); ?></h2>
				<table class="form-table field-map-table">

                    <thead>
                        <tr>
                            <th class="col-field-name"><?php _e( 'Field Name', 'cio' ); ?></th>
                            <th class="col-event-prop"><?php _e( 'Property', 'cio' ); ?></th>
                            <th class="col-data-type"><?php _e( 'Type', 'cio' ); ?></th>
                            <th class="col-id-field"><?php _e( 'ID Field', 'cio' ); ?></th>
                            <th class="col-email-field"><?php _e( 'Email Field', 'cio' ); ?></th>
                        </tr>
                    </thead>

					<?php $form = \GFAPI::get_form( $_POST['form_id'] ); ?>

					<?php foreach ( $form['fields'] as $field ) : ?>

						<tr class="regular-text">
							<th class="col-field-name" scope="row"><?php esc_html_e( $field['label'] ); ?></th>
							<td class="col-event-prop">

								<?php

                                    $args = array(
                                        'name'  => "fields[{$field['id']}]",
                                        'class' => array( 'regular-text', 'field-name' )
                                    );

                                    make_text_field( $args );

								?>

							</td>
                            <td class="col-data-type">
                                <label>
                                    <strong class="label"><?php _e( 'Type', 'cio' ); ?></strong>
                                        <?php

                                            $args = array(
                                                'name' => "types[{$field['id']}]",
                                                'options' => array(
                                                    'customer' => __( 'Customer', 'cio' ),
                                                    'event'    => __( 'Event', 'cio' )
                                                )
                                            );

                                            make_select( $args );

                                        ?>
                                </label>
                            </td>
                            <td class="col-id-field">
                                <label>
                                    <input type="radio"
                                           name="id_field"
                                           class="id-field"
                                           required
                                           value="<?php esc_attr_e( $field['id'] ); ?>">
                                    <span class="field-label"><?php _e( 'ID Field', 'cio' ); ?></span>
                                </label>
                            </td>
                            <td class="col-email-field">
                                <label>
                                    <input type="radio"
                                           name="email_field"
                                           class="email-field"
                                           required
                                           value="<?php esc_attr_e( $field['id'] ); ?>">
                                    <span class="field-label"><?php _e( 'Email Field', 'cio' ); ?></span>
                                </label>
                            </td>
						</tr>

					<?php endforeach; ?>

				</table>

			<?php endif; ?>

			<?php wp_nonce_field( 'save_event', 'save_event_nonce' ); ?>
			<?php submit_button( __( 'Save Event', 'cio' ) ); ?>

		</form>

	</div>

<?php }


function do_event_edit_page() {

    $args = array(
	    'id' => $_GET['event']
    );

    $events = get_events( $args );

    if ( $events ) : $event = $events[0]; ?>

        <div class="wrap">

            <?php settings_errors( 'cio-events' ); ?>

            <h2><?php _e( 'Edit Event', 'cio' ); ?></h2>

            <form method="post" class="cio-edit-map">
                <table class="form-table">

                    <tbody>
                        <tr class="regular-text">
                            <th scope="row"><?php _e( 'Event', 'cio' ); ?></th>
                            <td>
                                <?php

                                    $args = array(
                                        'name'  => 'event_name',
                                        'class' => 'regular-text',
                                        'desc'  => __( '', 'cio' ),
                                        'value' => $event['event_name'],
                                        'attrs' => array(
                                            'required' => 'required'
                                        )
                                    );

                                    make_text_field( $args );

                                ?>
                            </td>
                        </tr>
                        <tr class="regular-text">
                            <th scope="row"><?php _e( 'Gravity Form', 'cio' ); ?></th>
                            <td>
                                <?php

                                    $args = array(
                                        'name'     => 'form_id',
                                        'class'    => array( 'regular-text', 'gf-select' ),
                                        'options'  => get_forms_without_events( array( 'include' => $event['form_id'] ) ),
                                        'desc'     => __( '', 'cio'),
                                        'selected' => isset( $_POST['form_id'] ) ? $_POST['form_id'] : $event['form_id'],
                                        'attrs' => array(
                                            'required' => 'required'
                                        )
                                    );

                                    make_select( $args );

                                ?>
                            </td>
                        </tr>
                    </tbody>

                </table>

                <?php

                    $load_fields_for = false;

                    if ( isset( $_POST['load_fields'] ) && !empty( $_POST['form_id'] ) ) {

                        $load_fields_for = $_POST['form_id'];

                    } elseif ( !empty( $event['form_id'] ) ) {

                        $load_fields_for = $event['form_id'];

                    }

                ?>

                <?php if ( $load_fields_for ) : ?>

                    <h2><?php _e( 'Map Fields', 'cio' ); ?></h2>
                    <table class="form-table field-map-table">

                        <thead>
                            <tr>
                                <th class="col-field-name"><?php _e( 'Field Name', 'cio' ); ?></th>
                                <th class="col-event-prop"><?php _e( 'Property', 'cio' ); ?></th>
                                <th class="col-data-type"><?php _e( 'Type', 'cio' ); ?></th>
                                <th class="col-id-field"><?php _e( 'ID Field', 'cio' ); ?></th>
                                <th class="col-email-field"><?php _e( 'Email Field', 'cio' ); ?></th>
                            </tr>
                        </thead>

                        <?php $form = \GFAPI::get_form( $load_fields_for ); ?>

                        <?php foreach ( $form['fields'] as $field ) : ?>

                            <tbody>
                                <tr class="regular-text">
                                    <th class="col-field-name" scope="row"><?php esc_html_e( $field['label'] ); ?></th>
                                    <td class="col-event-prop">

                                        <?php

                                            $args = array(
                                                'name'  => "fields[{$field['id']}]",
                                                'value' => !empty( $event['field_map'][ $field['id'] ]['property'] )
                                                                 ? $event['field_map'][ $field['id'] ]['property']
                                                                 : '',
                                                'class' => array( 'regular-text', 'field-name' )
                                            );

                                            make_text_field( $args );

                                        ?>

                                    </td>
                                    <td class="col-data-type">
                                        <label>
                                            <strong class="label"><?php _e( 'Type', 'cio' ); ?></strong>

                                            <?php

                                                $args = array(
                                                    'name'     => "types[{$field['id']}]",
                                                    'options'  => array(
                                                        'customer' => __( 'Customer', 'cio' ),
                                                        'event'    => __( 'Event', 'cio' )
                                                    ),
                                                    'selected' => !empty( $event['field_map'][ $field['id'] ]['type'] )
                                                                        ? $event['field_map'][ $field['id'] ]['type']
	                                                                    : '',
                                                );

                                                make_select( $args );

			                                ?>

                                        </label>
                                    </td>
                                    <td class="col-id-field">
                                        <label>
                                            <input type="radio"
                                                   name="id_field"
                                                   class="id-field"
                                                   required
                                                   <?php $form['id'] == $event['form_id'] ? checked( $event['id_field'], $field['id'] ) : null; ?>
                                                   value="<?php esc_attr_e( $field['id'] ); ?>">
                                            <span class="field-label"><?php _e( 'ID Field', 'cio' ); ?></span>
                                        </label>
                                    </td>
                                    <td class="col-email-field">
                                        <label>
                                            <input type="radio"
                                                   name="email_field"
                                                   class="email-field"
                                                   required
                                                    <?php $form['id'] == $event['form_id'] ? checked( $event['email_field'], $field['id'] ) : null; ?>
                                                   value="<?php esc_attr_e( $field['id'] ); ?>">
                                            <span class="field-label"><?php _e( 'Email Field', 'cio' ); ?></span>
                                        </label>
                                    </td>
                                </tr>
                            </tbody>

                        <?php endforeach; ?>

                    </table>

                <?php endif; ?>

                <?php wp_nonce_field( 'save_event', 'save_event_nonce' ); ?>
                <?php submit_button( __( 'Save Event', 'cio' ) ); ?>

            </form>

        </div>

    <?php endif;

}


function do_black_magic() {

    return get_events() === false;

}

add_filter( 'cio_do_black_magic', 'cio\do_black_magic' );