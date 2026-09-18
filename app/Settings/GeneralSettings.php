<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public int $global_discount;

    public int $min_order_value;
    public int $starting_year;
    public string $company_name;
    public string $company_address;
    public string $email_id;
    public string $website;
    public ?string $logo;
    public array $mobile_numbers = [];
    public string $whatsapp_number;
    public string $marquee_content;

    public static function group(): string
    {
        return 'general';
    }
}
