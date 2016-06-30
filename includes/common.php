<?php


function we_get_template($template_name,$args = array()){
	

if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = ''.WE_PLUGIN_PATH.'templates/email.php';

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}



	include( $located );



	
	
}