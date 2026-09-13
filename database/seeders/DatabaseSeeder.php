<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $pharmacy = Pharmacy::updateOrCreate(
            ['slug' => 'citycare-demo-pharmacy'],
            [
                'name' => 'CityCare Demo Pharmacy',
                'address' => 'Main Boulevard, Model Town',
                'city' => 'Lahore',
                'contact_number' => '0300-1234567',
                'is_active' => true,
            ]
        );
        Wallet::firstOrCreate(['pharmacy_id' => $pharmacy->id], ['currency' => 'PKR', 'is_active' => true]);

        $categories = [
            'Pain Relief', 'Antibiotics', 'Fever & Cold', 'Diabetes',
            'Blood Pressure', 'Heart Care', 'Vitamins & Supplements',
            'Digestive Health', 'Skin Care', 'Allergy', 'Respiratory Care', 'Others',
        ];

        $categoryModels = [];
        foreach ($categories as $categoryName) {
            $categoryModels[$categoryName] = MedicineCategory::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'is_active' => true]
            );
        }

        $demoPasswords = [
            'admin' => env('DEMO_ADMIN_PASSWORD'),
            'staff' => env('DEMO_STAFF_PASSWORD'),
            'customer' => env('DEMO_CUSTOMER_PASSWORD'),
            'rider' => env('DEMO_RIDER_PASSWORD'),
        ];

        $admin = null;
        if (! in_array(null, $demoPasswords, true) && ! in_array('', $demoPasswords, true)) {
            $admin = User::updateOrCreate(
                ['email' => 'admin@pharmacy.test'],
                ['name' => 'System Admin', 'password' => Hash::make($demoPasswords['admin']), 'role' => 'admin', 'is_active' => true]
            );

            $staff = User::updateOrCreate(
                ['email' => 'staff@pharmacy.test'],
                ['name' => 'CityCare Staff', 'phone_number' => '03001234567', 'password' => Hash::make($demoPasswords['staff']), 'role' => 'pharmacy_staff', 'is_active' => true]
            );

            User::updateOrCreate(
                ['email' => 'customer@pharmacy.test'],
                ['name' => 'Demo Customer', 'phone_number' => '03009876543', 'password' => Hash::make($demoPasswords['customer']), 'role' => 'customer', 'is_active' => true]
            );

            User::updateOrCreate(
                ['email' => 'rider@pharmacy.test'],
                ['name' => 'Demo Rider', 'phone_number' => '03001112222', 'password' => Hash::make($demoPasswords['rider']), 'role' => 'rider', 'is_active' => true]
            );

            $pharmacy->staff()->syncWithoutDetaching([$staff->id]);
        }

        $medicines = [
            ['medicine_name' => 'Panadol 500mg', 'category' => 'Pain Relief', 'price' => 3.50, 'stock_quantity' => 120, 'description' => 'Paracetamol tablets for fever and mild pain relief.'],
            ['medicine_name' => 'Augmentin 625mg', 'category' => 'Antibiotics', 'price' => 8.75, 'stock_quantity' => 0, 'description' => 'Amoxicillin/Clavulanate antibiotic tablets.'],
            ['medicine_name' => 'Flumex Syrup', 'category' => 'Fever & Cold', 'price' => 4.20, 'stock_quantity' => 45, 'description' => 'Cough and cold relief syrup for adults and children.'],
            ['medicine_name' => 'Glucophage 500mg', 'category' => 'Diabetes', 'price' => 5.90, 'stock_quantity' => 60, 'description' => 'Metformin tablets for blood sugar control.'],
            ['medicine_name' => 'Norvasc 5mg', 'category' => 'Blood Pressure', 'price' => 6.10, 'stock_quantity' => 30, 'description' => 'Amlodipine tablets for hypertension.'],
            ['medicine_name' => 'Vitamin C 1000mg', 'category' => 'Vitamins & Supplements', 'price' => 2.99, 'stock_quantity' => 200, 'description' => 'Immune support effervescent tablets.'],
        ];

        foreach ($medicines as $medicine) {
            Medicine::updateOrCreate(
                ['pharmacy_id' => $pharmacy->id, 'medicine_name' => $medicine['medicine_name']],
                [
                    'category_id' => $categoryModels[$medicine['category']]->id,
                    'price' => $medicine['price'],
                    'stock_quantity' => $medicine['stock_quantity'],
                    'description' => $medicine['description'],
                    'created_by' => $admin?->id,
                    'is_active' => true,
                ]
            );
        }

    }
}
