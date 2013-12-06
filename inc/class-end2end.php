<?php

/**
 * End to End
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class End2End {

	/*
	 * Load scripts
	 *
	 * @global object $post The primary post object
	 */
	public function encryption_script() {
		global $post;

		// The main AES encryption script which does all the grunt work
		wp_enqueue_script(
			'aes-encryption',
			END2END_URL . 'js/aes-encryption.js',
			array( 'jquery' ),
			'1.0',
			true
		);

		// Implement end to end encryption
		wp_enqueue_script(
			'end-to-end-init',
			END2END_URL . 'js/init.js',
			array( 'jquery', 'aes-encryption' ),
			'1.0',
			true
		);

		// If encryption has been set previously, then set variable so that JS knows what to do
		if ( true == get_post_meta( $post->ID, '_end2end' ) ) {
			wp_localize_script( 'aes-encryption', 'encryption_set', '1' );
		}
	}

}
new End2End;
