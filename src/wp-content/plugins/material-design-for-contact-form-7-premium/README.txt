=== Material Design for Contact Form 7 ===
Contributors: contactform7addons, gusruss89, freemius
Tags: contact form 7, material design, contact form 7 addon, contact form 7 style, contact form 7 theme
Requires at least: 4.4
Tested up to: 5.1
Stable tag: trunk
Donate link: https://www.paypal.me/AngusRussell
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Make your website's forms as beautiful and interactive as an app! Add Google's "Material Design" to your Contact Form 7 forms.

== Description ==

Contact Form 7 forms can be as responsive and interactive as an app, just by adding Google's "Material Design" theme.

= Contact Form 7 style =
This plugin provides a bunch of shortcodes that are made to wrap around your CF7 form tags and apply a material design theme to them.

= Contact Form 7 + Material Design in action =
Take 30 seconds and see how your contact forms could look. Watch the video below and/or play with the [live demos](http://cf7materialdesign.com).

https://www.youtube.com/watch?v=HET7Lwx279I

= What's new in Version 2.0? =
* Uses the new Material Design. Text and select fields now come in boxed and outlined variants. Textareas now have an inner label. Check out the screenshots below.
* New button variants. Elevated (default), unelevated and outlined.
* Better browser support. IE11, iOS8+, plus all the modern browsers.
* Custom CSS now has syntax highlighting.
* You can choose to continue using the original/legacy styles if you prefer.

= Contact Form 7 can be more interactive =
Make your form fields react to user input more intuitively. Field labels start as placeholders and float up when you focus the field. Checkboxes and radios animate when you click them. Submit buttons include the Material Design 'ripple' effect. And more!

= Currently supported: =
* Light or dark theme
* Text input (includes text, email, url, tel, number, date) - boxed and outlined variants
* Textarea with optional auto-resizing
* Select/drop-down menu - boxed and outlined variants
* Checkboxes
* Radios
* Acceptance
* File upload field
* Submit button (including loading spinner)
* Quiz
* ReCaptcha
* Other (validation/success messages etc)

= Pro version: =
* Customize colours and fonts
* Arrange fields into columns
* Turn radios and checkboxes into [switches](https://material.io/guidelines/components/selection-controls.html#selection-controls-switch)
* Add leading icons to text and select fields
* Group fields into sections with [cards](https://material.io/guidelines/components/cards.html)
* Direct email support

You can upgrade to pro at any time without leaving WordPress.

= Works well with these other plugins: =
* [Contact Form 7 Live Preview](https://wordpress.org/plugins/cf7-live-preview/)
* Mailchimp for WordPress
* Conditional Fields for Contact Form 7
* Contact Form 7 Multi-Step Forms
* Invisible reCaptcha for WordPress (but not CF7 Invisible reCaptcha)
* Multifile Upload Field for Contact Form 7 (basic support)

= Responsive Contact Form =
Material Design for ContactForm7 is a fully responsive Contact Form 7 theme. It adapts to your screen size and works on any device.

= Contact Form Style =
Material Design for Contact Form 7 applies the default Material Design colours and fonts to your form by default, but you can use the WordPress customizer to change the fonts and colours to your liking if you're on the Pro version.

== Frequently Asked Questions ==

= What is Material Design? =
Material Design is a set of guidelines, written by Google, that outline how your website or app should look and behave. Most Google apps use Material Design, including Android itself.

= Do I need a Material Design theme as well? =
Not at all! The beauty of Material Design is that you can take as much or as little of it as you like. It's perfectly fine to just have your forms styled with Material Design and not the rest of your site.

For more ways to add Material Design to your WordPress site (without changing your theme), see [WordPress Material Design](https://cf7materialdesign.com/material-design-wordpress/).

= How do I use this plugin? =
All documentation can be found by clicking 'Help' (top right of the screen) and then 'Material Design' from the CF7 form editor screen.

For a more in-depth tutorial, see [How to apply Material Design to Contact Form 7](https://cf7materialdesign.com/how-to-apply-material-design-to-contact-form-7/).

= It doesn't look right for me! =
Some themes have styles that override the material design styles. If this happens to you, post a link to your form page in the [support forum](https://wordpress.org/support/plugin/material-design-for-contact-form-7/) and I'll help you fix it.

== Installation ==
1. Upload the zip to the `/wp-content/plugins/` directory and unzip
1. Activate the plugin through the 'Plugins' menu in WordPress

OR go to 'Plugins' > 'Add new', and search for 'material design for contact form 7' to install through the WordPress dashboard.

== Screenshots ==
1. A simple form
2. Simple form using the outlined style
3. Some more form fields, and custom colour (pro feature)
4. Dark theme example
5. Shortcode generator
6. Shortcode generator

== Changelog ==
= 2.5.4 =
* Update Freemius SDK
* Fix for Safari datepickers sometimes being hidden behind other elements
* Fix one of the nags which kept appearing after being closed

= 2.5.3 =
* Adds CSS to remove duplicate dropdown arrows from select fields on some themes
* Adds a fast option to download the latest pro version for existing license holders

= 2.5.2 =
* Fixes a bug with include_blank in select fields

= 2.5.1 =
* Updates to comply with the "Contact Form 7" trademark policy

= 2.5.0 =
* Allow exclusive checkboxes to be turned into switches
* Fields now show invalid state after invalid form submission
* A fix for avada theme selects

= 2.4.2 =
* Fixed a bug with polyfilled dates in Safari

= 2.4.1 =
* Fixed an upgrade bug
* Added some CSS to combat some themes overriding our styles
* Fixed terminology for switches

= 2.4.0 =
* Exposed a JavaScript function to manually initialize the plugin scripts (for use on dynamically loaded forms).

= 2.3.5 =
* Improve file field display
* Activated max-width fix from 2.3.4

= 2.3.4 =
* Added info about affiliate program in dashboard
* Added a fix for some themes that set label's max-width to 100%
* Security improvements

= 2.3.2 =
* Added translations! Russian, Japanese, French, German and Spanish

= 2.3.1 =
* Update to allow translation of shortcode generator

= 2.3.0 =
* Made plugin translateable

= 2.2.2 =
* Allow `default:get` on select fields

= 2.2.1 =
* Fixed outlined fields autofill issue
* Update docs to work in latest cf7

= 2.2.0 =
* Changed outlined fields to no longer use svg
* Added icons as a pro feature

= 2.1.6 =
* Added a datepicker polyfill

= 2.1.5 =
* Some CSS fixes for iOS date fields and labels

= 2.1.4 =
* Fix columns on tight-spaced forms

= 2.1.3 =
* Fix a PHP compatibility error

= 2.1.2 =
* Fix an old bug in pre CF7v5 acceptance fields

= 2.1.1 =
* Fix a bug in the upgrade from v1 process

= 2.1.0 =
* Integrate with Multifile Upload Field for Contact Form 7

= 2.0.0 =
* MAJOR UPDATE
* Entirely new Material Design library
* Text and select fields now come in boxed or outlined variants
* New button variants. Elevated (default), unelevated and outlined.
* Textareas have an inner label
* Better browser support
* Custom CSS syntax highlighting

= 1.8.1 =
* Fix a few bugs and an error that occured on older PHP versions

= 1.8.0 =
* MAJOR NEW FEATURE: Shortcode Generator UI. No more manual shortcode creation and constantly referring to the documentation.
* Slightly darkened the default label colour and input border

= 1.7.7 =
* Fix an issue created by the 1.7.6 on some systems

= 1.7.6 =
* Update the acceptance shortcodes to work with the latest CF7 update

= 1.7.5 =
* Fixed a PHP warning when no custom styles had been set
* GDPR compliance

= 1.7.4 =
* Fixed a bug where you needed to toggle "Use custom styles" off and on again before it actually worked

= 1.7.3 =
* Fixed a checkout issue

= 1.7.2 =
* Behind-the-scenes updates

= 1.7.1 =
* Fixed a JavaScript error in last release

= 1.7.0 =
* Add customization option for button colours
* Allow organising checkboxes and radios into columns
* Fix "Changes you made may not have been saved" message on unedited forms
* Allow hiding admin customize message on front end
* Fix close button on ad for premium version
* Update Freemius API
* Better default styles for text field labels

= 1.6.2 =
* Add a fix for themes that turn the submit input into a button

= 1.6.1 =
* Added integration with Contact Form 7 Live Preview plugin

= 1.6.0 =
* Added more customization options
* Updated Freemius SDK
* Added a shortcode to ensure layout attributes work when deeply nested
* Fix a Firefox bug with select options on dark theme
* Added some CSS to make themes less likely to override styles

= 1.5.18 =
* Freemius SDK update, fixes a bug with staging/deployment
* Added an affiliate program - earn money by promoting the plugin

= 1.5.17 =
* Fix a bug with file inputs inside conditional field groups

= 1.5.16 =
* Fix a bug with Safari and multi-column layouts

= 1.5.15 =
* Added customize link to front end forms

= 1.5.14 =
* Allow integration with other plugins
* Updated documentation

= 1.5.13 =
* Update Freemius SDK

= 1.5.12 =
* Fix a bug with required select fields

= 1.5.11 =
* Fix a bug with text field default values

= 1.5.10 =
* Fix a bug with quiz field
* Dequeue Roboto if not needed

= 1.5.9 =
* Fix a couple of IE Edge bugs

= 1.5.8 =
* Fix acceptance field bug

= 1.5.7 =
* Fix iOS datepicker bug
* Fix a CSS conflict with a WooThemes theme

= 1.5.6 =
* Improve file upload styles

= 1.5.5 =
* Update min-height of autosizing textareas

= 1.5.4 = 
* Feature: auto-resizing textareas
* Better escaping of attributes and html
* Better customization for file input
* More consistent checkbox alignment with long and short labels

= 1.5.3 =
* Make [md-text] shortcode work with html5 datepicker

= 1.5.2 =
* Fix a negative margin issue with CSS grid

= 1.5.1 =
* Minor behind the scenes updates

= 1.5.0 =
* [Premium version] allow turning checkboxes and radios into [switches](https://material.io/guidelines/components/selection-controls.html#selection-controls-switch)
* Update to fix vertical spacing with CSS grid

= 1.4.0 =
* Added file upload field

= 1.3.3 =
* Added spacing options
* Added support for quiz and recaptcha

= 1.3.2 =
* Improved docs

= 1.3.1 =
* Minor bug fixes
* Ensure CF7 plugin is active

= 1.3.0 =
* Added plugin sub-menu page
* Add pro version info

= 1.2.0 =
* Under the hood updates

= 1.1.0 =
* Refactored CSS to be more specific
* Added dark theme option

= 1.0.0 =
* First release