<?php
/*
Plugin Name: WP End-to End
Plugin URI: http://geek.ryanhellyer.net/products/end-to-end/
Description: Provides true end to end encryption in WordPress

Author: Ryan Hellyer
Version: 1.0
Author URI: http://geek.ryanhellyer.net/

Copyright 2013 Ryan Hellyer

The encryption functionality is provided by Chris Veness (www.movable-type.co.uk/tea-block.html)
and based on work by David Wheeler and Roger Needham of Cambridge University (http://www.ftp.cl.cam.ac.uk/ftp/papers/djw-rmn/djw-rmn-tea.html)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/


/**
 * Secure Content
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Secure_Content {

	/**
	 * Class constructor
	 */
	public function __construct() {
	}

	/*
	 * Load scripts
	 */
	public function encryption_script() {
		global $post;

		// The main AES encryption script which does all the grunt work
		wp_enqueue_script(
			'aes-encryption',
			plugin_dir_url( __FILE__ ) . 'js/aes-encryption.js',
			array( 'jquery' ),
			'1.0',
			true
		);

		// Implement end to end encryption
		wp_enqueue_script(
			'end-to-end-init',
			plugin_dir_url( __FILE__ ) . 'js/init.js',
			array( 'jquery', 'aes-encryption' ),
			'1.0',
			true
		);

		// If encryption has been set previously, then set variable so that JS knows what to do
		if ( true == get_post_meta( $post->ID, '_secure_content' ) ) {
			wp_localize_script( 'aes-encryption', 'encryption_set', '1' );
		}
	}

}
new Secure_Content;


/**
 * Secure Content Frontend
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Secure_Content_Frontend extends Secure_Content {

	/**
	 * Class constructor
	 */
	public function __construct() {
		remove_filter ('the_content','wpautop');
		add_filter( 'the_content', array( $this, 'the_content' ) );
	}

	function the_content( $content ) {		
		// If encryption has been set previously, then set variable so that JS knows what to do
		if ( true != get_post_meta( get_the_ID(), '_secure_content' ) ) {
			return $content;
		}

		// If encryption has been set previously, then set variable so that JS knows what to do
		if ( true == get_post_meta( get_the_ID(), '_secure_content' ) ) {
			$this->encryption_script();
			wp_localize_script( 'aes-encryption', 'encryption_set', '1' );
			wp_localize_script( 'aes-encryption', 'encryption_frontend', '1' );

			// Need to be on single post page to decrypt (avoids needing to deal with decrypting multiple boxes on same page)
			if ( ! is_singular() ) {
				return __( 'Encrypted content, please visit single post to decrypt', 'secure-content' );
			}
		}

		$content = '
		<form id="secure-content">
			<label for="encryption-key">Please the encryption key</label>
			<input id="encryption-key" type="password"/>

			<div id="secure-content-text">' . esc_html( $content ) . '</div>
			<div style="display:none" id="secure-content-temporary-storage">' . esc_html( $content ) . '</div>
		</form>
		';

		return $content;
	}

}
new Secure_Content_Frontend();


/**
 * Secure Content Admin
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Secure_Content_Admin extends Secure_Content {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts',       array( $this, 'scripts' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'form_fields' ) );
		add_action( 'save_post',                   array( $this, 'save_post' ) );
	}

	/*
	 * Load scripts
	 */
	public function scripts( $hook ) {
		global $post;

		// Only show scripts on post editing pages
	    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
	    	$this->encryption_script();
		}
	}

	public function form_fields() {
		global $post;

		wp_nonce_field( 'secure_content', 'secure_content_nonce' );

		echo '<div class="misc-pub-section misc-pub-section-last">';
		echo '<label>Enter encryption key</label> ';
		echo '<input type="password" id="encryption-key" name="encryption-key" />';
		echo '<div style="display:none" id="secure-content-temporary-storage"></div>';
		echo '</div>';
	}

	/*
	 * Store whether post is encrypted or not
	 */
	public function save_post( $post_id ) {

		// Bail out now if nonce doesn't verify
		if ( isset( $_POST['secure_content_nonce'] ) && ! wp_verify_nonce( $_POST['secure_content_nonce'], 'secure_content' ) ) {
			return $post_id;
		}

		// Set post meta if encryption key set, otherwise delete it
		if ( isset( $_POST['encryption-key'] ) && '' != $_POST['encryption-key'] ) {
			update_post_meta( $post_id, '_secure_content', true );
		} else {
			delete_post_meta( $post_id, '_secure_content' );
		}

		return $post_id;
	}

}
new Secure_Content_Admin();
