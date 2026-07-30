# TouchPix Integration — Capability Report

_Researched 2026-07-30. Account default number: **445419** (dashboard/support identifier, not an API credential)._

## Bottom line
**TouchPix has no public/documented REST API, no webhooks, and no Zapier/Make/GoHighLevel/native CRM connector.** `api.touchpix.com` is an internal backend (every path 301-redirects to `designs.touchpix.com`); there is no developer portal, Swagger/OpenAPI, or API docs. The automated "webhook → match a video to a raffle UUID → deliver → update CRM" architecture in the original build spec **cannot be built against TouchPix** — the API it assumes does not exist. TouchPix must be treated as a closed booth + cloud-gallery product.

## Confirmed capabilities (official)
- **Native sharing at the booth:** email, SMS (short code, STOP opt-out), WhatsApp, social, direct download, QR code, offline "Scanpix", a Sharing Station mode, and TV/AirPlay display with QR download. Source: touchpix.com/features, /knowledge-base/how-to-setup-a-sharing-station, /sms-terms-of-service.
- **Advanced Surveys / Data Collection extension (paid):** captures Text/Date/Multiple-choice/Checkbox/Dropdown inputs at the booth. **Every survey response is auto-linked to the exact photo/video/GIF the guest took.** Data exports to a clean file "ready to be imported into a CRM" — manual/file-based, no API, no machine-set reference ID. Source: touchpix.com/product/advanced-surveys-extension-14-day-access.
- **Cloud dashboard + galleries:** auto-sync of media to cloud; "My Events" dashboard searchable by event name/number; per-event web galleries; Dropbox + Google Drive full-quality upload. Source: touchpix.com/features, /knowledge-base/how-to-send-out-the-event-web-gallery-to-your-customer.
- **Retention:** event galleries are **deleted 6 months after creation** (date shown per event); export to Drive/Dropbox to preserve. Source: touchpix.com/reminder-touchpix-event-gallery-expiration.

## Unsupported / not documented
- Public REST API, webhooks, programmatic media-list retrieval, custom external-reference/tag on a session via API, and Zapier/Make/GHL/native CRM connectors. None found.

## Viable integration paths (choose one)
**A — Booth-native delivery + reconcile (simplest, fully official).**
Guest fills the GHL/Elite360 raffle form (→ CRM contact + raffle entry). At the booth, the Advanced Surveys extension captures the guest's email/phone (+ booth code) auto-linked to the video; TouchPix delivers the video natively (email/SMS/QR). Periodically export TouchPix survey+media data and import/match into the CRM. Not real-time; matching relies on email/phone or a booth code entered as a survey field.

**B — Own the delivery via Drive/Dropbox.**
Point TouchPix auto-upload at a Drive/Dropbox folder. Use Zapier/Make (which DO integrate with GHL + Drive/Dropbox) to pick up new videos, deliver them, and log to the CRM. Video↔entrant matching still needs an operational convention (one booth session per guest, timestamp correlation, or a scanned code entered as a survey field).

**C — Ask TouchPix directly.**
Contact TouchPix support/sales about any unpublished/enterprise data access. Nothing public exists.

## Note on the original WordPress-plugin spec
The `her-next-mission-raffle` WordPress plugin design assumes a WordPress site and a TouchPix API. **hernextmission.org is a static HTML site** (nginx hard-404s all WP/PHP paths) and **TouchPix has no API** — so that architecture is not applicable as written. The form's CRM capture is already handled by the embedded GoHighLevel/Elite360 form; the TouchPix side must use path A, B, or C above.
