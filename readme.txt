=== Opiner Me ===
Plugin URI: https://opiner.me
Author: eeqsoft
Author URI: https://www.eeqsoft.com
Contributors: eeqsoft
Donate link: https://www.paypal.me/WEBEEQ
Tags: rating, star-rating, reviews, review-form, json-ld
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Modular opinion form with star rating, shortcode support, built-in spam protection, and JSON-LD schema markup.

== Description ==
Opiner Me – Feedback & Star Rating Plugin

Opiner Me is a lightweight WordPress plugin for adding star ratings, collecting user reviews, and displaying a simple opinion form anywhere on your site. It includes SEO-friendly JSON-LD review schema, built-in spam protection, and clean shortcodes for maximum flexibility.

Perfect for blogs, service pages, portfolios, and any website that needs quick, minimal, and fast-loading feedback tools.

Official website: <a href="https://opiner.me">Opiner Me WP Plugin</a>
Plugin live demo: <a href="https://opiner.me/live-demo">Check it out here</a>
Installation video: <a href="https://www.youtube.com/watch?v=xW6uvR4Z0Y4">Watch on YouTube</a>

== Features ==
* Lightweight star rating and review plugin for WordPress
* Opinion and feedback form that fits any theme
* Simple shortcodes to display form, rating, reviews, and schema
* Built-in spam protection via SpamGuard module
* JSON-LD review schema for better SEO and rich results
* Clean admin panel to manage user opinions and ratings
* No external scripts, no bloat, performance-friendly

== Why choose Opiner Me? ==
* **Lightweight and fast** – no unnecessary code, no bloat, no performance impact.
* **Simple to use** – install, activate, add shortcode, done. No complex setup.
* **SEO-ready** – built-in JSON-LD markup helps search engines understand your ratings.
* **Clean UI** – minimal, theme-agnostic design that fits any WordPress site.
* **Modular architecture** – extend or customize functionality with clear, structured PHP classes.
* **Spam protection included** – the SpamGuard module filters basic unwanted submissions.
* **Flexible shortcodes** – display the form, the list, or both together anywhere on your site.
* **Privacy-friendly** – all opinions stay in your database; nothing is sent to external servers.

== Use Cases ==
* Add star ratings to posts, pages, or custom post types
* Collect quick user reviews with a simple opinion form
* Display ratings and review lists anywhere using shortcodes
* Improve SEO with JSON-LD review schema for rich results
* Gather lightweight feedback without heavy review plugins
* Add a minimal rating widget to service pages or portfolios

== Shortcodes ==
[opiner_me] – display form and list together
[opiner_me_form] – display only opinion form
[opiner_me_list] – display submitted reviews
[opiner_me_rating] – display average rating
[opiner_me_schema] – display JSON-LD

== Video ==
Quick installation video (2.5 minutes): <a href="https://www.youtube.com/watch?v=xW6uvR4Z0Y4">Watch on YouTube</a>

This short video shows how to install and use Opiner Me in under 3 minutes.

== Installation ==
1. Upload the plugin files to /wp-content/plugins/opiner-me/ or install via the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Place the [opiner_me] shortcode in any post or page to display the opinion form and review list.

== Screenshots ==
1. Opinion form with star rating and feedback fields.
2. Frontend list of reviews with star ratings and structured layout.
3. Lightweight star rating widget that fits any theme.
4. Full review module rendered using the [opiner_me] shortcode.
5. Standalone star rating shortcode for posts, pages or custom content.
6. Automatically generated JSON-LD review schema for SEO-rich results.
7. Admin panel showing submitted user reviews and ratings.
8. Lightweight settings panel with essential configuration options.

== Frequently Asked Questions ==
= Can I customize the opinion form or star rating? =
Yes. The form uses clean HTML and CSS, so you can easily adjust styles. The plugin is theme-agnostic and works well with most layouts.

= Does the plugin block spam submissions? =
Yes. Opiner Me includes a built-in SpamGuard module that filters basic spam and bot submissions. You can extend it with your own logic if needed.

= Does it work with any WordPress theme? =
Yes. The plugin is lightweight and designed to fit naturally into any theme. Minor CSS tweaks may be applied for perfect visual alignment.

= Will the star ratings and reviews help with SEO? =
Yes. Opiner Me outputs JSON-LD review schema, which helps search engines understand your ratings and may improve visibility in search results.

= How do I display the form, rating, or review list? =
Use the shortcodes:
[opiner_me_form] – opinion form
[opiner_me_list] – review list
[opiner_me_rating] – rating widget
[opiner_me_schema] – JSON-LD schema
[opiner_me] – form + list together

= Where are user reviews stored? =
All opinions and ratings are stored in your WordPress database. Nothing is sent to external servers.

= Is the plugin lightweight? =
Yes. Opiner Me loads no external scripts and avoids unnecessary assets. It is optimized for speed and minimal footprint.

== Performance ==
Opiner Me is optimized for speed and minimal footprint:

* No external scripts or heavy assets
* Lightweight codebase designed for fast loading
* Minimal database usage for storing reviews and ratings
* Works efficiently on shared hosting environments
* Zero impact on Core Web Vitals and page performance

== Changelog ==
= 1.0.1 =
* Fixed: Removed unwanted backslashes in opinion text and author fields using wp_unslash()
* Improved: Cleaner output handling for user-submitted reviews and ratings
* Improved: Minor UI adjustments in the admin panel
* Updated: Internal code structure for better stability and readability

= 1.0.0 =
* Initial release with opinion form, star rating, review list, JSON-LD schema, SpamGuard, and shortcode support

== Compatibility ==
Opiner Me is fully compatible with modern WordPress environments.

* Tested with WordPress versions 6.0-6.9
* Requires PHP 8.0 or higher
* Works with any theme thanks to lightweight, theme-agnostic design
* Compatible with classic editor and block editor (Gutenberg)

== Privacy ==
Opiner Me does not collect, store, or transmit any personal data to external servers.
All submitted opinions, ratings, and review content remain in your WordPress database.
No tracking, analytics, or third-party requests are used by the plugin.

== Upgrade Notice ==
= 1.0.1 =
Fixes unwanted backslashes in opinion text and author fields.
Improves output handling for user reviews and ratings.

= 1.0.0 =
Initial release of the opinion form, star rating, review list, JSON-LD schema, and shortcodes.
