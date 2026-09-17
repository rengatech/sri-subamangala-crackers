<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Settings\GeneralSettings;
use App\Services\WhatsAppApiService;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\BankAccount;
use App\Models\Customer;
use Redirect;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;


class OrderController extends Controller
{

    public function store(Request $request, GeneralSettings $settings, WhatsAppApiService $whatsAppService)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => ['required', 'regex:/^[0-9]{10}$/'],
            'whatsapp_number' => ['required', 'regex:/^[0-9]{10}$/'],
            'address' => 'required',
            'order_items' => 'required|array',
            'order_items.*.id' => 'required|integer|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
        ]);

        // Validate minimum order value
        $discount = $settings->global_discount;
        $orderTotal = 0;
        foreach ($request->order_items as $item) {
            $orderTotal += $this->getItemTotal($item['id'], $item['quantity'], $discount);
        }

        if ($orderTotal < $settings->min_order_value) {
            return back()->withErrors([
                'order_items' => 'Minimum order value is ₹' . $settings->min_order_value,
            ]);
        }

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'whatsapp_number' => $request->whatsapp_number,
        ]);

        $address = $customer->address()->create([
            'address' => $request->address,
            'city_town' => $request->city_town,
            'district' => $request->district,
            'state' => $request->state,
            'pin_code' => $request->pin_code,
        ]);

        $order = $customer->orders()->create([
            'address_id' => $address->id,
        ]);

        $order_items = [];
        foreach ($request->order_items as $item) {
            array_push($order_items, ['product_id' => $item['id'], 'quantity' => $item['quantity'], 'total' => $this->getItemTotal($item['id'], $item['quantity'], $discount)]);
        }

        $items = $order->items()->createMany($order_items);

        $net_total = $items->sum('total');
        $order->net_total = $net_total;
        $order->save();

        $pdf_name = $this->slugify($customer->name) . '_' . $customer->whatsapp_number . '_' . date("dmY") . '_' . $net_total . '.pdf';

        $order = Order::with('customer', 'address', 'items')->find($order->id)->toArray();

        $order['global_discount'] = $settings->global_discount;
        $order['Company_address'] = $settings->company_address;
        $order['Company_name'] = $settings->company_name;
        $order['mobile_number_1'] = $settings->mobile_number_1;

        Pdf::loadView('orders.download', $order)
            ->save(storage_path('app/public/order-pdf/') . $pdf_name);

        $pdf_link = asset(Storage::url('order-pdf/' . $pdf_name));

        $whatsAppService->sendMediaMessage($customer->whatsapp_number, $customer->name, $pdf_link, $pdf_name);
        $whatsAppService->sendMediaMessage('9003660673', $customer->name, $pdf_link, $pdf_name);

        return redirect()->route('thankyou', $order['id']);
    }

    public function slugify($string)
    {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '_', $string);
        $string = preg_replace('/-+/', '_', $string);
        return $string;
    }

    protected function getItemTotal($product_id, $quantity, $discount)
    {
        $product = Product::find($product_id);

        return $quantity * round($product->price - round($product->price * $discount / 100));
    }


    public function printOrder(Request $request)
    {
        $order = Order::with('customer', 'address', 'items')->find($request->id);
        $bank_accounts = BankAccount::all();

        return view('orders.print', compact('order', 'bank_accounts'));
    }

    public function downloadOrder(Request $request, GeneralSettings $settings)
    {

        $order = Order::with('customer', 'address', 'items.product')->find($request->id)->toArray();
        // return response()->json($order);

        $order['global_discount'] = $settings->global_discount;
        $order['Company_address'] = $settings->company_address;
        $order['Company_name'] = $settings->company_name;
        $order['mobile_number_1'] = $settings->mobile_number_1;


        $pdf = Pdf::loadView('orders.download', $order);

        // $pdf->render();

        return $pdf->stream();

        // return $pdf->download('invoice.pdf');

        // return view('orders.download', $order);

    }

    public function bulkPdfDownload(Request $request, GeneralSettings $settings)
    {
        $orderIds = $request->input('order_ids');
        $orders = Order::with('customer', 'address', 'items.product')->whereIn('id', $orderIds)->get();
        $global_discount = $settings->global_discount;
        $company_address = $settings->company_address;
        $company_name = $settings->company_name;

        $orderData = [];
        foreach ($orders as $order) {
            $orderArray = $order->toArray();
            $orderArray['total_items'] = $order->items->sum('quantity');
            $orderArray['product_count'] = $order->items->unique('product_id')->count();
            $orderData[] = $orderArray;
        }

        $pdf = Pdf::loadView('orders.bulk-download', compact('orderData', 'global_discount', 'company_address', 'company_name'));
        $filename = 'bulk_orders_' . Carbon::now()->format('YmdHis') . '.pdf';
        return $pdf->download($filename);
    }

    public function getLatestOrder()
    {
        // Replace this with your logic to get the latest order from the database
        $latestOrder = Order::latest()->first();

        return response()->json([
            'orderId' => $latestOrder ? $latestOrder->id : null,
        ]);
    }
}
