# Admin Changes Merge Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Bring all unmerged features from `origin/admin-changes` into main — form refactoring, WhatsApp Business API integration, bulk PDF download, price list PDF, webcam capture, order status summary, bank account images, frontend UI updates — while preserving existing year filter and bulk actions, and excluding combo pack code.

**Architecture:** Each resource's form is refactored from custom `CustomerForm`/`AddressForm` components to standard Filament Select fields. New features (bulk PDF, price list, webcam, WhatsApp Business API, status summary) are added as new files. Existing bulk actions are preserved and missing ones (Download PDF) are uncommented/added. Frontend Vue pages are updated with new logo, cart redesign, form field changes, and contact modal.

**Tech Stack:** Laravel, Filament v3, Livewire, Blade, DomPDF (barryvdh/laravel-dompdf), WhatsApp Business API (Meta Graph API), Vue 3 + Inertia.js

---

## Important Notes for the Implementer

- **DO NOT** add year filters to individual resources — the global `HasYearFilter` trait + `YearSelector` Livewire component already handles this.
- **DO NOT** remove any existing bulk actions — preserve all of them and add missing ones.
- **DO NOT** add any combo pack related code (models, relationships, routes, etc).
- **PRESERVE** the existing `HasYearFilter` trait usage and `getEloquentQuery()` overrides in all resources.
- When replacing `CustomerForm`/`AddressForm`, use standard Filament `Select` components.
- The `lr_screenshot_path` field already exists in the Dispatch model fillable array on main.

---

### Task 1: Create New Blade Views

These are new files that don't exist on main. Pull them from the branch.

**Files:**
- Create: `resources/views/orders/bulk-download.blade.php`
- Create: `resources/views/categories.blade.php`
- Create: `resources/views/filament/pages/webcam-upload.blade.php`
- Create: `resources/views/filament/resources/order-status-summary.blade.php`

**Step 1: Create bulk-download blade**

```bash
git show origin/admin-changes:resources/views/orders/bulk-download.blade.php > resources/views/orders/bulk-download.blade.php
```

**Step 2: Create categories (price list) blade**

```bash
git show origin/admin-changes:resources/views/categories.blade.php > resources/views/categories.blade.php
```

**Step 3: Create webcam-upload blade**

```bash
mkdir -p resources/views/filament/pages
git show origin/admin-changes:resources/views/filament/pages/webcam-upload.blade.php > resources/views/filament/pages/webcam-upload.blade.php
```

**Step 4: Create order-status-summary blade**

```bash
mkdir -p resources/views/filament/resources
git show origin/admin-changes:resources/views/filament/resources/order-status-summary.blade.php > resources/views/filament/resources/order-status-summary.blade.php
```

**Step 5: Commit**

```bash
git add resources/views/orders/bulk-download.blade.php resources/views/categories.blade.php resources/views/filament/pages/webcam-upload.blade.php resources/views/filament/resources/order-status-summary.blade.php
git commit -m "feat: add blade views for bulk download, price list, webcam capture, and order status summary"
```

---

### Task 2: Update Models (Customer, Address, BankAccount, Order)

**Files:**
- Modify: `app/Models/Customer.php`
- Modify: `app/Models/Address.php`
- Modify: `app/Models/BankAccount.php`
- Modify: `app/Models/Order.php`

**Step 1: Update Customer model**

Replace the entire content of `app/Models/Customer.php` with:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'mobile_number', 'whatsapp_number'];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function address()
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getFormattedWhatsappNumberAttribute(): string
    {
        return preg_replace('/[^0-9]/', '', $this->whatsapp_number);
    }

    public function getWhatsappUrlAttribute(): string
    {
        return "https://wa.me/{$this->formatted_whatsapp_number}";
    }

    public function getWhatsappMessageUrl(string $message): string
    {
        $encodedMessage = urlencode($message);
        return "https://wa.me/{$this->formatted_whatsapp_number}?text={$encodedMessage}";
    }
}
```

Note: We keep both `address()` and `addresses()` for backward compatibility. We omit `comboPackOrder()`, `WhatsappMessage` relationships, and the self-referential `customers()` relationship.

**Step 2: Update Address model**

Replace `app/Models/Address.php` with:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['address', 'city_town', 'district', 'city', 'state', 'pin_code'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function dispatch()
    {
        return $this->hasMany(Dispatch::class);
    }
}
```

