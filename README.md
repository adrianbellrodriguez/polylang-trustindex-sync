# Polylang Language Sync for Trustindex Widgets

> Automatically displays any Trustindex Reviews widget in the language the visitor is browsing — powered by Polylang.

![Plugin icon](assets/icon.svg)

## The problem

Trustindex widgets store their display language as a single WordPress option. On a multilingual site powered by [Polylang](https://wordpress.org/plugins/polylang/), the widget always shows in the language last saved in its settings — regardless of what language the visitor is browsing in.

This causes mixed-language output: texts rendered server-side (e.g. *"A base de 11 reseñas"*, *"Leer más"*) appear in a different language than those rendered by the JS loader (e.g. *"EXCELLENT"*).

## How it works

The plugin auto-detects all active Trustindex plugins and hooks into three places for each one:

| Hook | What it does |
|---|---|
| `option_trustindex-{platform}-lang` | Returns the Polylang language to Trustindex's PHP render instead of the DB value |
| `do_shortcode_tag` | Removes `data-no-translation="true"` and injects `data-language="XX"` so the JS loader also translates correctly |
| `init` | Flushes Trustindex's cached HTML templates when the active language changes |

## Supported platforms

| Platform | Plugin folder |
|---|---|
| Airbnb | `review-widgets-for-airbnb` |
| AliExpress | `review-widgets-for-aliexpress` |
| Alibaba | `review-widgets-for-alibaba` |
| Amazon | `review-widgets-for-amazon` |
| Bookatable | `review-widgets-for-bookatable` |
| Booking.com | `review-widgets-for-booking` |
| Capterra | `review-widgets-for-capterra` |
| Expedia | `review-widgets-for-expedia` |
| Facebook | `review-widgets-for-facebook` |
| Foursquare | `review-widgets-for-foursquare` |
| Google | `review-widgets-for-google` |
| Hotels.com | `review-widgets-for-hotels` |
| Opentable | `review-widgets-for-opentable` |
| Thumbtack | `review-widgets-for-thumbtack` |
| Tripadvisor | `review-widgets-for-tripadvisor` / `wp-tripadvisor-review-widgets` |
| Yelp | `review-widgets-for-yelp` |
| Zillow | `review-widgets-for-zillow` |
| WordPress | `review-widgets-for-wordpress` |

## Requirements

- WordPress 6.2+
- PHP 7.4+
- [Polylang](https://wordpress.org/plugins/polylang/) (free or Pro)
- One or more [Trustindex](https://wordpress.org/plugins/search/trustindex/) Reviews widget plugins

## Installation

1. Upload the `polylang-trustindex-sync` folder to `/wp-content/plugins/`.
2. Activate the plugin from **Plugins → Installed Plugins**.
3. No configuration needed — active Trustindex plugins are detected automatically.

## Custom language slug mapping

If your Polylang language slugs are not standard 2-letter ISO codes, map them in your theme's `functions.php`:

```php
add_filter( 'pta_language_map', function( $map ) {
    $map['valenciano'] = 'ca';
    $map['por']        = 'pt';
    return $map;
} );
```

## Changelog

### 1.0.0
- Initial release.

## Author

**Adrián Bell Rodríguez** — [github.com/adrianbellrodriguez](https://github.com/adrianbellrodriguez)

## License

GPL-2.0-or-later — see [LICENSE](LICENSE) file.
