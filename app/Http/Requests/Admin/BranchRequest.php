<?php

namespace App\Http\Requests\Admin;

use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BranchRequest extends FormRequest
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
        return [
            'name'      => ['required', 'string', 'min:3', 'max:255', Rule::unique(Branch::class)],
            'address'   => ['required', 'string', 'min:5', 'max:255'],
            'type'      => ['required'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'code_misa' => ['nullable', 'regex:/^[A-Z0-9]{2,255}$/', 'unique:branches,code_misa'],
        ];
    }

    public function rulesForUpdate()
    {
        return [
            'name'      => ['required', 'string', 'min:3', 'max:255', Rule::unique(Branch::class)->ignore($this->route('branches'))],
            'address'   => ['required', 'string', 'min:5', 'max:255'],
            'type'      => ['required'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'code_misa' => ['nullable', 'regex:/^[A-Z0-9]{2,30}$/', Rule::unique(Branch::class, 'code_misa')->ignore($this->route('branches'))],
        ];
    }

    // messages chung
    // Thông điệp lỗi tiếng Việt
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập :attribute.',
            'name.string'   => ':attribute phải là chuỗi ký tự.',
            'name.min'      => ':attribute phải có ít nhất :min ký tự.',
            'name.max'      => ':attribute không được vượt quá :max ký tự.',
            'name.unique'   => ':attribute đã tồn tại trong hệ thống.',

            'address.required' => 'Vui lòng nhập :attribute.',
            'address.string'   => ':attribute phải là chuỗi ký tự.',
            'address.min'      => ':attribute phải có ít nhất :min ký tự.',
            'address.max'      => ':attribute không được vượt quá :max ký tự.',

            'branch_id.integer' => ':attribute không hợp lệ.',
            'branch_id.exists'  => ':attribute được chọn không tồn tại.',
            'branch_id.not_in'  => ':attribute không được trùng với chính chi nhánh này.',

            'code_misa.regex'  => ':attribute chỉ gồm chữ IN HOA và số (2–30 ký tự), không khoảng trắng.',
            'code_misa.unique' => ':attribute đã tồn tại.',
        ];
    }

    // Gán tên hiển thị thân thiện
    public function attributes(): array
    {
        return [
            'name'      => 'tên chi nhánh',
            'address'   => 'địa chỉ',
            'branch_id' => 'chi nhánh cha',
            'code_misa' => 'mã MISA',
            'type'      => 'vai trò',
        ];
    }
}