Note: We keep `dispatch()` (it's used), add `orders()`, and exclude `comboPackOrder()`.

**Step 3: Update BankAccount model — add `image` to fillable**

In `app/Models/BankAccount.php`, change the fillable line:

```php
// OLD:
protected $fillable = [ 'bank_name' , 'branch', 'name','account_number','ifsc_code','upi_id','g_pay' ];

// NEW:
protected $fillable = ['bank_name', 'branch', 'name', 'account_number', 'ifsc_code', 'upi_id', 'g_pay', 'image'];
```

**Step 4: Add `lr_screenshot` to Order model fillable**

In `app/Models/Order.php`, add `lr_screenshot` to fillable:

```php
// OLD:
protected $fillable = ['customer_id', 'net_total', 'discount_total', 'sub_total', 'address_id', 'status'];

// NEW:
protected $fillable = ['customer_id', 'net_total', 'discount_total', 'sub_total', 'address_id', 'status', 'lr_screenshot'];
```

**Step 5: Commit**

```bash
git add app/Models/Customer.php app/Models/Address.php app/Models/BankAccount.php app/Models/Order.php
git commit -m "feat: update models with WhatsApp helpers, image fields, and orders relationship"
```

---

### Task 3: Update Controllers and Routes

**Files:**
- Modify: `app/Http/Controllers/OrderController.php`
- Modify: `app/Http/Controllers/CategoryController.php`
- Modify: `routes/web.php`

**Step 1: Add `bulkPdfDownload` method to OrderController**

Add this method to `app/Http/Controllers/OrderController.php` after the `downloadOrder` method (around line 134):

```php
public function bulkPdfDownload(Request $request, GeneralSettings $settings)
{
    $orderIds = $request->input('order_ids');
    $orders = Order::with('customer', 'address', 'items')->whereIn('id', $orderIds)->get();
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

    $pdf = PDF::loadView('orders.bulk-download', compact('orderData', 'global_discount', 'company_address', 'company_name'));
    $filename = 'bulk_orders_' . \Carbon\Carbon::now()->format('YmdHis') . '.pdf';
    return $pdf->download($filename);
}
```

Also add `use Carbon\Carbon;` at the top of the file if not present.

**Step 2: Update CategoryController with `downloadPdf` method**

Replace `app/Http/Controllers/CategoryController.php` with:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['products' => function ($query) {
            $query->where('out_of_stock', false);
        }])->get();
        return response()->json($categories);
    }

    public function downloadPdf()
    {
        $categories = Category::with(['products' => function ($query) {
            $query->where('out_of_stock', false)
                ->withSum('orderItems as quantity', 'quantity');
        }])->get();

        $today = now();
        $priceValidity = [
            'start_date' => $today->toDateString(),
            'end_date' => $today->addDays(3)->toDateString()
        ];
        $message = "Price is valid for 3 days from today.";

        $pdf = Pdf::loadView('categories', compact('categories', 'priceValidity', 'message'));
        return $pdf->download('categories_and_products.pdf');
    }
}
```

**Step 3: Update routes/web.php — add bulk download and pricelist routes**

Add these lines to `routes/web.php`:

After the line `Route::get('/admin/orders/{id}/download', ...)`:
```php
Route::get('/admin/orders/bulk-download', [OrderController::class, 'bulkPdfDownload'])->name('orders.bulk-download');
```

Add a CategoryController import at the top:
```php
use App\Http\Controllers\CategoryController;
```

Before or after the contact-form route:
```php
Route::get('/pricelist', [CategoryController::class, 'downloadPdf'])->name('pricelist');
```

**Step 4: Commit**

```bash
git add app/Http/Controllers/OrderController.php app/Http/Controllers/CategoryController.php routes/web.php
git commit -m "feat: add bulk PDF download and price list PDF routes and controllers"
```

---

### Task 4: Refactor AllOrdersResource

**Files:**
- Modify: `app/Filament/Resources/AllOrdersResource.php`

**Changes:**
1. Replace `CustomerForm`/`AddressForm` with standard Select fields for customer_id, address_id, city_town
2. Add "Status Summary" header action (renders order-status-summary blade view)
3. **Keep all existing bulk actions** (Download PDF, Inventory)
4. Keep existing `HasYearFilter` trait and `getEloquentQuery()`
5. Uncomment/enable Download PDF bulk action if commented
6. Clean up duplicate imports

**Step 1: Update form schema**

Replace the form method with:
```php
public static function form(Form $form): Form
{
    return $form->schema([
        Section::make('Order Summary')->schema([
            Select::make('customer_id')
                ->label('Customer')
                ->options(Customer::all()->pluck('name', 'id')->filter()->toArray() + [null => 'No customer found'])
                ->required()
                ->searchable(),

            Forms\Components\TextInput::make('net_total')
                ->required()
                ->maxLength(255),

            Select::make('address_id')
                ->label('Address')
                ->options(Address::all()->pluck('address', 'id')->filter()->toArray() + [null => 'No address found'])
                ->required()
                ->searchable(),

            Select::make('city_town')
                ->label('City Town')
                ->options(Address::all()->pluck('city_town', 'id')->filter()->toArray() + [null => 'No address found'])
                ->required()
                ->searchable(),

            Forms\Components\Select::make('status')
                ->options([
                    'placed' => 'Confirmed',
                    'packing' => 'Packing',
                    'cancelled' => 'Cancelled',
                    'dispatched' => 'Dispatched',
                ])
                ->required(),
        ])->columns(),
    ]);
}
```

**Step 2: Add Status Summary header action to table**

Add to the table method, before `->columns([`:
```php
->headerActions([
    Tables\Actions\Action::make('statusSummary')
        ->label('Status Summary')
        ->view('filament.resources.order-status-summary'),
])
```

**Step 3: Update imports**

Replace old imports with:
```php
use App\Filament\Resources\AllOrdersResource\Pages;
use App\Filament\Resources\AllOrdersResource\RelationManagers;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Address;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Traits\HasYearFilter;
```

Remove: `AddressForm`, `CustomerForm`, `Orders`, `Orderstatus`, `BankAccount`, `Payment`, `TextColumn`, `SelectColumn`, `ButtonAction`, `DateTimePicker`, `ExcelExport`, `Http`.

**Step 4: Ensure all bulk actions are present**

The table should have these bulk actions (keep existing, add Download PDF if missing):
```php
->bulkActions([
    Tables\Actions\DeleteBulkAction::make(),
    Tables\Actions\BulkAction::make('Download Pdf')
        ->action(function (Collection $records) {
            $recordIds = $records->pluck('id')->toArray();
            if (count($recordIds) === 0) {
                return response()->json(['message' => 'No records selected.']);
            }
            $apiUrl = route('orders.bulk-download', ['order_ids' => $recordIds]);
            return redirect($apiUrl);
        })
        ->icon('heroicon-o-document-arrow-down'),
    ExportBulkAction::make('Inventory')
        ->action(function (Collection $records) {
            $recordIds = $records->pluck('id')->toArray();
            if (count($recordIds) === 0) {
                return response()->json(['message' => 'No records selected.']);
            }
            $apiUrl = route('orders.SelectedOrders', ['order_ids' => $recordIds]);
            return redirect($apiUrl);
        })
        ->icon('heroicon-o-document-arrow-down')
        ->label('Inventory'),
])
```

**Step 5: Commit**

```bash
git add app/Filament/Resources/AllOrdersResource.php
git commit -m "refactor: AllOrdersResource - standard form fields, status summary header, bulk actions"
```

---

### Task 5: Refactor DispatchResource

**Files:**
- Modify: `app/Filament/Resources/DispatchResource.php`

**Changes:**
1. Replace `CustomerForm`/`AddressForm` with Select fields
2. Remove `LR_number` and `transport` from form, add `FileUpload` for lr_screenshot
3. Add `ImageColumn` with clickable preview in table (already partially done on main)
4. Add download PDF action per row
5. Keep existing `HasYearFilter` trait and bulk actions
6. Keep Bulk Refund and Bulk Cancel actions

**Step 1: Update imports**

Ensure these imports exist:
```php
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use App\Models\Customer;
use App\Models\Address;
```

**Step 2: Update form — replace CustomerForm/AddressForm with Selects, add FileUpload**

```php
public static function form(Form $form): Form
{
    return $form->schema([
        Section::make('Customer Details')->schema([
            Select::make('customer_id')
                ->label('Customer')
                ->options(fn () => Customer::pluck('name', 'id'))
                ->required()
                ->searchable(),
        ]),
        Section::make('Address Details')->schema([
            Select::make('address_id')
                ->label('Address')
                ->options(fn () => Address::pluck('address', 'id'))
                ->required()
                ->searchable(),
            Select::make('city_town')
                ->label('City Town')
                ->options(fn () => Address::pluck('city_town', 'id'))
                ->required()
                ->searchable(),
        ])->columns(2),
        Section::make('Dispatch Details')->schema([
            Select::make('order_id')
                ->label('Order Id')
                ->options(fn () => Order::pluck('id', 'id'))
                ->searchable()
                ->required(),
            DateTimePicker::make('book_date')->required(),
            DateTimePicker::make('delivery_date')->required(),
            FileUpload::make('lr_screenshot_path')
                ->label('Upload LR Screenshot')
                ->image()
                ->imagePreviewHeight('150')
                ->directory('lr-screenshots')
                ->disk('public')
                ->visibility('public')
                ->downloadable()
                ->openable()
                ->acceptedFileTypes(['image/*'])
                ->maxSize(10240)
                ->nullable(),
        ])->columns(2),
    ]);
}
```

**Step 3: Ensure table has ImageColumn and download action**

The table columns should include the ImageColumn for lr_screenshot_path (already on main but verify), and the download action:
```php
Tables\Actions\Action::make('download')
    ->icon('heroicon-o-document-arrow-down')
    ->url(
        fn (Dispatch $record): string => route('admin.orders.download', ['id' => $record->order_id]),
        shouldOpenInNewTab: true
    ),
```

**Step 4: Ensure all bulk actions remain** (DeleteBulkAction, Inventory, Bulk Refund, Bulk Cancel)

**Step 5: Commit**

```bash
git add app/Filament/Resources/DispatchResource.php
git commit -m "refactor: DispatchResource - standard form fields, LR screenshot upload"
```

---

### Task 6: Refactor PackingOrderResource

**Files:**
- Modify: `app/Filament/Resources/PackingOrderResource.php`

**Changes:**
1. Replace `CustomerForm`/`AddressForm` with Select fields
2. Add FileUpload for LR screenshot in form
3. Add webcam capture placeholder
4. Update Dispatch action to use LR screenshot instead of LR_number/transport
5. Fix duplicate `->query()` calls (keep only the packing filter)
6. Fix duplicate status column
7. **Keep ALL existing bulk actions** (Download PDF, Inventory, Bulk Dispatch, Bulk Cancel, Bulk Refund)

**Step 1: Update imports — add FileUpload, Hidden, Placeholder, HtmlString, Storage**

```php
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;
use App\Models\Address;
```

Remove: `CustomerForm`, `AddressForm`.

**Step 2: Replace form with standard selects + LR screenshot upload + webcam**

```php
public static function form(Form $form): Form
{
    return $form->schema([
        Section::make('Order Summary')->schema([
            Select::make('customer_id')
                ->label('Customer')
                ->options(fn () => Customer::all()->pluck('name', 'id'))
                ->required()
                ->searchable(),
            TextInput::make('net_total')->label('Net Total')->required(),
            Select::make('address_id')
                ->label('Address')
                ->options(fn () => Address::all()->pluck('address', 'id'))
                ->required()
                ->searchable(),
            Select::make('city_town')
                ->label('City / Town')
                ->options(fn () => Address::all()->pluck('city_town', 'id'))
                ->required()
                ->searchable(),
            Select::make('status')
                ->label('Order Status')
                ->options([
                    'placed' => 'Placed',
                    'packing' => 'Packing',
                    'dispatched' => 'Dispatched',
                    'cancel' => 'Cancelled',
                ])
                ->required(),
            FileUpload::make('lr_screenshot')
                ->label('Upload LR Screenshot')
                ->image()
                ->imagePreviewHeight('150')
                ->directory('lr-screenshots')
                ->disk('public')
                ->visibility('public')
                ->preserveFilenames()
                ->nullable(),
            Hidden::make('photo_data'),
            Placeholder::make('camera_input')
                ->label('Laptop Camera')
                ->content(new HtmlString(view('filament.pages.webcam-upload')->render())),
        ])->columns(2),
    ]);
}
```

**Step 3: Fix table query — remove duplicate, keep single packing filter**

```php
->query(fn () => Order::query()->where('status', 'packing')->latest('created_at'))
```

**Step 4: Fix duplicate status column — keep only one**

**Step 5: Update Dispatch row action — use FileUpload instead of LR_number/transport**

```php
ButtonAction::make('dispatched')
    ->label('Dispatch')
    ->color('success')
    ->icon('heroicon-o-truck')
    ->form([
        FileUpload::make('lr_screenshot')
            ->label('Upload LR Screenshot')
            ->image()
            ->imagePreviewHeight('150')
            ->directory('lr-screenshots')
            ->disk('public')
            ->required(),
        Hidden::make('photo_data'),
        Placeholder::make('camera_input')
            ->label('Take a Photo')
            ->content(new HtmlString(view('filament.pages.webcam-upload')->render())),
    ])
    ->action(function (array $data, Order $record) {
        $lrScreenshotPath = $data['lr_screenshot'];

        Dispatch::create([
            'order_id' => $record->id,
            'lr_screenshot_path' => $lrScreenshotPath,
        ]);

        $record->update([
            'status' => 'dispatched',
            'lr_screenshot' => $lrScreenshotPath,
        ]);
    }),
```

**Step 6: Keep ALL existing bulk actions**

Ensure these are all present: DeleteBulkAction, Download PDF, Inventory, Bulk Dispatch (update form to use FileUpload), Bulk Cancel, Bulk Refund.

For Bulk Dispatch, update form to match new LR screenshot approach:
```php
Tables\Actions\BulkAction::make('Bulk Dispatch')
    ->form([
        FileUpload::make('lr_screenshot')
            ->label('Upload LR Screenshot')
            ->image()
            ->directory('lr-screenshots')
            ->disk('public'),
        DatePicker::make('book_date')->label('Book Date')->default(now()),
        DatePicker::make('delivery_date')->label('Delivery Date')->default(now()),
    ])
    ->action(function (Collection $records, array $data) {
        foreach ($records as $record) {
            Dispatch::create([
                'order_id' => $record->id,
                'lr_screenshot_path' => $data['lr_screenshot'] ?? null,
                'book_date' => $data['book_date'],
                'delivery_date' => $data['delivery_date'],
            ]);
            $record->update(['status' => 'dispatched']);
        }
    }),
```

**Step 7: Commit**

```bash
git add app/Filament/Resources/PackingOrderResource.php
git commit -m "refactor: PackingOrderResource - standard forms, LR screenshot, webcam, preserve bulk actions"
```

---

### Task 7: Refactor OrderResource

**Files:**
- Modify: `app/Filament/Resources/OrderResource.php`

**Changes:**
1. Update form selects with null-safe fallbacks
2. Change city_town from TextInput to Select dropdown
3. Uncomment/enable "Download PDF" bulk action
4. Keep all existing bulk actions

**Step 1: Update form — add null-safe options and Select for city_town**

In the form method, update the customer_id select:
```php
Select::make('customer_id')
    ->label('Customer')
    ->options(Customer::all()->pluck('name', 'id')->filter()->toArray() + [null => 'No customer found'])
    ->required()
    ->searchable(),
```

Update the address_id select:
```php
Select::make('address_id')
    ->label('Address')
    ->options(Address::all()->pluck('address', 'id')->filter()->toArray() + [null => 'No address found'])
    ->required()
    ->searchable(),
```

Change city_town from TextInput to Select:
```php
Select::make('city_town')
    ->label('City Town')
    ->options(Address::all()->pluck('city_town', 'id')->filter()->toArray() + [null => 'No address found'])
    ->required()
    ->searchable(),
```

**Step 2: Uncomment "Download PDF" bulk action**

Find the commented-out `Download pdf` BulkAction and uncomment it. It should use the `orders.bulk-download` route.

**Step 3: Commit**

```bash
git add app/Filament/Resources/OrderResource.php
git commit -m "refactor: OrderResource - null-safe selects, city_town dropdown, enable Download PDF"
```

---

### Task 8: Refactor CustomerResource — WhatsApp Integration

**Files:**
- Modify: `app/Filament/Resources/CustomerResource.php`

**Changes:**
1. Add `whatsapp_number` field to form
2. Add clickable WhatsApp number column with wa.me link
3. Add WhatsApp row action button
4. Add bulk "Send WhatsApp Message" action
5. Fix duplicate name column
6. Keep existing bulk actions

**Step 1: Add imports**

```php
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
```

**Step 2: Add whatsapp_number to form**

After the `mobile_number` TextInput, add:
```php
Forms\Components\TextInput::make('whatsapp_number')
    ->required(),
```

**Step 3: Fix table columns — remove duplicate name, add whatsapp_number**

Replace the columns array with:
```php
->columns([
    Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
    Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
    Tables\Columns\TextColumn::make('mobile_number')->searchable()->sortable(),
    Tables\Columns\TextColumn::make('whatsapp_number')
        ->searchable()
        ->sortable()
        ->url(fn ($record) => "https://wa.me/" . preg_replace('/[^0-9]/', '', $record->whatsapp_number))
        ->openUrlInNewTab()
        ->icon('heroicon-o-chat-bubble-left-ellipsis')
        ->iconColor('success')
        ->tooltip('Click to open WhatsApp chat'),
])
```

**Step 4: Add WhatsApp row action**

Add before `EditAction`:
```php
Tables\Actions\Action::make('whatsapp')
    ->label('WhatsApp')
    ->icon('heroicon-o-chat-bubble-left-ellipsis')
    ->color('success')
    ->url(fn ($record) => "https://wa.me/" . preg_replace('/[^0-9]/', '', $record->whatsapp_number))
    ->openUrlInNewTab(),
```

**Step 5: Add bulk WhatsApp message action**

Add to bulkActions before DeleteBulkAction:
```php
Tables\Actions\BulkAction::make('send_whatsapp_bulk')
    ->label('Send WhatsApp Message')
    ->icon('heroicon-o-chat-bubble-left-ellipsis')
    ->color('success')
    ->form([
        Forms\Components\Textarea::make('message')
            ->label('WhatsApp Message')
            ->required()
            ->rows(4)
            ->placeholder('Enter your message here...'),
    ])
    ->action(function (Collection $records, array $data) {
        $message = urlencode($data['message']);
        $numbers = $records->pluck('whatsapp_number')->map(function ($number) {
            return preg_replace('/[^0-9]/', '', $number);
        })->filter()->toArray();

        if (empty($numbers)) {
            Notification::make()
                ->title('No valid WhatsApp numbers found')
                ->danger()
                ->send();
            return;
        }

        Notification::make()
            ->title('WhatsApp Bulk Message Prepared')
            ->body("Message prepared for " . count($numbers) . " customers. Click on individual numbers to send.")
            ->success()
            ->persistent()
            ->send();

        $firstUrl = "https://wa.me/{$numbers[0]}?text={$message}";
        redirect($firstUrl);
    })
    ->deselectRecordsAfterCompletion(),
```

**Step 6: Commit**

```bash
git add app/Filament/Resources/CustomerResource.php
git commit -m "feat: CustomerResource - WhatsApp integration with clickable links and bulk messaging"
```

---

### Task 9: Update BankAccountResource — Image Upload

**Files:**
- Modify: `app/Filament/Resources/BankAccountResource.php`

**Changes:**
1. Add FileUpload for bank logo/screenshot
2. Add ImageColumn in table
3. Comment out upi_id (matching branch)

**Step 1: Add imports**

```php
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
```

**Step 2: Add FileUpload to form after g_pay field**

```php
FileUpload::make('image')
    ->label('Upload Screenshot')
    ->image()
    ->directory('bank-images')
    ->imagePreviewHeight('150')
    ->maxSize(2048)
    ->nullable(),
```

**Step 3: Add ImageColumn to table after g_pay column**

```php
ImageColumn::make('image')
    ->label('Logo')
    ->circular()
    ->height(40),
```

**Step 4: Commit**

```bash
git add app/Filament/Resources/BankAccountResource.php
git commit -m "feat: BankAccountResource - add image upload and display"
```

---

### Task 10: Refactor Remaining Resources (CancelOrder, DeletedOrders, ConfirmOrder, RefundOrders)

**Files:**
- Modify: `app/Filament/Resources/CancelOrderResource.php`
- Modify: `app/Filament/Resources/DeletedOrdersResource.php`
- Modify: `app/Filament/Resources/ConfirmOrderResource.php`
- Modify: `app/Filament/Resources/RefundOrdersResource.php`

**Changes for ALL four:**
1. Replace `CustomerForm`/`AddressForm` with standard Select fields (customer_id, address_id, city_town) with null-safe fallbacks
2. Keep all existing bulk actions
3. Add `use App\Models\Customer;` and `use App\Models\Address;` and `use Filament\Forms\Components\Select;` imports if missing

**Additional changes per resource:**

**CancelOrderResource:**
- Update form to use Select fields
- Keep Download PDF and Inventory bulk actions

**DeletedOrdersResource:**
- Update form to use Select fields
- Change bulk action from DeleteBulkAction to RestoreBulkAction (more logical for deleted orders view)

**ConfirmOrderResource:**
- Update form selects with null-safe fallbacks (already partially uses Select)
- Fix address_id to pluck 'address' instead of 'name'
- Change city_town from TextInput to Select
- Uncomment/enable "Download PDF" bulk action
- Keep all existing bulk actions (Inventory, Bulk Packing, Bulk Refund)

**RefundOrdersResource:**
- Replace CustomerForm/AddressForm with Select fields
- Add imports for Customer, Address, Select
- Keep all existing bulk actions (Bulk Place)

**Step 1: Update each file's form method with the standard Select pattern**

The standard pattern for all four:
```php
Select::make('customer_id')
    ->label('Customer')
    ->options(Customer::all()->pluck('name', 'id')->filter()->toArray() + [null => 'No customer found'])
    ->required()
    ->searchable(),

Forms\Components\TextInput::make('net_total')
    ->required()
    ->maxLength(255),

Select::make('address_id')
    ->label('Address')
    ->options(Address::all()->pluck('address', 'id')->filter()->toArray() + [null => 'No address found'])
    ->required()
    ->searchable(),

Select::make('city_town')
    ->label('City Town')
    ->options(Address::all()->pluck('city_town', 'id')->filter()->toArray() + [null => 'No address found'])
    ->required()
    ->searchable(),
```

**Step 2: Commit**

```bash
git add app/Filament/Resources/CancelOrderResource.php app/Filament/Resources/DeletedOrdersResource.php app/Filament/Resources/ConfirmOrderResource.php app/Filament/Resources/RefundOrdersResource.php
git commit -m "refactor: remaining order resources - standard form fields, preserve bulk actions"
```

---

### Task 11: Verify and Test

**Step 1: Run artisan checks**

```bash
php artisan route:list --path=admin
php artisan route:list --path=bulk
php artisan route:list --path=pricelist
```

Expected: All new routes (orders.bulk-download, pricelist) should appear.

**Step 2: Clear caches**

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

**Step 3: Check for syntax errors**

```bash
php artisan tinker --execute="echo 'OK';"
```

Expected: `OK` output with no errors.

**Step 4: Verify Filament resources load**

```bash
php artisan filament:check
```

Or manually visit `/admin` in browser and check each resource page loads without errors.

**Step 5: Commit any fixes if needed**

---

## Summary of All Changes

| Area | What Changes |
|------|-------------|
| **Blade Views** | 4 new files (bulk-download, categories, webcam-upload, order-status-summary) |
| **Models** | Customer (WhatsApp helpers), Address (orders relation), BankAccount (image), Order (lr_screenshot) |
| **Controllers** | OrderController (bulkPdfDownload), CategoryController (downloadPdf) |
| **Routes** | 2 new routes (bulk-download, pricelist) |
| **AllOrdersResource** | Form refactored, status summary header, bulk actions preserved |
| **DispatchResource** | Form refactored, LR screenshot upload, download action |
| **PackingOrderResource** | Form refactored, LR screenshot, webcam, dispatch action updated, all bulk actions kept |
| **OrderResource** | Null-safe selects, city_town dropdown, Download PDF enabled |
| **CustomerResource** | WhatsApp number field + clickable link + row action + bulk messaging |
| **BankAccountResource** | Image upload and display |
| **CancelOrderResource** | Form refactored |
| **DeletedOrdersResource** | Form refactored, RestoreBulkAction instead of Delete |
| **ConfirmOrderResource** | Form refactored, Download PDF enabled |
| **RefundOrdersResource** | Form refactored |

---

### Task 12: WhatsApp Business API — Model, Migration, Service, Config

**Files:**
- Create: `app/Models/WhatsAppTemplate.php`
- Create: `database/migrations/2025_07_03_043413_create_whats_app_templates_table.php`
- Modify: `app/Services/WhatsAppApiService.php` (already exists on main, update it)
- Modify: `config/services.php`

**Step 1: Create WhatsAppTemplate model**

Create `app/Models/WhatsAppTemplate.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppTemplate extends Model
{
    protected $table = 'whatsapp_templates';

    protected $fillable = [
        'template_name',
        'body',
        'variables',
        'language_code',
        'status'
    ];

    protected $casts = [
        'variables' => 'array',
    ];
}
```

**Step 2: Create migration**

Create `database/migrations/2025_07_03_043413_create_whats_app_templates_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name')->unique();
            $table->text('body');
            $table->json('variables')->nullable();
            $table->string('language_code')->default('ta');
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
```

**Step 3: Update WhatsAppApiService**

Verify `app/Services/WhatsAppApiService.php` has the `sendMediaMessage` method that sends template-based messages with PDF attachments via Meta Graph API v17.0. Key: uses `WHATSAPP_PHONE_NUMBER_ID` and `WHATSAPP_ACCESS_TOKEN` env vars, template name `estimate_requested`, language Tamil (`ta`), prepends `91` to phone numbers.

**Step 4: Add WhatsApp config to config/services.php**

Add to `config/services.php` before the closing `];`:

```php
'whatsapp' => [
    'token' => env('WHATSAPP_TOKEN'),
    'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
],
```

**Step 5: Commit**

```bash
git add app/Models/WhatsAppTemplate.php database/migrations/2025_07_03_043413_create_whats_app_templates_table.php app/Services/WhatsAppApiService.php config/services.php
git commit -m "feat: WhatsApp Business API model, migration, service, and config"
```

---

### Task 13: WhatsApp Business API — Controller, Filament Pages, Blade Views, Routes

**Files:**
- Create: `app/Http/Controllers/WhatsAppChatController.php`
- Create: `app/Filament/Pages/WhatsAppChat.php`
- Create: `app/Filament/Pages/WhatsAppTemplates.php`
- Create: `resources/views/filament/pages/whats-app-chat.blade.php`
- Create: `resources/views/filament/pages/whatsapp-templates.blade.php`
- Create: `resources/views/filament/pages/send-whatsapp-message.blade.php`
- Modify: `routes/web.php`

**Step 1: Create WhatsAppChatController**

Create `app/Http/Controllers/WhatsAppChatController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChatController extends Controller
{
    public function showMetaTemplates()
    {
        try {
            $response = Http::withToken(config('services.whatsapp.token'))
                ->timeout(30)
                ->get('https://graph.facebook.com/v18.0/' . config('services.whatsapp.business_account_id') . '/message_templates');

            $templates = [];

            if ($response->successful()) {
                $data = $response->json('data', []);

                $templates = collect($data)->map(function ($template) {
                    $bodyComponent = collect($template['components'] ?? [])->firstWhere('type', 'BODY');
                    $body = $bodyComponent['text'] ?? '';

                    $variables = [];
                    if ($body) {
                        preg_match_all('/\{\{(\d+)\}\}/', $body, $matches);
                        $variables = array_unique($matches[1]);
                    }

                    return [
                        'id' => $template['name'],
                        'template_name' => $template['name'],
                        'body' => $body,
                        'variables' => $variables,
                        'status' => $template['status'] ?? 'unknown',
                        'language' => $template['language'] ?? 'en',
                        'category' => $template['category'] ?? 'UTILITY'
                    ];
                })->toArray();
            }

            return view('filament.pages.whatsapp-templates', compact('templates'));
        } catch (\Exception $e) {
            Log::error('WhatsApp Templates Error: ' . $e->getMessage());
            return view('filament.pages.whatsapp-templates', ['templates' => []]);
        }
    }

    public function selectTemplate(Request $request)
    {
        return response()->json(['status' => 'Template selected', 'template' => $request->input('template_name')]);
    }
}
```

**Step 2: Create WhatsAppChat Filament page**

Create `app/Filament/Pages/WhatsAppChat.php`:

```php
<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use Filament\Pages\Page;

class WhatsAppChat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string $view = 'filament.pages.whats-app-chat';
    protected static ?string $navigationGroup = 'Customers';
    protected static ?string $title = 'WhatsApp Chat';

    public function getViewData(): array
    {
        return [
            'customers' => Customer::all(),
        ];
    }
}
```

**Step 3: Create WhatsAppTemplates Filament page**

Create `app/Filament/Pages/WhatsAppTemplates.php`:

```php
<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class WhatsAppTemplates extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string $view = 'filament.pages.whatsapp-templates';
    protected static ?string $title = 'WhatsApp Templates';
    protected static ?string $navigationLabel = 'WhatsApp Templates';
    protected static ?string $navigationGroup = 'Customers';
    protected static ?int $navigationSort = 10;

    public $templates = [];
    public $selectedTemplate = null;

    public function mount(): void
    {
        $this->loadTemplates();
    }

    public function loadTemplates(): void
    {
        try {
            $response = Http::withToken(config('services.whatsapp.token'))
                ->timeout(30)
                ->get('https://graph.facebook.com/v18.0/' . config('services.whatsapp.business_account_id') . '/message_templates');

            if ($response->successful()) {
                $data = $response->json('data', []);
                $this->templates = collect($data)->map(function ($template) {
                    $bodyComponent = collect($template['components'] ?? [])->firstWhere('type', 'BODY');
                    $body = $bodyComponent['text'] ?? '';
                    $variables = [];
                    if ($body) {
                        preg_match_all('/\{\{(\d+)\}\}/', $body, $matches);
                        $variables = array_unique($matches[1]);
                    }
                    return [
                        'id' => $template['name'],
                        'template_name' => $template['name'],
                        'body' => $body,
                        'variables' => $variables,
                        'status' => $template['status'] ?? 'unknown',
                        'language' => $template['language'] ?? 'en',
                        'category' => $template['category'] ?? 'UTILITY'
                    ];
                })->toArray();
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Templates Error: ' . $e->getMessage());
            $this->templates = [];
        }
    }

    public function selectTemplate($templateId): void
    {
        $this->selectedTemplate = $templateId;
        Notification::make()
            ->success()
            ->title('Template Selected')
            ->body('Template "' . $templateId . '" has been selected successfully.')
            ->send();
    }

    public function refreshTemplates(): void
    {
        $this->loadTemplates();
        Notification::make()
            ->success()
            ->title('Templates Refreshed')
            ->body('Templates have been refreshed from Meta API.')
            ->send();
    }

    protected function getViewData(): array
    {
        return [
            'templates' => $this->templates,
            'selectedTemplate' => $this->selectedTemplate
        ];
    }
}
```

**Step 4: Create WhatsApp blade views**

Pull from branch:
```bash
git show origin/admin-changes:resources/views/filament/pages/whats-app-chat.blade.php > resources/views/filament/pages/whats-app-chat.blade.php
git show origin/admin-changes:resources/views/filament/pages/whatsapp-templates.blade.php > resources/views/filament/pages/whatsapp-templates.blade.php
git show origin/admin-changes:resources/views/filament/pages/send-whatsapp-message.blade.php > resources/views/filament/pages/send-whatsapp-message.blade.php
```

**Step 5: Add WhatsApp routes to routes/web.php**

Add WhatsApp controller import at top:
```php
use App\Http\Controllers\WhatsAppChatController;
```

Add routes:
```php
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/whatsapp-templates', [WhatsAppChatController::class, 'showMetaTemplates'])->name('admin.whatsapp.templates');
    Route::post('/whatsapp-templates/select', [WhatsAppChatController::class, 'selectTemplate'])->name('admin.whatsapp.select');
});
```

**Step 6: Add .env variables (document only, don't commit)**

Add to `.env.example`:
```
WHATSAPP_TOKEN=
WHATSAPP_BUSINESS_ACCOUNT_ID=
WHATSAPP_PHONE_NUMBER_ID=
WHATSAPP_ACCESS_TOKEN=
```

**Step 7: Commit**

```bash
git add app/Http/Controllers/WhatsAppChatController.php app/Filament/Pages/WhatsAppChat.php app/Filament/Pages/WhatsAppTemplates.php resources/views/filament/pages/whats-app-chat.blade.php resources/views/filament/pages/whatsapp-templates.blade.php resources/views/filament/pages/send-whatsapp-message.blade.php routes/web.php
git commit -m "feat: WhatsApp Business API - chat page, templates page, controller, routes"
```

---

### Task 14: Frontend — Update Logo, Favicon, App Title

**Files:**
- Copy: `public/assets/img/MADHU.svg` (from branch)
- Copy: `public/assets/img/MADHU.SVG.png` (from branch)
- Copy: `public/assets/img/nav/nav-90%.jpg` (from branch)
- Copy: `public/assets/img/percentage1.png` (from branch)
- Modify: `resources/views/app.blade.php`

**Step 1: Copy new asset files from branch**

```bash
git checkout origin/admin-changes -- public/assets/img/MADHU.svg
git checkout origin/admin-changes -- "public/assets/img/MADHU.SVG.png"
mkdir -p public/assets/img/nav
git checkout origin/admin-changes -- "public/assets/img/nav/nav-90%.jpg"
git checkout origin/admin-changes -- public/assets/img/percentage1.png
```

**Step 2: Update app.blade.php**

- Change title phone number from `9629312923` to `9600331523`
- Change favicon from `favicon.png` to `MADHU.svg`

**Step 3: Commit**

```bash
git add public/assets/img/MADHU.svg "public/assets/img/MADHU.SVG.png" "public/assets/img/nav/nav-90%.jpg" public/assets/img/percentage1.png resources/views/app.blade.php
git commit -m "feat: update logo to MADHU.svg, update favicon and contact number"
```

---

### Task 15: Frontend — Update Navbar.vue

**Files:**
- Modify: `resources/js/Components/partials/Navbar.vue`

**Changes:**
1. Complete rewrite with new sticky header styling
2. Change logo from `logo1.png` to `MADHU.svg` with new sizing (100w, 165w/76h for text logo)
3. Add "Contact Now" button with modal dialog showing 4 contact numbers
4. Update nav links: remove Combo Packs link reference to `combo-packs` route (since combo packs removed), add Gift Box, Payment Methods, Price List button
5. Add scoped CSS for modal overlay, responsive logo container, sticky header
6. Add `ref` import and `showModal` reactive state

**Step 1: Pull the full updated Navbar.vue from branch**

```bash
git show origin/admin-changes:resources/js/Components/partials/Navbar.vue > resources/js/Components/partials/Navbar.vue
```

**Step 2: Remove combo-packs nav link** (since we removed combo packs)

Edit the file to remove or comment out the Combo Packs `<li>` nav item.

**Step 3: Commit**

```bash
git add resources/js/Components/partials/Navbar.vue
git commit -m "feat: Navbar - new sticky header, MADHU logo, contact modal, price list button"
```

---

### Task 16: Frontend — Update Home.vue

**Files:**
- Modify: `resources/js/Pages/Home.vue`

**Changes:**
1. Add hero section message: "The Product Image is only for your reference — packing and brand may change."
2. Remove old floating cart button (`cartnew-icon`) from after the form section
3. Remove old in-cart customer details form (delivery_location, district fields)
4. Add new fixed cart button at bottom-left (`#waBtn1`) with cart badge (`.maincart` class)
5. Update customer detail form fields: change `delivery_location` to `city_town`, change `district` to `address` (textarea)
6. Add `fontstyle` class to mobile/whatsapp number inputs
7. Update CSS: add `#waBtn1` fixed positioning, `.maincart` badge styles, `.fontstyle`, responsive adjustments
8. Update cart summary CSS with new responsive `summary-row-wrapper`, `summary-box`, `summary-value`, `net-total-value`, `min-order-error` classes
9. Add `.logo1 { width: 200px }` style

