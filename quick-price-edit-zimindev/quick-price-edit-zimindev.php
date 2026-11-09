<?php
/**
* Plugin Name: Quick Price Edit for WooCommerce
* Plugin URI: https://zimin.dev
* Description: Adds a separate admin page to quickly edit SKU, regular and sale prices of simple products and variations, with CSV import and export for Excel. Interface in English, Ukrainian, Russian, Belarusian, Kazakh, Polish, German and French.
* Version: 1.9.0
* Author: Sasha Zimin
* Author URI: https://zimin.dev
* Text Domain: quick-price-edit
* Requires at least: 5.0
* Requires PHP: 7.4
* Requires Plugins: woocommerce
* License: GPL-2.0-or-later
* Update URI: https://zimin.dev/plugins/quick-price-edit-zimindev
*/

// Update URI: WordPress never looks for updates of this plugin on wordpress.org and never replaces it
// with a same-named plugin from the directory.

if ( ! defined( 'ABSPATH' ) ) {
   exit;
}

/**
* Before 1.9.0 the plugin lived in the quick-price-edit folder. The old copy is deactivated silently and its
* folder deleted directly (it keeps no data: prices are ordinary WooCommerce product data).
*/
if ( ! function_exists( 'qpe_zimindev_legacy_cleanup' ) ) {
   /**
    * @return bool Whether the old copy was deactivated in this request.
    */
   function qpe_zimindev_legacy_cleanup() {
       $old = 'quick-price-edit/quick-price-edit.php';
       $dir = wp_normalize_path( WP_PLUGIN_DIR . '/quick-price-edit' );
       if ( ! is_file( $dir . '/quick-price-edit.php' ) || get_transient( 'qpe_legacy_failed' ) ) {
           return false;
       }
       // Only our own old copy, never a different plugin that happens to use the same folder name.
       $head = get_file_data( $dir . '/quick-price-edit.php', array( 'name' => 'Plugin Name', 'author' => 'Author' ) );
       if ( 'Quick Price Edit for WooCommerce' !== $head['name'] || 'Sasha Zimin' !== $head['author'] ) {
           return false;
       }
       if ( ! function_exists( 'deactivate_plugins' ) ) {
           require_once ABSPATH . 'wp-admin/includes/plugin.php';
       }
       $deactivated = false;
       if ( is_plugin_active( $old ) || is_plugin_active_for_network( $old ) ) {
           deactivate_plugins( $old, true, is_plugin_active_for_network( $old ) );
           $deactivated = true;
       }
       // Never delete the folder that this request runs from.
       if ( 0 === strpos( wp_normalize_path( __FILE__ ), $dir . '/' ) ) {
           return $deactivated;
       }
       // A symlink (developer folder): remove only the link itself, not the files it points to.
       if ( is_link( $dir ) ) {
           @unlink( $dir ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions.unlink_unlink
       } else {
           try {
               $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
               foreach ( $it as $file ) {
                   if ( $file->isDir() && ! $file->isLink() ) {
                       @rmdir( $file->getPathname() ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
                   } else {
                       wp_delete_file( $file->getPathname() );
                   }
               }
           } catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
               // No permission for some folder: recorded below, and further attempts stop.
           }
           @rmdir( $dir ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
       }
       if ( file_exists( $dir ) ) {
           // Do not repeat the walk on every admin request; the old copy is already deactivated.
           set_transient( 'qpe_legacy_failed', 1, DAY_IN_SECONDS );
       }
       wp_clean_plugins_cache( false );
       return $deactivated;
   }
}

// Old copy still active? WordPress loads plugins alphabetically, so this folder (quick-price-edit-zimindev)
// comes BEFORE quick-price-edit/: if this copy loaded too, the old one would redeclare the same functions.
// (A listed plugin whose file is gone does not count: WordPress skips it, so this copy must load.)
$qpe_old_active = is_file( WP_PLUGIN_DIR . '/quick-price-edit/quick-price-edit.php' ) && (
   in_array( 'quick-price-edit/quick-price-edit.php', (array) get_option( 'active_plugins', array() ), true )
   || ( is_multisite() && isset( ( (array) get_site_option( 'active_sitewide_plugins', array() ) )['quick-price-edit/quick-price-edit.php'] ) )
);

if ( defined( 'QPE_VERSION' ) || defined( 'QPE_FILE' ) || $qpe_old_active ) {
   // The old copy runs (or will run) in this request: do not load this one, deactivate the old one
   // instead, so only this copy runs from the next request.
   register_activation_hook( __FILE__, 'qpe_zimindev_legacy_cleanup' );
   add_action(
       'admin_init',
       function () {
           if ( ! current_user_can( 'activate_plugins' ) || ! qpe_zimindev_legacy_cleanup() ) {
               return;
           }
           // Once and only for GET, so the admin cannot loop.
           if ( ! wp_doing_ajax() && 'GET' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && ! isset( $_GET['qpe_migrated'] ) ) { // phpcs:ignore WordPress.Security
               wp_safe_redirect( add_query_arg( 'qpe_migrated', 1, isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : admin_url( 'plugins.php' ) ) );
               exit;
           }
       }
   );
   return;
}

// The old quick-price-edit folder (if still there) is removed from the admin.
add_action(
   'admin_init',
   function () {
       if ( current_user_can( 'activate_plugins' ) ) {
           qpe_zimindev_legacy_cleanup();
       }
   }
);

define( 'QPE_VERSION', '1.9.0' );
define( 'QPE_FILE', __FILE__ );

require_once __DIR__ . '/includes/plugin.php';
