<?php
/**
 * Plugin Name:       Polylang Language Sync for Trustindex Widgets
 * Plugin URI:        https://github.com/adrianbellrodriguez/polylang-trustindex-sync
 * Description:       Automatically syncs the active Polylang language with any Trustindex Reviews widget (Airbnb, Google, Booking, Tripadvisor, Yelp and more), so that all widget texts are always displayed in the language the visitor is browsing in — no manual configuration needed.
 * Version:           1.1.0
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

define( 'PTA_VERSION',     '1.1.0' );
define( 'PTA_PLUGIN_FILE', __FILE__ );
define( 'PTA_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );

// ── i18n ─────────────────────────────────────────────────────────────────────
// Loads the .mo file for the current WordPress locale from /languages/.
// This makes the plugin description (and any future strings) translatable
// in the Plugins list screen of the WordPress dashboard.
add_action( 'plugins_loaded', function () {
    load_plugin_textdomain(
        'polylang-trustindex-sync',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
});

// ── Auto-updates from GitHub via Plugin Update Checker ───────────────────────
// Requires vendor/plugin-update-checker/ — see README.md for setup.
$puc_autoload = PTA_PLUGIN_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';
if ( file_exists( $puc_autoload ) ) {
    require_once $puc_autoload;

    $updateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/adrianbellrodriguez/polylang-trustindex-sync/',
        __FILE__,
        'polylang-trustindex-sync'
    );

    $updateChecker->getVcsApi()->enableReleaseAssets();
}
// ─────────────────────────────────────────────────────────────────────────────

require_once PTA_PLUGIN_DIR . 'includes/class-pta-language-sync.php';

add_action( 'plugins_loaded', function () {
    PTA_Language_Sync::get_instance();
} );
