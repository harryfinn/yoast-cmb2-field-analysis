# Yoast CMB2 Field Analysis WP Plugin

## About this plugin

This plugin adds in a `js` based method of recalculating Yoast SEO's content
scores when updating page content, specifically custom meta fields added via the
CMB2 library.

A `js` method is the required due to the `php` filters that used to allow for
recalculating being removed from the plugin towards the end of 2015.

This plugin will work with either the CMB2 WordPress plugin or the library files
if used directly within your WordPress theme.

## Installing

To install, simply download a `.zip` version of this repository and upload to
your WordPress instance via the Admin screens,
`Plugins -> Add New -> Upload Plugin`.

## Usage

In order for your CMB2 field content to be calculated in the SEO score, ensure
that the field name (`id` field for group attributes) contains `_cmb2_`.
It is a good idea to use this as part of a prefixing structure for your CMB2
fields but it can also be used in a number of different ways:

-   `_cmb2_hero_banner_text`
-   `_my_theme_cmb2_hero_banner_text` (recommended)
-   `_my_theme_hero_banner_cmb2_`

## WordPress and Yoast SEO dependencies

Currently, this plugin has been tested and working with WordPress
`4.5` and Yoast SEO `3.2.3`

This plugin will be updated and retested with new versions of both
WordPress and the Yoast SEO plugin, with the version numbers above
updated to reflect this.
