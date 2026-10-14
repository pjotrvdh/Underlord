---
paths:
  - 'database/migrations/**'
---

# Migrations

## Never write down migrations
Following Spatie's opinion, migrations in this project only have an `up()` method. Remove the `down()` stub that `php artisan make:migration` generates. Roll back by writing a new forward migration instead. Rebuild locally with `ddev exec artisan migrate:fresh --seed`.

## No production yet
While there is no production database, editing existing migrations is fine and no backwards compatibility is needed. Once production exists, every schema change is a new migration.

## Game data tables
Game data tables (settings, creatures, rooms, spells, waves) use their handle as the primary key (`key`, or `number` for waves). They have no `created_at`; use `$table->timestamp('updated_at')->nullable()` plus a nullable `updated_by` foreign key to `users`.
