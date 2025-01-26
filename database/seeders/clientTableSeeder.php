<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\clients;
use App\Models\services_client;
use Illuminate\Support\Facades\Hash;

class clientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $client =  clients::create([
            'name' => 'client1',
            'email' => 'client1@example.com',
            'password' => Hash::make('client1'), // Hashing the password
            'birth_date' => '1990-01-01',
            'img' => 'default.jpg',
            'mobile' => '1234567890',
            'age' => 35,
            'city' => 'City Name',
            'work' => 'Work Title',
            'center' => 'Center Name',
            'gender' => 'Male',
            'status' => 'Active',
            'landline' => '9876543210',
            'na_number' => 'NA123',
            'governorate' => 'Governorate Name',
            'another_mobile' => '1122334455',
            'Village_Street' => 'Street Name',
            'marital_status' => 'Single',
            'num_of_children' => 0,
            'Academic_qualification' => 'Bachelor\'s Degree',
        ]);

        services_client::create([
            'client_id' => $client->id,
            'service_name' => 'Service Name',
            'service_cost' => 100.00,
            'payment_status' => 'paid',

        ]);
    }
}
