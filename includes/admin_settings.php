<?php

namespace cio;


function add_menu_pages() {

	add_submenu_page( 'gf_edit_forms', __( 'Customer.io', 'cio' ), __( 'Customer.io', 'cio' ), 'manage_options', 'customer-io', 'cio\do_menu_page' );

}

add_action( 'admin_menu', 'cio\add_menu_pages', 100 );


function register_settings() {

	register_setting( 'cio-settings', Options::API_KEY, array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field'
	) );

}

add_action( 'init', 'cio\register_settings' );


function add_settings_sections() {

	add_settings_section( 'cio-api', __( 'Customer.io API', 'cio' ), '', 'cio-settings' );

}

add_action( 'admin_init', 'cio\add_settings_sections' );


function add_settings_fields() {

	add_settings_field(
		Options::API_KEY,
		__( 'API Key', 'cio' ),
		'cio\do_settings_text_field',
		'cio-settings',
		'cio-api',
		array(
			'name'  => Options::API_KEY,
			'value' => get_option( Options::API_KEY ),
			'class' => 'regular-text'
		)
	);

}

add_action( 'admin_init', 'cio\add_settings_fields' );


function do_menu_page() {

	$tabs = apply_filters( 'cio_settings_tabs',  array(
		'cio-settings' => __( 'Settings', 'cio' )
	) );

	reset( $tabs );

	$active = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? $_GET['tab'] : key( $tabs );

	?>

	<div class="wrap cio-admin-page">

		<h2 style="display: none"></h2>

		<?php settings_errors( 'customer-io' ); ?>

		<h2 class="nav-tab-wrapper">

			<?php foreach( $tabs as $tab => $title ) : ?>

				<a href="<?php echo add_query_arg( 'tab', $tab ); ?>"
				   class="nav-tab <?php echo $active == $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( $title ); ?></a>

			<?php endforeach; ?>

		</h2>

		<div class="inner">

			<form method="post" action="options.php">

				<?php do_settings_sections( $active ); ?>

				<?php settings_fields( $active ); ?>

				<?php submit_button(); ?>

			</form>

		</div>

	</div>

<?php }


function do_settings_text_field( $args ) {

	$defaults = array(
		'type'  => 'text',
		'value' => '',
		'class' => array(),
		'attrs' => array()
	);

	$args = wp_parse_args( $args, $defaults );

	echo '<input type="' . esc_attr( $args['type'] ) . '" name="' . $args['name'] . '" value="' . esc_attr( $args['value'] ) .
	     '" class="' . esc_attr( is_array( $args['class'] ) ? implode( ' ', $args['class'] ) : $args['class'] ) . '" ';

	foreach ( $args['attrs'] as $attr => $values ) {
		echo $attr . '="' . esc_attr( is_array( $values ) ? implode( ' ', $values ) : $values ) . '" ';
	}


	echo '/>';
}
