<?php

namespace MultiVendorMarketplace\Core;

defined('ABSPATH') || exit;

class RoleManager {
    const ROLE_VENDOR = 'marketplace_vendor';
    const ROLE_VENDOR_STAFF = 'marketplace_vendor_staff';
    const ROLE_ADMIN = 'marketplace_admin';

    public function register_roles() {
        $this->add_vendor_role();
        $this->add_vendor_staff_role();
        $this->add_admin_role();
    }

    private function add_vendor_role() {
        remove_role(self::ROLE_VENDOR);

        add_role(
            self::ROLE_VENDOR,
            esc_html__('Marketplace Vendor', 'multi-vendor-marketplace'),
            [
                'read' => true,
                'delete_posts' => false,
                'edit_posts' => false,
                'publish_posts' => false,
            ]
        );

        $this->add_vendor_capabilities();
    }

    private function add_vendor_staff_role() {
        remove_role(self::ROLE_VENDOR_STAFF);

        add_role(
            self::ROLE_VENDOR_STAFF,
            esc_html__('Marketplace Vendor Staff', 'multi-vendor-marketplace'),
            [
                'read' => true,
            ]
        );
    }

    private function add_admin_role() {
        $admin_role = get_role('administrator');
        if (!$admin_role) {
            return;
        }

        $this->add_admin_capabilities($admin_role);
    }

    private function add_vendor_capabilities() {
        $role = get_role(self::ROLE_VENDOR);
        if (!$role) {
            return;
        }

        $capabilities = [
            'edit_own_vendor_profile' => true,
            'edit_own_store' => true,
            'manage_store_media' => true,
            'add_vendor_products' => true,
            'edit_own_vendor_products' => true,
            'delete_own_vendor_products' => true,
            'duplicate_vendor_products' => true,
            'bulk_edit_vendor_products' => true,
            'import_vendor_products' => true,
            'create_vendor_offers' => true,
            'edit_vendor_offers' => true,
            'delete_vendor_offers' => true,
            'view_offer_analytics' => true,
            'view_own_vendor_orders' => true,
            'edit_own_vendor_orders' => true,
            'manage_vendor_order_status' => true,
            'cancel_vendor_orders' => true,
            'view_vendor_wallet' => true,
            'view_vendor_commissions' => true,
            'request_vendor_withdrawal' => true,
            'view_withdrawal_history' => true,
            'create_vendor_coupons' => true,
            'edit_vendor_coupons' => true,
            'delete_vendor_coupons' => true,
            'view_coupon_analytics' => true,
            'manage_vendor_shipping' => true,
            'add_vendor_shipping_rules' => true,
            'edit_vendor_shipping_rules' => true,
            'delete_vendor_shipping_rules' => true,
            'view_vendor_dashboard' => true,
            'access_vendor_analytics' => true,
            'access_vendor_reports' => true,
            'manage_vendor_staff' => true,
            'add_staff_members' => true,
            'edit_staff_members' => true,
            'delete_staff_members' => true,
            'assign_staff_roles' => true,
            'respond_to_reviews' => true,
            'send_vendor_messages' => true,
            'view_vendor_messages' => true,
        ];

        foreach ($capabilities as $cap => $grant) {
            $role->add_cap($cap, $grant);
        }
    }

    private function add_admin_capabilities($admin_role) {
        $capabilities = [
            'manage_marketplace_settings' => true,
            'manage_marketplace_features' => true,
            'manage_marketplace_plugins' => true,
            'manage_marketplace_vendors' => true,
            'add_marketplace_vendor' => true,
            'edit_marketplace_vendors' => true,
            'delete_marketplace_vendors' => true,
            'approve_marketplace_vendors' => true,
            'reject_marketplace_vendors' => true,
            'suspend_marketplace_vendors' => true,
            'view_marketplace_vendor_earnings' => true,
            'view_marketplace_vendor_orders' => true,
            'view_marketplace_vendor_all' => true,
            'manage_marketplace_commissions' => true,
            'edit_marketplace_commission' => true,
            'view_marketplace_commission_reports' => true,
            'adjust_marketplace_commission' => true,
            'manage_marketplace_withdrawals' => true,
            'approve_marketplace_withdrawal' => true,
            'reject_marketplace_withdrawal' => true,
            'process_marketplace_withdrawal' => true,
            'view_marketplace_withdrawal_reports' => true,
            'manage_marketplace_products' => true,
            'approve_marketplace_products' => true,
            'reject_marketplace_products' => true,
            'view_marketplace_dashboard' => true,
            'view_marketplace_reports' => true,
            'export_marketplace_reports' => true,
            'view_marketplace_analytics' => true,
            'manage_marketplace_audit_logs' => true,
            'manage_marketplace_notifications' => true,
            'manage_marketplace_emails' => true,
            'manage_marketplace_security' => true,
        ];

        foreach ($capabilities as $cap => $grant) {
            $admin_role->add_cap($cap, $grant);
        }
    }

    public static function remove_roles() {
        remove_role(self::ROLE_VENDOR);
        remove_role(self::ROLE_VENDOR_STAFF);
    }
}
