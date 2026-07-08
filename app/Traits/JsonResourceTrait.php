<?php

namespace App\Traits;

trait JsonResourceTrait
{
    /**
     * Format the created_at date.
     *
     * @param  string|null  $date
     */
    private function formatCreatedAt($date): ?string
    {
        return $date ? date('d M Y', strtotime($date)) : null;
    }
}
