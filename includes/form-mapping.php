<?php

namespace cio;


function do_form_mapping_tab( $tab ) {

	if ( $tab === 'cio-mappings' ) {

		$table = new FormsTable();

		$table->prepare_items();
		$table->display();

	}

}

add_action( 'cio_menu_page_tab', 'cio\do_form_mapping_tab' );