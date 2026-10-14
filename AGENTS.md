# AGENTS.md

Instructions for coding agents working on Underlord.

## Read first

- [`.docs/game-design.md`](.docs/game-design.md): every mechanic, number and rule in the game.
- [`.docs/architecture.md`](.docs/architecture.md): how `underlord.html` is built: state, entity AI, rendering, input, saving.
- [`.docs/decisions.md`](.docs/decisions.md): what the owner asked for and why things are the way they are. Check it before changing behaviour, so you don't undo a deliberate choice.

## Project shape

- The game logic is **one file**: `underlord.html`, with inline CSS and JS, no libraries and no build step. It is published as a claude.ai Artifact, and artifacts only allow scripts from a few CDNs.
- **Settings are YAML** in `config/*.yml`. The game parses them with its own small YAML-subset parser (`parseYAML`: block maps, `- ` lists, and `{}`/`[]` flow collections). Each file is also embedded in `underlord.html` as a `<script type="text/yaml" id="cfg-NAME">` block. **After editing a yml file, run `node tools/sync-config.cjs`.** The sim fails when the copies are out of sync. When the game is served over http(s), it fetches the real files, which take precedence over the embedded copies.
- **Graphics are SVGs** in `assets/`, listed in `config/assets.yml`; see `assets/README.md`. Everything still has a procedural fallback, so a missing file never breaks the game.
- Put new tunable numbers in `config/game.yml` (or the matching yml file), not in code.
- The file is written *for the Artifact tool*: there is no `<!doctype>`, `<html>`, `<head>` or `<body>`, because the publisher wraps it. It starts with `<meta charset>`, then `<title>` and `<style>`. The CSS includes `[hidden]{display:none!important}` and `body{margin:0}` itself, because those come from the artifact wrapper and are missing when the file is opened locally.
- The code style is deliberately dense: short helpers, one-line functions and few comments. Match it.
- **UI text is English.** The owner talks to you in **Dutch**, so reply in Dutch.

## Publishing

- Live artifact: https://claude.ai/artifact/KpshEgP4ECCxr4pLyG13Rr
- To update it, publish `underlord.html` with the Artifact tool and `url` set to the link above. Pass `files` with every file under `config/` and `assets/` (published path = repo path), so the live version gets the YAML and SVGs as well. From a new session you must first `read` the artifact. Leave `capabilities` out so the stored declaration (`db`, `user`) carries over.
- `db` and `user` are used for saving. The save lives at `data/users/<viewer id>/saves/slot1`, and `localStorage` is the fallback.

## Testing

1. Syntax check:

   ```bash
   awk '/<script>/{f=1;next}/<\/script>/{f=0}f' underlord.html > /tmp/u.js && node --check /tmp/u.js
   ```

2. Config sync: `node tools/sync-config.cjs --check`.
3. Headless simulation: `node tools/sim.cjs 700`. It must finish without exceptions and print `save/load ok`. Read the status lines: imps should stay alive, at least one portal should get claimed, creatures should arrive and wave 1 should be beaten.
4. The sim hooks into the startup line `newGame();resize();requestAnimationFrame(frame);`. If you change that line, update `HOOK` in `tools/sim.cjs`.
5. The sim does not cover rendering, input, zoom or fog visuals. For a visual check, serve the folder (`python3 -m http.server 8765`), open it in the browser pane, and use the Cheats panel to set up a scene.

## Rules of thumb

- **Dungeon Keeper fidelity:** when the owner asks for a mechanic "like Dungeon Keeper", research how DK1/DK2 really do it and copy its limits as well as its options. Note in `.docs/decisions.md` anything you had to invent.
- **Save compatibility:** if you change what `serialize()` stores, or the map/state shape, bump `SAVE_V`. Old saves are then refused with a clear message instead of loading broken.
- **Balance changes:** run the sim before and after, and mention the effect.
- **Keep the docs current:** after a change to mechanics, numbers or architecture, update the matching file in `.docs/`, and `README.md` if the player-facing rules or controls changed.

## Git

- Remote: `github.com/pjotrvdh/Underlord` (private), branch `main`.
- Commit messages are a single short subject line, with no body and no co-author or attribution trailer.
- Pull requests are always created as **drafts**.
