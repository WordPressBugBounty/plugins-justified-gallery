=== Justified Gallery  ===
Contributors: matczar, damian-gora
Tags: best gallery plugin, wordpress gallery, justified gallery, gallery grid, gutenberg block
Requires at least: 4.6
Tested up to: 6.7
Requires PHP: 5.4
Stable tag: 1.10.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


WordPress gallery plugin. Display WordPress galleries in a responsive justified image grid and a pretty lightbox. 


== Description ==

This simple plugin brings the WordPress gallery to a higher level by adding a nice justified image grid and a pretty lightbox.
**Just install and activate the plugin. That's all.**

Create galleries as before, but enjoy the **responsive layout**, beautiful **justified image grid** and handy **lightbox**.

I love the native WordPress gallery. It’s easy to use and as intuitive as possible. Everything would be perfect if it were not for the column layout and lack of lightbox. Many themes display the WordPress gallery in an ugly and “no eye-catching” way. This was my inspiration to write this simple plugin.

= Demo =
See how it works on the [DEMO](https://justifiedgallery.com/?utm_source=wordpress_org&utm_medium=webpage&utm_campaign=readme) site.

= Features =
*  **Easy to use**. You can use the native WordPress gallery just as you did before
* **Compatibility with Gutenberg**. You can use block called Justified Gallery
*  Beautiful, justified layout thanks to [Justified Gallery by Miro](http://miromannino.github.io/Justified-Gallery)
*  **Support of image descriptions**. Descriptions can also be turned off on a settings page
*  **Lightbox**. The gallery can be viewed with popular [PhotoSwipe](http://photoswipe.com/) by Dmitry Semenov
*  **Responsiveness**. The gallery adapts to various screen width options. It works perfectly on mobile devices
*  **High quality images**. The URL of images is tailored to the needs in an intelligent way. Smaller images will be download on smaller screens, larger images on larger screens
*  Justify your images without cropping them
*  **Configurable**. You can set up a width of the gap between images and height of rows.

= How to use? =
If you are using the block-editor on your site, simply insert the Justified Gallery block and add images. You can also transform Gallery block to Justified Gallery block.

If you are still using the Classic Editor, Justified Gallery is based on native WordPress galleries (`[gallery]` shortcode) and works out of the box. Create galleries as before. Read more about [The WordPress Gallery](https://wordpress.org/support/article/the-wordpress-gallery/)

= Feedback =

Any suggestions or comments are welcome. Feel free to contact me using this [contact form](https://justifiedgallery.com/#contact).

= Credits =

Justified Gallery plugin was originally created by Damian Góra. It is now owned and maintained by Mateusz Czardybon.

== Installation ==

1. Install the plugin from within the Dashboard or upload the directory `justified-gallery` and all its contents to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings -> Justified Gallery (admin menu) and set your preferences.
4. Create post or page and insert Justified Gallery block.

== Justified Gallery PRO ==

**[Upgrade now!](https://justifiedgallery.com/?utm_source=wordpress_org&utm_medium=webpage&utm_campaign=readme)**

If you are eg a **photographer** and publish **large galleries (+50 photos per page)**, you may be interested in the optimizing the loading time of galleries. With [the premium version](https://justifiedgallery.com/?utm_source=wordpress_org&utm_medium=webpage&utm_campaign=readme), inter alia, you speed up galleries load time even up to **20 times**.

**Highlighted Premium Features**

*  All Free Features
*  Speed up galleries load time even 20x faster!
*  Lightboxes customization
*  Tiles style customization
*  All future Pro features at current price

== Screenshots ==

1. Gallery demo page
2. Lightbox Gallery
3. Settings page of Justified Gallery

== Changelog ==

= 1.10.0, January 27, 2025 =
* UPDATE: Freemius SDK

= 1.9.0, November 18, 2024 =
* ADD: Ability to set background and text color in the gallery preview
* ADD: Ability to limit the number of images in the gallery preview
* FIX: Unwanted image cropping when max row height is set to -1
* FIX: Incorrect display of links in the description in Photoswipe lightbox
* FIX: Deprecation warning when editing a gallery in the block editor
* TWEAK: Stop using a server-side mobile device recognition library
* UPDATE: Freemius SDK v2.9.0

= 1.8.1, July 08, 2023 =
* FIX: Incorrect image caption background color for Layla tile style

= 1.8.0, July 05, 2023 =
* FIX: Unnecessary loading of Swipebox files when using Photoswipe lightbox
* FIX: Unnecessary database post query in plugin settings page
* FIX: Ensure that plugin notices are closed by the current user
* UPDATE: Justified Gallery library v3.8.1
* UPDATE: Freemius SDK v2.5.10

= 1.7.3, January 11, 2023 =
* FIX: Plugin security enhancements and code-related best practices

= 1.7.2, January 10, 2023 =
* FIX: Plugin security enhancements and code-related best practices
* ADD: Update Photoswipe library
* ADD: Update Swipebox library

= 1.7.1, December 23, 2022 =
* FIX: Securing shortcode attributes
* ADD: Freemius SDK v2.5.3

= 1.7.0, November 22, 2022 =
* ADD: New "Simple" tile style with two caption display modes
* ADD: Ability to transform core gallery block and [gallery] shortcode into a Justified Gallery block
* ADD: Possibility to edit Justified Gallery block settings directly in the block editor
* CHANGE: Latest Freemius SDK

= 1.6.0, May 24, 2022 =
* REFACTOR: Rebuilt gallery block for the block editor
* FIX: Justified Gallery block cannot be selected in the block editor
* FIX: Justified Gallery block preview in the block editor is not visible in WordPress 6.0

= 1.5.1, March 01, 2022 =
* CHANGE: Updated Freemius SDK (security fixes)

= 1.5.0, July 20, 2021 =
* FIX: Change to 'media' category in block editor
* FIX: Compability with PHP 8
* CHANGE: Latest Freemius SDK

= 1.4.5, December 09, 2020 =
* FIX: Swipebox compability with jQuery 3.x
* CHANGE: Latest Freemius SDK

= 1.4.4, May 11, 2020 =
* ADD: Compability with Twenty Twenty theme

= 1.4.3, December 19, 2019 =
* CHANGE: Latest Freemius SDK

= 1.4.2, March 02, 2019 =
* ADD: Security patch

= 1.4.1, December 16, 2018 =
* FIX: Undefined wp.editor for the Gutenberg block in WordPress 5
* CHANGE: Latest Freemius SDK

= 1.4.0, September 13, 2018 =
* ADD: New Gutemberg block called "Justified Gallery"
* ADD: New option to disable outer space of the grid, when adding space between images!.
* FIX: Conflict with the Jetpack Lazy Images module
* FIX: Conflict with the Lazy Load by WP Rocket
* FIX: Description in Photoswipe white (wedding) theme
* CHANGE: Latest Freemius SDK

= 1.3.1, April 3, 2018 =
* ADD: Notice "How to use?" on fresh installs
* FIX: Blurry images on some themes
* FIX: Swipebox default settings
* CHANGE: Small changes on settings page

= 1.3.0, March 14, 2018 =
* ADD: New lightbox - Swipebox
* ADD: New tile style Layla
* ADD: New hooks and filters
* ADD: Performance improvement - load script only if needed
* ADD: Lightbix preview on the settings page
* ADD: Tiles style preview on the settings page
* ADD: Pro version advertisement
* FIX: Restoring the param link=none
* FIX: Description appearance
* CHANGE: plugin textdomain as a string
* CHANGE: Code refactoring

= 1.2.3, September 23, 2017 =
* FIX: Quotation marks in a image description
* FIX: Images were linked to "Attachment Page" for selected option "Media File"
* CHANGE: Move admin menu item to the Settings

= 1.2.2, July 25, 2017 =
* ADD: Admin notice for better feedback from users
* FIX: Share URL after changes in Facebook API

= 1.2.1, July 10, 2017 =
* ADD: Freemius SDK
* ADD: Option to disable hover effect
* FIX: Facebook share link
* FIX: Unnecessary hover effect on mobile devices
* CHANGE: File structure refactoring

= 1.2.0, February 25, 2017 =
* CHANGE: LightGallery is replaced by PhotoSwipe because of license
* FIX: Admin bar that covers lightbox window

= 1.1.0, August 17, 2016 =
* ADD: Compatibility with the Enhanced Media Library
* ADD: Allows to use links in a media description
* FIX: PHP Warnings caused by using the 'post_gallery' filter by other plugins without a third parameter: $instance
* FIX: Trivial CSS

= 1.0.0, June 25, 2016 =
* ADD: Loupe icon if there is no caption

= 0.9.1, June 4, 2016 =
* First public release
