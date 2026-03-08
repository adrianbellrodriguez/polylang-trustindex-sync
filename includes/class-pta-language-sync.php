<?php
/**
 * Core class — wires all filters and actions for every active Trustindex plugin.
 *
 * All Trustindex "Widgets for X Reviews" plugins share the same architecture:
 *
 *   • Active plugin path: {folder}/{folder}.php
 *   • DB option:          trustindex-{platform}-lang
 *   • Shortcode:          trustindex-widget-{platform}
 *   • Cache option:       trustindex-{platform}-review-content
 *
 * This class auto-detects active Trustindex plugins at runtime and registers
 * the appropriate hooks for each one automatically.
 *
 * @package Polylang_Trustindex_Sync
 */

defined( 'ABSPATH' ) || exit;

class PTA_Language_Sync {

    /** @var PTA_Language_Sync|null */
    private static $instance = null;

    /**
     * Maps WordPress plugin folder names → Trustindex internal platform slug.
     *
     * Folder names extracted from the official WordPress.org plugin URLs.
     * Slugs verified against the TrustindexPlugin_{slug} class instantiation
     * in each plugin's main file.
     *
     * @var array<string,string>  folder => slug
     */
    private static $plugin_map = [
        // ── Review platforms ──────────────────────────────────────────────────
        'review-widgets-for-airbnb'                        => 'airbnb',
        'widgets-for-aliexpress-reviews'                   => 'aliexpress',
        'widgets-for-alibaba-reviews'                      => 'alibaba',
        'review-widgets-for-amazon'                        => 'amazon',
        'review-widgets-for-booking-com'                   => 'booking',
        'review-widgets-for-capterra'                      => 'capterra',
        'widgets-for-ebay-reviews'                         => 'ebay',
        'review-widgets-for-expedia'                       => 'expedia',
        'free-facebook-reviews-and-recommendations-widgets'=> 'facebook',
        'review-widgets-for-foursquare'                    => 'foursquare',
        'wp-reviews-plugin-for-google'                     => 'google',
        'review-widgets-for-hotels-com'                    => 'hotels',
        'reviews-widgets-for-opentable'                    => 'opentable',
        'widgets-for-sourceforge-reviews'                  => 'sourceforge',
        'widgets-for-thumbtack-reviews'                    => 'thumbtack',
        'review-widgets-for-tripadvisor'                   => 'tripadvisor',
        'reviews-widgets-for-yelp'                         => 'yelp',
        'widgets-for-zillow-reviews'                       => 'zillow',
        'reviews-widgets'                                  => 'wordpress',
        // ── WooCommerce ───────────────────────────────────────────────────────
        'customer-reviews-for-woocommerce'                 => 'woocommerce',
        'customer-reviews-collector-for-woocommerce'       => 'woocommerce',
        // ── Alternative Tripadvisor plugin ────────────────────────────────────
        'wp-tripadvisor-review-widgets'                    => 'tripadvisor',
    ];

    /**
     * Platforms detected as active on this WordPress install.
     *
     * @var string[]
     */
    private $active_platforms = [];

    /** Singleton */
    public static function get_instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->active_platforms = $this->detect_active_platforms();

        if ( empty( $this->active_platforms ) ) {
            return;
        }

        // 1. Filter every active platform's DB lang option.
        foreach ( $this->active_platforms as $platform ) {
            add_filter(
                "option_trustindex-{$platform}-lang",
                [ $this, 'override_lang_option' ]
            );
        }

        // 2. Fix HTML output for all Trustindex shortcodes.
        add_filter( 'do_shortcode_tag', [ $this, 'fix_shortcode_html' ], 10, 2 );

