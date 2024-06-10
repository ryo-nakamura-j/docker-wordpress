=== Tabs Shortcodes ===
Contributors: philbuchanan
Author URI: http://philbuchanan.com/
Donate Link: http://philbuchanan.com/
Tags: tab, tabs, shortcodes
Requires at least: 3.3
Tested up to: 3.8
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a few shortcodes to allow for tabbed content.

== Description ==
Adds a few shortcodes to allow for tabbed content.

**NOTE:** If you are not comfortable using WordPress shortcodes, this plugin may not be for you.

= Features =

* Adds two shortcodes for adding a tabbed interface to your site
* No default CSS added (you will **need** to [add your own](http://wordpress.org/plugins/tabs-shortcodes/other_notes/))
* Only adds JavaScript on pages that use the shortcodes
* Does not require jQuery

= The Shortcodes =

The two shortcodes that are added are:

`[tabs]`

and

`[tab title=""]`

= Basic Usage Example =

    [tabs]
    [tab title="First Tab"]Content for tab one goes here.[/tab]
    [tab title="Second Tab"]Content for tab two goes here.[/tab]
    [/tabs]

This will output the following HTML:

    <ul class="tabs">
        <li><a href="#tab-1" class="active">First Tab</a></li>
        <li><a href="#tab-2">Second Tab</a></li>
    </ul>
    <section id="tab-1" class="tab active">Content for tab one goes here.</section>
    <section id="tab-2" class="tab">Content for tab two goes here.</section>

= Settings =

There are no settings for the plugin. The only additional setup you will need to do is [add some css](http://wordpress.org/plugins/tabs-shortcodes/other_notes/) to style the tabs however you'd like. Adding the CSS is very important as the tabs will not display as tabs until you do so.

== Installation ==
1. Upload the 'tabs-shortcodes' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the Plugins menu in WordPress.
3. Add the shortcodes to your content.
4. Add some [CSS](http://wordpress.org/plugins/tabs-shortcodes/other_notes/#Other-Notes) to your themes stylesheet to make the tabs look the way you want.

== Other Notes ==

= Sample CSS =

Here is some sample CSS to get you started. Make adjustments as necessary if you want to customize the look and feel of the tabs.

    /* Tabs Styles */
    ul.tabs {
        list-style: none;
        margin: 0;
        border-bottom: 1px solid #ccc;
    }
    ul.tabs li {display: inline-block;}
    ul.tabs a {
        display: block;
        position: relative;
        top: 1px;
        padding: 5px 10px;
        border: 1px solid transparent;
        text-decoration: none;
    }
    ul.tabs a.active {
        border-color: #ccc;
        border-bottom-color: #fff;
    }
    section.tab {
        display: none;
        margin-bottom: 15px;
        padding: 15px 0;
    }
    section.tab.active {display: block;}

= Issues/Suggestions =

For bug reports or feature requests or if you'd like to contribute to the plugin you can check everything out on [Github](https://github.com/philbuchanan/Tabs-Shortcodes/).

== Changelog ==
= 1.0.1 =
* Drastically simplified JavaScript

= 1.0 =
* Initial release

== Upgrade Notice ==
= 1.0.1 =
Drastically simplified JavaScript.

= 1.0 =
Initial release.