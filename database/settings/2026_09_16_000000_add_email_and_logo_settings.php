<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $existing = \DB::table('settings')->where('group', 'general')->pluck('name')->toArray();

        if (!in_array('email_id', $existing)) {
            $this->migrator->add('general.email_id', 'mahendranramar80@gmail.com');
        }
        if (!in_array('website', $existing)) {
            $this->migrator->add('general.website', 'https://madhucrackers.com');
        }
        if (!in_array('logo', $existing)) {
            $this->migrator->add('general.logo', '');
        }
    }
};