        // 3. Flush cached review-content templates on language change.
        add_action( 'init', [ $this, 'maybe_flush_template_cache' ] );
    }

    // -------------------------------------------------------------------------
    // Platform discovery
    // -------------------------------------------------------------------------

    /**
     * Scans the WordPress active plugins list against the known plugin map
     * and returns the Trustindex platform slugs currently active.
     *
     * Falls back to checking DB option names if folder scanning finds nothing.
     *
     * @return string[]
     */
    private function detect_active_platforms(): array {
        $found = [];

        $active_plugins = (array) get_option( 'active_plugins', [] );

        if ( is_multisite() ) {
            $network        = array_keys( (array) get_site_option( 'active_sitewide_plugins', [] ) );
            $active_plugins = array_merge( $active_plugins, $network );
        }

        foreach ( $active_plugins as $plugin_path ) {
            $folder = strtok( $plugin_path, '/' );
            if ( isset( self::$plugin_map[ $folder ] ) ) {
                $found[] = self::$plugin_map[ $folder ];
            }
        }

        // Fallback: check DB options in case folder names differ.
        if ( empty( $found ) ) {
            foreach ( array_unique( array_values( self::$plugin_map ) ) as $slug ) {
                if ( false !== get_option( "trustindex-{$slug}-lang", false ) ) {
                    $found[] = $slug;
                }
            }
        }

        return array_unique( $found );
    }

    // -------------------------------------------------------------------------
    // 1. DB option filter
    // -------------------------------------------------------------------------

    /**
     * Returns the Polylang language code instead of the DB value so that
     * Trustindex's PHP render uses the visitor's current language.
     *
     * @param  mixed $saved_lang
     * @return mixed
     */
    public function override_lang_option( $saved_lang ) {
        if ( is_admin() ) {
            return $saved_lang;
        }
        $lang = $this->get_polylang_language();
        return ! empty( $lang ) ? $lang : $saved_lang;
    }

    // -------------------------------------------------------------------------
    // 2. Shortcode HTML filter
    // -------------------------------------------------------------------------

    /**
     * Processes the HTML output of any Trustindex shortcode:
     *   a) Removes data-no-translation="true" so the JS loader re-translates texts.
     *   b) Injects / updates data-language="XX" on the loader <div>.
     *
     * @param  string $output
     * @param  string $tag
     * @return string
     */
    public function fix_shortcode_html( string $output, string $tag ): string {
        $is_trustindex = ( $tag === 'trustindex-widget' )
            || ( strpos( $tag, 'trustindex-widget-' ) === 0 );

        if ( ! $is_trustindex || is_admin() ) {
            return $output;
        }

        if ( strpos( $output, 'cdn.trustindex.io' ) === false ) {
            return $output;
        }

        $lang = $this->get_polylang_language();
        if ( empty( $lang ) ) {
            return $output;
        }

        $output = preg_replace( '/\s*data-no-translation=["\']true["\']/i', '', $output );

        $output = preg_replace_callback(
            '/(<div\b[^>]*data-src="https:\/\/cdn\.trustindex\.io\/[^"]*"[^>]*)(>)/i',
            function ( array $matches ) use ( $lang ): string {
                if ( strpos( $matches[1], 'data-language' ) !== false ) {
                    return preg_replace(
                        '/data-language=["\'][^"\']*["\']/i',
                        'data-language="' . esc_attr( $lang ) . '"',
                        $matches[0]
                    );
                }
                return $matches[1] . ' data-language="' . esc_attr( $lang ) . '"' . $matches[2];
            },
            $output
        );

        return $output;
    }

    // -------------------------------------------------------------------------
    // 3. Template cache flush
    // -------------------------------------------------------------------------

    /**
     * Deletes the cached widget HTML template for every active platform whenever
     * the Polylang language changes, forcing Trustindex to regenerate it.
     */
    public function maybe_flush_template_cache(): void {
        if ( is_admin() || ! function_exists( 'pll_current_language' ) ) {
            return;
        }

        $lang = $this->get_polylang_language();
        if ( empty( $lang ) ) {
            return;
        }

        $cached_lang = get_option( 'pta_cached_lang', '' );

        if ( $cached_lang !== $lang ) {
            foreach ( $this->active_platforms as $platform ) {
                delete_option( "trustindex-{$platform}-review-content" );
            }
            update_option( 'pta_cached_lang', $lang, false );
        }
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    /**
     * Returns the active Polylang language as a 2-letter ISO 639-1 code,
     * or null if Polylang is not available.
     *
     * Custom slug mappings via the 'pta_language_map' filter:
     *
     *   add_filter( 'pta_language_map', function( $map ) {
     *       $map['valenciano'] = 'ca';
     *       return $map;
     *   } );
     *
     * @return string|null
     */
    private function get_polylang_language(): ?string {
        if ( ! function_exists( 'pll_current_language' ) ) {
            return null;
        }

        $pll_lang = pll_current_language( 'slug' );

        if ( empty( $pll_lang ) ) {
            return null;
        }

        /** @var array<string,string> $map */
        $map = apply_filters( 'pta_language_map', [] );

        if ( isset( $map[ $pll_lang ] ) ) {
            return $map[ $pll_lang ];
        }

        return strlen( $pll_lang ) === 2 ? $pll_lang : substr( $pll_lang, 0, 2 );
    }
}
