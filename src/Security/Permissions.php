<?php

namespace MultiVendorMarketplace\Security;

defined('ABSPATH') || exit;

class Permissions {
    /**
     * Check if user is vendor
     */
    public static function is_vendor($user_id = 0) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        if (!$user_id) {
            return false;
        }

        $user = get_user_by('ID', $user_id);
        return $user && in_array('marketplace_vendor', $user->roles, true);
    }

    /**
     * Check if user is vendor manager (admin)
     */
    public static function is_marketplace_admin($user_id = 0) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        if (!$user_id) {
            return false;
        }

        return user_can($user_id, 'manage_marketplace_vendors');
    }

    /**
     * Verify vendor ownership with security checks
     */
    public static function verify_vendor_ownership($vendor_id, $user_id = 0) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        if (!$user_id) {
            return false;
        }

        // Admins can manage any vendor
        if (self::is_marketplace_admin($user_id)) {
            return true;
        }

        // Vendor can only manage their own account
        $vendor_repo = new \MultiVendorMarketplace\Vendor\Repository();
        $vendor = $vendor_repo->get($vendor_id);

        if (!$vendor) {
            return false;
        }

        return $vendor->user_id === $user_id;
    }

    /**
     * Sanitize and validate input
     */
    public static function sanitize_vendor_data($data) {
        $sanitized = [];

        if (isset($data['store_name'])) {
            $sanitized['store_name'] = sanitize_text_field($data['store_name']);
        }

        if (isset($data['store_slug'])) {
            $sanitized['store_slug'] = sanitize_key($data['store_slug']);
        }

        if (isset($data['description'])) {
            $sanitized['description'] = wp_kses_post($data['description']);
        }

        if (isset($data['phone'])) {
            $sanitized['phone'] = sanitize_text_field($data['phone']);
        }

        if (isset($data['address'])) {
            $sanitized['address'] = sanitize_text_field($data['address']);
        }

        if (isset($data['city'])) {
            $sanitized['city'] = sanitize_text_field($data['city']);
        }

        if (isset($data['state'])) {
            $sanitized['state'] = sanitize_text_field($data['state']);
        }

        if (isset($data['country'])) {
            $sanitized['country'] = sanitize_text_field($data['country']);
        }

        if (isset($data['postal_code'])) {
            $sanitized['postal_code'] = sanitize_text_field($data['postal_code']);
        }

        return $sanitized;
    }

    /**
     * Check nonce
     */
    public static function verify_nonce($nonce_field, $action) {
        $nonce = isset($_POST[$nonce_field]) ? sanitize_key($_POST[$nonce_field]) : '';
        
        if (!$nonce || !wp_verify_nonce($nonce, $action)) {
            return false;
        }

        return true;
    }
}
