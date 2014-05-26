<?php

class WP_JSON_API_Admin {

	public function __construct() {
	
		add_action( 'admin_menu', array( &$this, 'wp_json_api_add_admin_page' ) ); 		
	
	}
	
	public function wp_json_api_add_admin_page() {
	
		$wpbkkpr_settings_page = add_management_page( 'WP JSON API', 'WP JSON API', 'manage_options', 'wp-json-api-admin-page', array( &$this, 'wp_json_api_admin_page' ) ); 

	}
	
	private function wp_json_api_admin_tabs( $current = 'api_info' ) {

		$tabs = array( 'api_info' => 'API Info', 'usage' => 'Usage' );
		echo '<h2 class="nav-tab-wrapper">';
		foreach( $tabs as $tab => $name ){
		    $class = ( $tab == $current ) ? ' nav-tab-active' : '';
		    echo "<a class='nav-tab$class' href='/wp-admin/tools.php?page=wp-json-api-admin-page&tab=$tab'>$name</a>";
		
		}
		echo '</h2>';		
		
	}
	
	public function wp_json_api_admin_page() {
	
		?>
		<div class="wrap">
			<h2>WP JSON API Help</h2>
			<p>
				This API allows you to retrieve Post data as a json object from your WordPress Installation.
			</p>
			<p>
				The objects come back with the keys 'post|postmeta' for each object in the array.
			<style type="text/css">
				.form-table td,
				.form-table th { 
					padding: 4px; margin-bottom: 4px !important;
				}
				#wp-json-api-usage p,
				#wp-json-api-info li {
					background: #fff;
					padding: 10px;
					border-radius: 3px;
					-webkit-border-radius: 3px;
					border: 1px solid #d1d1d1;
				}		 				
			</style>
			
			<?php
			$this->wp_json_api_admin_tabs( ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'api_info' );
			
