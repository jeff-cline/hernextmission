# hnm-raffle

HTML-native raffle service for `hernextmission.org/raffle` — **no WordPress**. Owns the pieces
GoHighLevel/Elite360 + TouchPix can't: opaque **booth codes**, **QR**, a branded **confirmation
page**, and an **admin/operator reconciliation** console. Node + Express + a durable JSON store,
run under pm2 behind nginx (same pattern as the other apps on the box).

## Data flow
1. Guest submits the embedded **GHL/Elite360** form → GHL creates the CRM contact (native).
2. A GHL **workflow → outbound webhook** POSTs the submission to `POST /api/raffle/intake`
   (header `x-hnm-secret`). We create a raffle entry + booth code + confirmation token and return them.
3. Guest sees booth code + QR at `GET /r/:token` (link it from the GHL confirmation/redirect or SMS).
4. Booth captures the 360 video (**TouchPix native** — it has no API, see `../docs/touchpix-integration.md`).
   A Zap/Make/operator POSTs the final video URL to `POST /api/raffle/video` → matched to the entry →
   surfaced for delivery. Delivery (GHL custom-field update or Twilio) is pluggable.

## Endpoints
- `GET  /api/raffle/health`
- `POST /api/raffle/intake`      — GHL webhook target (create/dedup entry, returns boothCode + confirmUrl)
- `GET  /r/:token`               — branded guest confirmation (booth code + QR)
- `POST /api/raffle/video`       — set the final video URL for an entry (match by boothCode/email/phone)
- `GET  /raffle-admin`           — dashboard, assign video URL, CSV export (basic auth)
- `GET  /raffle-operator`        — booth attendant: scan/search booth code, mark ready (basic auth)
- `GET  /api/raffle/export.csv`  — auditable entry export (basic auth)

## Run
```
cp .env.example .env   # fill in secrets
npm install
npm start              # 127.0.0.1:$PORT
```
nginx proxies `/api/raffle/`, `/r/`, `/raffle-admin`, `/raffle-operator` to this port; everything
else on hernextmission.org stays static and untouched.

## Not done yet (needs credentials / a decision)
- Wire the **GHL outbound webhook** to `/api/raffle/intake` (needs Elite360/GHL access).
- Pick the **TouchPix path** (A native+reconcile, or B Drive→Zapier) for `/api/raffle/video`.
- Wire **delivery** (update GHL contact custom field → GHL sends, or Twilio direct).
