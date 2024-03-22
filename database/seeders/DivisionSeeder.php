<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $divisions = [

            [
                'name' => 'FARO & DEO',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'ADAMAWA')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MBERE',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'ADAMAWA')->where('level_id', 2)->first()->id,
            ],
            [
                'name' => 'MAYO BANYO',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'ADAMAWA')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'DJEREM',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'ADAMAWA')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'VINA',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'ADAMAWA')->where('level_id', 2)->first()->id,

            ],

            [
                'name' => 'HAUT SANAGA',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'LEKIE',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MFOUNDI',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MBAM ET INOUBOU',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MBAM ET KIM',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MFOU ET AFAMBA',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MEFOU ET AKONO',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'NYONG ET SO\'O',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'NYONG ET KELLE',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'NYONG ET MFOUMOU',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,

            ],

            [
                'name' => 'BOUMBA ET NGOKO',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'EAST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'LOM ET DJEREM',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'EAST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'KADEY',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'EAST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'HAUT NYONG',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'EAST REGION')->where('level_id', 2)->first()->id,

            ],

            [
                'name' => 'WOURI',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'LITTORAL')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'NKAM',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'LITTORAL')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MOUNGO',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'LITTORAL')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'SANAGA MARITIME',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'LITTORAL')->where('level_id', 2)->first()->id,

            ],

            [
                'name' => 'LOGONE ET CHARI',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'DIAMARÉ',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MAYO TSANAGA',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MAYO DANAY',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MAYO KANI',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MAYO SAVA',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->first()->id,

            ],

            [
                'name' => 'BENUE',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'NORTH REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MAYO LOUTI',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'NORTH REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'FARO',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'NORTH REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MAYO REY',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'NORTH REGION')->where('level_id', 2)->first()->id,

            ],

            [
                'name' => 'BUI',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'BOYO',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'DONGA MATUM',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MENCHUM',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MEZAM',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MOMO',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'NGOKETUNJA',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->first()->id,

            ],


            [
                'name' => 'FAKO',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MEME',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'NDIAN',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'KUPE MANENGUBA',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MANYU',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'LEBIALEM',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->first()->id,

            ],


            [
                'name' => 'MVILA',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'SOUTH REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'DJA ET LOBO',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'SOUTH REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'OCEAN',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'SOUTH REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'NTEM',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'SOUTH REGION')->where('level_id', 2)->first()->id,

            ],

            [
                'name' => 'BAMBOUTOUS',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MIFI',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'HAUT-NKAM',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'HAUT PLATEAU',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'MENOUA',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'KOUNG-KHI',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'NOUN',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->first()->id,

            ],
            [
                'name' => 'NDE',
                'level_id' => 3,
                'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->first()->id,

            ],

        ];

        foreach($divisions as $division){
            Zone::query()->updateOrCreate($division);
        }
    }
}
