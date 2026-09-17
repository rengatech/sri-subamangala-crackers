<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['products'])->get();
        return response()->json($categories);
    }

    public function downloadPdf()
    {
        ini_set('max_execution_time', 120);

        $categories = Category::with([
            'products' => function ($query) {
                $query->select('id', 'category_id', 'name', 'unit', 'price')
                    ->orderBy('name');
            }
        ])
            ->has('products')
            ->orderBy('category')
            ->get();

        $today = now();
        $priceValidity = [
            'start_date' => $today->toDateString(),
            'end_date' => $today->addDays(3)->toDateString(),
        ];
        $message = 'Price is valid for 3 days from today.';

        $settings = app(\App\Settings\GeneralSettings::class);

        $pdf = Pdf::loadView('categories', compact('categories', 'priceValidity', 'message', 'settings'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('categories_and_products.pdf');
    }
}
