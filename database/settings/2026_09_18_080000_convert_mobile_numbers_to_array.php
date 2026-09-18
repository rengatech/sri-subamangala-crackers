<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $numbers = [];

        foreach (['mobile_number_1', 'mobile_number_2', 'mobile_number_3', 'mobile_number_4', 'mobile_number_5'] as $name) {
            $row = DB::table('settings')
                ->where('group', 'general')
                ->where('name', $name)
                ->first();

            if ($row) {
                $value = json_decode($row->payload, true);
                $value = is_string($value) ? trim($value) : $value;

                if (!empty($value) && $value !== '-') {
                    $numbers[] = (string) $value;
                }
            }
        }

        $numbers = array_values(array_unique($numbers));

        $this->migrator->add('general.mobile_numbers', $numbers);

        foreach (['mobile_number_1', 'mobile_number_2', 'mobile_number_3', 'mobile_number_4', 'mobile_number_5'] as $name) {
            $this->migrator->delete("general.$name");
        }
    }

    public function down(): void
    {
        $row = DB::table('settings')->where('group', 'general')->where('name', 'mobile_numbers')->first();
        $numbers = $row ? (json_decode($row->payload, true) ?? []) : [];

        $this->migrator->delete('general.mobile_numbers');

        foreach (['mobile_number_1', 'mobile_number_2', 'mobile_number_3', 'mobile_number_4', 'mobile_number_5'] as $i => $name) {
            $this->migrator->add("general.$name", $numbers[$i] ?? '');
        }
    }
};
