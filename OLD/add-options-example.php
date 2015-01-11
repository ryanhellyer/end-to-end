<?php
/*
Plugin Name: Add Options Example
Plugin URI: http://geek.hellyer.kiwi/
Description: Adds an example options page with fancy sortable list, uploader etc.

Author: Ryan Hellyer
Version: 1.0
Author URI: http://geek.hellyer.kiwi/

Copyright 2014 Ryan Hellyer

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
 * Example Options Page with rows
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.0
 */
class Example_Options_Page_With_Rows {

	/**
	 * Set some constants for setting options.
	 */
	const MENU_SLUG = 'example-page';
	const GROUP     = 'example-group';
	const OPTION    = 'example-option';

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {

		// Add to hooks
		add_action( 'admin_init',    array( $this, 'register_settings' ) );
		add_action( 'admin_menu',    array( $this, 'create_admin_page' ) );
		add_action( 'admin_footer',  array( $this, 'scripts' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

//		add_action( 'plugins_loaded',        array( $this, 'ajax_file_upload' ) );
//		add_action( 'plugins_loaded',        array( $this, 'get_time' ) );
	}

	public function ajax_file_upload() {
		if ( isset( $_GET['ajax_file_upload'] ) ) {

			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			$uploadedfile = $_FILES['file'];
			$upload_overrides = array( 'test_form' => false );
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

			ob_start();
			print_r( $_FILES );
			$files = ob_get_content();
			ob_end_clean();
			$time = time();
			update_option( 'bla', $files . "\n\n" . $time );
			echo get_option( 'bla' );
			exit;
		}
	}

	public function get_time() {
		if ( isset( $_GET['get_time'] ) ) {
			echo get_option( 'bla' );
			exit;
		}
	}

	/**
	 * Init plugin options to white list our options.
	 */
	public function register_settings() {
		register_setting(
			self::GROUP,               // The settings group name
			self::OPTION,              // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);
	}

	/**
	 * Create the page and add it to the menu.
	 */
	public function create_admin_page() {
		add_options_page(
			__ ( 'Example admin page', 'plugin-slug' ), // Page title
			__ ( 'Example page', 'plugin-slug' ),       // Menu title
			'manage_options',                           // Capability required
			self::MENU_SLUG,                            // The URL slug
			array( $this, 'admin_page' )                // Displays the admin page
		);
	}

	/**
	 * Output the admin page.
	 */
	public function admin_page() {

		?>
		<div class="wrap">
			<h2><?php _e( 'Example admin page', 'plugin-slug' ); ?></h2>
			<p><?php _e( 'Place a description of what the admin page does here to help users make better use of the admin page.', 'plugin-slug' ); ?></p>

			<form method="post" action="options.php">

				<table class="wp-list-table widefat plugins">
					<thead>
						<tr>
							<th class='check-column'>
								<label class="screen-reader-text" for="cb-select-all-1">Alle auswählen</label>
								<input id="cb-select-all-1" type="checkbox" />
							</th>
							<th class='column-author'>
								Autor
							</th>
							<th class='column-author'>
								Autor
							</th>
							<th class='column-author'>
								Autor
							</th>
							<th class='column-author'>
								Autor
							</th>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<th class='check-column'>
								<label class="screen-reader-text" for="cb-select-all-1">Alle auswählen</label>
								<input id="cb-select-all-1" type="checkbox" />
							</th>
							<th class='column-author'>
								Autor
							</th>
							<th class='column-author'>
								Autor
							</th>
							<th class='column-author'>
								Autor
							</th>
							<th class='column-author'>
								Autor
							</th>
						</tr>
					</tfoot>

					<tbody id="add-rows"><?php

					// Grab options array and output a new row for each setting
					$options = get_option( self::OPTION );
					if ( is_array( $options ) ) {
						foreach( $options as $key => $value ) {
							echo $this->get_row( $value );
						}
					}

					// Add a new row by default
					echo $this->get_row();
					?>
					</tbody>
				</table>

				<input type="button" id="add-new-row" value="<?php _e( 'Add new row', 'plugin-slug' ); ?>" />

				<?php settings_fields( self::GROUP ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'plugin-slug' ); ?>" />
				</p>
			</form>

		</div><?php
	}

	/**
	 * Get a single table row.
	 * 
	 * @param  string  $value  Option value
	 * @return string  The table row HTML
	 */
	public function get_row( $value = '' ) {

		if ( ! is_array( $value ) ) {
			$value = array();
		}

		if ( ! isset( $value['title'] ) ) {
			$value['title'] = '';
		}

		if ( ! isset( $value['file'] ) ) {
			$value['file'] = '';
		}

		// Create the required HTML
		$row_html = '

					<tr class="sortable inactive">
						<th>
							<label>' . __( 'Enter your input string.', 'plugin-slug' ) . '</label>
						</th>
						<td>
							<input type="text" name="' . esc_attr( self::OPTION ) . '[][title]" value="' . esc_attr( $value['title'] ) . '" />
							<span class="read-more-text"><br />some text goes here</span>
						</td>
						<td>
							<input class="file-upload" type="file" name="' . esc_attr( self::OPTION ) . '[][file]" />
							<span class="read-more-text"><br />' . __( 'Upload file', 'plugin-slug' ) . '</span>
						</td>
					</tr>';

		// Strip out white space (need on line line to keep JS happy)
		$row_html = str_replace( '	', '', $row_html );
		$row_html = str_replace( "\n", '', $row_html );

		// Return the final HTML
		return $row_html;
	}

	/**
	 * Sanitize the page or product ID.
	 *
	 * @param   array   $input   The input string
	 * @return  array            The sanitized string
	 */
	public function sanitize( $input ) {
		$output = array();

		// Loop through each bit of data
		foreach( $input as $key => $value ) {

			// Sanitize input data
			$sanitized_key   = absint( $key );
			if ( isset( $value['title'] ) ) {
				$sanitized_value['title'] = wp_kses_post( $value['title'] );
			}
			if ( isset( $value['file'] ) ) {
				$sanitized_value['file'] = wp_kses_post( $value['file'] );
			}

			// Put sanitized data in output variable
			$output[$sanitized_key] = $sanitized_value;

		}

		// Return the sanitized data
		return $output;
	}

	/**
	 * Output scripts into the footer.
	 * This is not best practice, but is implemented like this here to ensure that it can fit into a single file.
	 */
	public function scripts() {
		?>
<style>
.read-more-text {
	display: none;
}
.sortable .toggle {
	display: inline !important;
}
</style>
		<script>

			jQuery(function($){ 

				/**
				 * Adding some buttons
				 */
				function add_buttons() {

					// Loop through each row
					$( ".sortable" ).each(function() {

						// If no input field found with class .remove-setting, then add buttons to the row
						if(!$(this).find('input').hasClass('remove-setting')) {

							// Add a remove button
							$(this).append('<td><input type="button" class="remove-setting" value="X" /></td>');

							// Add read more button
							$(this).append('<td><input type="button" class="read-more" value="More" /></td>');

							// Remove button functionality
							$('.remove-setting').click(function () {
								$(this).parent().parent().remove();
							});

							// Read more button functionality
							$('.read-more-text').css('display','none');
							$(this).find(".read-more").click(function(){
								$(this).parent().parent().find('.read-more-text').toggleClass('toggle');
							});

						}

					});

				}

				// Create the required HTML (this should be added inline via wp_localize_script() once JS is abstracted into external file)
				var html = '<?php echo $this->get_row( '' ); ?>';

				// Add the buttons
				add_buttons();

				// Add a fresh row on clicking the add row button
				$( "#add-new-row" ).click(function() {
					$( "#add-rows" ).append( html ); // Add the new row
					add_buttons(); // Add buttons tot he new row
				});

				// Allow for resorting rows
				$('#add-rows').sortable({
					axis: "y", // Limit to only moving on the Y-axis
				});

 			});

		</script><?php
	}

	/**
	 * Registers the JavaScript for handling the media uploader.
	 *
	 * @since 1.3
	 */
	public function enqueue_scripts( $hook ) {

		// Bail out now if not on correct settings page
		if ( 'settings_page_example-page' != $hook ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'custom',
			plugin_dir_url( __FILE__ ) . 'assets/admin.js',
			array( 'jquery' ),
			'1.0',
			'all'
		);

		wp_enqueue_script(
			'ajax-file-upload',
			plugin_dir_url( __FILE__ ) . 'assets/jquery.ajaxfileupload.js',
			array( 'jquery' ),
			'1.0',
			'all'
		);

		wp_enqueue_script(
			'file-upload',
			plugin_dir_url( __FILE__ ) . 'assets/file-upload.js',
			array( 'ajax-file-upload' ),
			'1.0',
			'all'
		);

		wp_localize_script( 'custom', 'custom_meta_image_name', 'custom' );

		wp_localize_script(
			'file-upload', // Enqueued script
			'test_url_submit', // JS variable
//			'http://local.wordpress-trunk.dev/unique-headers/mu-plugins/test/test.php'
			home_url( '?ajax_file_upload=true' ) // Submit URL
		);

	}

	/**
	 * Registers the stylesheets for handling the meta box
	 *
	 * @since 1.3
	 */
	public function enqueue_styles( $hook ) {

		// Bail out now if not on correct settings page
		if ( 'settings_page_example-page' != $hook ) {
			return;
		}

		wp_enqueue_style(
			'custom',
			plugin_dir_url( __FILE__ ) . 'assets/admin.css',
			array()
		);

	}

}
new Example_Options_Page_With_Rows();
