<?php

namespace App\Http\Requests\Admin;

use App\Models\ProductCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductCategoryRequest extends FormRequest
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
            'name'  => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique(ProductCategory::class),
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096', // 4MB
            ],
        ];
    }

    public function rulesForUpdate(): array
    {


        return [
            'name'  => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('product_categories', 'name')->ignore($this->route('product_categories')),
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.min'      => 'Tên danh mục phải có ít nhất :min ký tự.',
            'name.max'      => 'Tên danh mục tối đa :max ký tự.',
            'name.unique'   => 'Tên danh mục đã tồn tại.',

            'image.image'   => 'Tập tin phải là hình ảnh.',
            'image.mimes'   => 'Ảnh chỉ chấp nhận các định dạng: jpg, jpeg, png, webp.',
            'image.max'     => 'Dung lượng ảnh tối đa 4MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'  => 'tên danh mục',
            'image' => 'ảnh',
        ];
    }
}
