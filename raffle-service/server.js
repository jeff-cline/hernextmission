"use strict";
/**
 * Her Next Mission — 360 Raffle service (HTML-native, no WordPress).
 *
 * Owns the pieces GoHighLevel/Elite360 + TouchPix can't: opaque booth codes,
 * QR, a branded confirmation page, and an admin/operator reconciliation console.
 *
 * Data flow (see docs/touchpix-integration.md):
 *   1. Guest submits the embedded GHL form  → GHL creates the CRM contact (native).
 *   2. GHL workflow "outbound webhook" POSTs the submission to  POST /api/raffle/intake
 *      → we create a raffle entry + booth code + confirmation token, return them.
 *   3. Guest sees booth code + QR (branded /r/:token, or GHL sends the link).
 *   4. Booth captures the video (TouchPix native). A Zap/Make/operator posts the
 *      final video URL to  POST /api/raffle/video  → matched → delivery triggered.
 *
 * Nothing here calls TouchPix (it has no API). Delivery + CRM update are pluggable.
 */
const express = require("express");
const QRCode = require("qrcode");
const store = require("./store");
const U = require("./util");

const PORT = parseInt(process.env.PORT || "3210", 10);
const PUBLIC_BASE = process.env.HNM_PUBLIC_BASE || "https://hernextmission.org";
const INTAKE_SECRET = process.env.HNM_INTAKE_SECRET || "";
const VIDEO_SECRET = process.env.HNM_VIDEO_SECRET || "";
const ADMIN_USER = process.env.HNM_ADMIN_USER || "hnm";
const ADMIN_PASS = process.env.HNM_ADMIN_PASS || "";
const IP_SALT = process.env.HNM_IP_SALT || "hnm-salt";
const EVENT_NAME = process.env.HNM_EVENT_NAME || "Her Next Mission 360 Raffle";
const TERMS_VERSION = process.env.HNM_TERMS_VERSION || "2026-07-30";

const app = express();
app.disable("x-powered-by");
app.set("trust proxy", true);
app.use(express.json({ limit: "128kb" }));
app.use(express.urlencoded({ extended: true, limit: "128kb" }));

// ---------- tiny in-memory rate limiter ----------
const hits = new Map();
function rateLimit(key, max, windowMs) {
  const now = Date.now();
  const arr = (hits.get(key) || []).filter((t) => now - t < windowMs);
  arr.push(now);
  hits.set(key, arr);
  return arr.length <= max;
}

// ---------- brand shell ----------
const CSS = `
  :root{--purple-deep:#2A1A52;--purple:#5B3FA8;--purple-soft:#8C77C7;--gold:#D4A537;--gold-soft:#F2D278;--gold-glow:#FCEFC0;--gold-deep:#8C6618;--navy-deep:#050A22;--ivory:#FAF7EE;--ink:#0A0A14}
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Inter',-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;color:var(--ivory);
    background:radial-gradient(1000px 600px at 20% -10%,rgba(139,119,199,.35),transparent 60%),linear-gradient(160deg,#33205f,var(--purple-deep) 50%,var(--navy-deep));min-height:100vh}
  .wrap{max-width:560px;margin:0 auto;padding:28px 20px}
  .card{background:var(--ivory);color:var(--ink);border-radius:20px;overflow:hidden;box-shadow:0 30px 70px rgba(5,10,34,.5),0 0 0 1px rgba(212,165,55,.5)}
  .card h1{font-family:Georgia,serif}
  .accent{height:5px;background:linear-gradient(90deg,var(--gold-deep),var(--gold),var(--gold-glow),var(--gold),var(--gold-deep))}
  a{color:var(--gold-deep)}
`;
function page(title, body) {
  return `<!doctype html><html lang="en"><head><meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1"><title>${U.esc(title)}</title>
  <meta name="robots" content="noindex"><style>${CSS}</style></head><body>${body}</body></html>`;
}

