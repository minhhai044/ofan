<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->rulesForCreate();
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->rulesForUpdate();
        }
        return [];
    }

    public function rulesForCreate(): array
    {
        return [
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],

            'name'       => ['required', 'string', 'min:2', 'max:255', Rule::unique(Product::class, 'name')],
            'sku'        => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9\-_]+$/', Rule::unique(Product::class, 'sku')],
            'code_misa'  => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Z0-9_]+$/', Rule::unique(Product::class, 'code_misa')],
            'bar_code'   => ['required', 'string', 'size:13', 'regex:/^\d{13}$/', Rule::unique(Product::class, 'bar_code')],
            'slug'       => ['nullable', 'string', 'max:255', Rule::unique(Product::class, 'slug')],

            'commission_discount' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'price'      => ['nullable', 'regex:/^\d{1,20}$/'],
            'price_sale' => ['nullable', 'regex:/^\d{1,20}$/', 'lte:price'],

            'filter_stages' => ['nullable', 'integer', 'min:0', 'max:50'],
            'unit'          => ['nullable', 'string', 'max:50'],
            'is_active'     => ['nullable', 'boolean'],
            'is_special'    => ['nullable', 'boolean'],

            // Ảnh
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], // 4MB
            'featured_images'         => ['nullable', 'array', 'max:10'],
            'featured_images.*'       => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'description' => ['nullable', 'string'],

            // Lõi lọc
            'filters'                                      => ['nullable', 'array'],
            'filters.*.product_filter_id'                  => ['nullable', 'integer', 'exists:products,id', 'distinct'],
            'filters.*.maintenance_schedule'               => ['nullable', 'integer', 'min:0', 'max:120'],
            'filters.*.quantity'                           => ['nullable', 'integer', 'min:1', 'max:9999'],
            'filters.*.is_active'                          => ['nullable', 'boolean'],

            // Phụ kiện
            'accessories'                                   => ['nullable', 'array'],
            'accessories.*.product_accessory_id'           => ['nullable', 'integer', 'exists:products,id', 'distinct'],
            'accessories.*.quantity'                        => ['nullable', 'integer', 'min:1', 'max:9999'],
            'accessories.*.is_active'                       => ['nullable', 'boolean'],
        ];
    }

    public function rulesForUpdate(): array
    {


        return [
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'],

            'name'       => ['required', 'string', 'min:2', 'max:255', Rule::unique(Product::class, 'name')->ignore($this->route('products'))],
            'sku'        => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9\-_]+$/', Rule::unique(Product::class, 'sku')->ignore($this->route('products'))],
            'code_misa'  => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Z0-9_]+$/', Rule::unique(Product::class, 'code_misa')->ignore($this->route('products'))],
            'bar_code'   => ['required', 'string', 'size:13', 'regex:/^\d{13}$/', Rule::unique(Product::class, 'bar_code')->ignore($this->route('products'))],
            'slug'       => ['nullable', 'string', 'max:255', Rule::unique(Product::class, 'slug')->ignore($this->route('products'))],

            'commission_discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'price'      => ['required', 'regex:/^\d{1,20}$/'],
            'price_sale' => ['required', 'regex:/^\d{1,20}$/', 'lte:price'],

            'filter_stages' => ['nullable', 'integer', 'min:0', 'max:50'],
            'unit'          => ['nullable', 'string', 'max:50'],
            'is_active'     => ['nullable', 'boolean'],
            'is_special'    => ['nullable', 'boolean'],

            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'featured_images'         => ['nullable', 'array', 'max:10'],
            'featured_images.*'       => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'description' => ['nullable', 'string'],

            'filters'                                      => ['nullable', 'array'],
            'filters.*.product_filter_id'                  => ['nullable', 'integer', 'exists:products,id', 'distinct'],
            'filters.*.maintenance_schedule'               => ['nullable', 'integer', 'min:0', 'max:120'],
            'filters.*.quantity'                           => ['nullable', 'integer', 'min:1', 'max:9999'],
            'filters.*.is_active'                          => ['nullable', 'boolean'],

            'accessories'                                   => ['nullable', 'array'],
            'accessories.*.product_accessory_id'           => ['nullable', 'integer', 'exists:products,id', 'distinct'],
            'accessories.*.quantity'                        => ['nullable', 'integer', 'min:1', 'max:9999'],
            'accessories.*.is_active'                       => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Vui lòng nhập :attribute.',
            'min'      => ':attribute phải lớn hơn hoặc bằng :min.',
            'max'      => ':attribute không được vượt quá :max.',
            'size'     => ':attribute phải đúng :size ký tự.',
            'regex'    => ':attribute không đúng định dạng.',
            'unique'   => ':attribute đã tồn tại.',
            'exists'   => ':attribute không hợp lệ.',
            'integer'  => ':attribute phải là số nguyên.',
            'numeric'  => ':attribute phải là số.',
            'boolean'  => ':attribute không hợp lệ.',
            'lte'      => ':attribute phải nhỏ hơn hoặc bằng :value.',
            'array'    => ':attribute không hợp lệ.',
            'featured_images.max' => 'Chỉ cho phép tối đa 10 ảnh.',
            'featured_images.*.max'   => 'Mỗi ảnh tối đa 4MB.',
            'featured_images.*.mimes' => 'Ảnh phải là jpg, jpeg, png hoặc webp.',
        ];
    }

    public function attributes(): array
    {
        return [
            'product_category_id' => 'danh mục',
            'name'       => 'tên sản phẩm',
            'sku'        => 'SKU',
            'code_misa'  => 'mã MISA',
            'bar_code'   => 'mã vạch',
            'slug'       => 'đường dẫn',
            'commission_discount' => 'hoa hồng',
            'price'      => 'giá',
            'price_sale' => 'giá khuyến mãi',
            'filter_stages' => 'số cấp lọc',
            'unit'          => 'đơn vị',
            'is_active'     => 'trạng thái',
            'is_special'    => 'đặc biệt',
            'featured_image' => 'ảnh đại diện',
            'featured_images'        => 'ảnh bộ sưu tập',
            'featured_images.*'      => 'ảnh bộ sưu tập',
            'description'   => 'mô tả',

            'filters'                                => 'lõi lọc',
            'filters.*.product_filter_id'            => 'lõi lọc',
            'filters.*.maintenance_schedule'         => 'bảo trì (tháng)',
            'filters.*.quantity'                     => 'số lượng',
            'filters.*.is_active'                    => 'trạng thái lõi',

            'accessories'                             => 'phụ kiện',
            'accessories.*.product_accessory_id'      => 'phụ kiện',
            'accessories.*.quantity'                  => 'số lượng',
            'accessories.*.is_active'                 => 'trạng thái phụ kiện',
        ];
    }

    /**
     * (Tuỳ chọn nhưng nên có) Chuẩn hoá đầu vào trước khi validate.
     * - Bỏ dấu chấm tiền tệ; ép MISA in hoa; rỗng slug thì để null.
     */
    protected function prepareForValidation(): void
    {
        $price      = preg_replace('/\D/', '', (string) $this->input('price'));
        $priceSale  = preg_replace('/\D/', '', (string) $this->input('price_sale'));

        $this->merge([
            'price'      => $price !== '' ? $price : null,
            'price_sale' => $priceSale !== '' ? $priceSale : null,
            'code_misa'  => strtoupper((string) $this->input('code_misa')),
            'slug'       => $this->filled('slug') ? $this->input('slug') : null,
        ]);
    }
}
