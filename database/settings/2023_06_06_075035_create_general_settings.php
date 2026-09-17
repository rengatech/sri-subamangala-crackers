<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {

        $this->migrator->add('general.global_discount', 50);
        $this->migrator->add('general.min_order_value', 2000);
        $this->migrator->add('general.starting_year', 2023);
        $this->migrator->add('general.company_name', 'MC');
        $this->migrator->add('general.company_address', '1 st ');
        $this->migrator->add('general.mobile_number_1', 9080564089);
        $this->migrator->add('general.mobile_number_2', 9080564089);
        $this->migrator->add('general.mobile_number_3', 9080564089);
        $this->migrator->add('general.mobile_number_4', 9080564089);
        $this->migrator->add('general.mobile_number_5', 9080564089);
      
    }
};
