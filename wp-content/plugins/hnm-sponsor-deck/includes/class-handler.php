<?php
/**
 * Sponsor deck request handler.
 *
 * Validates submitted form, captures lead via HNM_CRM, emails admins,
 * and redirects back with a one-time signed download token in the URL.
 *
 * @package HNM_Sponsor_Deck
 */

declare(strict_types=1);

namespace HNM\Deck;

defined('ABSPATH') || exit;

final class Handler
{
    public const ACTION  = 'hnm_deck_request';
    public const SOURCE  = 'sponsor-deck';
    public const ADMIN_EMAILS_OPTION = 'hnm_deck_admin_emails';

    public static function register(): void
    {
        add_action('admin_post_nopriv_' . self::ACTION, [self::class, 'handle']);
        add_action('admin_post_' . self::ACTION, [self::class, 'handle']);
    }

    public static function handle(): void
    {
        check_admin_referer(Form::NONCE);

        // Honeypot — silently succeed on bots.
        if (!empty($_POST['website'] ?? '')) {
            wp_safe_redirect(self::redirect_url('sent'));
            exit;
        }

        $data = [
            'name'     => (string) ($_POST['name'] ?? ''),
            'email'    => (string) ($_POST['email'] ?? ''),
            'phone'    => (string) ($_POST['phone'] ?? ''),
            'business' => (string) ($_POST['business'] ?? ''),
            'notes'    => (string) ($_POST['notes'] ?? ''),
            'source'   => self::SOURCE,
        ];

        if (!class_exists(\HNM\CRM\Lead_Repository::class)) {
            wp_die(__('CRM plugin missing. Activate hnm-crm.', 'hnm-sponsor-deck'));
        }

        $lead_id = \HNM\CRM\Lead_Repository::insert($data);

        if ($lead_id === 0) {
            wp_safe_redirect(self::redirect_url('error'));
            exit;
        }

        self::notify_admins($lead_id, $data);
        self::deliver_deck($lead_id, $data);

        wp_safe_redirect(self::redirect_url('sent'));
        exit;
    }

    /**
     * @param array<string,string> $data
     */
    private static function notify_admins(int $lead_id, array $data): void
    {
        $admins = self::admin_emails();
        if (count($admins) === 0) {
            return;
        }

        $subject = sprintf('[HNM] Sponsor deck request — %s', $data['business'] ?: $data['name']);
        $body    = "A new sponsor deck request was submitted.\n\n"
            . "Name:     {$data['name']}\n"
            . "Email:    {$data['email']}\n"
            . "Phone:    {$data['phone']}\n"
            . "Company:  {$data['business']}\n"
            . "Notes:    {$data['notes']}\n\n"
            . 'View: ' . get_edit_post_link($lead_id, 'raw');

        wp_mail($admins, $subject, $body);
    }

    /**
     * Email the requester a one-time signed download URL good for 72 hours.
     *
     * @param array<string,string> $data
     */
    private static function deliver_deck(int $lead_id, array $data): void
    {
        $token = Download::issue_token($lead_id, $data['email']);
        $url   = add_query_arg(['hnm_deck_token' => $token], home_url('/sponsor-deck-download/'));

        $subject = 'Her Next Mission — your sponsor deck';
        $body    = "Hi {$data['name']},\n\n"
            . "Thank you for requesting the Her Next Mission sponsor deck. Your one-time download link is below — it's good for 72 hours.\n\n"
            . $url . "\n\n"
            . "If you have questions, just reply to this email.\n\n"
            . "Pay it forward.\n— Her Next Mission Foundation";

        wp_mail($data['email'], $subject, $body);
    }

    /**
     * @return list<string>
     */
    private static function admin_emails(): array
    {
        $stored = (string) get_option(self::ADMIN_EMAILS_OPTION, '');
        if ($stored === '') {
            // Default: site admin email + jeff.cline@me.com if not the same.
            $defaults = [get_option('admin_email')];
            $defaults[] = 'jeff.cline@me.com';
            return array_values(array_unique(array_filter($defaults, 'is_email')));
        }
        $parts = array_map('trim', explode(',', $stored));
        return array_values(array_filter($parts, 'is_email'));
    }

    private static function redirect_url(string $status): string
    {
        $base = (string) ($_POST['redirect_to'] ?? home_url('/sponsor-deck/'));
        return add_query_arg('hnm_deck', $status, $base);
    }
}