// ---------- health ----------
app.get("/api/raffle/health", (req, res) => {
  res.json({ ok: true, service: "hnm-raffle", entries: store.all().length, event: EVENT_NAME });
});

// ---------- map a loose payload (GHL webhook / test) into our fields ----------
function mapSubmission(b) {
  const full = String(b.full_name || b.name || "").trim();
  let first = b.firstName || b.first_name || "";
  let last = b.lastName || b.last_name || "";
  if (!first && full) {
    const parts = full.split(/\s+/);
    first = parts.shift() || "";
    last = parts.join(" ");
  }
  const truthy = (v) => v === true || v === "true" || v === "yes" || v === "on" || v === 1 || v === "1";
  return {
    first_name: String(first || "").trim(),
    last_name: String(last || "").trim(),
    email: U.normalizeEmail(b.email),
    phone: U.normalizePhone(b.phone || b.phone_number),
    organization: String(b.organization || b.company || "").trim(),
    source: String(b.source || b.how_heard || b["How did you hear about us?"] || "").trim(),
    next_mission: String(b.nextMission || b.next_mission || b.message || "").trim(),
    consent_sms: truthy(b.consentSms || b.consent_sms || b.sms_consent),
    crm_contact_id: String(b.contact_id || b.crm_contact_id || "").trim(),
    utm: {
      source: b.utm_source || "", medium: b.utm_medium || "", campaign: b.utm_campaign || "",
      content: b.utm_content || "", term: b.utm_term || "",
    },
    source_url: String(b.source_url || b.page_url || "").trim(),
  };
}

// ---------- INTAKE (called by the GHL form-submission webhook) ----------
app.post("/api/raffle/intake", (req, res) => {
  const ip = req.ip;
  if (!rateLimit("intake:" + ip, 20, 60_000)) return res.status(429).json({ error: "rate_limited" });
  if (INTAKE_SECRET && req.get("x-hnm-secret") !== INTAKE_SECRET) return res.status(401).json({ error: "unauthorized" });

  const b = req.body || {};
  const m = mapSubmission(b);
  if (!m.first_name && !m.email && !m.phone) return res.status(400).json({ error: "name, email or phone required" });

  // Idempotency + dedup: explicit key, else same email/phone → return the existing entry.
  const idem = String(b.idempotencyKey || b.idempotency_key || "").trim();
  let existing = (idem && store.byIdempotencyKey(idem)) || store.byEmail(m.email) || store.byPhone(m.phone) || null;
  if (existing) {
    // enrich blanks without creating a duplicate ticket
    const patch = {};
    for (const k of ["first_name", "last_name", "email", "phone", "organization", "source", "next_mission"])
      if (m[k] && !existing[k]) patch[k] = m[k];
    if (m.crm_contact_id && !existing.crm_contact_id) patch.crm_contact_id = m.crm_contact_id;
    if (Object.keys(patch).length) store.update(existing.id, patch);
    store.log(existing.id, "intake_duplicate", "returned existing entry");
    return res.json({ ok: true, duplicate: true, boothCode: existing.booth_code, token: existing.token, confirmUrl: `${PUBLIC_BASE}/r/${existing.token}` });
  }

  const now = new Date().toISOString();
  const entry = {
    id: U.uuid(),
    booth_code: U.generateBoothCode(store.boothCodeExists.bind(store)),
    token: U.opaqueToken(),
    idempotency_key: idem || null,
    ...m,
    crm_status: m.crm_contact_id ? "crm_synced" : "crm_pending",
    raffle_status: "registered",
    video_status: "awaiting_capture",
    video_url: "",
    delivery_status: "pending",
    consent_terms_version: TERMS_VERSION,
    consent_timestamp: now,
    ip_hash: U.hashIp(ip, IP_SALT),
    user_agent: String(req.get("user-agent") || "").slice(0, 300),
    event_name: EVENT_NAME,
    created_at: now,
    updated_at: now,
    log: [{ t: now, event: "registered", detail: "intake received" }],
  };
  store.insert(entry);
  return res.json({ ok: true, boothCode: entry.booth_code, token: entry.token, confirmUrl: `${PUBLIC_BASE}/r/${entry.token}` });
});

