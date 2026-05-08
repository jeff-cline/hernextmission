<?php
/**
 * Plugin Name: HNM CRM
 * Plugin URI:  https://hernextmission.org
 * Description: Lead capture for Her Next Mission — sponsor deck requests, contact forms, and any other lead source roll up to a single Lead custom post type with admin-only access and CSV export.
 * Version:     0.1.0
 * Author:      Her Next Mission Foundation
 * License:     GPL-2.0-or-later
 * Text Domain: hnm-crm
 * Requires at least: 6.5
 * Requires PHP: 8.1
 *
 * @package HNM_CRM
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

define('HNM_CRM_VERSION', '0.1.0');
define('HNM_CRM_DIR', plugin_dir_path(__FILE__));

require_once HNM_CRM_DIR . 'includes/class-lead-cpt.php';
require_once HNM_CRM_DIR . 'includes/class-admin.php';
require_once HNM_CRM_DIR . 'includes/class-csv-export.php';
require_once HNM_CRM_DIR . 'includes/class-lead-repository.php';

add_action('init', static function (): void {
    \HNM\CRM\Lead_CPT::register();
});

add_action('admin_init', static function (): void {
    \HNM\CRM\Admin::init();
    \HNM\CRM\CSV_Export::init();
});
