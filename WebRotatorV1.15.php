<?php
/**
 * WebRotatorV1.15.php
 *
 * Purpose.  A single PHP page that emits a styled landing UI and JavaScript to open one browser
 * window and rotate through a server defined list of URLs on a timer.  It loops until stopped.
 *
 * Authorship.  Designed with and for Nilesh Ramrattan, MBA.  Coded by ChatGPT based on Nilesh’s input, requirements, and suggestions.
 *
 * Project lineage.  This file is part of the WebRotator project.  A simple rotator prototype exists as a separate project and is not part of WebRotator.
 *
 * Changes in 1.15.  Footer now displays an automatic configuration tag that updates when $urls or $intervalMs change.  Header logo is now a hyperlink to https://Ramrattan.com and opens in a new window.
 */

// ------------------------------
// Server side configuration
// ------------------------------

$urls = [
  'https://www.nbcnews.com',
  'https://abcnews.go.com',
  'https://www.cbsnews.com',
  'https://www.foxnews.com',
  'https://www.cnn.com',
  'https://www.reuters.com',
  'https://apnews.com',
  'https://www.nytimes.com',
  'https://www.wsj.com'
];

// Default interval in milliseconds
$intervalMs = 10000;

// Remember last index between reloads
$rememberLast = true;

// Resolve logo file from the current directory
$logoFile = null;
if (file_exists(__DIR__ . '/RamrattanLogo.png')) {
  $logoFile = 'RamrattanLogo.png';
} elseif (file_exists(__DIR__ . '/RamrattanLogo.jpg')) {
  $logoFile = 'RamrattanLogo.jpg';
}