**Step 1: Pull the updated Home.vue from branch**

```bash
git show origin/admin-changes:resources/js/Pages/Home.vue > resources/js/Pages/Home.vue
```

**Step 2: Verify the cart and form sections are correct**

Ensure the cart form uses `city_town` and `address` instead of `delivery_location` and `district`.

**Step 3: Commit**

```bash
git add resources/js/Pages/Home.vue
git commit -m "feat: Home.vue - hero message, cart redesign, form field updates, responsive CSS"
```

---

### Task 17: Frontend — Update Remaining Pages and Components

**Files:**
- Modify: `resources/js/Components/partials/Navbar1.vue`
- Modify: `resources/js/Components/partials/Footer.vue`
- Modify: `resources/js/Layouts/OtherLayout.vue`
- Modify: `resources/js/Pages/About.vue`
- Modify: `resources/js/Pages/Contact.vue`
- Modify: `resources/js/Pages/Home2.vue`
- Modify: `public/assets/css/style.css`

**Step 1: Update Navbar1.vue**

- Change logo from `assets/img/logo1.png` to `assets/img/MADHU.svg`
- Remove/comment out Combo Packs link

**Step 2: Update Footer.vue**

- Update Google Maps iframe embed URL to new coordinates

**Step 3: Update OtherLayout.vue**

