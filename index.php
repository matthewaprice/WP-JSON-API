<?php
/*
Plugin Name: WP JSON API
Plugin URI: http://matthewaprice.com
Description: Turns your site into an api
Author: Matthew Price
Version: 1.0
Author URI: http://matthewaprice.com
*/

/**
 * Examples
 *
 * The key and token here are examples...look at your settings page to find yours
 *

	// get all posts
	http://yoursite.com/?
	 	 wpj_api_key=4f81b1d964419548f01ca35bbaeeda75
	 	&wpj_api_token=43b9787b8a0cd00a8115c14b2b7c3a27	 

	// get most recent 5 posts
	http://yoursite.com/?
	 	 wpj_api_key=4f81b1d964419548f01ca35bbaeeda75
	 	&wpj_api_token=43b9787b8a0cd00a8115c14b2b7c3a27	
	 	&wpj_limit=5
	 		 	
	// get all posts that have been tagged with the tag ID of 30 	
	http://yoursite.com/?
	 	 wpj_api_key=4f81b1d964419548f01ca35bbaeeda75
	 	&wpj_api_token=43b9787b8a0cd00a8115c14b2b7c3a27
	 	&wpj_tax=post_tag
	 	&wpj_terms=30
	 	// can also use multiple terms like this:  30__12__15 

	// get all posts that have been posted in the category with the category ID of 10 	
	http://yoursite.com/?
	 	 wpj_api_key=4f81b1d964419548f01ca35bbaeeda75
	 	&wpj_api_token=43b9787b8a0cd00a8115c14b2b7c3a27
	 	&wpj_tax=category
	 	&wpj_terms=10
	 	// can also use multiple terms like this:  30__12__15
	 		 	
	// get all posts with a specific custom field value
	// the value is meta_key,meta_value,compare (compare is optional...if not used then "=" is assumed.
	// if the compare is "between", then separate the values with a double underscore...any multiple values should use a double underscore
	http://theme.matthewaprice.com/?
		 wpj_api_key=4f81b1d964419548f01ca35bbaeeda75
		&wpj_api_token=43b9787b8a0cd00a8115c14b2b7c3a27
		&wpj_meta=favorite_color,green

	http://theme.matthewaprice.com/?
		 wpj_api_key=4f81b1d964419548f01ca35bbaeeda75
		&wpj_api_token=43b9787b8a0cd00a8115c14b2b7c3a27
		&wpj_meta=favorite_color,green__blue__red,IN		
	 	 
 *
 */
 	
global $wp_json_api;
$wp_json_api = new stdClass();
$wp_json_api->classes = array( 'WP_JSON_API_Admin', 'WP_JSON_API' );
foreach ( $wp_json_api->classes as $wp_json_api_class )
	require plugin_dir_path( __FILE__ ) . 'classes/' . $wp_json_api_class . '.php';

$wp_json_api->keys = array( 'wpj_api_key', 'wpj_api_token', 'request_type', 'data_type' );

$wp_json_api_admin = new WP_JSON_API_Admin();

function wp_json_api_set_credentials() {

	$api_key = get_option( 'wp_json_api_key' );
	if ( !$api_key )
		update_option( 'wp_json_api_key', md5( rand( 1000,9999 ) ) );

	$api_token = get_option( 'wp_json_api_token' );
	if ( !$api_key )
		update_option( 'wp_json_api_token', md5( rand( 1000,9999 ) ) );
					
}

add_action( 'init', 'wp_json_api_set_credentials' );

function wp_json_api_run() {
	
	global $wp_json_api;
		
	if ( !isset( $_GET['wpj_api_key'] ) && !isset( $_GET['wpj_api_token'] ) )
		return false;

	// initialize the call
	$api_call = new WP_JSON_API();

	// if the key and token match the db values, go ahead and run the api
	if ( $api_call->api_credential_check() ) {
		$api_call->get_request_object();
		exit;	
	}		
		
}	

add_action( 'init', 'wp_json_api_run' );

/*

require $_SERVER['DOCUMENT_ROOT'] . '/api/functions.php';

global $key, $token, $request_type, $data_type, $limit, $post_slug, $tag;

$api = new WP_API( $key, $token, $request_type, $data_type, $limit, $post_slug, $tag );
$check = $api->credentialCheck();
if ( $check ) :
	$response = $api->parseRequest();
	echo json_encode( $response );
else :
	echo json_encode( array( 'error' => 'Cannot connect to garcia' ) );
endif;
*/

?>