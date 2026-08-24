<?php
/**
 * Plugin Name: Multi-Vendor Marketplace for WooCommerce
 * Plugin URI: https://github.com/amirreza-86/Plugin
 * Description: Production-grade multi-vendor marketplace for WooCommerce with Elementor integration
 * Version: 1.0.0
 * Author: Marketplace Team
 * Author URI: https://marketplace.example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: multi-vendor-marketplace
 * Domain Path: /languages
 * WC requires at least: 7.0
 * WC tested up to: 8.4
 * Requires PHP: 8.1
 * Requires at least: 6.0
 */

defined('ABSPATH') || exit;

/**
 * Plugin constants
 */
define('MVM_PLUGIN_FILE', __FILE__);
define('MVM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MVM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MVM_VERSION', '1.0.0');
define('MVM_DB_VERSION', '1');
define('MVM_TEXT_DOMAIN', 'multi-vendor-marketplace');
define('MVM_PREFIX', 'mv_');
define('MVM_CAPABILITY_PREFIX', 'marketplace_');

/**
 * Main Plugin Class
 */
class MultiVendorMarketplace {
    private static $instance = null;
    private $container = [];

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Plugin initialization
     */
    public function __construct() {
        $this->check_requirements();
        $this->load_autoloader();
        $this->init_hooks();
    }

    /**
     * Check plugin requirements
     */
    private function check_requirements() {
        if (!function_exists('WC')) {
            add_action('admin_notices', [$this, 'woocommerce_missing_notice']);
            return false;
        }

        if (version_compare(get_bloginfo('version'), '6.0', '<')) {
            add_action('admin_notices', [$this, 'wordpress_version_notice']);
            return false;
        }

        if (version_compare(WC()->version, '7.0', '<')) {
            add_action('admin_notices', [$this, 'woocommerce_version_notice']);
            return false;
        }

        if (PHP_VERSION_ID < 80100) {
            add_action('admin_notices', [$this, 'php_version_notice']);
            return false;
        }

        return true;
    }

    /**
     * Load PSR-4 autoloader
     */
    private function load_autoloader() {
        require_once MVM_PLUGIN_DIR . 'vendor/autoload.php';
        spl_autoload_register([$this, 'autoload']);
    }

    /**
     * PSR-4 Autoloader
     */
    public function autoload($class) {
        if (0 !== strpos($class, 'MultiVendorMarketplace\\')) {
            return false;
        }

        $parts = explode('\\', substr($class, 23));
        $path  = MVM_PLUGIN_DIR . 'src/' . implode('/', $parts) . '.php';

        if (file_exists($path)) {
            require_once $path;
            return true;
        }

        return false;
    }

    /**
     * Initialize plugin hooks
     */
    private function init_hooks() {
        register_activation_hook(MVM_PLUGIN_FILE, [$this, 'on_activate']);
        register_deactivation_hook(MVM_PLUGIN_FILE, [$this, 'on_deactivate']);
        register_uninstall_hook(MVM_PLUGIN_FILE, [__CLASS__, 'on_uninstall']);

        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
        add_action('admin_init', [$this, 'on_admin_init']);
        add_action('wp_loaded', [$this, 'on_wp_loaded']);
    }

    /**
     * Plugin activation hook
     */
    public function on_activate() {
        // Create database tables
        $database = new \MultiVendorMarketplace\Database\Installer();
        $database->install();

        // Create roles and capabilities
        $roles = new \MultiVendorMarketplace\Core\RoleManager();
        $roles->register_roles();

        // Flush rewrite rules
        flush_rewrite_rules();

        do_action('mvm_plugin_activated');
    }

    /**
     * Plugin deactivation hook
     */
    public function on_deactivate() {
        flush_rewrite_rules();
        do_action('mvm_plugin_deactivated');
    }

    /**
     * Plugin uninstall hook
     */
    public static function on_uninstall() {
        // Check if user wants to delete data on uninstall
        $delete_data = get_option('mvm_delete_data_on_uninstall', false);
        if (!$delete_data) {
            return;
        }

        // Delete plugin data (optional)
        $database = new \MultiVendorMarketplace\Database\Installer();
        $database->uninstall();
    }

    /**
     * Plugins loaded hook
     */
    public function on_plugins_loaded() {
        load_plugin_textdomain(MVM_TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Initialize container services
        $this->setup_container();

        do_action('mvm_plugins_loaded');
    }

    /**
     * Admin init hook
     */
    public function on_admin_init() {
        do_action('mvm_admin_init');
    }

    /**
     * WordPress loaded hook
     */
    public function on_wp_loaded() {
        do_action('mvm_wp_loaded');
    }

    /**
     * Setup dependency container
     */
    private function setup_container() {
        // TODO: Initialize service container
    }

    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        echo '<div class="notice notice-error"><p>';
        echo esc_html__('Multi-Vendor Marketplace requires WooCommerce to be installed and activated.', MVM_TEXT_DOMAIN);
        echo '</p></div>';
    }

    /**
     * WordPress version notice
     */
    public function wordpress_version_notice() {
        echo '<div class="notice notice-error"><p>';
        echo esc_html__('Multi-Vendor Marketplace requires WordPress 6.0 or later.', MVM_TEXT_DOMAIN);
        echo '</p></div>';
    }

    /**
     * WooCommerce version notice
     */
    public function woocommerce_version_notice() {
        echo '<div class="notice notice-error"><p>';
        echo esc_html__('Multi-Vendor Marketplace requires WooCommerce 7.0 or later.', MVM_TEXT_DOMAIN);
        echo '</p></div>';
    }

    /**
     * PHP version notice
     */
    public function php_version_notice() {
        echo '<div class="notice notice-error"><p>';
        echo esc_html__('Multi-Vendor Marketplace requires PHP 8.1 or later.', MVM_TEXT_DOMAIN);
        echo '</p></div>';
    }
}

/**
 * Initialize plugin
 */
function multi_vendor_marketplace() {
    return MultiVendorMarketplace::get_instance();
}

multi_vendor_marketplace();
