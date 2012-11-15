<?php
/*
Plugin Name: HotLink no More
Plugin URI: http://innerbot.com/wordpress-plugins/hotlink-no-more
Description: A simple plugin that automagically processes all posts and pages that hotlink to images, downloading the images to your uploads folder, and replacing hotlinks with local links.
Version: 0.1
Author: Greg Johnson
Author URI: http://innerbot.com 
*/

/**
 * Copyright 2012, InnerBot.com. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

class HotlinkNoMore {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'textdomain' ) );

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

	    $this->register_hotlink_scanner();
	
	} // end constructor

	/**
	 * Fired when the plugin is activated.
	 */
	public function activate() {
		// TODO define activation functionality here
	} // end activate

	/**
	 * Fired when the plugin is uninstalled.
	 */
	public function uninstall() {
		delete_option('hotlink-no-more');
	} // end uninstall

	/**
	 * Loads the plugin text domain for translation
	 */
	public function textdomain() {
		
		load_plugin_textdomain( 'hotlink-no-more', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	// helper function to hook up the hotlink_scanner during 
	// instantiation and at the end of HotlinkNoMore::localize_hotlinked_files()
	function register_hotlink_scanner() {
    	add_action( 'save_post', array( &$this, 'localize_hotlinked_files' ) );
	} // end register_hotlink_scanner()

	// helper function to unhook the hotlink scanner
	// used to prevent infinite loop when save_post hook fires
	function unhook_hotlink_scanner() {
		remove_action( 'save_post', array( &$this, 'localize_hotlinked_files' ) );
	} // end unhook_hotlink_scanner()

	

	function localize_hotlinked_files($post_id) {

		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return;

		$the_content = $_POST['content'];

		$links = $this->extract_hotlinks($the_content);

		foreach( $links as $link ) {

			// check that link matches file extensions
			if($this->allowed_extensions($link)) {
				// download the file
				if( $local_file_path = $this->make_it_local($link, $post_id) )
					// replace external url with new local url
					str_replace($link, $local_file_path, $the_content);
			}
		}

		$this->unhook_hotlink_scanner();

		wp_update_post( array( 'ID'=>$post_id, 'post_content' => $the_content ) );

		$this->register_hotlink_scanner();

	} // end localize_hotlinked_files()

	// returns an array of all url's in $content
	function extract_hotlinks($content) {



	} // end extract_hotlinks()

	// breaks link down to see if the filename of the url is
	// of an allowed filetype
	function allowed_extensions($url) {

	} // end allowed_extensions()

	// This takes the provided url, downloads it to the server
	// inserts the file into the media db as an attachment, and 
	// returns the new local url
	function make_it_local($url, $post_id) {

	} // end make_it_local()
	  
} // end class


$hotlink_no_more = new HotlinkNoMore();