// ---------- GUEST CONFIRMATION PAGE ----------
app.get("/r/:token", async (req, res) => {
  const e = store.byToken(req.params.token);
  if (!e) return res.status(404).send(page("Not found", `<div class="wrap"><div class="card"><div class="accent"></div><div style="padding:28px"><h1>Ticket not found</h1><p>Double-check your link, or ask a booth attendant for help.</p></div></div></div>`));
  let qr = "";
  try { qr = await QRCode.toDataURL(e.booth_code, { margin: 1, width: 320, color: { dark: "#2A1A52", light: "#FFFFFF" } }); } catch {}
  const dest = e.email ? U.maskEmail(e.email) : U.maskPhone(e.phone);
  res.send(page("Your Raffle Ticket — Her Next Mission", `
    <div class="wrap"><div class="card"><div class="accent"></div>
      <div style="background:linear-gradient(165deg,#3a2470,var(--purple-deep));color:var(--ivory);padding:22px 26px">
        <div style="font-size:.7rem;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-soft);font-weight:800">★ Admit One ★</div>
        <h1 style="font-size:1.7rem;margin-top:6px">You're entered${e.first_name ? ", " + U.esc(e.first_name) : ""}! 🎟️</h1>
      </div>
      <div style="padding:26px;text-align:center">
        <p style="color:#555">Show this code at the 360° booth:</p>
        <div style="font-family:Georgia,serif;font-size:2.6rem;font-weight:700;letter-spacing:.04em;color:var(--purple-deep);margin:8px 0 14px">${U.esc(e.booth_code)}</div>
        ${qr ? `<img src="${qr}" alt="booth code QR" width="220" height="220" style="border-radius:12px">` : ""}
        <ol style="text-align:left;max-width:360px;margin:20px auto 0;color:#333;line-height:1.7">
          <li>Show this code (or QR) to the booth attendant.</li>
          <li>Step in and capture your 360° video.</li>
          <li>We'll send your video to <b>${U.esc(dest) || "you"}</b>.</li>
        </ol>
        <p style="color:#888;font-size:.8rem;margin-top:20px">${U.esc(EVENT_NAME)} · Keep this page or screenshot it.</p>
      </div>
    </div></div>`));
});

// ---------- VIDEO READY (Zap/Make/operator posts the final URL) ----------
app.post("/api/raffle/video", (req, res) => {
  if (VIDEO_SECRET && req.get("x-hnm-secret") !== VIDEO_SECRET) return res.status(401).json({ error: "unauthorized" });
  const b = req.body || {};
  const mediaId = String(b.mediaId || b.media_id || "").trim();
  const url = String(b.videoUrl || b.video_url || "").trim();
  if (!url) return res.status(400).json({ error: "videoUrl required" });

  let e = (b.boothCode && store.byBoothCode(b.boothCode)) || (b.email && store.byEmail(b.email)) || (b.phone && store.byPhone(b.phone)) || null;
  if (!e) return res.status(404).json({ error: "no matching raffle entry", hint: "provide boothCode, email or phone" });

  // idempotent by mediaId
  if (mediaId && e.touchpix_media_id === mediaId && e.video_status === "delivered")
    return res.json({ ok: true, alreadyDelivered: true, id: e.id });

  store.update(e.id, { video_url: url, touchpix_media_id: mediaId || e.touchpix_media_id || "", video_status: "video_ready", delivery_status: "pending" });
  store.log(e.id, "video_ready", url);

  // Delivery is pluggable. When GHL/Twilio creds are wired, send here and mark delivered.
  // For now we mark delivery_pending and surface it in the admin console for a manual/automated send.
  return res.json({ ok: true, id: e.id, boothCode: e.booth_code, status: "video_ready" });
});

