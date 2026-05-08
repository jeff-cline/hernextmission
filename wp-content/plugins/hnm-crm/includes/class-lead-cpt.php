<?php
/**
 * Lead custom post type registration.
 *
 * @package HNM_CRM
 */

declare(strict_types=1);

namespace HNM\CRM;

defined('ABSPATH') || exit;

final class Lead_CPT
{
    public const POST_TYPE = 'hnm_lead';

    public const META_NAME     = '_hnm_name';
    public const META_EMAIL    = '_hnm_email';
    public const META_PHONE    = '_hnm_phone';
    public const META_BUSINESS = '_hnm_business';
    public const META_SOURCE   = '_hnm_source';
    public const META_NOTES    = '_hnm_notes';

    public static function register(): void
    {
        register_post_type(self::POST_TYPE, [
            'label'           => __('Leads', 'hnm-crm'),
            'labels'          => [
                'singular_name' => __('Lead', 'hnm-crm'),
                'add_new_item'  => __('Add Lead', 'hnm-crm'),
                'edit_item'     => __('Edit Lead', 'hnm-crm'),
                'menu_name'     => __('Leads', 'hnm-crm'),
            ],
            // Admin-only — never publicly visible.
            'public'              => false,
            'show_ui'             => true,
            'show_in_rest'        => false,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => false,
            'menu_icon'           => 'dashicons-businessperson',
            'supports'            => ['title', 'editor', 'custom-fields'],
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            'capabilities'        => [
                'create_posts' => 'manage_options',
            ],
            'has_archive'         => false,
            'rewrite'             => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
        ]);

        // Register meta keys so they're queryable.
        $meta_keys = [
            self::META_NAME, self::META_EMAIL, self::META_PHONE,
            self::META_BUSINESS, self::META_SOURCE, self::META_NOTES,
        ];
        foreach ($meta_keys as $key) {
            register_post_meta(self::POST_TYPE, $key, [
                'type'              => 'string',
                'single'            => true,
                'show_in_rest'      => false,
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback'     => static fn(): bool => current_user_can('manage_options'),
            ]);
        }
    }
}
