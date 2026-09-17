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
    public string $mobile_number_1;
    public string $mobile_number_2;
    public string $mobile_number_3;
    public string $mobile_number_4;
    public string $mobile_number_5;
    public string $marquee_content;

    public static function group(): string
    {
        return 'general';
    }




}