// ---------- BASIC AUTH for admin/operator ----------
function auth(req, res, next) {
  if (!ADMIN_PASS) return res.status(503).send("Admin not configured (set HNM_ADMIN_PASS).");
  const h = req.get("authorization") || "";
  const [, b64] = h.split(" ");
  const [u, p] = Buffer.from(b64 || "", "base64").toString().split(":");
  if (u === ADMIN_USER && p === ADMIN_PASS) return next();
  res.set("WWW-Authenticate", 'Basic realm="HNM Raffle"').status(401).send("Auth required");
}

// ---------- ADMIN DASHBOARD ----------
app.get("/raffle-admin", auth, (req, res) => {
  const all = store.all().sort((a, b) => (a.created_at < b.created_at ? 1 : -1));
  const c = {
    total: all.length,
    awaiting: all.filter((e) => e.video_status === "awaiting_capture").length,
    ready: all.filter((e) => e.video_status === "video_ready").length,
    delivered: all.filter((e) => e.video_status === "delivered").length,
    review: all.filter((e) => e.video_status === "manual_review").length,
  };
  const stat = (l, v, col) => `<div style="background:#fff;border:1px solid #e5e0d2;border-radius:12px;padding:14px 16px;min-width:120px"><div style="font-size:1.8rem;font-weight:800;color:${col}">${v}</div><div style="font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;color:#888">${l}</div></div>`;
  const rows = all.map((e) => `<tr>
      <td style="font-family:monospace;font-weight:700">${U.esc(e.booth_code)}</td>
      <td>${U.esc((e.first_name + " " + e.last_name).trim() || "—")}</td>
      <td>${U.esc(U.maskEmail(e.email))}<br><span style="color:#999">${U.esc(U.maskPhone(e.phone))}</span></td>
      <td><span style="padding:2px 8px;border-radius:99px;font-size:.72rem;background:#efe9dc">${U.esc(e.video_status)}</span></td>
      <td>${e.video_url ? `<a href="${U.esc(e.video_url)}" target="_blank" rel="noopener">video</a>` : "—"}</td>
      <td><form method="post" action="/raffle-admin/assign" style="display:flex;gap:4px">
        <input type="hidden" name="boothCode" value="${U.esc(e.booth_code)}">
        <input name="videoUrl" placeholder="paste video URL" style="padding:6px;border:1px solid #ccc;border-radius:6px;font-size:.75rem;width:150px">
        <button style="padding:6px 10px;border:0;border-radius:6px;background:var(--purple);color:#fff;font-weight:700;cursor:pointer">Assign</button>
      </form></td>
    </tr>`).join("");
  res.send(page("Raffle Admin — HNM", `<div style="max-width:1080px;margin:0 auto;padding:24px 18px">
    <div class="accent" style="border-radius:4px"></div>
    <h1 style="color:var(--ivory);margin:16px 0">Raffle Admin <span style="font-size:.9rem;font-weight:400;color:var(--gold-soft)">${U.esc(EVENT_NAME)}</span></h1>
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px">
      ${stat("Total", c.total, "#2A1A52")}${stat("Awaiting", c.awaiting, "#8C6618")}${stat("Video ready", c.ready, "#5B3FA8")}${stat("Delivered", c.delivered, "#2e7d5b")}${stat("Review", c.review, "#b23")}
    </div>
    <p style="margin-bottom:12px"><a href="/api/raffle/export.csv" style="color:var(--gold-soft)">⬇ Export entries CSV</a></p>
    <div style="background:#fff;border-radius:12px;overflow:auto"><table style="width:100%;border-collapse:collapse;font-size:.85rem">
      <thead><tr style="text-align:left;background:#f3efe6"><th style="padding:10px">Code</th><th>Name</th><th>Contact</th><th>Status</th><th>Video</th><th>Assign video URL</th></tr></thead>
      <tbody>${rows || `<tr><td colspan="6" style="padding:20px;text-align:center;color:#999">No entries yet.</td></tr>`}</tbody>
    </table></div>
    <p style="color:#8C77C7;font-size:.8rem;margin-top:14px">Operator console: <a href="/raffle-operator" style="color:var(--gold-soft)">/raffle-operator</a></p>
  </div>`));
});

