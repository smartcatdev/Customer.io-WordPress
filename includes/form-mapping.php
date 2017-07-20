<?php

namespace cio;


function enqueue_editor_scripts() {

	wp_enqueue_script( 'cio-editor-js', asset( 'admin/js/editor.js' ), array( 'jquery' ), VERSION );

}

add_action( 'admin_enqueue_scripts', 'cio\enqueue_editor_scripts' );


function do_form_mapping_tab( $tab ) {

	if ( $tab === 'cio-mappings' ) {

		echo '<h1 class="wp-heading-inline">' . __( 'Form Mappings', 'cio' ) . '</h1>';
		echo '<a href="' . menu_page_url( 'cio-new-mapping', false ). '" class="page-title-action">' . __( 'Add New', 'cio' ) . '</a>';
		echo '<hr class="wp-header-end">';

		$table = new FormsTable();

		$table->prepare_items();
		$table->display();

	}

}

add_action( 'cio_menu_page_tab', 'cio\do_form_mapping_tab' );


function add_edit_pages() {

	add_submenu_page( '', __( 'Add New Mapping', '' ), '', 'edit_posts', 'cio-new-mapping', 'cio\do_new_mapping_page' );

}

add_action( 'admin_menu', 'cio\add_edit_pages' );


function do_new_mapping_page() { ?>

	<div class="wrap">

		<h2><?php _e( 'Add New Form Mapping', 'cio' ); ?></h2>

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
									'value' => isset( $_POST['event_name'] ) ? $_POST['event_name'] : ''
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
									'selected' => isset( $_POST['form_id'] ) ? $_POST['form_id'] : ''
								);

								make_select( $args );

							?>
						</td>
					</tr>
				</tbody>
			</table>

			<?php if ( !empty ( $_POST['load_fields'] ) ) : ?>

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

							</td>
						</tr>

					<?php endforeach; ?>

				</table>

			<?php endif; ?>

			<input id="load-fields" type="hidden" name="load_fields" value="false" />

			<?php wp_nonce_field( 'save_form_mapping', 'save_mapping_nonce' ); ?>

			<?php submit_button( __( 'Save Mapping', 'cio' ) ); ?>

		</form>

	</div>

<?php }
