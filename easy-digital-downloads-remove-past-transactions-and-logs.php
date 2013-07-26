<?php
/*
Plugin Name: Easy Digital Downloads - Remove Purchases
Plugin URI: http://www.nasteriadis.gr/easy-digital-downloads-remove-purchases
Description: This plugin is an extension of the easy digital downloads and enables the user to delete transactions stores in the db both completely or partially.
Version: 1.0
Author: Nick C. Asteriadis
Author URI: http://www.nasteriadis.gr/
Depends: Easy Digital Downloads
License:

  Copyright 2013 (nickast@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

require_once('helpers.php');
require_once('delete-functions.php');

add_action('init','check_edd_existance');
function check_edd_existance(){

	$edd_slug = 'easy-digital-downloads';

	if(!is_plugin_active_from_slug($edd_slug)){
		if(is_admin())
			// give admin notice
			add_action('admin_notices', 'edd_does_not_exist_notice');
		else
			// halt plugin in front end
			return;	
	}

}

add_filter('edd_reports_contextual_help', 'add_delete_help_screen',10,1);
function add_delete_help_screen($screen){
	$screen->add_help_tab( 
		array(
			'id'	    => 'edd-reports-delete',
			'title'	    => __( 'Delete', 'eddrp' ),
			'content'	=>
				'<p>' . __( 'This screen allows you to delete the past transaction.', 'edd' ) . '</p>' . 
				'<p>' . __( '<strong>All</strong> - You can delete all the past transaction from the db', 'edd' ) . '</p>' .
				'<p>' . __( '<strong>Partial Delete</strong> - You can select which transaction you want to delete', 'edd' ) . '</p>'
		) 
	);
}

add_action('admin_head', 'add_admin_head_styles');
function add_admin_head_styles(){
	?>
	<style>
	.delete-items { margin: 0 10px 10px 0!important;}
	.delete-button { margin-top: 20px!important; }
	</style>
	<?php
}

add_action('edd_reports_tabs', 'add_delete_screen_option');
function add_delete_screen_option(){
	global $edd_options;

	$current_page = admin_url( 'edit.php?post_type=download&page=edd-reports' );
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'reports';
	
	?>
	<a href="<?php echo add_query_arg( array( 'tab' => 'delete', 'settings-updated' => false ), $current_page ); ?>" class="nav-tab <?php echo $active_tab == 'delete' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Delete', 'edd' ); ?></a>
	<?php
}

function edd_reports_tab_delete() {
	?>
	<div class="metabox-holder">
		<div id="post-body">
			<div id="post-body-content">

				<?php do_action( 'edd_reports_tab_delete_content_top' ); ?>

				<div class="postbox">
					<h3><span><?php _e( 'Delete all past transaction', 'edd' ); ?></span></h3>
					<div class="inside">
						<p><?php _e( 'By clicking here you can delete all the past transction.', 'edd' ); ?></p>
						<p><a class="button" href="<?php echo wp_nonce_url( add_query_arg( array( 'edd-action' => 'delete_transactions' ) ), 'edd_delete_transactions' ); ?>"><?php _e( 'Delete Transactions', 'edd' ); ?></a></p>
					</div><!-- .inside -->
				</div><!-- .postbox -->

				
				<div class="postbox">
					<h3><span><?php _e('Custom Delete of Purchases Data', 'edd'); ?></span></h3>
					<div class="inside">
						<p><?php _e( 'Select the data you want to delete from your database', 'edd' ); ?></p>
						<p>
							<form method="post">
								<input type="checkbox" class='delete-items' name='delete[earnings]' value='1'><label>Delete Product Earnings</label><br/>
								<input type="checkbox" class='delete-items' name='delete[purchase_meta]' value='1'><label>Delete Purchase Meta records</label><br/>
								<input type="checkbox" class='delete-items' name='delete[log_meta]' value='1'><label>Delete Log Meta</label><br/>
								<input type="checkbox" class='delete-items' name='delete[transaction_and_logs]' value='1'><label>Delete Transactions & Logs</label><br/>
								
								<input type="hidden" name="edd-action" value="partially_delete_items"/>
								<input type="submit" value="<?php _e( 'Delete Data', 'edd' ); ?>" class="button-secondary delete-button"/>
							</form>
						</p>
					</div><!-- .inside -->
				</div><!-- .postbox -->

				<?php do_action( 'edd_reports_tab_delete_content_bottom' ); ?>

			</div><!-- .post-body-content -->
		</div><!-- .post-body -->
	</div><!-- .metabox-holder -->
	<?php
}
add_action( 'edd_reports_tab_delete', 'edd_reports_tab_delete' );

function edd_partially_delete_items($post_values){
	
	if(!isset($post_values['delete']))
		return;

	if(isset($checked_options['earnings']))
		delete_product_earnings_meta();

	if(isset($checked_options['purchase_meta']))
		delete_payment_meta();

	if(isset($checked_options['log_meta']))
		delete_log_meta();

	if(isset($checked_options['transaction_and_logs']))
		delete_transactions_and_logs();

}
add_action('edd_partially_delete_items', 'edd_partially_delete_items');


function edd_delete_transactions( $data ) {
	$edd_delete_transaction = $_GET['_wpnonce'];

	if ( wp_verify_nonce( $edd_delete_transaction, 'edd_delete_transactions' ) ) {
		
		/**
		 * table: postmeta
		 * task: delete keys: _edd_download_earnings / _edd_download_sales
		 * connected with: download ids of the posts table
		**/
		$res[] = update_product_earnings_meta();
		
		/**
		 * table: postmeta
		 * task: delete keys: _edd_payment_gateway / _edd_payment_meta / _edd_payment_mode / _edd_payment_purchase_key / _edd_payment_total / _edd_payment_user_email / _edd_payment_user_id / _edd_payment_user_ip
		 * connected with: edd_payment ids of the posts table
		**/
		$res[] = delete_payment_meta();

		/**
		 * table: postmeta
		 * task: delete keys: _edd_log_payment_id / _edd_log_ip / _edd_log_file_id / _edd_log_user_id / _edd_log_user_info
		 * connected with: edd_log ids of the posts table
		**/
		$res[] = delete_log_meta();

		/**
		 * table: term_relationships
		 * task: delete all records
		 * connected with: edd_log ids of the posts table
		**/
		$res[] = delete_taxonomy_records();

		/**
		 * table: posts
		 * task: delete all records of the type edd_log and edd_payment
		 * connected with: no dependancies
		**/
		$res[] = delete_transactions_and_logs();

		/**
		 * table: term_taxonomy
		 * task: update all records in the table by making all values of the count column with edd_log_type identifier equal to 0
		 * connected with: no dependancies
		**/
		$res[] = update_earning_count();

		/* show an success message when everything is OK */
		add_action('admin_notices', 'edd_successfull_deletion_of_all_records');

	} else {
		echo 'unable to verify nonce';
	}
}
add_action( 'edd_delete_transactions', 'edd_delete_transactions' );

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