// ------------------------------
// Derived version tag for the footer
// Any change to $urls or $intervalMs yields a new signature
// ------------------------------
$baseVersion = '1.15';
$cfgMaterial = [
  'urls' => array_values($urls),
  'intervalMs' => (int)$intervalMs,
];
$cfgHash = substr(sha1(json_encode($cfgMaterial)), 0, 8);
$versionTag = $baseVersion . ' • cfg ' . $cfgHash;   // Shown in the footer
$cacheBust  = $cfgHash;                               // Used for logo cache busting

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>URL Rotator</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root{
      --bg:#0f172a;          /* page background per baseline */
      --card:#111827;
      --ink:#e5e7eb;
      --muted:#a1a1aa;
      --accent:#38bdf8;
      --accent-2:#22d3ee;
      --border:#1f2937;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      background:var(--bg);
      color:var(--ink);
      font:16px/1.6 system-ui,-apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, "Noto Sans", sans-serif;
      letter-spacing:.2px;
    }
    .wrap{max-width:1100px;margin:48px auto;padding:0 24px 56px}
    header{
      margin-bottom:24px;
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:16px;
    }
    h1{margin:0 0 6px;font-size:clamp(28px,3.2vw,40px);line-height:1.15}
    .subtitle{color:var(--muted);margin:0 0 18px;font-size:clamp(14px,1.8vw,16px)}
    .logo a{display:inline-block}
    .logo img{
      display:block;
      height:64px;
      width:auto;
      border-radius:8px;
      box-shadow:0 4px 12px rgba(0,0,0,.4);
      transition:transform .2s ease, box-shadow .2s ease;
    }
    .logo img:hover{transform:scale(1.05);box-shadow:0 6px 16px rgba(0,0,0,.55)}
    .grid{display:grid;grid-template-columns:1fr;gap:18px}
    @media(min-width:900px){.grid{grid-template-columns:1.1fr .9fr}}
    .card{
      background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border:1px solid var(--border);
      border-radius:14px;
      padding:22px;
      box-shadow:0 10px 30px rgba(0,0,0,.25);
    }
    h2{margin:0 0 12px;font-size:20px;color:#e2e8f0;letter-spacing:.3px}
    h3{margin:14px 0 8px;font-size:16px;color:#dbeafe}
    .row{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin:10px 0}
    button{
      background:linear-gradient(180deg, rgba(56,189,248,.15), rgba(34,211,238,.12));
      border:1px solid rgba(56,189,248,.4);
      color:var(--ink);
      padding:10px 16px;
      border-radius:10px;
      cursor:pointer;
      transition:transform .02s ease-in-out, box-shadow .2s;
      box-shadow:0 3px 10px rgba(0,0,0,.25);
    }
    button:hover{transform:translateY(-1px)}
    button:disabled{opacity:.45;cursor:not-allowed}
    .ghost{background:transparent;border-color:var(--border)}
    input[type="number"]{
      background:var(--bg);
      border:1px solid var(--border);
      color:var(--ink);
      padding:8px 10px;
      border-radius:10px;
      width:140px
    }
    label{color:var(--muted)}
    code{background:rgba(255,255,255,.06);border:1px solid var(--border);padding:2px 6px;border-radius:6px}
    ol{margin:8px 0 0 22px;padding:0}
    li{margin:6px 0}
    .tag{
      display:inline-block;background:rgba(56,189,248,.12);color:var(--accent);
      border:1px solid rgba(56,189,248,.35);padding:2px 10px;border-radius:999px;
      font-size:12px;letter-spacing:.3px;margin-left:8px
    }
    .status{font-size:15px;color:var(--muted);margin-top:2px}
    .current{font-weight:600;color:#e2e8f0}
    .bar{
      height:10px;border-radius:999px;background:rgba(56,189,248,.18);
      border:1px solid rgba(56,189,248,.35);overflow:hidden;flex:1 1 340px
    }
    .bar>div{
      height:100%;width:0%;
      background:linear-gradient(90deg, var(--accent), var(--accent-2));
      transition:width .2s linear
    }
    footer{color:var(--muted);font-size:13px;margin-top:28px;text-align:center}
    a{color:var(--accent);text-decoration:none}
    a:hover{text-decoration:underline}
    .mono{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace}
  </style>
</head>
<body>
  <div class="wrap">
    <header>
      <div>
        <h1>URL Rotator</h1>
        <p class="subtitle">Opens a single window, shows each site for the selected interval, then loops.  Click Start to begin.  Allow popups for this page.</p>
      </div>
      <?php if ($logoFile): ?>
      <div class="logo">
        <!-- Logo now hyperlinks to Ramrattan.com in a new window -->
        <a href="https://Ramrattan.com" target="_blank" rel="noopener">
          <img src="<?php echo htmlspecialchars($logoFile, ENT_QUOTES); ?>?v=<?php echo $cacheBust; ?>" alt="Ramrattan logo">
        </a>
      </div>
      <?php endif; ?>
    </header>

    <div class="grid">
      <section class="card">
        <h2>Controls</h2>
        <div class="row">
          <strong>Interval</strong>
          <input id="interval" type="number" min="1000" step="1000" value="<?php echo (int)$intervalMs; ?>" class="mono"> ms
          <label style="margin-left:12px;">
            <input id="loopChk" type="checkbox" checked> Loop forever
          </label>
        </div>
        <div class="row">
          <label><input id="rememberChk" type="checkbox" <?php echo $rememberLast ? 'checked' : ''; ?>> Remember last index</label>
        </div>
        <div class="row">
          <button id="startBtn">Start</button>
          <button id="pauseBtn" class="ghost" disabled>Pause</button>
          <button id="resumeBtn" disabled>Resume</button>
          <button id="nextBtn" class="ghost" disabled>Next</button>
          <button id="prevBtn" class="ghost" disabled>Prev</button>
          <button id="stopBtn" disabled>Stop</button>
        </div>
        <div class="row">
          <div class="bar"><div id="progress"></div></div>
          <span id="eta" class="status mono" style="min-width:90px;text-align:right">0.0 s</span>
        </div>
      </section>

      <section class="card">
        <h2>Status</h2>
        <div class="row" style="margin-top:6px">
          <div>
            <div class="status">Current index</div>
            <div class="current mono" id="idxLabel">0</div>
          </div>
          <div style="margin-left:22px">
            <div class="status">Now showing</div>
            <div class="current mono" id="nowShowing">None</div>
          </div>
        </div>
        <h3 style="margin-top:18px">Quick tips</h3>
        <ul>
          <li>If nothing opens, your browser blocked the window.  Allow popups and click Start again.</li>
          <li>Use Next and Prev to jump without waiting.  Resume to continue the timer.</li>
        </ul>
      </section>
    </div>

    <section class="card" style="margin-top:18px">
      <h2>Playlist</h2>
      <ol id="list"></ol>
    </section>

    <footer>
      Built for simple rotations and screens.  All URLs are edited server side in <code class="mono">WebRotatorV1.15.php</code>.  Works best when this tab stays in the foreground.
      <div class="mono" style="margin-top:6px">WebRotator <?php echo htmlspecialchars($versionTag, ENT_QUOTES); ?></div>
    </footer>
  </div>

<script>
/**
 * Client side logic.  One window is opened on Start.  A timer advances the index and
 * updates that window location.  Controls provide pause, resume, next, previous, and stop.
 * We can optionally persist the last index in localStorage.
 */

const urls = <?php echo json_encode(array_values($urls), JSON_UNESCAPED_SLASHES); ?>;

let idx = 0;
let timer = null;
let win = null;
let looping = true;
let startTs = 0;
let msPeriod = Math.max(1000, parseInt(<?php echo (int)$intervalMs; ?>, 10));

const intervalInput = document.getElementById('interval');
const loopChk       = document.getElementById('loopChk');
const rememberChk   = document.getElementById('rememberChk');

const startBtn  = document.getElementById('startBtn');
const pauseBtn  = document.getElementById('pauseBtn');
const resumeBtn = document.getElementById('resumeBtn');
const nextBtn   = document.getElementById('nextBtn');
const prevBtn   = document.getElementById('prevBtn');
const stopBtn   = document.getElementById('stopBtn');

const listEl     = document.getElementById('list');
const idxLabel   = document.getElementById('idxLabel');
const nowShowing = document.getElementById('nowShowing');
const progress   = document.getElementById('progress');
const eta        = document.getElementById('eta');

function setButtons(state) {
  startBtn.disabled  = state !== 'idle';
  pauseBtn.disabled  = state !== 'running';
  resumeBtn.disabled = state !== 'paused';
  nextBtn.disabled   = state === 'idle';
  prevBtn.disabled   = state === 'idle';
  stopBtn.disabled   = state === 'idle';
}

function restoreIndex() {
  if (!<?php echo $rememberLast ? 'true' : 'false'; ?>) return;
  try {
    const saved = localStorage.getItem('url_rotator_idx');
    if (saved !== null) idx = Math.min(Math.max(parseInt(saved, 10) || 0, 0), urls.length - 1);
  } catch(e) {}
}
function persistIndex() {
  if (!rememberChk.checked) return;
  try { localStorage.setItem('url_rotator_idx', String(idx)); } catch(e) {}
}

function renderList() {
  listEl.innerHTML = urls.map((u, i) =>
    `<li${i===idx ? ' class="current"' : ''}><a href="${u}" target="_blank" rel="noopener">${u}</a>${i===idx ? '<span class="tag">current</span>' : ''}</li>`
  ).join('');
}

function openWindowIfNeeded() {
  if (!win || win.closed) {
    win = window.open(urls[idx], 'rotatorWindow');
    if (!win) {
      alert('The browser blocked the window.  Allow popups for this page, then click Start again.');
      return false;
    }
  }
  return true;
}

function updateStatus() {
  idxLabel.textContent = String(idx);
  nowShowing.textContent = urls[idx] || 'None';
  renderList();
}

function navigateToCurrent() {
  if (win && !win.closed) {
    win.location.href = urls[idx];
  }
  updateStatus();
  startTs = performance.now();
  progress.style.width = '0%';
  eta.textContent = (msPeriod / 1000).toFixed(1) + ' s';
}

function tick() {
  const t = performance.now();
  const elapsed = t - startTs;
  const pct = Math.max(0, Math.min(100, (elapsed / msPeriod) * 100));
  progress.style.width = pct.toFixed(2) + '%';
  const remain = Math.max(0, msPeriod - elapsed);
  eta.textContent = (remain / 1000).toFixed(1) + ' s';
  if (elapsed >= msPeriod) {
    idx++;
    if (idx >= urls.length) {
      if (looping) idx = 0; else { pause(); return; }
    }
    persistIndex();
    navigateToCurrent();
  }
}

function run() {
  clearInterval(timer);
  msPeriod = Math.max(1000, parseInt(intervalInput.value, 10) || 10000);
  timer = setInterval(tick, 100);
}

function start() {
  looping = loopChk.checked;
  if (!openWindowIfNeeded()) return;
  setButtons('running');
  navigateToCurrent();
  run();
}

function pause() {
  clearInterval(timer);
  setButtons('paused');
}

function resume() {
  if (!openWindowIfNeeded()) return;
  setButtons('running');
  startTs = performance.now();
  run();
}

function step(delta) {
  clearInterval(timer);
  idx = (idx + delta + urls.length) % urls.length;
  persistIndex();
  navigateToCurrent();
}

function stopAll() {
  clearInterval(timer);
  try { if (win && !win.closed) win.close(); } catch(e) {}
  win = null;
  setButtons('idle');
  progress.style.width = '0%';
  eta.textContent = '0.0 s';
  updateStatus();
}

startBtn.addEventListener('click', start);
pauseBtn.addEventListener('click', pause);
resumeBtn.addEventListener('click', resume);
nextBtn.addEventListener('click', () => step(1));
prevBtn.addEventListener('click', () => step(-1));
stopBtn.addEventListener('click', stopAll);
loopChk.addEventListener('change', () => { looping = loopChk.checked; });

restoreIndex();
renderList();
updateStatus();
setButtons('idle');
</script>
</body>
</html>
