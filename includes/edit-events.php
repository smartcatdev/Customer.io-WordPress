<?php

namespace cio;


function enqueue_editor_scripts() {

	wp_enqueue_script( 'cio-editor-js', asset( 'admin/js/editor.js' ), array( 'jquery' ), VERSION );

}

add_action( 'admin_enqueue_scripts', 'cio\enqueue_editor_scripts' );


function set_active_menu_item() {

    global $parent_file, $submenu_file;

    if ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'cio' ) !== false ) {


    }

}

add_filter( 'admin_head', 'cio\set_active_menu_item' );


function do_form_events_tab( $tab ) {

	if ( $tab === 'cio-events' ) {

		echo '<h1 class="wp-heading-inline">' . __( 'Events', 'cio' ) . '</h1>';
		echo '<a href="' . menu_page_url( 'cio-new-event', false ). '" class="page-title-action">' . __( 'Add New', 'cio' ) . '</a>';
		echo '<hr class="wp-header-end">';

		$table = new EventsTable();

		$table->prepare_items();
		$table->display();

	}

}

add_action( 'cio_menu_page_tab', 'cio\do_form_events_tab' );


function add_edit_pages() {

	add_submenu_page( '', __( 'Add New Event', 'cio' ), '', 'edit_posts', 'cio-new-event', 'cio\do_new_event_page' );
	add_submenu_page( '', __( 'Edit Event', 'cio' ), '', 'edit_posts', 'cio-edit-event', 'cio\do_event_edit_page' );

}

add_action( 'admin_menu', 'cio\add_edit_pages', 200 );


function save_event() {

    global $wpdb;

    if ( !isset( $_POST['load_fields'] ) && isset( $_POST['save_event_nonce'] ) &&
         wp_verify_nonce( $_POST['save_event_nonce'], 'save_event' ) ) {

        $time = current_time( 'mysql', 1 );

        $data = array(
            'form_id'      => intval( $_POST['form_id'] ),
            'event_name'   => sanitize_title( $_POST['event_name'] ),
            'status'       => 'active',
            'id_field'     => intval( $_POST['id_field'] ),
            'field_map'    => serialize( $_POST['fields'] ),
            'date_created' => $time,
            'date_updated' => $time
        );

        $formats = array( '%d', '%s', '%s', '%d', '%s', '%s', '%s' );

        if ( $wpdb->insert( "{$wpdb->prefix}cio_events", $data, $formats ) ) {

            add_settings_error( 'cio-events', 'save-event', __( 'Event successfully updated' ), 'updated' );

        } else {

	        add_settings_error( 'cio-events', 'save-event', __( 'An error occurred while updating this event' ) );

        }

    }

}

add_action( 'admin_init', 'cio\save_event' );


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
									'options'  => array( '' => __( 'Select a form', 'cio' ) ) + get_forms(),
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
				<table class="form-table">

					<?php $form = \GFAPI::get_form( $_POST['form_id'] ); ?>

					<?php foreach ( $form['fields'] as $field ) : ?>

						<tr class="regular-text">
							<th scope="row"><?php esc_html_e( $field['label'] ); ?></th>
							<td>

								<?php

                                    $args = array(
                                        'name'  => "fields[{$field['id']}]",
                                        'class' => 'regular-text'
                                    );

                                    make_text_field( $args );

								?>

                                <label>
                                    <input type="radio"
                                           name="id_field"
                                           required
                                           value="<?php esc_attr_e( $field['id'] ); ?>"><?php _e( 'ID Field', 'cio' ); ?>
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
                                    'options'  => get_forms(),
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
                    <table class="form-table">

                        <?php $form = \GFAPI::get_form( $load_fields_for ); ?>

                        <?php foreach ( $form['fields'] as $field ) : ?>

                            <tr class="regular-text">
                                <th scope="row"><?php esc_html_e( $field['label'] ); ?></th>
                                <td>

                                    <?php

                                        $args = array(
                                            'name'  => "fields[{$field['id']}]",
                                            'value' => !empty( $event['field_map'][ $field['id'] ] )
                                                             ? $event['field_map'][ $field['id'] ]
                                                             : '',
                                            'class' => 'regular-text'
                                        );

                                        make_text_field( $args );

                                    ?>

                                    <label>
                                        <input type="radio"
                                               name="id_field"
                                               required
                                               <?php $form['id'] == $event['form_id'] ? checked( $event['id_field'], $field['id'] ) : null; ?>
                                               value="<?php esc_attr_e( $field['id'] ); ?>"><?php _e( 'ID Field', 'cio' ); ?>
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

    <?php else : ?>

        <?php wp_die(); ?>

    <?php endif;

}
