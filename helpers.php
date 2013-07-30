<?php

function edd_does_not_exist_notice() {
	echo '<div id="message" class="error"><p><strong>[Easy Digital Downloads - Remove Past Transactions & Logs] requires the <a target="_blank" href="http://wordpress.org/plugins/easy-digital-downloads/">Easy Digital Downloads</a> plugin to be installed and activated.</strong></p></div>';
}

function edd_successfull_deletion_of_all_records() {
	echo '<div id="message" class="updated"><p><strong>All previous transaction records and logs have been deleted</strong></p></div>';
}

function edd_unauthorised_nonce() {
	echo '<div id="message" class="error"><p><strong>Unauthorised nonce</strong></p></div>';
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

function update_product_earnings_meta(){
	//delete the transaction earnings
	$all_downloads = new WP_Query(array('post_type' => 'download', 'posts_per_page' => -1));
	if(!$all_downloads)
		return;

	$download_ids = array();
	foreach ($all_downloads->posts as $download) {
		array_push($download_ids, $download->ID);
	}

	return update_product_postmeta_records($download_ids);
}

function delete_taxonomy_records(){
		//get all logs meta
	$all_logs = new WP_Query(array('post_type' => 'edd_log', 'posts_per_page' => -1));
	if(!$all_logs)
		return;

	$log_ids = array();
	foreach ($all_logs->posts as $log) {
		array_push($log_ids, $log->ID);
	}

	return delete_term_relationship_records($log_ids);
}

function delete_payment_meta(){
	//delete the purchase info
	$all_payments = new WP_Query(array('post_type' => 'edd_payment', 'posts_per_page' => -1));
	if(!$all_payments)
		return;

	$payments_ids = array();
	foreach ($all_payments->posts as $payment) {
		array_push($payments_ids, $payment->ID);
	}

	return delete_payment_postmeta_records($payments_ids);

}

function delete_log_meta(){
	//get all logs meta
	$all_logs = new WP_Query(array('post_type' => 'edd_log', 'posts_per_page' => -1));
	if(!$all_logs)
		return;

	$log_ids = array();
	foreach ($all_logs->posts as $log) {
		array_push($log_ids, $log->ID);
	}

	return delete_logs_postmeta_records($log_ids);
}