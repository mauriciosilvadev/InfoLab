<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $currentYear = (string) now()->year;

        $this->migrator->add('system.current_semester_year', $currentYear);
        $this->migrator->add('system.current_semester_period', '1');
        $this->migrator->add('system.monitoring_laboratory_id', null);
        $this->migrator->add('system.contact_emails', []);
        $this->migrator->add('system.useful_links', []);
    }
};
