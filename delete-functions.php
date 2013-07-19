<?php

$query_results = array();

function delete_product_postmeta_records($transactions = array()){

	global $wpdb, $query_results;

	if(!$transactions)
		return false;

	$meta_keys = array(
		'_edd_download_earnings',
		'_edd_download_sales'
	);

	foreach ($transactions as $transaction) {
		foreach ($meta_keys as $key) {
			$query = "
				DELETE 
				FROM ".$wpdb->prefix."postmeta
				WHERE post_id = $transaction
				AND meta_key = '".$key."'
			";

			//array($query_results, $wpdb->query($query));
			//echo $query."<br/>";
			$wpdb->query($query);
		}
	}

}

function delete_payment_postmeta_records($payment_ids = array()){

	global $wpdb, $query_results;

	if(!$payment_ids)
		return false;

	$meta_keys = array(
		'_edd_payment_gateway',
		'_edd_payment_meta',
		'_edd_payment_mode',
		'_edd_payment_purchase_key',
		'_edd_payment_total',
		'_edd_payment_user_email',
		'_edd_payment_user_id',
		'_edd_payment_user_ip',
	);

	foreach ($payment_ids as $payment) {
		foreach ($meta_keys as $key) {
			$query = "
				DELETE 
				FROM ".$wpdb->prefix."postmeta
				WHERE post_id = $payment
				AND meta_key = '".$key."'
			";

			//array($query_results, $wpdb->query($query));
			//echo $query."<br/>";
			$wpdb->query($query);
		}
	}

}

function delete_logs_postmeta_records($logs = array()){

	global $wpdb, $query_results;

	if(!$logs)
		return false;

	$meta_keys = array(
		'_edd_log_payment_id',
	);

	foreach ($logs as $log) {
		foreach ($meta_keys as $key) {
			$query = "
				DELETE 
				FROM ".$wpdb->prefix."postmeta
				WHERE post_id = $log
				AND meta_key = '$key'
			";

			//array($query_results, $wpdb->query($query));
			//echo $query."<br/>";
			$wpdb->query($query);
		}
	}

}

function delete_transactions_and_logs(){

	global $wpdb, $query_results;

	$post_types = array(
		'edd_log',
		'edd_payment'
	);

	foreach ($post_types as $type) {
		$query = "
			DELETE 
			FROM ".$wpdb->prefix."posts
			WHERE post_type = '$type'
		";

		//array($query_results, $wpdb->query($query));
		//echo $query."<br/>";
		$wpdb->query($query);
	}
}

function get_transactions_of_the_products($args = array()){

}


