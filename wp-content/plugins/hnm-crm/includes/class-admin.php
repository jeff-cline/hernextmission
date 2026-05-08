<?php
/**
 * Admin UI for leads — list-table columns and detail meta box.
 *
 * @package HNM_CRM
 */

declare(strict_types=1);

namespace HNM\CRM;

defined('ABSPATH') || exit;

final class Admin
{
    public static function init(): void
    {
        $cpt = Lead_CPT::POST_TYPE;

        add_filter("manage_{$cpt}_posts_columns", [self::class, 'columns']);
        add_action("manage_{$cpt}_posts_custom_column", [self::class, 'render_column'], 10, 2);
        add_filter("manage_edit-{$cpt}_sortable_columns", [self::class, 'sortable_columns']);
        add_action('add_meta_boxes', [self::class, 'add_meta_boxes']);
    }

    /**
     * @param array<string,string> $columns
     * @return array<string,string>
     */
    public static function columns(array $columns): array
    {
        return [
            'cb'         => $columns['cb'] ?? '',
            'title'      => __('Lead', 'hnm-crm'),
            'hnm_email'  => __('Email', 'hnm-crm'),
            'hnm_phone'  => __('Phone', 'hnm-crm'),
            'hnm_source' => __('Source', 'hnm-crm'),
            'date'       => $columns['date'] ?? __('Date', 'hnm-crm'),
        ];
    }

    public static function render_column(string $column, int $post_id): void
    {
        switch ($column) {
            case 'hnm_email':
                $value = (string) get_post_meta($post_id, Lead_CPT::META_EMAIL, true);
                echo $value !== ''
                    ? '<a href="mailto:' . esc_attr($value) . '">' . esc_html($value) . '</a>'
                    : '—';
                break;
            case 'hnm_phone':
                echo esc_html((string) get_post_meta($post_id, Lead_CPT::META_PHONE, true) ?: '—');
                break;
            case 'hnm_source':
                $source = (string) get_post_meta($post_id, Lead_CPT::META_SOURCE, true);
                echo $source !== ''
                    ? '<code>' . esc_html($source) . '</code>'
                    : '—';
                break;
        }
    }

    /**
     * @param array<string,string> $columns
     * @return array<string,string>
     */
    public static function sortable_columns(array $columns): array
    {
        $columns['hnm_source'] = 'hnm_source';
        return $columns;
    }

    public static function add_meta_boxes(): void
    {
        add_meta_box(
            'hnm_lead_details',
            __('Lead Details', 'hnm-crm'),
            [self::class, 'render_meta_box'],
            Lead_CPT::POST_TYPE,
            'normal',
            'high'
        );
    }

    public static function render_meta_box(\WP_Post $post): void
    {
        $rows = [
            __('Name', 'hnm-crm')     => Lead_CPT::META_NAME,
            __('Email', 'hnm-crm')    => Lead_CPT::META_EMAIL,
            __('Phone', 'hnm-crm')    => Lead_CPT::META_PHONE,
            __('Business', 'hnm-crm') => Lead_CPT::META_BUSINESS,
            __('Source', 'hnm-crm')   => Lead_CPT::META_SOURCE,
        ];

        echo '<table class="form-table" style="margin-top:0">';
        foreach ($rows as $label => $key) {
            $value = (string) get_post_meta($post->ID, $key, true);
            printf(
                '<tr><th style="width:160px;text-align:left">%s</th><td>%s</td></tr>',
                esc_html($label),
                esc_html($value)
            );
        }
        echo '</table>';

        echo '<p style="margin-top:1.5em"><em>' . esc_html__('Notes are stored in the post content area below.', 'hnm-crm') . '</em></p>';
    }
}
