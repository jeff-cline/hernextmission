<?php
/**
 * Block pattern category registration.
 *
 * Patterns themselves live as PHP files in /patterns/ and are auto-discovered
 * by WordPress 6.0+ via the pattern header comment.
 *
 * @package HerNextMission
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

add_action('init', static function (): void {
    register_block_pattern_category(
        'hnm',
        ['label' => __('Her Next Mission', 'her-next-mission')]
    );
});
