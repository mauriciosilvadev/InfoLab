<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SystemSettings extends Settings
{
    public string $current_semester_year;

    public string $current_semester_period;

    public ?int $monitoring_laboratory_id;

    public array $contact_emails;

    public array $useful_links;

    public static function group(): string
    {
        return 'system';
    }
}
