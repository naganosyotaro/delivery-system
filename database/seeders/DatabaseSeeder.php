<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Shipment;
use App\Models\StatusUpdate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * 開発用テストデータの作成
     */
    public function run(): void
    {
        // 管理者ユーザー作成
        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // スタッフユーザー作成
        $staff = User::create([
            'name' => 'スタッフ田中',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // ドライバーユーザー作成
        $driver = User::create([
            'name' => 'ドライバー佐藤',
            'email' => 'driver@example.com',
            'password' => Hash::make('password'),
            'role' => 'driver',
        ]);

        // 顧客作成
        $customers = [
            Customer::create([
                'company_name' => '株式会社テスト',
                'contact_name' => '山田太郎',
                'email' => 'yamada@test.co.jp',
                'phone' => '03-1234-5678',
                'address' => '東京都渋谷区神南1-1-1',
            ]),
            Customer::create([
                'company_name' => 'サンプル商事',
                'contact_name' => '鈴木一郎',
                'email' => 'suzuki@sample.co.jp',
                'phone' => '03-9876-5432',
                'address' => '東京都新宿区西新宿2-2-2',
            ]),
            Customer::create([
                'company_name' => 'デモ株式会社',
                'contact_name' => '高橋花子',
                'email' => 'takahashi@demo.co.jp',
                'phone' => '06-1111-2222',
                'address' => '大阪府大阪市北区梅田3-3-3',
            ]),
        ];

        // 発送データ作成
        $statuses = ['pending', 'picked_up', 'in_transit', 'delivered', 'undelivered'];
        $sizes = ['S', 'M', 'L', 'XL'];
        $items = ['書類', '衣類', '食品', '精密機器', '日用品', 'サンプル品'];
        $prefectures = ['東京都', '神奈川県', '埼玉県', '千葉県', '大阪府', '京都府'];

        for ($i = 0; $i < 30; $i++) {
            $status = $statuses[array_rand($statuses)];
            $customer = $customers[array_rand($customers)];
            $prefecture = $prefectures[array_rand($prefectures)];

            $shipment = Shipment::create([
                'customer_id' => $customer->id,
                'tracking_number' => Shipment::generateTrackingNumber(),
                'sender_name' => $customer->contact_name,
                'sender_address' => $customer->address,
                'sender_phone' => $customer->phone,
                'recipient_name' => '届け先 ' . ($i + 1),
                'recipient_address' => $prefecture . '○○市△△区□□町' . rand(1, 10) . '-' . rand(1, 30),
                'recipient_phone' => '0' . rand(80, 99) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999),
                'item_name' => $items[array_rand($items)],
                'size' => $sizes[array_rand($sizes)],
                'weight' => rand(1, 100) / 10,
                'quantity' => rand(1, 3),
                'status' => $status,
                'shipping_fee' => rand(5, 30) * 100,
                'preferred_delivery_at' => now()->addDays(rand(0, 7))->setHour(rand(9, 17)),
                'notes' => rand(0, 2) == 0 ? 'サンプル備考' : null,
                'created_by' => $staff->id,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);

            // ステータス履歴を作成
            StatusUpdate::create([
                'shipment_id' => $shipment->id,
                'user_id' => $staff->id,
                'status' => 'pending',
                'notes' => '発送受付',
                'created_at' => $shipment->created_at,
            ]);

            if (in_array($status, ['picked_up', 'in_transit', 'delivered', 'undelivered'])) {
                StatusUpdate::create([
                    'shipment_id' => $shipment->id,
                    'user_id' => $driver->id,
                    'status' => 'picked_up',
                    'notes' => '集荷完了',
                    'created_at' => $shipment->created_at->addHours(rand(1, 4)),
                ]);
            }

            if (in_array($status, ['in_transit', 'delivered', 'undelivered'])) {
                StatusUpdate::create([
                    'shipment_id' => $shipment->id,
                    'user_id' => $driver->id,
                    'status' => 'in_transit',
                    'notes' => '配送開始',
                    'created_at' => $shipment->created_at->addHours(rand(5, 12)),
                ]);
            }

            if ($status == 'delivered') {
                StatusUpdate::create([
                    'shipment_id' => $shipment->id,
                    'user_id' => $driver->id,
                    'status' => 'delivered',
                    'notes' => '配達完了',
                    'created_at' => $shipment->created_at->addHours(rand(13, 24)),
                ]);
            }

            if ($status == 'undelivered') {
                StatusUpdate::create([
                    'shipment_id' => $shipment->id,
                    'user_id' => $driver->id,
                    'status' => 'undelivered',
                    'notes' => 'ご不在のため持ち帰り',
                    'created_at' => $shipment->created_at->addHours(rand(13, 24)),
                ]);
            }
        }
    }
}
