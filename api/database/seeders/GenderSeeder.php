<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();
        $genders = [
            [
                'name' => 'Male',
                'description' => 'The term male is a term to describe cisgendered people who were assigned male at birth and embrace that identification for themselves.',
                'disabled_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Female',
                'description' => 'The traditional or conservative definition of "female" is a person who is biologically born with ovaries and typically has the capacity to produce eggs.',
                'disabled_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'LGBTQQIP2SAA',
                'description' => 'Lesbian, Gay, Bisexual, Transgender, Queer, Questioning, Intersex, Pansexual, Two-Spirit, Asexual, Ally',
                'disabled_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Attack Helicopter',
                'description' => "I sexually identify as an Attack Helicopter",
                'disabled_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        Gender::insert($genders);
    }
}
