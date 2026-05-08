<?php
/**
 * Lead repository — the only place that creates lead posts.
 *
 * @package HNM_CRM
 */

declare(strict_types=1);

namespace HNM\CRM;

defined('ABSPATH') || exit;

final class Lead_Repository
{
    /**
     * Insert a new lead.
     *
     * @param array{name:string,email:string,phone?:string,business?:string,source:string,notes?:string} $data
     * @return int Lead post ID, or 0 on failure.
     */
    public static function insert(array $data): int
    {
        $name     = sanitize_text_field($data['name'] ?? '');
        $email    = sanitize_email($data['email'] ?? '');
        $phone    = sanitize_text_field($data['phone'] ?? '');
        $business = sanitize_text_field($data['business'] ?? '');
        $source   = sanitize_text_field($data['source'] ?? 'unknown');
        $notes    = sanitize_textarea_field($data['notes'] ?? '');

        if ($name === '' || !is_email($email)) {
            return 0;
        }

        $post_id = wp_insert_post([
            'post_type'   => Lead_CPT::POST_TYPE,
            'post_status' => 'publish',
            'post_title'  => sprintf('%s · %s', $name, $email),
            'post_content'=> $notes,
            'meta_input'  => [
                Lead_CPT::META_NAME     => $name,
                Lead_CPT::META_EMAIL    => $email,
                Lead_CPT::META_PHONE    => $phone,
                Lead_CPT::META_BUSINESS => $business,
                Lead_CPT::META_SOURCE   => $source,
                Lead_CPT::META_NOTES    => $notes,
            ],
        ], true);

        if (is_wp_error($post_id) || !is_int($post_id)) {
            return 0;
        }

        do_action('hnm_crm_lead_created', $post_id, $data);

        return $post_id;
    }
}
