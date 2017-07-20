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

	register_setting( 'cio-settings', Options::SITE_ID, array(
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
		Options::SITE_ID,
		__( 'Site ID', 'cio' ),
		'cio\make_text_field',
		'cio-settings',
		'cio-api',
		array(
			'name'  => Options::SITE_ID,
			'value' => get_option( Options::SITE_ID ),
			'class' => 'regular-text',
            'desc'  => __( 'Your Customer.io Site ID found under Integrations > Customer.io API > Access Keys', 'cio' )
		)
	);

	add_settings_field(
		Options::API_KEY,
		__( 'API Key', 'cio' ),
		'cio\make_text_field',
		'cio-settings',
		'cio-api',
		array(
			'name'  => Options::API_KEY,
			'value' => get_option( Options::API_KEY ),
			'class' => 'regular-text',
			'desc'  => __( 'Your Customer.io API Key found under Integrations > Customer.io API > Access Keys', 'cio' )
		)
	);

}

add_action( 'admin_init', 'cio\add_settings_fields' );


function verify_api_keys() {

    if ( isset( $_POST[ Options::API_KEY ] ) && isset( $_POST[ Options::SITE_ID ] ) ) {

        $site_id = get_option( Options::SITE_ID );
        $api_key = get_option( Options::API_KEY );

        if ( $_POST[ Options::SITE_ID ] != $site_id || $_POST[ Options::API_KEY ] != $api_key ) {

            $args = array(
	            'method'  => 'PUT',
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode( $_POST[ Options::SITE_ID ] . ':' . $_POST[ Options::API_KEY ] )
                )
            );

            $res = wp_remote_request( API_ENDPOINT . 'wp-cio-verification', $args );

            if ( $res['response']['code'] === 200 ) {

	            add_settings_error( 'customer-io', 'api-key', __( 'Your API Key has been verified' ), 'updated' );

            } else {

	            add_settings_error( 'customer-io', 'api-key', __( 'Could not verify your API key' ) );

            }

        }

    }

}

add_action( 'admin_init', 'cio\verify_api_keys' );


function do_menu_page() {

    $tabs = array(
	    'cio-settings' => array(
		    'title'    => __( 'Settings', 'cio' ),
		    'callback' => 'cio\do_settings_tab'
	    ),
	    'cio-mappings' => array(
		    'title' => __( 'Form Mappings', 'cio' )
	    )
    );

	$tabs = apply_filters( 'cio_settings_tabs', $tabs );

	reset( $tabs );

	$active = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? $_GET['tab'] : key( $tabs );

	?>

	<div class="wrap cio-admin-page">

		<h2 style="display: none"></h2>

		<?php settings_errors( 'customer-io' ); ?>

		<h2 class="nav-tab-wrapper">

			<?php foreach( $tabs as $id => $tab ) : ?>

				<a href="<?php echo add_query_arg( 'tab', $id ); ?>"
				   class="nav-tab <?php echo $active == $id ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( $tab['title'] ); ?></a>

			<?php endforeach; ?>

		</h2>

		<div class="inner">

            <?php do_action( 'cio_menu_page_tab', $active ); ?>

		</div>

	</div>

<?php }


function do_settings_tab( $tab ) {

    if ( $tab === 'cio-settings' ) : ?>

        <form method="post" action="options.php">

            <?php do_settings_sections( $tab ); ?>

            <?php settings_fields( $tab ); ?>

            <?php submit_button(); ?>

        </form>

    <?php endif;

}

add_action( 'cio_menu_page_tab', 'cio\do_settings_tab' );

