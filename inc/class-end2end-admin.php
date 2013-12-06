<?php

/**
 * End to End Admin
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class End2End_Admin extends End2End {

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

		// Only show scripts on post editing pages
	    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
	    	$this->encryption_script();
		}
	}

	/*
	 * The form fields above the publish button
	 */
	public function form_fields() {
		wp_nonce_field( 'end2end', 'end2end_nonce' );

		echo '<div class="misc-pub-section misc-pub-section-last">';
		echo '<label>Enter encryption key</label> ';
		echo '<input type="password" id="encryption-key" name="encryption-key" />';
		echo '<div style="display:none" id="secure-content-temporary-storage"></div>';
		echo '</div>';
	}

	/*
	 * Store whether post is encrypted or not
	 *
	 * @param int $post_id The posts ID
	 * @return int The posts ID
	 */
	public function save_post( $post_id ) {

		// Bail out now if autosave is attempting to do it's thing (it took me over an hour to realise this was what was causing my encryptions to become unset :/)
		if ( defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Bail out now if nonce isn't sent
		if ( isset( $_POST['end2end_nonce'] ) ) {
			return;
		}

		// Bail out now if nonce doesn't verify
		if ( ! wp_verify_nonce( $_POST['end2end_nonce'], 'end2end' ) ) {
			return $post_id;
		}

		// Set post meta if encryption key set, otherwise delete it
		if ( isset( $_POST['encryption-key'] ) && '' != $_POST['encryption-key'] ) {
			update_post_meta( $post_id, '_end2end', true );
		} else {
			delete_post_meta( $post_id, '_end2end' );
		}

		return $post_id;
	}

}
new End2End_Admin();
