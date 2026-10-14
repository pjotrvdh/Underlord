// Headless smoke test for underlord.html.
// Runs the real game logic in Node with a stubbed DOM/canvas, plays a scripted
// opening (dig to the nearest portal, build rooms) and prints a status line
// every 100 simulated seconds. Ends with a save/load round trip.
//
// Usage: node tools/sim.cjs [seconds=700]
const fs = require('fs');
const path = require('path');

const html = fs.readFileSync(path.join(__dirname, '..', 'underlord.html'), 'utf8');
let src = html.slice(html.indexOf('<script>') + 8, html.lastIndexOf('</script>'));
const HOOK = 'newGame();resize();requestAnimationFrame(frame);';
if (!src.includes(HOOK)) throw new Error('Startup line changed; update HOOK in tools/sim.cjs');
src = src.replace(HOOK, 'newGame();resize();globalThis.T={get G(){return G},update,draw,ui,serialize,deserialize,applyRect,start(){running=true}};');

// Minimal DOM/canvas stubs: every unknown property is a callable no-op proxy,
// assigned properties are remembered so canvas width/height read back as numbers.
const noop = () => {};
const mk = () => {
  const st = {};
  return new Proxy(function () {}, {
    get: (t, k) => k in st ? st[k]
      : k === 'classList' ? { add: noop, remove: noop, toggle: noop }
      : k === 'style' ? {}
      : k === 'children' ? { length: 0 }
      : k === 'getBoundingClientRect' ? () => ({ left: 0, top: 0, width: 1000, height: 650 })
      : k === 'createImageData' ? (w, h) => ({ data: new Uint8Array(w * h * 4) })
      : mk(),
    set: (t, k, v) => { st[k] = v; return true; },
    apply: () => mk(),
  });
};
Object.assign(globalThis, {
  document: { getElementById: () => mk(), createElement: () => mk() },
  addEventListener: noop, devicePixelRatio: 1, requestAnimationFrame: noop,
  performance: { now: () => Date.now() },
  ResizeObserver: class { observe() {} },
  localStorage: { _s: {}, getItem(k) { return this._s[k] || null; }, setItem(k, v) { this._s[k] = v; } },
});
globalThis.window = globalThis;
(0, eval)(src);

const T = globalThis.T, W = 40, N = 1040;
T.start();
const g = T.G;

// Mark the shortest earth route from the heart ring to the nearest portal.
const portal = g.portals.slice().sort((a, b) =>
  Math.hypot(a % W - g.hx, (a / W | 0) - g.hy) - Math.hypot(b % W - g.hx, (b / W | 0) - g.hy))[0];
{
  const d = new Array(N).fill(1e9), pr = new Array(N).fill(-1), q = [];
  const start = g.hy * W + g.hx + 3; d[start] = 0; q.push(start);
  while (q.length) {
    q.sort((a, b) => d[a] - d[b]); const c = q.shift(); if (c === portal) break;
    for (const [dx, dy] of [[1, 0], [-1, 0], [0, 1], [0, -1]]) {
      const x = c % W + dx, y = (c / W | 0) + dy; if (x < 1 || y < 1 || x > 38 || y > 24) continue;
      const n = y * W + x, t = g.t[n]; if (t === 0 || t === 9 || t === 6) continue;
      if (d[c] + 1 < d[n]) { d[n] = d[c] + 1; pr[n] = c; q.push(n); }
    }
  }
  for (let c = portal; c >= 0; c = pr[c]) if (g.t[c] === 1 || g.t[c] === 2) g.mark[c] = 1;
}

const secs = +(process.argv[2] || 700);
let built = false;
for (let t = 0; t <= secs; t += 0.05) {
  T.update(0.05);
  // Like a player: every 30 s mark every explored gold or gem seam next to open ground.
  if (Math.abs(t % 30) < 0.05) for (let i = 0; i < N; i++)
    if ((g.t[i] === 2 || g.t[i] === 3) && g.seen[i] && [1, -1, W, -W].some(o => [4, 5].includes(g.t[i + o]))) g.mark[i] = 1;
  if (!built && t > 120) {
    // Build 9-tile rooms so every creature type can be attracted.
    built = true;
    const want = [1, 1, 1, 1, ...Array(9).fill(2), ...Array(9).fill(3), ...Array(9).fill(4), ...Array(9).fill(5)];
    let n = 0;
    for (let i = 0; i < N && n < want.length; i++)
      if (g.t[i] === 5 && !g.room[i] && Math.abs(i % W - g.hx) + Math.abs((i / W | 0) - g.hy) > 2) g.room[i] = want[n++];
    g.rc = [0, 0, 0, 0, 0, 0]; for (let i = 0; i < N; i++) if (g.room[i]) g.rc[g.room[i]]++;
  }
  if (Math.abs(t % 100) < 0.05) {
    T.draw(); T.ui();
    console.log(`t=${Math.round(t)}s imps=${g.ents.filter(e => e.kind === 'imp').length} marks=${g.mark.reduce((a, b) => a + b, 0)}`
      + ` portals=${g.portals.filter(p => g.pown[p]).length}/${g.portals.length} gold=${Math.round(g.money)}`
      + ` creatures=[${g.ents.filter(e => e.side === 0 && e.kind !== 'imp').map(e => e.kind + e.lvl).join(',')}]`
      + ` wave=${g.wave} heroes=${g.ents.filter(e => e.side === 1 && !e.guard).length} heart=${Math.round(g.heartHP)} research=${Math.round(g.research)}`);
  }
  if (g.over) { console.log(`game over: ${g.over} at ${Math.round(t)}s`); break; }
}

const before = T.G.ents.length;
const save = T.serialize();
T.deserialize(JSON.parse(JSON.stringify(save)));
const after = T.G.ents.length;
for (let k = 0; k < 200; k++) T.update(0.05);
console.log(`reinforced walls: ${[...T.G.t].filter(x => x === 10).length}`);
console.log(`save/load ok: ${(JSON.stringify(save).length / 1024).toFixed(1)} KB, entities ${before} saved, ${after} loaded`);
if (after !== before) { console.error('entity count changed on reload'); process.exit(1); }
