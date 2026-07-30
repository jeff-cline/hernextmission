"use strict";
// Tiny durable JSON store. Single pm2 process → single-threaded writes are safe.
// Atomic persistence via temp-file + rename. Swap for SQLite/Postgres later behind the same API.
const fs = require("fs");
const path = require("path");

const DATA_DIR = process.env.HNM_DATA_DIR || path.join(__dirname, "data");
const FILE = path.join(DATA_DIR, "entries.json");

fs.mkdirSync(DATA_DIR, { recursive: true });

let state = { entries: [] };
try {
  state = JSON.parse(fs.readFileSync(FILE, "utf8"));
  if (!Array.isArray(state.entries)) state = { entries: [] };
} catch {
  state = { entries: [] };
}

function persist() {
  const tmp = FILE + ".tmp";
  fs.writeFileSync(tmp, JSON.stringify(state, null, 2));
  fs.renameSync(tmp, FILE);
}

const last10 = (p) => String(p || "").replace(/\D/g, "").slice(-10);

module.exports = {
  all() {
    return state.entries.slice();
  },
  byId(id) {
    return state.entries.find((e) => e.id === id) || null;
  },
  byToken(token) {
    return state.entries.find((e) => e.token === token) || null;
  },
  byBoothCode(code) {
    const c = String(code || "").trim().toUpperCase();
    return state.entries.find((e) => e.booth_code === c) || null;
  },
  // dedup helpers
  byEmail(email) {
    const em = String(email || "").trim().toLowerCase();
    return em ? state.entries.find((e) => e.email === em) || null : null;
  },
  byPhone(phone) {
    const p = last10(phone);
    return p ? state.entries.find((e) => last10(e.phone) === p) || null : null;
  },
  byIdempotencyKey(k) {
    return k ? state.entries.find((e) => e.idempotency_key === k) || null : null;
  },
  boothCodeExists(code) {
    return state.entries.some((e) => e.booth_code === code);
  },
  insert(entry) {
    state.entries.push(entry);
    persist();
    return entry;
  },
  update(id, patch) {
    const e = this.byId(id);
    if (!e) return null;
    Object.assign(e, patch, { updated_at: new Date().toISOString() });
    persist();
    return e;
  },
  log(id, event, detail) {
    const e = this.byId(id);
    if (!e) return;
    e.log = e.log || [];
    e.log.push({ t: new Date().toISOString(), event, detail: detail || "" });
    persist();
  },
};
