<?php
/**
 * Plugin Name: HNM Sponsor Deck
 * Plugin URI:  https://hernextmission.org
 * Description: Gated sponsor-deck request flow. Renders a request form via [hnm_sponsor_deck_form] shortcode (or block fallback). On submit, validates, captures the lead through HNM CRM, emails admins, and returns a one-time signed download URL for the PDF. Configure the deck PDF in Settings → Sponsor Deck.
 * Version:     0.2.0
 * Author:      Her Next Mission
 * License:     GPL-2.0-or-later
 * Text Domain: hnm-sponsor-deck
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Requires Plugins: hnm-crm
 *
 * @package HNM_Sponsor_Deck
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

define('HNM_DECK_VERSION', '0.2.0');
define('HNM_DECK_DIR', plugin_dir_path(__FILE__));
define('HNM_DECK_URL', plugin_dir_url(__FILE__));

require_once HNM_DECK_DIR . 'includes/class-form.php';
require_once HNM_DECK_DIR . 'includes/class-handler.php';
require_once HNM_DECK_DIR . 'includes/class-download.php';
require_once HNM_DECK_DIR . 'includes/class-settings.php';

add_action('init', static function (): void {
    \HNM\Deck\Form::register();
    \HNM\Deck\Handler::register();
    \HNM\Deck\Download::register();
});

add_action('admin_init', static function (): void {
    \HNM\Deck\Settings::register();
});
