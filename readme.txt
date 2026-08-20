=== Opiner Me ===
Plugin URI: https://opiner.me
Author: eeqsoft
Author URI: https://www.eeqsoft.com
Contributors: eeqsoft
Donate link: https://www.paypal.me/WEBEEQ
Tags: rating, star-rating, reviews, review-form, json-ld
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 8.0
Stable tag: 1.2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Opiner Me – Feedback & Star Rating Plugin. Lightweight opinion form with star rating, shortcode support, spam protection, and JSON-LD schema markup.

== Description ==
Opiner Me is a lightweight WordPress plugin for adding star ratings, collecting user reviews, and displaying a simple opinion form anywhere on your site. It includes SEO-friendly JSON-LD review schema, built-in spam protection, and clean shortcodes for maximum flexibility.

Perfect for blogs, service pages, portfolios, and any website that needs quick, minimal, and fast-loading feedback tools.

**Official website:** <a href="https://opiner.me">Opiner Me WP Plugin</a>
**Live demo:** <a href="https://opiner.me/live-demo">Check it out here</a>
**Installation video:** <a href="https://www.youtube.com/watch?v=xW6uvR4Z0Y4">Watch on YouTube</a>

== Features ==
* Lightweight star rating and review plugin.
* Opinion and feedback form that fits any theme.
* Simple shortcodes to display form, rating, reviews, slider, and schema.
* Built-in spam protection via SpamGuard module.
* JSON-LD review schema for better SEO and rich results.
* Clean admin panel to manage user opinions and ratings.
* No external scripts, no bloat – performance-friendly.

== Why choose Opiner Me? ==
* **Lightweight and fast** – minimal code, no unnecessary assets.
* **Simple to use** – install, activate, add shortcode, done.
* **SEO-ready** – JSON-LD markup included by default.
* **Clean UI** – theme-agnostic design that blends with any layout.
* **Modular architecture** – extend or customize functionality easily.
* **Spam protection included** – built-in SpamGuard module.
* **Flexible shortcodes** – display form, list, rating, slider, or schema anywhere.
* **Privacy-friendly** – all data stays in your WordPress database.

== Use Cases ==
* Add star ratings to posts, pages, or custom post types.
* Collect quick user reviews with a simple opinion form.
* Display ratings and review lists anywhere using shortcodes.
* Show reviews in a rotating slider (PRO).
* Improve SEO with JSON-LD review schema.
* Gather lightweight feedback without heavy review plugins.

== Shortcodes ==
[opiner_me] – display form and list together  
[opiner_me_form] – display only the opinion form  
[opiner_me_list] – display submitted reviews  
[opiner_me_rating] – display average rating  
[opiner_me_schema] – output JSON‑LD schema  
[opiner_me_slider] – display a rotating slider with user opinions (PRO)

== Opiner Me PRO ==
The PRO version adds optional advanced features:

* Opinion slider shortcode.
* Email notifications for new opinions.
* Import/export tools for opinions (JSON format).
* Advanced opinion editor in the admin panel.
* License validation system with remote API check.
* Additional UI enhancements and workflow improvements.

== Video ==
Quick installation video: <a href="https://www.youtube.com/watch?v=xW6uvR4Z0Y4">Watch on YouTube</a>
Shows how to install and use Opiner Me in under 3 minutes.

== Installation ==
1. Upload the plugin files to `/wp-content/plugins/opiner-me/` or install via the WordPress plugins screen.
2. Activate the plugin through the “Plugins” screen.
3. Place the `[opiner_me]` shortcode in any post or page to display the star rating, opinion form, review list, and JSON‑LD schema.

== Screenshots ==
1. Opinion form with star rating and feedback fields.
2. Frontend list of reviews with star ratings.
3. Lightweight star rating widget.
4. Full review module rendered using the `[opiner_me]` shortcode.
5. Standalone star rating shortcode for posts or pages.
6. Automatically generated JSON-LD review schema.
7. Admin panel showing submitted reviews and ratings.
8. Lightweight settings panel with essential options.
9. Rotating slider with user opinions (PRO).

== Frequently Asked Questions ==
= Can I customize the opinion form or star rating? =
Yes. The form uses clean HTML and CSS, so you can easily adjust styles.

= Does the plugin block spam submissions? =
Yes. Opiner Me includes a built-in SpamGuard module that filters basic spam and bot submissions.

= Does it work with any WordPress theme? =
Yes. The plugin is lightweight and theme-agnostic.

= Will the star ratings and reviews help with SEO? =
Yes. Opiner Me outputs JSON-LD review schema for better search engine visibility.

= How do I display the form, rating, or review list? =
Use the shortcodes listed above.

= Where are user reviews stored? =
All opinions and ratings are stored in your WordPress database.

= Is the plugin lightweight? =
Yes. No external scripts, minimal footprint, optimized for speed.

== Performance ==
Opiner Me is optimized for speed and minimal footprint:

* No external scripts or heavy assets.
* Lightweight codebase designed for fast loading.
* Minimal database usage.
* Works efficiently on shared hosting.
* Zero impact on Core Web Vitals.

== Changelog ==
= 1.2.1 =
* Fixed: Average rating after review editing.
* Fixed: Number of votes after review editing.

= 1.2.0 =
* Added (PRO): Opinion slider module for displaying reviews in a rotating carousel.
* Added (PRO): Email notifications for new submitted opinions/ratings.
* Added (PRO): Export tool for downloading opinions to a JSON file.
* Added (PRO): Import tool for restoring or migrating opinions from a JSON file.
* Added (PRO): Advanced opinion editor in the admin panel with full edit capabilities.
* Added (PRO): License validation system with remote API check for active PRO subscriptions.
* Improved: Internal code structure and modular architecture for better maintainability.
* Fixed: Use of the wp_unslash() function in the admin panel and on pages and posts.
* Fixed: Rare issue where temporary translation files could block proper language loading.
* Fixed: Minor UI inconsistencies in the admin panel.

= 1.1.0 =
* Added: Star icon in the top admin bar with pending opinions counter.
* Added: Red notification badge in the left admin menu.
* Improved: “Blocked words” field changed to textarea.
* Improved: Form scrolls to success/error message after submission.
* Fixed: Uninstall no longer resets settings.

= 1.0.1 =
* Fixed: Removed unwanted backslashes using wp_unslash().
* Improved: Cleaner output handling for reviews.
* Improved: Minor UI adjustments.
* Updated: Internal code structure.

= 1.0.0 =
* Initial release with opinion form, star rating, review list, JSON-LD schema, SpamGuard, and shortcode support.

== Compatibility ==
* Tested with WordPress 6.0‑7.0.
* Requires PHP 8.0 or higher.
* Works with any theme.
* Compatible with Classic Editor and Gutenberg.

== Privacy ==
Opiner Me does not collect or transmit any personal data to external servers. All submitted opinions and ratings remain in your WordPress database.

== Upgrade Notice ==
= 1.2.1 =
Fixes star rating widget with average rating and votes.

= 1.2.0 =
Adds opinion slider, email notifications, import/export tools, advanced editor, and license validation. Recommended update for all users.

= 1.1.0 =
Introduces admin notifications and UI improvements.

= 1.0.1 =
Fixes unwanted backslashes and improves output handling.

= 1.0.0 =
Initial release.
