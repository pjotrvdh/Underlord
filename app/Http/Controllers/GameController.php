<?php

namespace App\Http\Controllers;

use App\Services\GameConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class GameController extends Controller
{
    public function __construct(private readonly GameConfig $config) {}

    public function show(): View
    {
        return view('game', [
            'boot' => [
                'config' => $this->config->toArray(),
                'user' => auth()->user()?->only(['id', 'name']),
                // Only admins get the cheats panel.
                'isAdmin' => (bool) auth()->user()?->is_admin,
                'saveUrl' => route('save.show'),
                'csrf' => csrf_token(),
            ],
        ]);
    }

    /** The same settings as JSON, for tools such as tools/sim.cjs. */
    public function config(): JsonResponse
    {
        return response()->json($this->config->toArray());
    }
}