- Add `mobile_number_1`, `mobile_number_2`, `mobile_number_4`, `mobile_number_5` props
- Pass all 5 mobile numbers to Footer component

**Step 4: Update About.vue**

- Add `mobile_number_1`, `mobile_number_2`, `mobile_number_4`, `mobile_number_5` props
- Pass all 5 numbers to OtherLayout
- Add `.logo1 { width: 200px }` style

**Step 5: Update Contact.vue**

- Pass all 5 mobile numbers to OtherLayout
- Add `.logo1 { width: 200px }` style

**Step 6: Update Home2.vue**

- Change all `logo1.png` references to `MADHU.svg` (4 occurrences)

**Step 7: Update style.css**

Add to `public/assets/css/style.css`:
```css
img.waimg1 {
    width: 10%;
    border-radius: 10px;
}
```

**Step 8: Commit**

```bash
git add resources/js/Components/partials/Navbar1.vue resources/js/Components/partials/Footer.vue resources/js/Layouts/OtherLayout.vue resources/js/Pages/About.vue resources/js/Pages/Contact.vue resources/js/Pages/Home2.vue public/assets/css/style.css
git commit -m "feat: frontend updates - logo, mobile numbers, footer map, responsive styles"
```

---

### Task 18: Verify and Test Everything

**Step 1: Run artisan checks**

