<?php

/**
 * End to End Frontend
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class End2End_Frontend extends End2End {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'filters' ) );
	}

	/*
	 * Add filter to provide content encryption
	 * Remove wpautop() filter due to it preventing the decryption from working
	 */
	public function filters() {

		// If encryption has been set previously, then set variable so that JS knows what to do
		if ( true != get_post_meta( get_the_ID(), '_end2end' ) ) {
			return;
		}

		remove_all_filters( 'the_content' );
		add_filter( 'the_content', array( $this, 'the_content' ) );
	}

	/*
	 * If on an encrypted post, then load required JavaScript
	 * Display encryption key field
	 *
	 * @param string $content The post content
	 * @return string
	 */
	public function the_content( $content ) {		

		// If encryption has been set previously, then set variable so that JS knows what to do
		if ( true == get_post_meta( get_the_ID(), '_end2end' ) ) {
			$this->encryption_script();
			wp_localize_script( 'aes-encryption', 'encryption_set', '1' );
			wp_localize_script( 'aes-encryption', 'encryption_frontend', '1' );

			// Need to be on single post page to decrypt (avoids needing to deal with decrypting multiple boxes on same page)
			if ( ! is_singular() ) {
				return __( 'Encrypted content, please visit single post to decrypt', 'end2end' );
			}
		}

		$content = '
		<form id="secure-content">
			<p>
				<label for="encryption-key">' . __( 'Please enter the encryption key', 'end2end' ) . '</label>
				<input id="encryption-key" type="password"/>
			</p>

			<div id="secure-content-text">' . esc_html( $content ) . '</div>
			<div style="display:none" id="secure-content-temporary-storage">' . esc_html( $content ) . '</div>
		</form>
		';

		return $content;
	}

}
new End2End_Frontend();
