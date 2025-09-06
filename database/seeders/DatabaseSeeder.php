<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ProductCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Branch::create([
            'name' => 'Nano Geyser',
            'address' => 'Kiến Hưng - Hà Đông - Hà Nội',
            'code_misa' => 'NANOGEYSER',
            'is_active' => 1,
            'type' => 0,
            'slug' => generateSlug('Nano Geyser')
        ]);
        Branch::create([
            'name' => 'Ofan',
            'address' => 'Hưng Yên - Hà Nội',
            'code_misa' => 'OFAN',
            'is_active' => 1,
            'branch_id' => 1,
            'type' => 1,
            'slug' => generateSlug('Ofan')
        ]);
        User::query()->create([
            'name' => 'System Admin',
            'phone' => '0338997846',
            'password' => '0338997846',
            'branch_id' => 1,
            'slug' => generateSlug('System Admin'),
            'code_misa' => codeMisa('System Admin', '0338997846')
        ]);
        ProductCategory::query()->create([
            'name' => 'Lõi lọc nước',
            'slug' => generateSlug('Lõi lọc nước')
        ]);
        ProductCategory::query()->create([
            'name' => 'Phụ kiện máy lọc nước',
            'slug' => generateSlug('Phụ kiện máy lọc nước')
        ]);

        // Reset cache permission
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ====== Khai báo permission bằng tiếng Việt ======
        $map = [
            'người dùng' => ['xem', 'thêm', 'sửa', 'xóa'],
            'vai trò'    => ['xem', 'gán'],
            'chi nhánh'  => ['xem', 'thêm', 'sửa', 'xóa'],
        ];

        // Tạo toàn bộ permission
        $tatCaQuyen = [];
        foreach ($map as $doiTuong => $hanhDongs) {
            foreach ($hanhDongs as $hd) {
                $tenQuyen = "{$hd} {$doiTuong}";
                $tatCaQuyen[] = $tenQuyen;
                Permission::firstOrCreate([
                    'name'       => $tenQuyen,
                    'code'       => codeName($tenQuyen),
                    'guard_name' => 'web',
                ]);
            }
        }

        // ====== Tạo roles ======
        $roles = [
            'Super Admin' => $tatCaQuyen,
            'Bán hàng'     => [
                'xem người dùng',
                'thêm người dùng',
                'sửa người dùng',
                'xem vai trò',
                'gán vai trò',
                'xem chi nhánh',
                'thêm chi nhánh',
                'sửa chi nhánh',
            ],
            'Kỹ thuật'   => [
                'xem người dùng',
                'xem chi nhánh',
            ],

            'Kế toán'   => [
                'xem người dùng',
                'xem chi nhánh',
            ],

            'Khách hàng' => [
                'xem người dùng',
                'xem chi nhánh',
            ],
        ];

        foreach ($roles as $tenRole => $dsQuyen) {
            $role = Role::firstOrCreate(['name' => $tenRole, 'guard_name' => 'web']);
            $role->syncPermissions($dsQuyen);
        }
    }
}
