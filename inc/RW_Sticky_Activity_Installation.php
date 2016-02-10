<?php
/**
 * Class RW_Sticky_Activity_Installation
 *
 * Contains some helper code for plugin installation
 *
 * @package   RW Sticky Activity
 * @author    Frank Staude
 * @license   GPL-2.0+
 * @link      https://github.com/rpi-virtuell/rw-sticky-activity
 */
class RW_Sticky_Activity_Installation {
    /**
     * Check some thinks on plugin activation
     *
     * @since   0.0.1
     * @access  public
     * @static
     * @return  void
     */
    public static function on_activate() {

        // check WordPress version
        if ( ! version_compare( $GLOBALS[ 'wp_version' ], '4.0', '>=' ) ) {
            deactivate_plugins( RW_Sticky_Activity::$plugin_filename );
            die(
            wp_sprintf(
                '<strong>%s:</strong> ' .
                __( 'This plugin requires WordPress 4.0 or newer to work', RW_Sticky_Activity::get_textdomain() )
                , RW_Sticky_Activity::get_plugin_data( 'Name' )
            )
            );
        }


        // check php version
        if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
            deactivate_plugins( RW_Sticky_Activity::$plugin_filename );
            die(
            wp_sprintf(
                '<strong>%1s:</strong> ' .
                __( 'This plugin requires PHP 5.3 or newer to work. Your current PHP version is %1s, please update.', RW_Sticky_Activity::get_textdomain() )
                , RW_Sticky_Activity::get_plugin_data( 'Name' ), PHP_VERSION
            )
            );
        }

        // check buddypress
        if ( ! function_exists( 'bp_activities' ) ) {
            deactivate_plugins( RW_Sticky_Activity::$plugin_filename );
            die(
            wp_sprintf(
                '<strong>%1s:</strong> ' .
                __( 'This plugin requires BuddyPress to work.', RW_Sticky_Activity::get_textdomain() )
                , RW_Sticky_Activity::get_plugin_data( 'Name' ), PHP_VERSION
            )
            );
        }
    }

    /**
     * Clean up after deactivation
     *
     * Clean up after deactivation the plugin
     *
     * @since   0.0.1
     * @access  public
     * @static
     * @return  void
     */
    public static function on_deactivation() {

    }

    /**
     * Clean up after uninstall
     *
     * Clean up after uninstall the plugin.
     * Delete options and other stuff.
     *
     * @since   0.0.1
     * @access  public
     * @static
     * @return  void
     *
     */
    public static function on_uninstall() {

    }
}
