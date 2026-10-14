// Copies config/*.yml into the matching <script type="text/yaml"> blocks of underlord.html,
// so the single-file game (opened from disk, or published) uses the same settings.
//
// Usage: node tools/sync-config.cjs          write the copies
//        node tools/sync-config.cjs --check  exit 1 if any copy is out of date
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const file = path.join(root, 'underlord.html');
const html = fs.readFileSync(file, 'utf8');
const re = /(<script type="text\/yaml" id="cfg-([\w-]+)" data-src="([^"]+)">\n)([\s\S]*?)(<\/script>)/g;

let stale = [];
const out = html.replace(re, (all, open, id, src, body, close) => {
  const yml = fs.readFileSync(path.join(root, src), 'utf8');
  if (yml.includes('</script>')) throw new Error(`${src} must not contain "</script>"`);
  if (yml !== body) stale.push(src);
  return open + yml + close;
});

if (process.argv.includes('--check')) {
  if (stale.length) { console.error('Out of date in underlord.html: ' + stale.join(', ') + '\nRun: node tools/sync-config.cjs'); process.exit(1); }
  console.log('config in sync');
} else {
  fs.writeFileSync(file, out);
  console.log(stale.length ? 'updated: ' + stale.join(', ') : 'already in sync');
}
