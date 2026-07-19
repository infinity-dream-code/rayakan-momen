import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.join(__dirname, '..');
const appCssPath = path.join(root, 'public/css/app.css');
const twCssPath = path.join(root, 'public/css/landing-tw.css');

const markerStart = '/* === BEGIN LANDING-TW === */';
const markerEnd = '/* === END LANDING-TW === */';

let app = fs.readFileSync(appCssPath, 'utf8');
const tw = fs.readFileSync(twCssPath, 'utf8').trim();

const blockRe = /\/\* === BEGIN LANDING-TW === \*\/[\s\S]*?\/\* === END LANDING-TW === \*\/\s*/;
app = app.replace(blockRe, '').trimEnd();

const merged = `${app}\n\n${markerStart}\n${tw}\n${markerEnd}\n`;
fs.writeFileSync(appCssPath, merged);
console.log('Injected landing-tw utilities into public/css/app.css');
