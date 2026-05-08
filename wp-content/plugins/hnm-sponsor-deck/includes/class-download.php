<?php
/**
 * Signed one-time download for the sponsor deck PDF.
 *
 * URL: /sponsor-deck-download/?hnm_deck_token=...
 *
 * Tokens:
 *   - Tied to a lead post ID + the email used to request.
 *   - Stored as transients with a 72-hour TTL.
 *   - Single-use: deleted on first successful download.
 *
 * @package HNM_Sponsor_Deck
 */

declare(strict_types=1);

namespace HNM\Deck;

defined('ABSPATH') || exit;

final class Download
{
    public const TTL                 = 72 * HOUR_IN_SECONDS;
    public const TRANSIENT_PREFIX    = 'hnm_deck_token_';
    public const PDF_OPTION          = 'hnm_deck_pdf_attachment_id';
    public const REWRITE_QUERY_VAR   = 'hnm_deck_dl';

    public static function register(): void
    {
        add_action('init', [self::class, 'add_rewrite']);
        add_filter('query_vars', [self::class, 'register_query_var']);
        add_action('template_redirect', [self::class, 'maybe_serve']);
    }

    public static function add_rewrite(): void
    {
        add_rewrite_rule('^sponsor-deck-download/?$', 'index.php?' . self::REWRITE_QUERY_VAR . '=1', 'top');
    }

    /**
     * @param array<int,string> $vars
     * @return array<int,string>
     */
    public static function register_query_var(array $vars): array
    {
        $vars[] = self::REWRITE_QUERY_VAR;
        return $vars;
    }

    public static function issue_token(int $lead_id, string $email): string
    {
        $raw = wp_generate_password(32, false, false);
        $token = sha1($raw . wp_salt('auth'));
        set_transient(self::TRANSIENT_PREFIX . $token, [
            'lead_id' => $lead_id,
            'email'   => sanitize_email($email),
        ], self::TTL);
        return $token;
    }

    public static function maybe_serve(): void
    {
        if (get_query_var(self::REWRITE_QUERY_VAR) !== '1') {
            return;
        }

        $token = isset($_GET['hnm_deck_token']) ? sanitize_text_field((string) $_GET['hnm_deck_token']) : '';
        if ($token === '') {
            wp_die(__('Missing token.', 'hnm-sponsor-deck'), '', ['response' => 400]);
        }

        $key = self::TRANSIENT_PREFIX . $token;
        $payload = get_transient($key);
        if (!is_array($payload) || empty($payload['lead_id'])) {
            wp_die(__('This download link has expired or is invalid. Please request the deck again.', 'hnm-sponsor-deck'), '', ['response' => 410]);
        }

        $attachment_id = (int) get_option(self::PDF_OPTION, 0);
        if ($attachment_id === 0) {
            wp_die(__('The sponsor deck PDF has not been configured yet. Admins: set it in Settings → Sponsor Deck.', 'hnm-sponsor-deck'), '', ['response' => 500]);
        }

        $file = get_attached_file($attachment_id);
        if ($file === false || !is_readable($file)) {
            wp_die(__('Sponsor deck file is not readable.', 'hnm-sponsor-deck'), '', ['response' => 500]);
        }

        // Single-use — invalidate.
        delete_transient($key);

        nocache_headers();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="her-next-mission-sponsor-deck.pdf"');
        header('Content-Length: ' . (string) filesize($file));
        readfile($file);
        exit;
    }
}
