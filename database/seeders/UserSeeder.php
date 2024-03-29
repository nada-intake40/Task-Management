<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::create(["full_name" => "Ahmed Mostafa Fawzy" , "email" => "ahmed1993@gmail.com" , 
                                 "password" => Hash::make(1234556789) , "phone" => "01285104045"]);
        $manager->assignRole('Manager');

        $user = User::create(["full_name" => "Nada Alaa Hassan" , "email" => "nada1994@gmail.com" , 
                                 "password" => Hash::make(1234556789) , "phone" => "01063169859"]);
        $user->assignRole('Employee');

        $user = User::create(["full_name" => "Mona Hussien Mohamed" , "email" => "mona2001@gmail.com" , 
                                 "password" => Hash::make(1234556789) , "phone" => "01285104000"]);
        $user->assignRole('Employee');

        $user = User::create(["full_name" => "Hager Reda" , "email" => "hager93@gmail.com" , 
                                 "password" => Hash::make(1234556789) , "phone" => "01286678745"]);
        $user->assignRole('Employee');
    }
}
