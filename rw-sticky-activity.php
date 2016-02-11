<?php
/**
 * RW Sticky Activity
 *
 * @package   RW Sticky Activity
 * @author    Frank Staude
 * @license   GPL-2.0+
 * @link      https://github.com/rpi-virtuell/rw-sticky-activity
 */

/*
 * Plugin Name:       RW Sticky Activity
 * Plugin URI:        https://github.com/rpi-virtuell/rw-sticky-activity
 * Description:       A plugin to sticky an buddxpress avtivity
 * Version:           0.0.4
 * Author:            Frank Staude
 * Author URI:        https://staude.net
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 * Text Domain:       rw-sticky-activity
 * Network:           true
 * GitHub Plugin URI: https://github.com/rpi-virtuell/rw-sticky-activity
 * GitHub Branch:     master
 * Requires WP:       4.0
 * Requires PHP:      5.3
 */

class RW_Sticky_Activity {
    /**
     * Plugin version
     *
     * @var     string
     * @since   0.0.1
     * @access  public
     */
    static public $version = "0.0.3";

    /**
     * Singleton object holder
     *
     * @var     mixed
     * @since   0.0.1
     * @access  private
     */
    static private $instance = NULL;

    /**
     * @var     mixed
     * @since   0.0.1
     * @access  public
     */
    static public $plugin_name = NULL;

    /**
     * @var     mixed
     * @since   0.0.1
     * @access  public
     */
    static public $textdomain = NULL;

    /**
     * @var     mixed
     * @since   0.0.1
     * @access  public
     */
    static public $plugin_base_name = NULL;

    /**
     * @var     mixed
     * @since   0.0.1
     * @access  public
     */
    static public $plugin_url = NULL;

    /**
     * @var     string
     * @since   0.0.1
     * @access  public
     */
    static public $plugin_filename = __FILE__;

    /**
     * @var     string
     * @since   0.0.1
     * @access  public
     */
    static public $plugin_version = '';

    /**
     * Plugin constructor.
     *
     * @since   0.0.1
     * @access  public
     * @uses    plugin_basename
     * @action  rw_sticky_activity_init
     */
    public function __construct () {
        // set the textdomain variable
        self::$textdomain = self::get_textdomain();

        // The Plugins Name
        self::$plugin_name = $this->get_plugin_header( 'Name' );

        // The Plugins Basename
        self::$plugin_base_name = plugin_basename( __FILE__ );

        // The Plugins Version
        self::$plugin_version = $this->get_plugin_header( 'Version' );

        // Load the textdomain
        $this->load_plugin_textdomain();

        // Add Filter & Actions
        add_action( 'widgets_init', function() {
            register_widget( 'RW_Sticky_Activity_Widget' );
        } );
        add_action( 'bp_activity_entry_meta',   array( 'RW_Sticky_Activity_Core', 'add_sticky_icon') );
        add_action( 'wp_enqueue_scripts',       array( 'RW_Sticky_Activity_Core','enqueue_style' ) );
        add_action('init',                      array( 'RW_Sticky_Activity_Core','register_script' ) );
        add_action( 'wp_ajax_pin_activity',     array( 'RW_Sticky_Activity_Core','pin_activity' ) );
        add_action( 'wp_ajax_unpin_activity',   array( 'RW_Sticky_Activity_Core','unpin_activity' ) );

        do_action( 'rw_sticky_activity_init' );
    }

    /**
     * Creates an Instance of this Class
     *
     * @since   0.0.1
     * @access  public
     * @return  RW_Remote_Auth_Client
     */
    public static function get_instance() {

        if ( NULL === self::$instance )
            self::$instance = new self;

        return self::$instance;
    }

    /**
     * Load the localization
     *
     * @since	0.0.1
     * @access	public
     * @uses	load_plugin_textdomain, plugin_basename
     * @filters rw_sticky_activity_translationpath path to translations files
     * @return	void
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( self::get_textdomain(), false, apply_filters ( 'rw_sticky_activity_translationpath', dirname( plugin_basename( __FILE__ )) .  self::get_textdomain_path() ) );
    }

    /**
     * Get a value of the plugin header
     *
     * @since   0.0.1
     * @access	protected
     * @param	string $value
     * @uses	get_plugin_data, ABSPATH
     * @return	string The plugin header value
     */
    protected function get_plugin_header( $value = 'TextDomain' ) {

        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php');
        }

        $plugin_data = get_plugin_data( __FILE__ );
        $plugin_value = $plugin_data[ $value ];

        return $plugin_value;
    }

    /**
     * get the textdomain
     *
     * @since   0.0.1
     * @static
     * @access	public
     * @return	string textdomain
     */
    public static function get_textdomain() {
        if( is_null( self::$textdomain ) )
            self::$textdomain = self::get_plugin_data( 'TextDomain' );

        return self::$textdomain;
    }

    /**
     * get the textdomain path
     *
     * @since   0.0.1
     * @static
     * @access	public
     * @return	string Domain Path
     */
    public static function get_textdomain_path() {
        return self::get_plugin_data( 'DomainPath' );
    }

    /**
     * return plugin comment data
     *
     * @since   0.0.1
     * @uses    get_plugin_data
     * @access  public
     * @param   $value string, default = 'Version'
     *		Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title
     * @return  string
     */
    public static function get_plugin_data( $value = 'Version' ) {

        if ( ! function_exists( 'get_plugin_data' ) )
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

        $plugin_data  = get_plugin_data ( __FILE__ );
        $plugin_value = $plugin_data[ $value ];

        return $plugin_value;
    }

}


if ( class_exists( 'RW_Sticky_Activity' ) ) {

    add_action( 'plugins_loaded', array( 'RW_Sticky_Activity', 'get_instance' ) );

    require_once 'inc/RW_Sticky_Activity_Autoloader.php';
    RW_Sticky_Activity_Autoloader::register();

    register_activation_hook( __FILE__, array( 'RW_Sticky_Activity_Installation', 'on_activate' ) );
    register_uninstall_hook(  __FILE__,	array( 'RW_Sticky_Activity_Installation', 'on_uninstall' ) );
    register_deactivation_hook( __FILE__, array( 'RW_Sticky_Activity_Installation', 'on_deactivation' ) );
}
