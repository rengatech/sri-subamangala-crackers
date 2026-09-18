<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.whatsapp_number', '');
    }

    public function down(): void
    {
        $this->migrator->delete('general.whatsapp_number');
    }
};