"use strict";
const crypto = require("crypto");

// Crockford base32 (no I,L,O,U) — human-friendly booth codes.
const ALPHABET = "0123456789ABCDEFGHJKMNPQRSTVWXYZ";

function randomCode(len = 5) {
  const bytes = crypto.randomBytes(len);
  let s = "";
  for (let i = 0; i < len; i++) s += ALPHABET[bytes[i] % ALPHABET.length];
  return s;
}

// HNM-XXXXX, guaranteed unique against the store.
function generateBoothCode(exists) {
  for (let i = 0; i < 50; i++) {
    const code = "HNM-" + randomCode(5);
    if (!exists(code)) return code;
  }
  return "HNM-" + randomCode(7); // vanishingly unlikely fallback
}

function uuid() {
  return crypto.randomUUID();
}

// Opaque, unguessable, URL-safe token for the guest confirmation link.
function opaqueToken() {
  return crypto.randomBytes(18).toString("base64url");
}

function normalizeEmail(e) {
  return String(e || "").trim().toLowerCase();
}

// Best-effort E.164 for US/CA; leaves already-plus numbers alone.
function normalizePhone(p) {
  const raw = String(p || "").trim();
  if (!raw) return "";
  if (raw.startsWith("+")) return "+" + raw.slice(1).replace(/\D/g, "");
  const d = raw.replace(/\D/g, "");
  if (d.length === 10) return "+1" + d;
  if (d.length === 11 && d[0] === "1") return "+" + d;
  return d ? "+" + d : "";
}

function hashIp(ip, salt) {
  return crypto.createHash("sha256").update(String(salt) + "|" + String(ip || "")).digest("hex").slice(0, 32);
}

function maskEmail(e) {
  const s = normalizeEmail(e);
  if (!s.includes("@")) return s ? "•••" : "";
  const [u, d] = s.split("@");
  return (u.slice(0, 2) + "•••") + "@" + d;
}
function maskPhone(p) {
  const d = String(p || "").replace(/\D/g, "");
  return d ? "•••-•••-" + d.slice(-4) : "";
}

function esc(s) {
  return String(s == null ? "" : s).replace(/[<>&"']/g, (c) =>
    ({ "<": "&lt;", ">": "&gt;", "&": "&amp;", '"': "&quot;", "'": "&#39;" }[c])
  );
}

module.exports = {
  generateBoothCode, uuid, opaqueToken,
  normalizeEmail, normalizePhone, hashIp,
  maskEmail, maskPhone, esc,
};
