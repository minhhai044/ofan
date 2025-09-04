<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {


        if ($this->isMethod('post')) {
            return $this->rulesForCreate();
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->rulesForUpdate();
        }

        return [];
    }

    public function rulesForCreate()
    {
        return [];
    }

    public function rulesForUpdate()
    {
        return [];
    }

    // messages chung
    public function messages()
    {
        return [];
    }

    // Gán tên hiển thị thân thiện
    public function attributes(): array
    {
        return [
            // 'name'      => 'tên chi nhánh',
            // 'address'   => 'địa chỉ',
            // 'branch_id' => 'chi nhánh cha',
            // 'code_misa' => 'mã MISA',
        ];
    }
}
