<?php

function edd_does_not_exist_notice() {
	echo '<div class="error"><p><strong>[Easy Digital Downloads - Remove Purchases] requires the <a href="#">Easy Digital Downloads</a> plugin to be installed and activated.</strong></p></div>';
}

function edd_successfull_deletion_of_all_records() {
	echo '<div id="message" class="updated"><p><strong>All previous transaction records and logs have been deleted</strong></p></div>';
}

function is_plugin_active_from_slug($slug)
{
	if(!function_exists('is_plugin_active'))
	{
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	$path = get_plugin_basename_from_slug($slug);

	if($slug === $path or !is_plugin_active($path))
	{
		return false;
	}
	return true;
}

function get_plugin_basename_from_slug( $slug )
{
	if(!function_exists('get_plugins'))
	{
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	$keys = array_keys( get_plugins() );
	foreach ( $keys as $key ) {
		if ( preg_match( '|^' . $slug .'|', $key ) )
			return $key;
	}
	return $slug;
}