<?php
/**
 * Plugin Name:       Polylang Language Sync for Trustindex Widgets
 * Plugin URI:        https://github.com/adrianbellrodriguez/polylang-trustindex-sync
 * Description:       Automatically syncs the active Polylang language with any Trustindex Reviews widget (Airbnb, Google, Booking, Tripadvisor, Yelp and more), so that all widget texts are always displayed in the language the visitor is browsing in — no manual configuration needed.
 * Version:           1.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Adrián Bell Rodríguez
 * Author URI:        https://github.com/adrianbellrodriguez
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       polylang-trustindex-sync
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'PTA_VERSION',     '1.2.0' );
define( 'PTA_PLUGIN_FILE', __FILE__ );
define( 'PTA_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );

require_once PTA_PLUGIN_DIR . 'includes/class-pta-language-sync.php';

/**
 * Boots the plugin after all plugins are loaded,
 * so Polylang and Trustindex are guaranteed to be available.
 */
add_action( 'plugins_loaded', function () {
    PTA_Language_Sync::get_instance();
} );
