<?php

/**
 * End to End Frontend
 * 
 * @copyright Copyright (c), Ryan Hellyerp_loca
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class End2End_Frontend extends End2End {

	/**
	 * Class constructor
	 */
	public function __construct() {
		parent::__construct(); 
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
			wp_localize_script( 'aes-encryption', 'end2end_set', '1' );
			wp_localize_script( 'aes-encryption', 'end2end_frontend', '1' );

			// Need to be on single post page to decrypt (avoids needing to deal with decrypting multiple boxes on same page)
			if ( ! is_singular() ) {
				return __( 'Encrypted content, please visit single post to decrypt', 'end2end' );
			}
		} else {
			return $content;
		}

		$content = '
		<noscript>
			<p>' . esc_html( $this->no_script_message ) . '</p>
		</noscript>
		<div id="end2end-frontend-form"></div>
		<div id="end2end-text">' . esc_html( $content ) . '</div>
		<div style="display:none" id="end2end-temporary-storage">' . esc_html( $content ) . '</div>
		<script>

		</script>
		';

		return $content;
	}

}
new End2End_Frontend();
