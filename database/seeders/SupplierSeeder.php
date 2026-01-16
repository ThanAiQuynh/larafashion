<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Nike Vietnam Co., Ltd',
                'code' => 'NCC-NIKE',
                'email' => 'contact@nike.vn',
                'phone' => '02812345678',
                'address' => 'Khu Công Nghiệp Amata, Biên Hòa, Đồng Nai',
                'is_active' => true,
            ],
            [
                'name' => 'Adidas North America',
                'code' => 'NCC-ADI',
                'email' => 'supply@adidas.com',
                'phone' => '02498765432',
                'address' => 'Lô 5, KCN Vsip 1, Thuận An, Bình Dương',
                'is_active' => true,
            ],
            [
                'name' => 'Xưởng May Gia Công Thành Công',
                'code' => 'NCC-THANHCONG',
                'email' => 'xuongmaythanhcong@gmail.com',
                'phone' => '0912345678',
                'address' => '123 Đường số 8, P. Linh Xuân, TP. Thủ Đức, TP. HCM',
                'is_active' => true,
            ],
            [
                'name' => 'Tổng Kho Sỉ Quần Áo H&M',
                'code' => 'NCC-HM',
                'email' => 'khohm@fashion.com',
                'phone' => '0334455667',
                'address' => 'Số 45, Ngõ 102 Khuất Duy Tiến, Thanh Xuân, Hà Nội',
                'is_active' => true,
            ],
            [
                'name' => 'Nhà Sản Xuất Giày Biti\'s',
                'code' => 'NCC-BITIS',
                'email' => 'sales@bitis.vn',
                'phone' => '02838383838',
                'address' => '22 Lý Chiêu Hoàng, Phường 10, Quận 6, TP. HCM',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(['code' => $supplier['code']], $supplier);
        }
    }
}
