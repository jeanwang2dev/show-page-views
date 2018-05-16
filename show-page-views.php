<?php
/*
Plugin Name:  Show Page Views
Plugin URI:   https://github.com/jeanwang2dev/show-page-views
Description:  A simple plugin that tracks page views and add one column in the all pages table
Version:      0.1.0
Author:       Jean Wang
Author URI:   https://jeanwang2dev.com/
License:      GPL2
*/

/**
 * Page view counter
 */
function page_view_counter($postID) {
	// Set a key name for the custom field views
	$col_metakey = "views";
	$col_value = get_post_meta($postID, $col_metakey, true);
	if($col_value==''){ 
		$col_value = 0;
		delete_post_meta($postID, $col_metakey);
		add_post_meta($postID, $col_metakey, '0');
    }else{
		$col_value++;
		update_post_meta($postID, $col_metakey, $col_value);
	}

}

/**
 * Trigger page view counter when a page is viewed by visitor
 */
function trigger_page_view_counter($post_id){
     
    //for a regular page 
    if ( !is_user_logged_in() &&  is_page()  ) {
        if ( empty ( $post_id) ) {
            global $post;
            $post_id = $post->ID;    
        }
        page_view_counter($post_id);
    }
     
    //for the blog index page
    if ( !is_user_logged_in() && !is_front_page() && is_home() ){  

        if ( empty ( $post_id) ) {
            global $post;
            $post_id =  get_option( 'page_for_posts' );
        }
        $posttype = get_post_type($post_id );
        page_view_counter($post_id);
    }
}
add_action( 'wp_head', 'trigger_page_view_counter');

/**
 * Dynamically inject counter result into pages
 */
function add_column($defaults){
    $defaults['views'] = __('Page Views');
    return $defaults; 
}

/**
 * Display Page Views Column in the all pages table
 */
function display_column($column_name){
    if($column_name === 'views'){
    	echo  get_post_meta(get_the_ID(), 'views', true);
    }
}

add_filter('manage_pages_columns', 'add_column');
add_action('manage_pages_custom_column', 'display_column',10,2);