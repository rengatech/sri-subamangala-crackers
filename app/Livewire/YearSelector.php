<?php

namespace App\Livewire;

use Livewire\Component;

class YearSelector extends Component
{
    public int $selectedYear;

    public function mount()
    {
        $this->selectedYear = session('admin_selected_year', now()->year);
    }

    public function updatedSelectedYear($value)
    {
        session(['admin_selected_year' => (int) $value]);
        $this->redirect(request()->header('Referer', '/admin'));
    }

    public function getYearsProperty(): array
    {
        $currentYear = now()->year;
        $years = [];
        for ($year = $currentYear; $year >= 2023; $year--) {
            $years[$year] = $year;
        }
        return $years;
    }

    public function render()
    {
        return view('livewire.year-selector');
    }
}
