<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Lấy id hiện tại từ route ({user} hoặc {users}), hỗ trợ model binding hoặc id */
    // protected function currentId(): ?int
    // {
    //     $param = $this->route('user') ?? $this->route('users');
    //     if ($param instanceof User) return $param->id;
    //     return $param ? (int) $param : null;
    // }

    /** Chuẩn hoá dữ liệu đầu vào trước khi validate */
    protected function prepareForValidation(): void
    {
        $name        = $this->input('name');
        $address     = $this->input('address');
        $email       = $this->input('email');
        $phone       = $this->input('phone');
        $codeMisa    = $this->input('code_misa');
        $basicSalary = $this->input('basic_salary');

        // Chuẩn hoá SĐT: giữ lại số, +, -, khoảng trắng; riêng khi sinh code sẽ chỉ lấy số
        $phone = is_string($phone) ? trim($phone) : $phone;

        // Ép code_misa IN HOA + trim khoảng trắng 2 đầu (giữ dấu _ ở giữa)
        if ($codeMisa !== null) {
            $codeMisa = strtoupper(trim($codeMisa));
        }

        // Ép lương về số (nếu là chuỗi “8.000.000” thì loại dấu chấm/phẩy)
        if (is_string($basicSalary)) {
            $basicSalary = preg_replace('/[^\d.]/', '', $basicSalary ?? '');
            // bỏ dấu chấm phẩy ngăn cách
            $basicSalary = str_replace([',', ' '], '', $basicSalary);
        }

        $this->merge([
            'name'         => is_string($name) ? trim($name) : $name,
            'address'      => is_string($address) ? trim($address) : $address,
            'email'        => is_string($email) ? trim($email) : $email,
            'phone'        => $phone,
            'code_misa'    => $codeMisa,
            'basic_salary' => $basicSalary !== '' ? $basicSalary : 0,
        ]);
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

    /** Tạo mới */
    public function rulesForCreate(): array
    {
        return [
            'branch_id'    => ['required', 'integer', 'exists:branches,id'],
            'name'         => ['required', 'string', 'min:3', 'max:255'],
            'phone'        => ['required', 'string', 'regex:/^[0-9+\-\s]{10,10}$/', Rule::unique('users', 'phone')],
            'email'        => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')],
            'address'      => ['nullable', 'string', 'max:255'],
            'avatar'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],  // 4MB
            'password'     => ['required', 'string', 'min:6', 'confirmed'],
            'code_misa'    => ['required', 'regex:/^[A-Z0-9_]{2,30}$/', Rule::unique('users', 'code_misa')],
            'gender'       => ['required', 'in:0,1'], // 0: Nam, 1: Nữ
            'role'         => ['nullable', 'in:0,1'], // 0: Member, 1: Admin
            'is_active'    => ['nullable', 'in:0,1'], // 0/1
            'fcm_token'    => ['nullable', 'string', 'max:512'],
            'bank_info'    => ['nullable', 'string', 'max:255'],
            'bank_qr'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], // 4MB
            'basic_salary' => ['nullable'],
            'birthday'     => ['nullable', 'date', 'before:tomorrow'],
        ];
    }

    /** Cập nhật */
    public function rulesForUpdate(): array
    {
        $id = $this->route('users');

        return [
            'branch_id'    => ['required', 'integer', 'exists:branches,id'],
            'name'         => ['required', 'string', 'min:3', 'max:255'],
            'phone'        => ['required', 'string', 'regex:/^[0-9+\-\s]{10,10}$/', Rule::unique('users', 'phone')->ignore($id)],
            'email'        => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'address'      => ['nullable', 'string', 'max:255'],
            'avatar'       => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'password'     => ['nullable', 'string', 'min:6', 'confirmed'],
            'code_misa'    => ['required', 'regex:/^[A-Z0-9_]{2,255}$/', Rule::unique('users', 'code_misa')->ignore($id)],
            'gender'       => ['required', 'in:0,1'],
            'role'         => ['nullable', 'in:0,1'],
            'is_active'    => ['nullable', 'in:0,1'],
            'fcm_token'    => ['nullable', 'string', 'max:512'],
            'bank_info'    => ['nullable', 'string', 'max:255'],
            'bank_qr'      => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'basic_salary' => ['nullable'],
            'birthday'     => ['nullable', 'date', 'before:tomorrow'],
        ];
    }

    /** Thông điệp lỗi */
    public function messages(): array
    {
        return [
            'branch_id.exists' => 'Chi nhánh được chọn không tồn tại.',
            'name.required'    => 'Vui lòng nhập họ và tên.',
            'name.min'         => 'Họ và tên phải có ít nhất :min ký tự.',
            'name.max'         => 'Họ và tên không vượt quá :max ký tự.',
            'phone.required'   => 'Vui lòng nhập số điện thoại.',
            'phone.regex'      => 'Số điện thoại không hợp lệ (8–20 ký tự, chỉ gồm số, khoảng trắng, +, -).',
            'phone.unique'     => 'Số điện thoại đã tồn tại.',
            'email.email'      => 'Email không đúng định dạng.',
            'email.unique'     => 'Email đã tồn tại.',
            'address.max'      => 'Địa chỉ không vượt quá :max ký tự.',
            'avatar.image'     => 'Avatar phải là ảnh.',
            'avatar.mimes'     => 'Avatar chỉ chấp nhận jpg, jpeg, png, webp.',
            'avatar.max'       => 'Dung lượng ảnh avatar tối đa 4MB.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'     => 'Mật khẩu tối thiểu :min ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'code_misa.required' => 'Vui lòng nhập mã MISA.',
            'code_misa.regex'  => 'Mã MISA chỉ gồm chữ IN HOA, số và dấu gạch dưới (_), 2–255 ký tự.',
            'code_misa.unique' => 'Mã MISA đã tồn tại.',
            'gender.in'        => 'Giới tính không hợp lệ.',
            'role.in'          => 'Vai trò không hợp lệ.',
            'is_active.in'     => 'Trạng thái không hợp lệ.',
            'bank_info.max'    => 'Thông tin ngân hàng không vượt quá :max ký tự.',
            'bank_qr.image'    => 'Ảnh QR ngân hàng phải là ảnh.',
            'bank_qr.mimes'    => 'Ảnh QR chỉ chấp nhận jpg, jpeg, png, webp.',
            'bank_qr.max'      => 'Dung lượng ảnh QR tối đa 4MB.',
            'basic_salary.numeric' => 'Lương cơ bản phải là số.',
            'basic_salary.min'     => 'Lương cơ bản không nhỏ hơn :min.',
            'birthday.date'        => 'Ngày sinh không hợp lệ.',
            'birthday.before'      => 'Ngày sinh phải trước ngày hiện tại.',
        ];
    }

    /** Tên thuộc tính thân thiện */
    public function attributes(): array
    {
        return [
            'branch_id'    => 'chi nhánh',
            'name'         => 'họ và tên',
            'phone'        => 'số điện thoại',
            'email'        => 'email',
            'address'      => 'địa chỉ',
            'avatar'       => 'ảnh đại diện',
            'password'     => 'mật khẩu',
            'code_misa'    => 'mã MISA',
            'gender'       => 'giới tính',
            'role'         => 'vai trò',
            'is_active'    => 'trạng thái hoạt động',
            'fcm_token'    => 'FCM token',
            'bank_info'    => 'thông tin ngân hàng',
            'bank_qr'      => 'ảnh QR ngân hàng',
            'basic_salary' => 'lương cơ bản',
            'birthday'     => 'ngày sinh',
        ];
    }
}