app.post("/raffle-admin/assign", auth, (req, res) => {
  const e = store.byBoothCode(req.body.boothCode);
  const url = String(req.body.videoUrl || "").trim();
  if (e && url) {
    store.update(e.id, { video_url: url, video_status: "video_ready", delivery_status: "pending" });
    store.log(e.id, "admin_assign_video", url);
  }
  res.redirect("/raffle-admin");
});

// ---------- OPERATOR CONSOLE (booth attendant) ----------
app.get("/raffle-operator", auth, (req, res) => {
  const q = String(req.query.code || "").trim();
  const e = q ? store.byBoothCode(q) : null;
  const result = q
    ? (e
        ? `<div style="background:#fff;color:#111;border-radius:14px;padding:20px;margin-top:16px">
             <div style="font-family:Georgia,serif;font-size:1.8rem;color:var(--purple-deep);font-weight:700">${U.esc(e.booth_code)}</div>
             <p style="margin:6px 0"><b>${U.esc((e.first_name + " " + e.last_name).trim() || "Guest")}</b></p>
             <p style="color:#555">Deliver to: ${U.esc(e.email ? U.maskEmail(e.email) : U.maskPhone(e.phone))}</p>
             <p style="color:#555">Status: <b>${U.esc(e.video_status)}</b></p>
             <form method="post" action="/raffle-operator/ready" style="margin-top:12px"><input type="hidden" name="boothCode" value="${U.esc(e.booth_code)}">
               <button style="padding:12px 18px;border:0;border-radius:10px;background:var(--gold);color:#2a1a08;font-weight:800;cursor:pointer">Mark ready for capture</button></form>
           </div>`
        : `<p style="color:#ffb3b3;margin-top:16px">No entry for “${U.esc(q)}”.</p>`)
    : "";
  res.send(page("Operator — HNM Raffle", `<div class="wrap"><div class="accent" style="border-radius:4px"></div>
    <h1 style="color:var(--ivory);margin:14px 0">Booth Operator</h1>
    <form method="get" style="display:flex;gap:8px">
      <input name="code" autofocus autocapitalize="characters" placeholder="Scan or type booth code (HNM-…)" value="${U.esc(q)}"
        style="flex:1;padding:14px;border-radius:10px;border:0;font-size:1rem">
      <button style="padding:14px 18px;border:0;border-radius:10px;background:var(--purple);color:#fff;font-weight:800;cursor:pointer">Find</button>
    </form>${result}</div>`));
});

app.post("/raffle-operator/ready", auth, (req, res) => {
  const e = store.byBoothCode(req.body.boothCode);
  if (e) { store.update(e.id, { video_status: e.video_status === "awaiting_capture" ? "processing" : e.video_status }); store.log(e.id, "operator_ready", "marked ready for capture"); }
  res.redirect("/raffle-operator?code=" + encodeURIComponent(req.body.boothCode || ""));
});

// ---------- CSV export ----------
app.get("/api/raffle/export.csv", auth, (req, res) => {
  const cols = ["booth_code", "first_name", "last_name", "email", "phone", "organization", "source", "video_status", "video_url", "consent_sms", "created_at"];
  const csv = [cols.join(",")].concat(
    store.all().map((e) => cols.map((k) => `"${String(e[k] == null ? "" : e[k]).replace(/"/g, '""')}"`).join(","))
  ).join("\n");
  res.set("Content-Type", "text/csv").set("Content-Disposition", 'attachment; filename="hnm-raffle-entries.csv"').send(csv);
});

app.listen(PORT, "127.0.0.1", () => console.log(`hnm-raffle listening on 127.0.0.1:${PORT}`));
