<?php
/**
 * Sponsor deck request form.
 *
 * Usage: [hnm_sponsor_deck_form]
 *
 * @package HNM_Sponsor_Deck
 */

declare(strict_types=1);

namespace HNM\Deck;

defined('ABSPATH') || exit;

final class Form
{
    public const SHORTCODE = 'hnm_sponsor_deck_form';
    public const NONCE     = 'hnm_deck_request';

    public static function register(): void
    {
        add_shortcode(self::SHORTCODE, [self::class, 'render']);
    }

    public static function render(): string
    {
        $action = esc_url(admin_url('admin-post.php'));
        $nonce  = wp_nonce_field(self::NONCE, '_wpnonce', true, false);

        // If we just submitted with success, render the success state.
        $sent = isset($_GET['hnm_deck']) && $_GET['hnm_deck'] === 'sent';

        ob_start();
        ?>
        <div class="hnm-deck-form-wrap">
            <?php if ($sent) : ?>
                <div class="hnm-deck-success" role="status">
                    <h3>Thank you. The deck is on its way.</h3>
                    <p>We've sent the sponsor deck to the email you provided. A team member will follow up within two business days.</p>
                    <p><em>Pay it forward. Her next mission.</em></p>
                </div>
            <?php else : ?>
                <form class="hnm-deck-form" method="post" action="<?php echo $action; ?>" novalidate>
                    <?php echo $nonce; ?>
                    <input type="hidden" name="action" value="hnm_deck_request" />
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr(get_permalink()); ?>" />

                    <p class="hnm-deck-form__field">
                        <label for="hnm-deck-name">Name <span aria-hidden="true">*</span></label>
                        <input id="hnm-deck-name" type="text" name="name" required autocomplete="name" />
                    </p>

                    <p class="hnm-deck-form__field">
                        <label for="hnm-deck-email">Email <span aria-hidden="true">*</span></label>
                        <input id="hnm-deck-email" type="email" name="email" required autocomplete="email" />
                    </p>

                    <p class="hnm-deck-form__field">
                        <label for="hnm-deck-phone">Phone</label>
                        <input id="hnm-deck-phone" type="tel" name="phone" autocomplete="tel" />
                    </p>

                    <p class="hnm-deck-form__field">
                        <label for="hnm-deck-business">Company / Organization</label>
                        <input id="hnm-deck-business" type="text" name="business" autocomplete="organization" />
                    </p>

                    <p class="hnm-deck-form__field">
                        <label for="hnm-deck-notes">Tell us what you're looking for</label>
                        <textarea id="hnm-deck-notes" name="notes" rows="4"></textarea>
                    </p>

                    <p class="hnm-deck-form__hp" aria-hidden="true">
                        <label for="hnm-deck-website">Website (leave blank)</label>
                        <input id="hnm-deck-website" type="text" name="website" tabindex="-1" autocomplete="off" />
                    </p>

                    <p class="hnm-deck-form__submit">
                        <button type="submit" class="hnm-button hnm-button--gold">Request Sponsor Deck</button>
                    </p>

                    <p class="hnm-deck-form__legal">By submitting you consent to receive the deck and follow-up from Her Next Mission.</p>
                </form>
            <?php endif; ?>
        </div>

        <style>
            .hnm-deck-form-wrap { max-width: 560px; margin: 2rem auto; }
            .hnm-deck-form__field { display: block; margin-bottom: 1.25rem; }
            .hnm-deck-form__field label { display: block; font-weight: 600; margin-bottom: 0.4rem; font-size: 0.9rem; letter-spacing: 0.04em; text-transform: uppercase; color: #0A2540; }
            .hnm-deck-form__field input,
            .hnm-deck-form__field textarea {
                width: 100%;
                padding: 0.85rem 1rem;
                border: 1px solid #E5DFD3;
                border-radius: 6px;
                font: inherit;
                background: #FAFAF6;
            }
            .hnm-deck-form__field input:focus,
            .hnm-deck-form__field textarea:focus {
                outline: 3px solid #C9A961;
                outline-offset: 2px;
                border-color: #C9A961;
            }
            .hnm-deck-form__hp { position: absolute; left: -10000px; top: auto; width: 1px; height: 1px; overflow: hidden; }
            .hnm-button--gold { background: #C9A961; color: #061A2E; border: 0; padding: 0.95rem 2rem; border-radius: 999px; font-weight: 600; cursor: pointer; }
            .hnm-button--gold:hover { background: #E5D4A1; }
            .hnm-deck-form__legal { font-size: 0.8rem; color: #4A4A4A; }
            .hnm-deck-success { background: #F5F0E8; padding: 2rem; border-left: 4px solid #C9A961; border-radius: 4px; }
            .hnm-deck-success h3 { margin-top: 0; color: #0A2540; }
        </style>
        <?php
        return (string) ob_get_clean();
    }
}
