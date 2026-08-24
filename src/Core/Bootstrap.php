<?php

namespace MultiVendorMarketplace\Core;

use MultiVendorMarketplace\Database\Installer;
use MultiVendorMarketplace\Database\Migration;

defined('ABSPATH') || exit;

class Bootstrap {
    private static $instance = null;
    private $container = [];

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->register_hooks();
        $this->load_services();
    }

    private function register_hooks() {
        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
        add_action('admin_init', [$this, 'on_admin_init']);
        add_action('wp_loaded', [$this, 'on_wp_loaded']);
        add_action('init', [$this, 'on_init']);
    }

    public function on_plugins_loaded() {
        load_plugin_textdomain(
            MVM_TEXT_DOMAIN,
            false,
            dirname(dirname(dirname(plugin_basename(MVM_PLUGIN_FILE)))) . '/languages'
        );

        do_action('mvm_plugins_loaded', $this);
    }

    public function on_admin_init() {
        $migration = new Migration();
        $migration->run();

        do_action('mvm_admin_init', $this);
    }

    public function on_wp_loaded() {
        do_action('mvm_wp_loaded', $this);
    }

    public function on_init() {
        // Register rewrite rules
        $this->register_rewrite_rules();

        do_action('mvm_init', $this);
    }

    private function register_rewrite_rules() {
        // Vendor store rewrite rule
        add_rewrite_rule(
            '^vendor/([^/]+)/?$',
            'index.php?marketplace_vendor_slug=$matches[1]',
            'top'
        );

        // Vendor dashboard rewrite rule
        add_rewrite_rule(
            '^vendor-dashboard/?$',
            'index.php?marketplace_vendor_dashboard=1',
            'top'
        );
    }

    private function load_services() {
        // Services will be registered here
        $this->container['vendor_manager'] = new \MultiVendorMarketplace\Vendor\Manager();
    }

    public function get_service($service_name) {
        return $this->container[$service_name] ?? null;
    }
}
