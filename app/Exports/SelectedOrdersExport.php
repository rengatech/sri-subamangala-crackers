<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;

class SelectedOrdersExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $selectedOrders;

    public function __construct($selectedOrders)
    {
        $this->selectedOrders = $selectedOrders;
    }

    public function collection()
    {
        return $this->selectedOrders;
    }
}