```bash
php artisan route:list --path=admin
php artisan route:list --path=bulk
php artisan route:list --path=pricelist
php artisan route:list --path=whatsapp
```

Expected: All new routes appear.

**Step 2: Clear caches**

```bash
php artisan cache:clear && php artisan config:clear && php artisan view:clear && php artisan route:clear
```

**Step 3: Check for PHP syntax errors**

```bash
php artisan tinker --execute="echo 'OK';"
```

**Step 4: Run migration**

```bash
php artisan migrate
```

Expected: `whatsapp_templates` table created.

**Step 5: Build frontend assets**

```bash
npm run build
```

Expected: No build errors.

**Step 6: Verify Filament admin panel**

- Visit `/admin` — all resources should load
- Check WhatsApp Chat page appears in Customers nav group
- Check WhatsApp Templates page appears in Customers nav group
- Check year selector still works globally

**Step 7: Commit any fixes if needed**

---

## Summary of All Changes

| Task | Area | What Changes |
|------|------|-------------|
| 1 | **Blade Views** | 4 new files (bulk-download, categories, webcam-upload, order-status-summary) |
| 2 | **Models** | Customer (WhatsApp helpers), Address (orders relation), BankAccount (image), Order (lr_screenshot) |
| 3 | **Controllers/Routes** | OrderController (bulkPdfDownload), CategoryController (downloadPdf), 2 new routes |
| 4 | **AllOrdersResource** | Form refactored, status summary header, bulk actions preserved |
| 5 | **DispatchResource** | Form refactored, LR screenshot upload, download action |
| 6 | **PackingOrderResource** | Form refactored, LR screenshot, webcam, dispatch action updated, all bulk actions kept |
| 7 | **OrderResource** | Null-safe selects, city_town dropdown, Download PDF enabled |
| 8 | **CustomerResource** | WhatsApp number field + clickable link + row action + bulk messaging |
| 9 | **BankAccountResource** | Image upload and display |
| 10 | **Remaining Resources** | CancelOrder, DeletedOrders, ConfirmOrder, RefundOrders — form refactored |
| 11 | **Verify Backend** | Route checks, cache clear, syntax test |
| 12 | **WhatsApp API** | WhatsAppTemplate model, migration, service config |
| 13 | **WhatsApp Pages** | Controller, 2 Filament pages, 3 blade views, routes |
| 14 | **Frontend Assets** | MADHU.svg logo, favicon, app title update |
| 15 | **Navbar.vue** | Sticky header, contact modal, price list button, new logo |
| 16 | **Home.vue** | Hero message, cart redesign, form fields, responsive CSS |
| 17 | **Other Pages** | Navbar1, Footer, OtherLayout, About, Contact, Home2 — logo + mobile numbers |
| 18 | **Full Verification** | Routes, migration, frontend build, admin panel check |

## What is NOT included (intentionally)

- Combo pack code (removed from project)
- Per-resource year filters (global YearSelector handles this)
- ComboPack.vue rewrite (combo packs removed)
- WhatsApp Livewire component (uses Message model that doesn't exist, incomplete)
