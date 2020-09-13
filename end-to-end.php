<?php
/*
Plugin Name: WP End-to End
Plugin URI: http://geek.ryanhellyer.net/products/end-to-end/
Description: Provides true end to end encryption in WordPress

Author: Ryan Hellyer
Version: 1.2
Author URI: http://geek.ryanhellyer.net/

Copyright 2013 Ryan Hellyer

The encryption functionality is from Chris Veness (http://www.movable-type.co.uk/scripts/aes.html)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/



/*
 * Set plugin folder URL
 */
define( 'END2END_URL', plugin_dir_url( __FILE__ ) );

/*
 * Load and instantiate the classes
 */
require( 'inc/class-end2end.php' );
if ( is_admin() ) {
	require( 'inc/class-end2end-admin.php' );	
} else {
	require( 'inc/class-end2end-frontend.php' );
}
