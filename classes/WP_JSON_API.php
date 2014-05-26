<?php

class WP_JSON_API {

	private $_api_key;
	
	private $_api_token;
	
	private $_request_type;
	
	private $_data_type;
	
	private $_post_type;
	
	private $_taxonomy;
	
	private $_term;
	
	private $_limit;
	 
	public function __construct() {
	
		$this->_request_type 	= ( isset( $_GET['wpj_request_type'] ) && in_array( $_GET['wpj_request_type'], $wp_json_api->request_types ) ) ? $_GET['wpj_request_type'] : 'pull';	
		
		$this->_data_type 		= ( isset( $_GET['wpj_data_type'] ) ) ? $_GET['wpj_data_type'] : 'post';
		$this->_post_type 		= ( isset( $_GET['wpj_post_type'] ) ) ? preg_replace( '/[^A-Za-z-_]/', '', $_GET['wpj_post_type'] ) : 'post';
		$this->_limit			= ( isset( $_GET['wpj_limit'] ) ) ? preg_replace( '/[^0-9]/', '', $_GET['wpj_limit'] ) : -1; 
		
	}
	
	public function api_credential_check() {
	
		if ( !preg_match( '/[^a-zA-Z0-9]/', $_GET['wpj_api_key'] ) ) {	
			$this->_api_key = $_GET['wpj_api_key'];
		} else {
			return false;
		}		

		if ( !preg_match( '/[^a-zA-Z0-9]/', $_GET['wpj_api_token'] ) ) {	
			$this->_api_token = $_GET['wpj_api_token'];
		} else {
			return false;
		}
		
		if ( get_option( 'wp_json_api_key' ) != $this->_api_key )
			return false;

		if ( get_option( 'wp_json_api_token' ) != $this->_api_token )
			return false;
			
		return true;
						
	}
	
	public function get_request_object() {
	
		switch ( $this->_request_type ) {
		
			case 'pull' :
				
				switch ( $this->_data_type ) {
				
					case 'post' :

						$query_args = array();
						$query_args['post_type'] = $this->_post_type;
						if ( $this->_limit > 0 ) {
							$query_args['posts_per_page'] = $this->_limit;
						} else {
							$query_args['nopaging'] = true;
						}
								
						if ( isset( $_GET['wpj_tax'] ) ) {
		
							if ( preg_match( '/|/', $_GET['wpj_terms'] ) ) {
								$terms = explode( '|', $_GET['wpj_terms'] );
							} else {
								$terms = array( $_GET['wpj_terms'] );
							}	
											
							$query_args['tax_query'] = array(
								array(
									'taxonomy' => $_GET['wpj_tax'],
									'field' => 'id',
									'terms' => $terms,
								)	
							);
						}
						
						if ( isset( $_GET['wpj_meta'] ) ) {
							$meta = explode( ',', $_GET['wpj_meta'] );
							if ( preg_match( '/|/', $meta[1] ) ) {
								$meta_value = explode( '|', $meta[1] );
							} else {
								$meta_value = $meta[1];
							}	
							$query_args['meta_query'] = array(
								array(
									'key' => $meta[0],
									'value' => $meta_value,
									'compare' => ( $meta[2] ) ? $meta[2] : '='
								)
							);
						}
						
						$requested_posts = new WP_Query( $query_args );
						if ( $requested_posts->have_posts() ) {
							$post_objects = array();
							$i = 0;
							while( $requested_posts->have_posts() ) {
								$requested_posts->the_post();
								global $post;
								
								// get the base posts table data
								$post_objects[$i]['post'] = $post;
								// get the postmeta info
								$post_objects[$i]['postmeta'] = get_post_meta( $post->ID );
								
								$i++;
							}
							echo json_encode( $post_objects );					
						}
						
						break;
					
				}		
				
				break;
		
		}
	
	}
	

}

?>