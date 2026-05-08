<?php
/**
 * Sponsor deck settings page.
 *
 * Settings → Sponsor Deck:
 *   - PDF attachment (Media-library picker by ID)
 *   - Comma-separated admin notification emails
 *
 * @package HNM_Sponsor_Deck
 */

declare(strict_types=1);

namespace HNM\Deck;

defined('ABSPATH') || exit;

final class Settings
{
    public const PAGE_SLUG     = 'hnm-sponsor-deck';
    public const OPTION_GROUP  = 'hnm_deck_settings';

    public static function register(): void
    {
        register_setting(self::OPTION_GROUP, Download::PDF_OPTION, [
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 0,
        ]);
        register_setting(self::OPTION_GROUP, Handler::ADMIN_EMAILS_OPTION, [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ]);

        add_action('admin_menu', [self::class, 'add_menu']);
    }

    public static function add_menu(): void
    {
        add_options_page(
            __('Sponsor Deck', 'hnm-sponsor-deck'),
            __('Sponsor Deck', 'hnm-sponsor-deck'),
            'manage_options',
            self::PAGE_SLUG,
            [self::class, 'render_page']
        );
    }

    public static function render_page(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'hnm-sponsor-deck'));
        }

        $pdf_id  = (int) get_option(Download::PDF_OPTION, 0);
        $emails  = (string) get_option(Handler::ADMIN_EMAILS_OPTION, '');
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Sponsor Deck', 'hnm-sponsor-deck'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields(self::OPTION_GROUP); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr(Download::PDF_OPTION); ?>">
                                <?php esc_html_e('PDF Attachment ID', 'hnm-sponsor-deck'); ?>
                            </label>
                        </th>
                        <td>
                            <input
                                name="<?php echo esc_attr(Download::PDF_OPTION); ?>"
                                id="<?php echo esc_attr(Download::PDF_OPTION); ?>"
                                type="number"
                                value="<?php echo esc_attr((string) $pdf_id); ?>"
                                class="regular-text"
                            />
                            <p class="description">
                                <?php esc_html_e('Upload the PDF in Media Library, copy the attachment ID from its URL (post=NNN), and paste it here.', 'hnm-sponsor-deck'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr(Handler::ADMIN_EMAILS_OPTION); ?>">
                                <?php esc_html_e('Admin notification emails', 'hnm-sponsor-deck'); ?>
                            </label>
                        </th>
                        <td>
                            <input
                                name="<?php echo esc_attr(Handler::ADMIN_EMAILS_OPTION); ?>"
                                id="<?php echo esc_attr(Handler::ADMIN_EMAILS_OPTION); ?>"
                                type="text"
                                value="<?php echo esc_attr($emails); ?>"
                                class="regular-text"
                            />
                            <p class="description">
                                <?php esc_html_e('Comma-separated. Defaults to the site admin email plus jeff.cline@me.com.', 'hnm-sponsor-deck'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

            <h2><?php esc_html_e('Setup checklist', 'hnm-sponsor-deck'); ?></h2>
            <ol>
                <li><?php esc_html_e('Create a Page at /sponsor-deck/ and add the [hnm_sponsor_deck_form] shortcode.', 'hnm-sponsor-deck'); ?></li>
                <li><?php esc_html_e('Upload the deck PDF to Media Library and paste its attachment ID above.', 'hnm-sponsor-deck'); ?></li>
                <li><?php esc_html_e('Test the flow: submit the form, confirm the email arrives with a working download link.', 'hnm-sponsor-deck'); ?></li>
            </ol>
        </div>
        <?php
    }
}
