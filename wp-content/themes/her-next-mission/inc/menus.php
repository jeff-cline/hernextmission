<?php
/**
 * Navigation menu registration.
 *
 * Primary nav (header):  Home, Podcasts, Events, About, Book a Call
 * Footer — Categories:   the core themes (Wellbeing, Transition, Understanding, Clarity, Identity)
 * Footer — Give:         Donate, Become a Sponsor, Featured Sponsors
 * Footer — Meta:         Privacy, Contact, etc.
 *
 * @package HerNextMission
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

add_action('after_setup_theme', static function (): void {
    register_nav_menus([
        'primary'           => __('Primary (header)', 'her-next-mission'),
        'footer-categories' => __('Footer — Categories', 'her-next-mission'),
        'footer-give'       => __('Footer — Give & Sponsor', 'her-next-mission'),
        'footer-meta'       => __('Footer — Meta', 'her-next-mission'),
    ]);
});
