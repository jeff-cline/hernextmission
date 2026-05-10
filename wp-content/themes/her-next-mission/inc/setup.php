<?php
/**
 * Theme setup: supports, scripts, styles.
 *
 * @package HerNextMission
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');

    add_editor_style('assets/css/main.css');

    load_theme_textdomain('her-next-mission', __DIR__ . '/../languages');
});

add_action('wp_enqueue_scripts', static function (): void {
    $theme_uri = get_stylesheet_directory_uri();
    $version = (string) wp_get_theme()->get('Version');

    wp_enqueue_style(
        'hnm-fonts',
        'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400;1,9..144,500&family=Inter:wght@400;500;600;700;800&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'hnm-main',
        $theme_uri . '/assets/css/main.css',
        ['hnm-fonts'],
        $version
    );

    wp_enqueue_script(
        'hnm-rocket',
        $theme_uri . '/assets/js/rocket-launch.js',
        [],
        $version,
        true
    );
});

add_filter('big_image_size_threshold', static fn(): int => 2560);

add_filter('upload_mimes', static function (array $mimes): array {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

/**
 * Print the compass favicon (and apple-touch-icon equivalent).
 * Lives in the theme so it ships with deployment, no media-library upload needed.
 */
add_action('wp_head', static function (): void {
    $theme_uri = get_stylesheet_directory_uri();
    echo '<link rel="icon" type="image/svg+xml" href="' . esc_url($theme_uri . '/assets/images/favicon.svg') . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url($theme_uri . '/assets/images/favicon.svg') . '">' . "\n";
    echo '<meta name="theme-color" content="#1F2A52">' . "\n";
}, 1);
