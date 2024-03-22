<?php

namespace Database\Seeders;

use App\Models\Vector;
use App\Models\VectorKey;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VectorKeysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['COLOR', 'IMAGE', 'FIGURE'];

        foreach ($types as $type) {
            $vectors = Vector::where('type', $type)->get();

            foreach ($vectors as $vector) {
                VectorKey::create([
                    'value' => "Example $type Value for Vector ID: $vector->id",
                    'type' => $type,
                    'name' => "Example $type Name for Vector ID: $vector->id",
                    'vector_id' => $vector->id,
                ]);
            }
        }
    }
}
