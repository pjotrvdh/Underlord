<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  @unless ($static)
    <meta name="csrf-token" content="{{ csrf_token() }}">
  @endunless
  <title>Underlord</title>
  <link rel="icon" type="image/svg+xml" href="{{ $static ? 'favicon.svg' : asset('favicon.svg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Grenze+Gotisch:wght@500;700&family=Alegreya+Sans:wght@400;500;700&display=swap">
  <script>
    // Settings come from the database (admin panel at /admin), or from the config defaults in the
    // static build; see App\Services\GameConfig.
    window.UNDERLORD = @json($boot);
  </script>
  @if ($static)
    <link rel="stylesheet" href="{{ $static['css'] }}">
    <script type="module" src="{{ $static['js'] }}"></script>
  @else
    @vite(['resources/css/underlord.css', 'resources/js/underlord.js'])
  @endif
</head>
<body>
<div class="app">
  <header>
    <h1>Underlord</h1>
    <div class="stats">
      <div class="stat gold"><small>Gold</small><b id="sGold">0</b></div>
      <div class="stat"><small>Imps</small><b id="sImps">0</b></div>
      <div class="stat"><small>Creatures</small><b id="sCre">0</b></div>
      <div class="stat"><small>Research</small><b id="sRes">0</b></div>
      <div class="stat"><small>Payday</small><b id="sPay">0</b></div>
      <div class="stat wave"><small>Heroes</small><b id="sWave">-</b></div>
      <div class="stat heart"><small>Dungeon Heart</small><div class="hbar"><i id="sHeart"></i></div></div>
    </div>
    <div class="ctrl">
      @unless ($static)
        @if (app('impersonate')->isImpersonating())
          <a class="btn" href="{{ route('filament-impersonate.leave') }}" title="Go back to your own admin account">Stop playing as {{ auth()->user()->name }}</a>
        @endif
        @auth
          <span class="saved" title="Your games are saved to this account">{{ auth()->user()->name }}</span>
          <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form>
        @else
          <a class="btn" href="{{ route('login') }}" title="Log in to save your games to your account">Log in</a>
        @endauth
      @endunless
      <span class="saved" id="savedAt"></span>
      <button id="bSave" type="button">Save</button>
      <button id="bLoad" type="button">Load</button>
      {{-- Hidden until someone types the cheat code (iliketocheat); see underlord.js. --}}
      <button id="bCheat" type="button" aria-expanded="false" aria-controls="cheats" hidden>Cheats</button>
      <button id="bPause" type="button">Pause</button>
      <button id="bSpeed" type="button">1×</button>
    </div>
  </header>

  <main class="stage" id="stage">
    <canvas id="cv"></canvas>
    <div class="banner" id="banner"></div>
    <div class="tip" id="tip" hidden></div>
    <div class="cheats" id="cheats" hidden>
      <h2>Cheats</h2>
      <div class="cgrid" id="cheatList"></div>
    </div>
    <div class="zoom"><button id="zin" type="button" title="Zoom in">+</button><button id="zout" type="button" title="Zoom out">−</button><button id="zfit" class="fit" type="button" title="Show the whole map">Fit</button></div>
    <div class="overlay" id="ov">
      <div class="card">
        <h2>Underlord</h2>
        <p>The heroes of the surface world are coming for your Dungeon Heart. Dig into the dark, build rooms, attract creatures and survive six invasions.</p>
        <ul>
          <li>Click a block to mark it for digging. Drag to mark a whole area at once. Your imps dig, haul gold and claim new ground.</li>
          <li>The map is hidden in clouds, but gold seams and portals shine through. Mark blocks anywhere, even in the clouds, and dig to find treasure and wandering creatures.</li>
          <li>Two portals are always visible. Claim one to attract creatures. Each creature needs a free bed in your Lair; stronger creatures also need bigger rooms. Each map has a limited pool of creatures.</li>
          <li>Pick creatures up with the hand and drop them where the fighting is. Right-click slaps: slapped imps work faster.</li>
          <li>Scroll or pinch to zoom. Drag with the right or middle mouse button, or use the arrow keys or WASD, to look around.</li>
        </ul>
        <div class="row">
          <button id="bStart" class="main" type="button">New game</button>
          <button id="bContinue" type="button" hidden>Continue saved game</button>
        </div>
      </div>
    </div>
  </main>

  <aside>
    <div class="mm">
      <canvas id="mini" aria-label="Minimap. Click or drag to move the view."></canvas>
      <button id="bCpu" class="cpu" type="button" aria-pressed="false" title="Computer Assistant: let the computer play for you">C</button>
    </div>
    <div class="cpu-modes" id="cpuModes" hidden>
      <span>Computer Assistant</span>
      <div class="seg" id="cpuSeg">
        <button type="button" data-mode="aggressive">Aggressive</button>
        <button type="button" data-mode="defensive">Defensive</button>
        <button type="button" data-mode="construction">Construction only</button>
        <button type="button" data-mode="move">Move only</button>
      </div>
    </div>
    <div><div class="tools hand" id="tHand"></div><div class="held" id="held" hidden></div></div>
    <div class="tabs" role="tablist" id="tabs">
      <button type="button" role="tab" data-tab="info" title="Information">?<span>Info</span></button>
      <button type="button" role="tab" data-tab="rooms" title="Rooms">▦<span>Rooms</span></button>
      <button type="button" role="tab" data-tab="spells" title="Spells">✦<span>Spells</span></button>
      <button type="button" role="tab" data-tab="creatures" title="Creatures">☗<span>Creatures</span></button>
    </div>
    <section class="tab" id="tab-info" role="tabpanel">
      <div class="gauge"><div class="glabel"><span>Payday</span><b id="gPayTxt"></b></div><div class="gbar"><i id="gPay"></i></div></div>
      <div class="gauge"><div class="glabel"><span>Research</span><b id="gResTxt"></b></div><div class="gbar res"><i id="gRes"></i></div></div>
      <h3>Portal</h3><ul class="pool" id="pool"></ul>
    </section>
    <section class="tab" id="tab-rooms" role="tabpanel" hidden><div class="tools" id="tRooms"></div></section>
    <section class="tab" id="tab-spells" role="tabpanel" hidden><div class="tools" id="tSpells"></div></section>
    <section class="tab" id="tab-creatures" role="tabpanel" hidden>
      <div class="crgrid" id="crGrid"></div>
      <p class="crhint">Click a number to pick up a creature doing that. Right-click to look at it. Click a portrait to pick up the most experienced one.</p>
      <ul class="crlist" id="crList"></ul>
    </section>
    <div class="info" id="info">Hover over the map for details.</div>
    <section><h2>Chronicle</h2><ul class="log" id="log"></ul></section>
  </aside>
</div>
</body>
</html>
