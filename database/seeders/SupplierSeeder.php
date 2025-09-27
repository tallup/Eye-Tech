<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Tech Suppliers Ltd',
                'contact_person' => 'John Doe',
                'email' => 'john@techsuppliers.com',
                'phone' => '+220 123 4567',
                'address' => '123 Tech Street, Banjul',
                'is_active' => true,
            ],
            [
                'name' => 'Mobile Accessories Plus',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@mobileaccessories.com',
                'phone' => '+220 987 6543',
                'address' => '456 Mobile Avenue, Serrekunda',
                'is_active' => true,
            ],
            [
                'name' => 'Premium Tech Solutions',
                'contact_person' => 'Ahmed Hassan',
                'email' => 'ahmed@premiumtech.com',
                'phone' => '+220 555 1234',
                'address' => '789 Premium Plaza, Brikama',
                'is_active' => true,
            ],
            [
                'name' => 'Digital Innovations Inc',
                'contact_person' => 'Fatou Jallow',
                'email' => 'fatou@digitalinnovations.com',
                'phone' => '+220 777 8888',
                'address' => '321 Digital Drive, Bakau',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }
    }
}