#### 0.0.1.beta.25
* Fix: Static blog page no longer pulls first post excerpt.
* Better error checking on section template array.

#### 0.0.1.beta.24
* Primary nav centers when logo is centered.

#### 0.0.1.beta.23
* Fix: Wrong checked value for Hide Featured Image field.

#### 0.0.1.beta.22
* Better heading handling of headings in light/dark content.

#### 0.0.1.beta.21
* Sections template in article, no longer replaces the loop.
* Only remove section entry spacing on Sections template.
* Fix: return checked() in sprintf().

#### 0.0.1.beta.20
* Much better light/dark content handling.

#### 0.0.1.beta.19
* image-bg class shouldn't change text colors

#### 0.0.1.beta.18
* Fix: light/dark content when buttons are in the content.

#### 0.0.1.beta.17
* Hotfix: Update minified stylesheet.

#### 0.0.1.beta.16
* Archives/shortcodes with image set to background all link whether or not they have an image.
* New mai_get_processed_content() helper function to cleanup returned shorcode content.

#### 0.0.1.beta.15
* New: Allow embeds in Sections template.
* Fix: mai_add_background_image_attributes() wrong function name in archives when using featured image as background image.
* Fix more link styling when over image background.
* Slightly darker overlay when overlay is part of an entry link (featured image background).
* Hide the "Hide Featured Image" field until there is actually a featured image saved to the post.

#### 0.0.1.beta.14
* Add wrap to header before/after hooks.

#### 0.0.1.beta.13
* Added PHPColors class for php color processing.
* Section inner styling is now solid white if on a solid color background. Remains transparent over image background.
* Section template no longer sets a default background color via each section's settings.
* Fix: Move parsing of section shortcode atts to the main get_section function. (Props Hans Swolfs and Robin Cornett!)

#### 0.0.1.beta.12
* Fix: Mobile menu nav location is the only one that should show when there is a menu assigned to it.

#### 0.0.1.beta.11
* Breaking Change: Active child theme requires `add_theme_support( 'mai-pro-engine' );` to activate Mai Pro Engine plugin.
* Fix: Remove duplicate aspect-ratio class from banner.
* Fix: Process entry meta shortcodes in [grid].
* Fix: no-js/js inline script not working.

#### 0.0.1.beta.10
* Hotfix image overlay when no content on [col], when nbsp in content.

#### 0.0.1.beta.9
* Hotfix image overlay when no content on [col].
* Hotfix for button text-shadow in section area.

#### 0.0.1.beta.8
* Add 'Sections' page template.
* Add background color support for sections (via new template and 'bg' shortcode attribute).
* Add overlay and inner style options instead of just on/off (true/false).
* More efficient site-header build.
* Add site-header hooks to prepare for TBA addon.

#### 0.0.1.beta.7
* Top margin on flex-grid to match flex-entry bottom margin.

#### 0.0.1.beta.6
* Remove wpauto from [col] cause it breaks if [grid] or other shortcode is in there.

#### 0.0.1.beta.5
* Hotfix comma exploding things.

#### 0.0.1.beta.4
* Move section title inside inner wrap.

#### 0.0.1.beta.3
* This is CHANGES.md file.

#### 0.0.1.beta.2
* This is CHANGES.md file.

#### 0.0.1.beta.1
* Initial beta release.