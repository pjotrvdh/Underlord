# AGENTS.md

Instructions for coding agents working on Underlord.

## Read first

- [`.docs/game-design.md`](.docs/game-design.md): every mechanic, number and rule in the game.
- [`.docs/architecture.md`](.docs/architecture.md): how `underlord.html` is built: state, entity AI, rendering, input, saving.
- [`.docs/decisions.md`](.docs/decisions.md): what the owner asked for and why things are the way they are. Check it before changing behaviour, so you don't undo a deliberate choice.

## Project shape

- The game is **one file**: `underlord.html`. It holds inline CSS and JS, no libraries and no build step. Keep it that way unless the owner asks otherwise. It is published as a claude.ai Artifact, and artifacts only allow scripts from a few CDNs.
- The file is written *for the Artifact tool*: there is no `<!doctype>`, `<html>`, `<head>` or `<body>` (the publisher wraps it), and `<title>` and `<style>` come first.
- The code style is deliberately dense: short helpers, one-line functions and few comments. Match it.
- **UI text is English.** The owner talks to you in **Dutch**, so reply in Dutch.

## Publishing

- Live artifact: https://claude.ai/artifact/KpshEgP4ECCxr4pLyG13Rr
- To update it, publish `underlord.html` with the Artifact tool and `url` set to the link above. From a new session you must first `read` the artifact. Leave `capabilities` out so the stored declaration (`db`, `user`) carries over.
- `db` and `user` are used for saving. The save lives at `data/users/<viewer id>/saves/slot1`, and `localStorage` is the fallback.

## Testing

1. Syntax check:

   ```bash
   awk '/<script>/{f=1;next}/<\/script>/{f=0}f' underlord.html > /tmp/u.js && node --check /tmp/u.js
   ```

2. Headless simulation: `node tools/sim.cjs 700`. It must finish without exceptions and print `save/load ok`. Read the status lines: imps should stay alive, at least one portal should get claimed, creatures should arrive and wave 1 should be beaten.
3. The sim hooks into the startup line `newGame();resize();requestAnimationFrame(frame);`. If you change that line, update `HOOK` in `tools/sim.cjs`.
4. The sim does not cover rendering, input, zoom or fog visuals. Say so when you report, unless you checked them in a browser.

## Rules of thumb

- **Dungeon Keeper fidelity:** when the owner asks for a mechanic "like Dungeon Keeper", research how DK1/DK2 really do it and copy its limits as well as its options. Note in `.docs/decisions.md` anything you had to invent.
- **Save compatibility:** if you change what `serialize()` stores, or the map/state shape, bump `SAVE_V`. Old saves are then refused with a clear message instead of loading broken.
- **Balance changes:** run the sim before and after, and mention the effect.
- **Keep the docs current:** after a change to mechanics, numbers or architecture, update the matching file in `.docs/`, and `README.md` if the player-facing rules or controls changed.

## Git

- Remote: `github.com/pjotrvdh/Underlord` (private), branch `main`.
- Commit messages are a single short subject line, with no body and no co-author or attribution trailer.
- Pull requests are always created as **drafts**.
