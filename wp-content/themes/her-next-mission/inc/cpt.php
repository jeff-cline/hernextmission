<?php
/**
 * Custom post types and taxonomies.
 *
 * Post types:
 *   - podcast_episode  : podcast episodes (Apple/Spotify embed in custom field)
 *   - story            : "Her Story" submitted/featured stories
 *   - event            : Summit, retreats, bootcamps
 *   - sponsor          : featured sponsors (logo wall + featured pages)
 *
 * Taxonomy:
 *   - hnm_theme        : Wellbeing, Transition, Understanding, Clarity, Identity
 *                        (drives the footer "Categories" nav)
 *
 * @package HerNextMission
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

add_action('init', static function (): void {
    register_post_type('podcast_episode', [
        'label'        => __('Podcast Episodes', 'her-next-mission'),
        'labels'       => [
            'singular_name' => __('Podcast Episode', 'her-next-mission'),
            'add_new_item'  => __('Add New Episode', 'her-next-mission'),
            'edit_item'     => __('Edit Episode', 'her-next-mission'),
            'menu_name'     => __('Podcast', 'her-next-mission'),
        ],
        'public'       => true,
        'show_in_rest' => true,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'has_archive'  => 'podcast',
        'rewrite'      => ['slug' => 'podcast/episodes', 'with_front' => false],
        'menu_icon'    => 'dashicons-microphone',
    ]);

    register_post_type('story', [
        'label'        => __('Stories', 'her-next-mission'),
        'labels'       => [
            'singular_name' => __('Story', 'her-next-mission'),
            'add_new_item'  => __('Add New Story', 'her-next-mission'),
            'edit_item'     => __('Edit Story', 'her-next-mission'),
            'menu_name'     => __('Stories', 'her-next-mission'),
        ],
        'public'       => true,
        'show_in_rest' => true,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt'],
        'has_archive'  => 'stories',
        'rewrite'      => ['slug' => 'stories', 'with_front' => false],
        'menu_icon'    => 'dashicons-format-quote',
    ]);

    register_post_type('event', [
        'label'        => __('Events', 'her-next-mission'),
        'labels'       => [
            'singular_name' => __('Event', 'her-next-mission'),
            'add_new_item'  => __('Add New Event', 'her-next-mission'),
            'edit_item'     => __('Edit Event', 'her-next-mission'),
            'menu_name'     => __('Events', 'her-next-mission'),
        ],
        'public'       => true,
        'show_in_rest' => true,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'has_archive'  => 'events',
        'rewrite'      => ['slug' => 'events', 'with_front' => false],
        'menu_icon'    => 'dashicons-calendar-alt',
    ]);

    register_post_type('sponsor', [
        'label'        => __('Sponsors', 'her-next-mission'),
        'labels'       => [
            'singular_name' => __('Sponsor', 'her-next-mission'),
            'add_new_item'  => __('Add New Sponsor', 'her-next-mission'),
            'edit_item'     => __('Edit Sponsor', 'her-next-mission'),
            'menu_name'     => __('Sponsors', 'her-next-mission'),
        ],
        'public'       => true,
        'show_in_rest' => true,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt'],
        'has_archive'  => 'sponsors',
        'rewrite'      => ['slug' => 'sponsors', 'with_front' => false],
        'menu_icon'    => 'dashicons-awards',
    ]);

    register_taxonomy('hnm_theme', ['post', 'story'], [
        'label'        => __('Themes', 'her-next-mission'),
        'labels'       => [
            'singular_name' => __('Theme', 'her-next-mission'),
            'menu_name'     => __('Themes', 'her-next-mission'),
        ],
        'public'       => true,
        'show_in_rest' => true,
        'hierarchical' => true,
        'rewrite'      => ['slug' => 'theme', 'with_front' => false],
    ]);

    register_taxonomy('sponsor_tier', ['sponsor'], [
        'label'        => __('Sponsor Tier', 'her-next-mission'),
        'public'       => true,
        'show_in_rest' => true,
        'hierarchical' => false,
        'rewrite'      => ['slug' => 'sponsor-tier', 'with_front' => false],
    ]);
});

/**
 * Seed the core taxonomy terms on theme activation.
 *
 * Wellbeing, Transition, Understanding, Clarity, Identity are the core
 * themes — they drive the footer category nav.
 */
add_action('after_switch_theme', static function (): void {
    $themes = [
        'Wellbeing'     => 'wellbeing',
        'Transition'    => 'transition',
        'Understanding' => 'understanding',
        'Clarity'       => 'clarity',
        'Identity'      => 'identity',
    ];
    foreach ($themes as $name => $slug) {
        if (!term_exists($name, 'hnm_theme')) {
            wp_insert_term($name, 'hnm_theme', ['slug' => $slug]);
        }
    }

    $tiers = [
        'Mission Partner ($250k)' => 'mission-partner',
        'Lead Sponsor ($50k)'     => 'lead-sponsor',
        'Featured Sponsor ($25k)' => 'featured-sponsor',
        'Supporting Sponsor'      => 'supporting-sponsor',
    ];
    foreach ($tiers as $name => $slug) {
        if (!term_exists($name, 'sponsor_tier')) {
            wp_insert_term($name, 'sponsor_tier', ['slug' => $slug]);
        }
    }
});
