<?php

namespace App\Http\Requests\Api;

class OrderRequest extends BaseRequest
{
    public function rulesForCreate()
    {
        return [
            'customer_id' => 'required|integer|exists:users,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'salesperson_id' => 'nullable|integer|exists:users,id',
            'order_date' => 'required|date',
            'status' => 'nullable|integer',
            'amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'vat_amount' => 'nullable|numeric|min:0',
            'shipping_fee' => 'nullable|numeric|min:0',
            'point' => 'nullable|integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|integer',
            'payment_status' => 'nullable|integer',
            'paid_amount' => 'nullable|numeric|min:0',
            'delivery_address' => 'nullable|string|max:500',
            'delivery_date' => 'nullable|date',
            'delivery_note' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:1000',
            
            // Order items validation
            'order_items' => 'required|array|min:1',
            'order_items.*.product_id' => 'required|integer|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.unit_price' => 'required|numeric|min:0',
            'order_items.*.discount_rate' => 'nullable|numeric|min:0|max:100',
            'order_items.*.discount_amount' => 'nullable|numeric|min:0',
            'order_items.*.total_amount' => 'required|numeric|min:0',
        ];
    }

    public function rulesForUpdate()
    {
        return [
            'customer_id' => 'sometimes|integer|exists:users,id',
            'branch_id' => 'sometimes|nullable|integer|exists:branches,id',
            'salesperson_id' => 'sometimes|nullable|integer|exists:users,id',
            'order_date' => 'sometimes|date',
            'status' => 'nullable|integer',
            'amount' => 'sometimes|numeric|min:0',
            'discount_amount' => 'sometimes|nullable|numeric|min:0',
            'vat_amount' => 'sometimes|nullable|numeric|min:0',
            'shipping_fee' => 'sometimes|nullable|numeric|min:0',
            'point' => 'sometimes|nullable|integer|min:0',
            'total_amount' => 'sometimes|numeric|min:0',
            'payment_method' => 'sometimes|nullable|integer',
            'payment_status' => 'sometimes|nullable|integer',
            'paid_amount' => 'sometimes|nullable|numeric|min:0',
            'delivery_address' => 'sometimes|nullable|string|max:500',
            'delivery_date' => 'sometimes|nullable|date',
            'delivery_note' => 'sometimes|nullable|string|max:500',
            'note' => 'sometimes|nullable|string|max:1000',
            
            // Order items validation for update
            'order_items' => 'sometimes|array|min:1',
            'order_items.*.product_id' => 'required_with:order_items|integer|exists:products,id',
            'order_items.*.quantity' => 'required_with:order_items|integer|min:1',
            'order_items.*.unit_price' => 'required_with:order_items|numeric|min:0',
            'order_items.*.discount_rate' => 'nullable|numeric|min:0|max:100',
            'order_items.*.discount_amount' => 'nullable|numeric|min:0',
            'order_items.*.total_amount' => 'required_with:order_items|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'Customer ID is required',
            'customer_id.exists' => 'Customer does not exist',
            'order_date.required' => 'Order date is required',
            'status.required' => 'Order status is required',
            'amount.required' => 'Order amount is required',
            'total_amount.required' => 'Total amount is required',
            'payment_method.required' => 'Payment method is required',
            'payment_status.required' => 'Payment status is required',
            'order_items.required' => 'Order items are required',
            'order_items.min' => 'At least one order item is required',
            'order_items.*.product_id.required' => 'Product ID is required for order item',
            'order_items.*.product_id.exists' => 'Product does not exist',
            'order_items.*.quantity.required' => 'Quantity is required for order item',
            'order_items.*.quantity.min' => 'Quantity must be at least 1',
            'order_items.*.unit_price.required' => 'Unit price is required for order item',
            'order_items.*.total_amount.required' => 'Total amount is required for order item',
        ];
    }
}