<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Process;

/**
 * The commit the game runs from, shown in the bottom-right corner of the game page.
 */
class AppVersion
{
    /**
     * The short hash and the commit time of HEAD, or null when git is not available.
     *
     * @return array{hash: string, time: string}|null
     */
    public function current(): ?array
    {
        $result = Process::path(base_path())->run(['git', 'log', '-1', '--format=%h %cI']);
        if ($result->failed()) {
            return null;
        }

        $parts = explode(' ', trim($result->output()));
        if (count($parts) !== 2) {
            return null;
        }

        return [
            'hash' => $parts[0],
            'time' => CarbonImmutable::parse($parts[1])->setTimezone(config()->string('app.timezone'))->format('Y-m-d H:i'),
        ];
    }
}