			$tab_function = 'wp_json_api_admin_info';
			if ( isset( $_GET['tab'] ) ) {
				switch ( $_GET['tab'] ) {
					case 'api-info' : $tab_function = 'wp_json_api_admin_info'; break;
					case 'usage'	: $tab_function = 'wp_json_api_admin_usage'; break;
				}
			}
			$this->{$tab_function}();
		?>
		</div>
		<?php
		
	}
	
	private function wp_json_api_admin_info() {
		
		?>
		<table class="form-table">
			<tr>
				<th scope="row">API Key</th>
				<td><input type="text" value="<?php echo get_option( 'wp_json_api_key' ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row">API Token</th>
				<td><input type="text" value="<?php echo get_option( 'wp_json_api_token' ); ?>" class="regular-text"></td>
			</tr>				
		</table>			
		<p>
			The following query string keys are available to you:  
			<ul id="wp-json-api-info">
				<li>
					<strong>wpj_api_key</strong><br>
					This is the value of the API Key above
				</li>
				<li>
					<strong>wpj_api_token</strong><br>
					This is the value of the API Token above
				</li>
				<li>
					<strong>wpj_request_type</strong><br>
					This is either pull or push.  This allows you to retrieve or update data via the api. Default = 'pull'
				</li>
				<li>
					<strong>wpj_data_type</strong><br>
					This is 'post' (and coming soon 'user'). This will determine what kind of data to retrieve/update.  Default = 'post'
				</li>
				<li>
					<strong>wpj_post_type</strong><br>
					If the <strong>wpj_data_type</strong> is 'post', then you can choose what post type to work with.  Default = 'post'
				</li>	
				<li>
					<strong>wpj_tax</strong><br>
					If working with posts, you can set a taxonomy to retrieve posts				
				</li>
				<li>
					<strong>wpj_terms</strong><br>
					If using <strong>wpj_tax</strong>, you set the terms for the posts.  Uses term IDs.  You can use one or multiple.  If multiple separate them with a pipe.
					<p>Example: wpj_tax=category&wpj_terms=10</p>						
					<p>Example: wpj_tax=post_tag&wpj_terms=30|31|32</p>
				</li>					
				<li>
					<strong>wpj_limit</strong><br>
					How many objects to retrieve
				</li>
				<li>
					<strong>wpj_meta</strong><br>
					If the <strong>wpj_data_type</strong> is 'post', then you can retrieve posts via custom fields.<br>
					You use the following format for this key:  meta_key,meta_value,meta_compare (meta compare defaults to '=', but you can use =, !=, >, >=, <, <=, LIKE, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, EXISTS.  If using IN, NOT IN, BETWEEN, NOT BETWEEN, then you have to feed in at least two values.
					<p>Example: wpj_meta=colors=red</p>
					<p>Example: wpj_meta=colors=red|blue|green</p>						
					<p>Example: wpj_meta=colors=red|blue|green,NOT IN --- This will get posts that do not have these colors</p>
					<p>Example: wpj_meta=price=10.00|20.00,BETWEEN --- This will get all posts that are between 10 and 20 dollars</p>										
				</li>
				<li>
					<strong>wpj_limit</strong><br>
					Determine how many objects to return.  Defaults to no limit
				</li>
			</ul>
		</p>
		<?php
		
	}

	private function wp_json_api_admin_usage() {
	
		?>
		<h3>Example Usage</h3>
		<?php
		$site_url = get_option( 'siteurl' );
		$api_key = get_option( 'wp_json_api_key' );
		$api_token = get_option( 'wp_json_api_token' );
		$base_api_url = '?wpj_api_key=' . $api_key . '&wpj_api_token=' . $api_token;
		?>
		<div id="wp-json-api-usage">
			<p>
				<strong>Retrieve All Posts</strong><br>
				<?php echo $site_url; ?>/<?php echo $base_api_url; ?>
			</p>
			<p>
				<strong>Retrieve 5 Recent Posts</strong><br>
				<?php echo $site_url; ?>/<?php echo $base_api_url; ?>&wpj_limit=5		
			</p>
			<p>
				<strong>Retrieve Posts in the Taxonomy 'category' with the Term of 10</strong><br>
				<?php echo $site_url; ?>/<?php echo $base_api_url; ?>&wpj_tax=category&wpj_terms=10		
			</p>				
			<p>
				<strong>Retrieve Posts in the Taxonomy 'post_tag' with the Term of 20</strong><br>
				<?php echo $site_url; ?>/<?php echo $base_api_url; ?>&wpj_tax=post_tag&wpj_terms=20
			</p>								
			<p>
				<strong>Retrieve Posts in the Taxonomy 'post_tag' with the Terms of 20,21,22</strong><br>
				<?php echo $site_url; ?>/<?php echo $base_api_url; ?>&wpj_tax=post_tag&wpj_terms=20|21|22
			</p>
			<p>
				<strong>Retrieve Posts with the custom field key of 'color' and value of 'red'</strong><br>
				<?php echo $site_url; ?>/<?php echo $base_api_url; ?>&wpj_meta=color,red
			</p>																		
			<p>
				<strong>Retrieve Posts with the custom field key of 'color' and value of 'red,blue,and green'</strong><br>
				<?php echo $site_url; ?>/<?php echo $base_api_url; ?>&wpj_meta=color,red|blue|green
			</p>																												
			<p>
				<strong>Retrieve Posts with the Post Type of 'product'</strong><br>
				<?php echo $site_url; ?>/<?php echo $base_api_url; ?>&wpj_post_type=product
			</p>
			<p>
				<strong>Retrieve Posts with the Post Type of 'product' and a custom field key of 'price' and value between '10 and 100 dollars'</strong><br>
				<?php echo $site_url; ?>/<?php echo $base_api_url; ?>&wpj_post_type=product&wpj_meta=price,10|20,between
			</p>			
		</div>	
		<?php
	
	}	
	
}