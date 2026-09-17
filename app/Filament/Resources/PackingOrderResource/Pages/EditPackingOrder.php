<?php

namespace App\Filament\Resources\PackingOrderResource\Pages;

use App\Filament\Resources\PackingOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackingOrder extends EditRecord
{
    protected static string $resource = PackingOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ReplicateAction::make('Replicate')
                    ->action(function (Model $order) {

                        $orderToClone = Order::with('customer', 'address', 'items')->find($order->id);

                        if ($orderToClone) {
                            $cloneOrder = $orderToClone->replicate();

                            $cloneOrder->created_at = now();
                            $cloneOrder->save();

                            $cloneCustomer = $orderToClone->customer->replicate();
                            $cloneCustomer->save();

                            $cloneAddress = $orderToClone->address->replicate();
                            $cloneAddress->save();

                            $cloneItems = collect();

                            foreach ($orderToClone->items as $itemToClone) {
                                $clonedItem = $itemToClone->replicate();
                                $clonedItem->save();
                                $cloneItems->push($clonedItem);
                            }

                            $cloneOrder->customer()->associate($cloneCustomer);
                            $cloneOrder->address()->associate($cloneAddress);
                            $cloneOrder->save();

                            $cloneOrder->items()->saveMany($cloneItems);

                            return $cloneOrder->id;
                        }
                        return null;

                    }),
                    Actions\Action::make('Refund')
                    ->action(function (array $data, Order $order) {
                        $order->update(['status' => 'refund']);
                        return Redirect::to('admin/packings');
                   })->icon('heroicon-o-arrow-uturn-right'),

        ];
    }
}
