<?php
/**
 * CSV export for leads. Admin-only.
 *
 * Triggered via Tools → Export Leads, or by visiting:
 *   /wp-admin/admin.php?action=hnm_crm_export&_wpnonce=...
 *
 * @package HNM_CRM
 */

declare(strict_types=1);

namespace HNM\CRM;

defined('ABSPATH') || exit;

final class CSV_Export
{
    public const ACTION = 'hnm_crm_export';

    public static function init(): void
    {
        add_action('admin_menu', [self::class, 'add_menu']);
        add_action('admin_post_' . self::ACTION, [self::class, 'export']);
    }

    public static function add_menu(): void
    {
        add_submenu_page(
            'edit.php?post_type=' . Lead_CPT::POST_TYPE,
            __('Export Leads', 'hnm-crm'),
            __('Export CSV', 'hnm-crm'),
            'manage_options',
            'hnm-crm-export',
            [self::class, 'render_page']
        );
    }

    public static function render_page(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'hnm-crm'));
        }

        $url = wp_nonce_url(
            admin_url('admin-post.php?action=' . self::ACTION),
            self::ACTION
        );

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Export Leads', 'hnm-crm') . '</h1>';
        echo '<p>' . esc_html__('Download all captured leads as a CSV. Admin-only.', 'hnm-crm') . '</p>';
        echo '<p><a class="button button-primary" href="' . esc_url($url) . '">' . esc_html__('Download CSV', 'hnm-crm') . '</a></p>';
        echo '</div>';
    }

    public static function export(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'hnm-crm'), '', ['response' => 403]);
        }
        check_admin_referer(self::ACTION);

        $posts = get_posts([
            'post_type'      => Lead_CPT::POST_TYPE,
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post_status'    => 'any',
        ]);

        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="hnm-leads-' . gmdate('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'wb');
        fputcsv($out, ['Date', 'Name', 'Email', 'Phone', 'Business', 'Source', 'Notes']);

        foreach ($posts as $post) {
            fputcsv($out, [
                get_the_date('Y-m-d H:i:s', $post),
                (string) get_post_meta($post->ID, Lead_CPT::META_NAME, true),
                (string) get_post_meta($post->ID, Lead_CPT::META_EMAIL, true),
                (string) get_post_meta($post->ID, Lead_CPT::META_PHONE, true),
                (string) get_post_meta($post->ID, Lead_CPT::META_BUSINESS, true),
                (string) get_post_meta($post->ID, Lead_CPT::META_SOURCE, true),
                (string) get_post_meta($post->ID, Lead_CPT::META_NOTES, true),
            ]);
        }

        fclose($out);
        exit;
    }
}
