// src/utils/helpers.js
import Hashids from "hashids";
const hashids = new Hashids("32esdf33esdfg4321", 6);

export function encodeId(id) {
  return hashids.encode(id);
}

export function decodeId(hash) {
  return hashids.decode(hash)[0];
}

export function truncateWords(text, maxLength = 40) {
  if (!text) return "";
  return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
}

export function toYMD(date) {
  return date.toISOString().split("T")[0];
}
