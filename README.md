# Polylang Language Sync for Trustindex Widgets

Automatically syncs the active Polylang language with any Trustindex Reviews widget (Airbnb, Google, Booking, Tripadvisor, Yelp and more), so that all widget texts are always displayed in the language the visitor is browsing in — no manual configuration needed.

Supports **auto-updates from GitHub** via [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker).

---

## Installation

### 1. Download the plugin ZIP from GitHub Releases

Go to [Releases](https://github.com/adrianbellrodriguez/polylang-trustindex-sync/releases) and download the latest `polylang-trustindex-sync.zip`.

### 2. Install the plugin

In your WordPress admin dashboard, go to **Plugins → Add New → Upload Plugin**, then upload the `polylang-trustindex-sync.zip` file.

### 3. Activate the plugin

Go to **Plugins → Installed Plugins** and activate *Polylang Language Sync for Trustindex Widgets*.

From now on, whenever you publish a new GitHub Release, WordPress will show the update notification automatically.

---

## How it works

| Hook | What it does |
|---|---|
| `option_trustindex-{platform}-lang` | Returns the Polylang language to Trustindex's PHP render instead of the DB value |
| `do_shortcode_tag` | Removes `data-no-translation="true"` and injects `data-language="XX"` so the JS loader also translates correctly |
| `init` | Flushes Trustindex's cached HTML templates when the active language changes |

---

## Supported platforms

| Platform | WordPress.org plugin |
|---|---|
| Airbnb | [review-widgets-for-airbnb](https://wordpress.org/plugins/review-widgets-for-airbnb/) |
| AliExpress | [widgets-for-aliexpress-reviews](https://wordpress.org/plugins/widgets-for-aliexpress-reviews/) |
| Alibaba | [widgets-for-alibaba-reviews](https://wordpress.org/plugins/widgets-for-alibaba-reviews/) |
| Amazon | [review-widgets-for-amazon](https://wordpress.org/plugins/review-widgets-for-amazon/) |
| Booking.com | [review-widgets-for-booking-com](https://wordpress.org/plugins/review-widgets-for-booking-com/) |
| Capterra | [review-widgets-for-capterra](https://wordpress.org/plugins/review-widgets-for-capterra/) |
| Ebay | [widgets-for-ebay-reviews](https://wordpress.org/plugins/widgets-for-ebay-reviews/) |
| Expedia | [review-widgets-for-expedia](https://wordpress.org/plugins/review-widgets-for-expedia/) |
| Facebook | [free-facebook-reviews-and-recommendations-widgets](https://wordpress.org/plugins/free-facebook-reviews-and-recommendations-widgets/) |
| Foursquare | [review-widgets-for-foursquare](https://wordpress.org/plugins/review-widgets-for-foursquare/) |
| Google | [wp-reviews-plugin-for-google](https://wordpress.org/plugins/wp-reviews-plugin-for-google/) |
| Hotels.com | [review-widgets-for-hotels-com](https://wordpress.org/plugins/review-widgets-for-hotels-com/) |
| Opentable | [reviews-widgets-for-opentable](https://wordpress.org/plugins/reviews-widgets-for-opentable/) |
| SourceForge | [widgets-for-sourceforge-reviews](https://wordpress.org/plugins/widgets-for-sourceforge-reviews/) |
| Thumbtack | [widgets-for-thumbtack-reviews](https://wordpress.org/plugins/widgets-for-thumbtack-reviews/) |
| Tripadvisor | [review-widgets-for-tripadvisor](https://wordpress.org/plugins/review-widgets-for-tripadvisor/) |
| Yelp | [reviews-widgets-for-yelp](https://wordpress.org/plugins/reviews-widgets-for-yelp/) |
| Zillow | [widgets-for-zillow-reviews](https://wordpress.org/plugins/widgets-for-zillow-reviews/) |
| WordPress | [reviews-widgets](https://wordpress.org/plugins/reviews-widgets/) |
| WooCommerce | [customer-reviews-for-woocommerce](https://wordpress.org/plugins/customer-reviews-for-woocommerce/) |

---

## Custom language slug mapping

```php
add_filter( 'pta_language_map', function( $map ) {
    $map['valenciano'] = 'ca';
    return $map;
} );
```

---

## Requirements

- WordPress 6.2+
- PHP 7.4+
- [Polylang](https://wordpress.org/plugins/polylang/) (free or Pro)
- One or more Trustindex Reviews widget plugins

---

## Changelog

### 1.1.0
- Added auto-update support from GitHub via Plugin Update Checker (PUC v5).
- Added translations for all 49 Trustindex-supported languages (.po + .mo files).
- Plugin description now appears translated in every language in the WordPress Plugins screen.
- Plugin folder slugs corrected to match official WordPress.org plugin directory URLs.

### 1.0.0
- Initial release — multi-platform detection, Polylang language sync for PHP render and JS loader.

---

## Author

**Adrián Bell Rodríguez** — [github.com/adrianbellrodriguez](https://github.com/adrianbellrodriguez)

## License

GPL-2.0-or-later
