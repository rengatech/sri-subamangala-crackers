<?php

namespace App\Http\Controllers;


use App\Models\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SelectedOrdersExport;

class ProductController extends Controller
{
    public function selectedOrders()
    {

        $recordIds = request()->input('order_ids');

        if (empty($recordIds)) {
            return response()->json(['message' => 'No records selected.']);
        }

        $selectedOrders = Order::with('items.product')->whereIn('id', $recordIds)->get();

        $productQuantities = [];

        foreach ($selectedOrders as $order) {
            foreach ($order->items as $orderItem) {
                $productName = $orderItem->product->name;
                $quantity = $orderItem->quantity;

                if (!isset($productQuantities[$productName])) {
                    $productQuantities[$productName] = 0;
                }

                $productQuantities[$productName] += $quantity;
            }
        }

        $productData = [];

        foreach ($productQuantities as $productName => $quantity) {
            $productData[] = [
                'productname' => $productName,
                'quantity' => $quantity,
            ];
        }

        $productDataCollection = collect($productData);

        return Excel::download(new SelectedOrdersExport($productDataCollection), 'Product_count.csv');
    }

}
