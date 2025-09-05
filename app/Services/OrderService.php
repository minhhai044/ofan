<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getAllOrders($limit = 20, $filters = [], $relation = [])
    {
        $query = Order::with($relation)->latest('id');

        $dateRangeable = ['created_at_start', 'created_at_end'];
        foreach ($filters as $field => $value) {
            if (isset($value) && $value !== '' && !in_array($field, $dateRangeable, true)) {
                $query->where($field, 'like', '%' . $value . '%');
            }

            if (isset($value) && $value !== '') {
                if ($field === 'created_at_start') {
                    $query->whereDate('created_at', '>=', $value);
                } elseif ($field === 'created_at_end') {
                    $query->whereDate('created_at', '<=', $value);
                }
            }
        }
        return $query->paginate($limit);
    }


    private function generateOrderCodeWithLock(): string
    {
        $today = date('Ymd');

        $lastOrder = Order::whereDate('created_at', now()->toDateString())
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder && preg_match('/-(\d+)$/', $lastOrder->order_code, $matches)) {
            $number = (int)$matches[1] + 1;
        } else {
            $number = 1;
        }

        return 'DH' . $today . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    public function storeOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $orderCode = $this->generateOrderCodeWithLock();

            $order = Order::create([
                'order_code' => $orderCode,
                'customer_id' => $data['customer_id'],
                'branch_id' => $data['branch_id'] ?? null,
                'salesperson_id' => $data['salesperson_id'] ?? null,
                'order_date' => $data['order_date'],
                'status' => $data['status'],
                'amount' => $data['amount'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'vat_amount' => $data['vat_amount'] ?? 0,
                'shipping_fee' => $data['shipping_fee'] ?? 0,
                'point' => $data['point'] ?? 0,
                'total_amount' => $data['total_amount'],
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_status'],
                'paid_amount' => $data['paid_amount'] ?? 0,
                'delivery_address' => $data['delivery_address'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? null,
                'delivery_note' => $data['delivery_note'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            foreach ($data['order_items'] as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount_rate' => $itemData['discount_rate'] ?? 0,
                    'discount_amount' => $itemData['discount_amount'] ?? 0,
                    'total_amount' => $itemData['total_amount'],
                ]);
            }

            return $order;
        });
    }

    public function findOrder(array $relation = [], string $id = '')
    {
        $query = Order::with($relation);
        if (!empty($id)) {
            return $query->find($id);
        } else {
            return false;
        }
    }

    public function updateOrder(array $data, int $id)
    {
        return Order::query()->find($id)->update($data);
    }

    public function updateOrderStatus(string $id, int $status)
    {
        return Order::query()->find($id)->update(['status' => $status]);
    }
}
