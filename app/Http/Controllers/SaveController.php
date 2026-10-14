<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * One save slot per player. The game sends its own JSON (serialize() in resources/js/underlord.js);
 * the server stores it as-is and only keeps the version and wave for overviews.
 */
class SaveController extends Controller
{
    public function show(Request $request): JsonResponse|Response
    {
        $save = $request->user()->saves()->where('slot', 'slot1')->first();

        return $save
            ? response()->json(['data' => $save->data, 'version' => $save->version, 'wave' => $save->wave, 'savedAt' => $save->updated_at])
            : response()->noContent();
    }

    public function update(Request $request): JsonResponse
    {
        $valid = $request->validate([
            'data' => ['required', 'string', 'max:524288', 'json'],
            'version' => ['required', 'integer', 'min:1'],
            'wave' => ['required', 'integer', 'min:0'],
        ]);

        $save = $request->user()->saves()->updateOrCreate(['slot' => 'slot1'], $valid);

        return response()->json(['savedAt' => $save->updated_at]);
    }
}
