<?php
if (!defined('ABSPATH')) exit;

function hnm_briefings_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);

    register_nav_menus([
        'primary' => __('Primary Menu', 'hnm-briefings'),
        'footer' => __('Footer Menu', 'hnm-briefings'),
    ]);
}
add_action('after_setup_theme', 'hnm_briefings_setup');

function hnm_briefings_enqueue_assets() {
    wp_enqueue_style('hnm-fonts', 'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400;1,9..144,500&family=Inter:wght@400;500;600;700;800&display=swap', [], null);
    wp_enqueue_style('hnm-site', get_template_directory_uri() . '/assets/css/site.css', [], 'v110');
    wp_enqueue_script('hnm-site', get_template_directory_uri() . '/assets/js/site.js', [], 'v110', true);
}
add_action('wp_enqueue_scripts', 'hnm_briefings_enqueue_assets');

function hnm_default_primary_links() {
    $links = [
        home_url('/') => 'Home',
        'https://hernextmission.org/about.html' => 'About',
        'https://hernextmission.org/programs.html' => 'Programs',
        'https://hernextmission.org/coaching.html' => 'Coaching',
        'https://hernextmission.org/transition-services.html' => 'Services',
        'https://hernextmission.org/podcast.html' => 'Podcast',
        'https://hernextmission.org/events.html' => 'Events',
        'https://hernextmission.org/sponsors.html' => 'Sponsors',
        'https://hernextmission.org/give.html' => 'Give',
    ];
    foreach ($links as $url => $label) {
        $is_current = untrailingslashit($url) === untrailingslashit(home_url(add_query_arg([], $GLOBALS['wp']->request ?? '')));
        printf('<a href="%s"%s>%s</a>', esc_url($url), $is_current ? ' aria-current="page"' : '', esc_html($label));
    }
    echo '<a href="' . esc_url(home_url('/')) . '">Blog</a>';
}
