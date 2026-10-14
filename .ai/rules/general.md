---
paths:
  - package.json
---

# General

## Run npm commands inside DDEV
Always run npm commands through DDEV, e.g. `ddev exec npm ci`, `ddev exec npm install` and `ddev exec npm run build`. Do not run host npm commands: they can rewrite `node_modules` for macOS instead of the DDEV container.
