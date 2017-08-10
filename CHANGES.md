### Changelog

#### 1.0.15 (8/10/17)
* Fixed: Error when /%category%/ was used in permalinks and your visit a 404's url.
* Fixed: Sub-menu text alignment when text wraps to a second line.

#### 1.0.14 (8/7/17)
* Changed: Better blockquote styling.
* Changed: Remove excess grid top margin.
* Changed: Sections template now sanitizes WYSIWYG editor value the same as WP.

#### 1.0.13.1 (8/1/17)
* Fixed: Page template loader no longer runs on all pages. More efficient and fixes potential conflicts. Props @timothyjensen.

#### 1.0.13 (8/1/17)
* Added: [grid] New filter on default args so developers can change the default settings for the shortcode.
* Changed: Better blockquote styling.
* Changed: Better button style handling, especially with WooCommerce buttons.
* Fixed: [grid] Image markup when image_location is before_entry link is false.
* Fixed: [grid] Slider dot color when on dark background.
* Fixed: mai_get_grid() helper function unnecessarily requiring $content param.

#### 1.0.12.2 (7/28/17)
* Changed: Add bottom margin to galleries.
* Fixed: Better browser support for gradient overlay.

#### 1.0.12.1 (7/27/17)
* Changed: More efficient fix for removing empty <p> tags from shortcodes in widgets.

#### 1.0.12 (7/26/17)
* Added: [grid] can now 'exclude_categories' from display. Example: Display all posts except those in the 'Recipes' category.
* Fixed: [grid] Center slider dots.

#### 1.0.11 (7/24/17)
* Changed: Hierarchical taxonomy terms now check parents all the way up the tree for any archive settings (props @hellofromTonya).

#### 1.0.10
* Added: Setting to disable term archives by taxonomy.

#### 1.0.9.1
* Fixed: [grid] was not linking correctly when displaying taxonomy terms with image_location="bg".

#### 1.0.9
* Added: Setting to allow featured images to be used as the banner image.
* Added: Child category/taxonomy archives now fallback to their parent term banner image (up to 4 levels deep).
* Added: [grid] slider can now autoplay via autoplay="true" and adjust autoplay speed with speed="3000".

#### 1.0.8
* Added: Entry pagination now shows a 'tiny' thumbnail.
* Fixed: Mai Pro front page now works as expected if set to display latest posts.
* Fixed: Featured image caption display if featured image is set to auto-display.

#### 1.0.7.2
* Fixed: Error when running PHP 5.3.

#### 1.0.7.1
* Fixed: Entry meta spacing.

#### 1.0.7
* Added: You can now align featured images left or right when post archives are in columns.
* Added: Default favicon.
* Changed: Odd sections now have a white background as a default.
* Fixed: Center logo on logo screen.
* Fixed: Section content alignment on Safari 7/8 and IE11.
* Fixed: WooCommerce notice content alignment.

#### 1.0.6
* Added: Screen reader text to read more links in [grid].
* Changed: Use dedicated anchor link for flex loop and grid entry bg links.

#### 1.0.5
* Added: Entry header/content/footer filters to [grid] shortcode entries.
* Fixed: Remove nested links when showing excerpts or full content in archive flex loop.
* Fixed: Remove redundent conditional checks in flex loop.
* Fixed: Better archive setting check for Woo custom taxos.
* Fixed: FacetWP arg is no longer true by default.
* Fixed: Extra entry pagination margin.
* Fixed: Table styling.
* FIxed: Mobile menu toggle won't shrink if logo is big on smaller screens.

#### 1.0.4
* Changed: Refactor archive settings output functions.
* Changed: Allow menu itmes to wrap on primary/secondary nav.
* Fixed: Cleanup tabs/spaces.

#### 1.0.3
* Added: Banner alignment setting.

#### 1.0.2.1
* Fixed: z-index issue on sections template prohibiting editing of some fields.

#### 1.0.2
* Added: FacetWP support in [grid] shortcode.
* Added: Add additional settings to each section.
* Changed: Move section settings to slide out side panel.

#### 1.0.1.1
* Fixed: Remove unnecessary width declaration on img.

#### 1.0.1
* Fixed: IE fix for full width section wrap not centering.

#### 1.0.0
* Initial release.
