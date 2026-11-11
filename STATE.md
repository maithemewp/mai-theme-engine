# State
Updated: 2026-11-11 by Claude

## Now
Legacy plugin, security fixes only (plus easy wins). 1.14.0 is a security and PHP 8 release: banner shortcode injection from search and user names, raw $_POST into genesis-settings, grid/section tag and class XSS, grid status and post type leaks, PHP 8 fatals and deprecations, WP 6.7 textdomain notice, restyled security-only upgrade notice.

## Next
Nothing planned. Possible later: upgrade the bundled plugin-update-checker (v4p11 logs PHP 8.4 implicit-nullable deprecations).

## Blocked / waiting on
Nothing.

## Verify
Local test site: ~/Herd/mailifestyle (https://mailifestyle.test), Genesis symlinked from ~/Herd/agiindustries, Mai Lifestyle Pro copied from GitHub, this repo symlinked as the plugin. WP_DEBUG logs to wp-content/debug.log.
- Anonymous: /?s=[grid grid_title_wrap=script grid_title=x] must render brackets as text.
- [grid status="private,draft"] shows only published posts when logged out.

## Gotchas
- Genesis fires genesis_setup during its functions.php include, before after_setup_theme. Translated strings there trigger the WP 6.7 notice.
- The banner runs do_shortcode on its content. Escape any visitor or user controlled text with mai_esc_shortcodes().
- Releases use git-flow: release/x.y.z from develop, merge to master and develop, tag on master.
- Tags publish updates to client sites through the GitHub updater. Ask before pushing or tagging.
- Leftover debug helper on the test site: wp-content/mu-plugins/zz-trace-textdomain.php.
