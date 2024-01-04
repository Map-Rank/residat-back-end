<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $cameroun = Zone::query()->create([
            'name' => 'Cameroun',
            'level_id' => 1,
        ]);

        $regions = [
            [
                'name'=>'ADAMAWA',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'children' => [
                    [
                        'name' => 'FARO & DEO',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'ADAMAWA')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Galim - Tinyére', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FARO & DEO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kontcha', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FARO & DEO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mayo Baleo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FARO & DEO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Tinyére', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FARO & DEO')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MBERE',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'ADAMAWA')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Dji', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBERE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Djohong', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBERE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Meiganga', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBERE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nfaoui', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBERE')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MAYO BANYO',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'ADAMAWA')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Bankim', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO BANYO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Banyo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO BANYO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mayo Darle', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO BANYO')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'DJEREM',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'ADAMAWA')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Ngoundal', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DJEREM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Tibati', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DJEREM')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'VINA',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'ADAMAWA')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Belel', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'VINA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nbe', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'VINA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nganha', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'VINA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngaoundere (urban)', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'VINA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngaoundere (rural)', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'VINA')->where('level_id', 3)->first()->id],
                        ]
                    ]
                ]
            ],
            [
                'name'=>'CENTER',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'children' => [
                    [
                        'name' => 'HAUT SANAGA',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Bibey', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT SANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Lembey-Yezoum', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT SANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mbandjock', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT SANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Minta', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT SANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nanga-eboko', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT SANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nkoteng', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT SANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nsem', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT SANAGA')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'LEKIE',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Batchenga', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEKIE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ebebda', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEKIE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Elig  Mfomou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEKIE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Evodoula', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEKIE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Lobo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEKIE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Monatélé', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEKIE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Obala', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEKIE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Okola', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEKIE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Sa\'a', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEKIE')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MFOUNDI',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Yaounde I', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOUNDI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Yaounde II', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOUNDI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Yaounde III', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOUNDI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Yaounde IV', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOUNDI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Yaounde V', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOUNDI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Yaounde VI', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOUNDI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Yaounde VII', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOUNDI')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MBAM ET INOUBOU',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Bafia', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET INOUBOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bokito', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET INOUBOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Deuk', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET INOUBOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kiiki', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET INOUBOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kon-Yambeta', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET INOUBOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Makenéné', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET INOUBOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ndikinimiki', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET INOUBOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nitoukou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET INOUBOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ombessa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET INOUBOU')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MBAM ET KIM',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Mbangasinna', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET KIM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngambe Tikar', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET KIM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngoro', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET KIM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ntui', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET KIM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Yoko', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MBAM ET KIM')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MFOU ET AFAMBA',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Afanloum', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOU ET AFAMBA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Awaé', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOU ET AFAMBA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Edzendouan', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOU ET AFAMBA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Esse', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOU ET AFAMBA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mfou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOU ET AFAMBA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nkolafamba', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOU ET AFAMBA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Olanguina', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOU ET AFAMBA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Soa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MFOU ET AFAMBA')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MEFOU ET AKONO',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Akono', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEFOU ET AKONO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bikok', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEFOU ET AKONO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mbankomo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEFOU ET AKONO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngoumou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEFOU ET AKONO')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'NYONG ET SO\'O',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Akoeman', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET SO\'O')->where('level_id', 3)->first()->id],
                            ['name'=> 'Dzeng', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET SO\'O')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mbalmayo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET SO\'O')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mengeme', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET SO\'O')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngomedzap', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET SO\'O')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nkolmetet', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET SO\'O')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'NYONG ET KELLE',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Biyouha', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET KELLE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bondjock', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET KELLE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bot-Makak', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET KELLE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Dibank', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET KELLE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Eséka', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET KELLE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Matomb', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET KELLE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Messondo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET KELLE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngog Mapubi', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET KELLE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngui Bassa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET KELLE')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'NYONG ET MFOUMOU',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'CENTER')->where('level_id', 2)->first()->id,
                        'children' => [
                            ['name'=> 'Akonolinga', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET MFOUMOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ayos', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET MFOUMOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Endom', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET MFOUMOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kobdombo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET MFOUMOU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mengang', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NYONG ET MFOUMOU')->where('level_id', 3)->first()->id],
                        ]
                    ]
                ]
            ],
            [
                'name'=>'EAST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
            ],
            [
                'name'=>'LITTORAL',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
            ],
            [
                'name'=>'FAR NORTH',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
            ],
            [
                'name'=>'NORTH REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
            ],
            [
                'name'=>'NORTH WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
            ],
            [
                'name'=>'SOUTH WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
            ],
            [
                'name'=>'SOUTH REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
            ],
            [
                'name'=>'WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
            ]
        ];

        $adamawas = [
            [
                'name' => 'FARO & DEO',
                'level_id' => 3,
                'parent_id' =>
            ],

        ]
    }
}
