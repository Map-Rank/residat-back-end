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
                'children' => [
                    [
                        'name' => 'BOUMBA ET NGOKO',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'EAST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Gari Gombo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BOUMBA ET NGOKO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mouloundou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BOUMBA ET NGOKO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Salapoumbé', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BOUMBA ET NGOKO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Yokadouma', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BOUMBA ET NGOKO')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'LOM ET DJEREM',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'EAST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Belabo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOM ET DJEREM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bertoua', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOM ET DJEREM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Betare Oya', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOM ET DJEREM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Diang', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOM ET DJEREM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Garoua Boulai', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOM ET DJEREM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mandjou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOM ET DJEREM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngoura', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOM ET DJEREM')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'KADEY',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'EAST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Batouri', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'KADEY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kentzou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'KADEY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kette', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'KADEY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mbang', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'KADEY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ndelele', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'KADEY')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'HAUT NYONG',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'EAST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Abong Mbang', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Angossas', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Atok', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Dimako', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Doumaintang', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Doumé', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Lomié', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mboma', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Messamena', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Messok', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mindourou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngoyla', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nguelemendouka', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                            ['name'=> 'Somalomo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT NYONG')->where('level_id', 3)->first()->id],
                        ]
                    ],
                ]
                
            ],
            [
                'name'=>'LITTORAL',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'children' => [
                    [
                        'name' => 'WOURI',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'LITTORAL')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Douala I', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'WOURI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Douala II', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'WOURI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Douala III', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'WOURI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Douala IV', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'WOURI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Douala V', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'WOURI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Douala VI', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'WOURI')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'NKAM',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'LITTORAL')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Ndobian', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NKAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nkondjock', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NKAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Yabassi', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NKAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Yingui', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NKAM')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MOUNGO',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'LITTORAL')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Baré', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bonalea', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Dibombarri', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Eboné', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Loum', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Manjo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mbanga', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Melong', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mombo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nkonsamba I', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nkongsamba II', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nkonsamba III', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Penja', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOUNGO')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'SANAGA MARITIME',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'LITTORAL')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Dizangué', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'SANAGA MARITIME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Dibamba', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'SANAGA MARITIME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Edea I', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'SANAGA MARITIME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Edea II', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'SANAGA MARITIME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Massock', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'SANAGA MARITIME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mouanko', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'SANAGA MARITIME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ndom', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'SANAGA MARITIME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngambe', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'SANAGA MARITIME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngwei', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'SANAGA MARITIME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nyanon', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'SANAGA MARITIME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Pouma', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'SANAGA MARITIME')->where('level_id', 3)->first()->id],
                        ]
                    ],
                ]
            ],
            [
                'name'=>'FAR NORTH',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'children' => [
                    [
                        'name' => 'LOGONE ET CHARI',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Blangoua', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOGONE ET CHARI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Darak', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOGONE ET CHARI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Fotokol', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOGONE ET CHARI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Hile-Alifa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOGONE ET CHARI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kousseri', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOGONE ET CHARI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Logone-Birni', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOGONE ET CHARI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Makary', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOGONE ET CHARI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Waza', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOGONE ET CHARI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Zina', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LOGONE ET CHARI')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'DIAMARÉ',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Bogo ', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DIAMARÉ')->where('level_id', 3)->first()->id],
                            ['name'=> 'Dargala', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DIAMARÉ')->where('level_id', 3)->first()->id],
                            ['name'=> 'Gawaza', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DIAMARÉ')->where('level_id', 3)->first()->id],
                            ['name'=> 'Maroua I', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DIAMARÉ')->where('level_id', 3)->first()->id],
                            ['name'=> 'Maroua II', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DIAMARÉ')->where('level_id', 3)->first()->id],
                            ['name'=> 'Maroua III', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DIAMARÉ')->where('level_id', 3)->first()->id],
                            ['name'=> 'Meri', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DIAMARÉ')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ndoukoula', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DIAMARÉ')->where('level_id', 3)->first()->id],
                            ['name'=> 'Petté', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DIAMARÉ')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MAYO TSANAGA',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Bourha', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO TSANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Hirna', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO TSANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Koza', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO TSANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mogodé', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO TSANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mokolo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO TSANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mozogo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO TSANAGA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Souledé-Roua', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO TSANAGA')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                    [
                        'name' => 'MAYO DANAY',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Datcheka', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO DANAY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Gobo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO DANAY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Gueme', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO DANAY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Guere', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO DANAY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kai-Kai', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO DANAY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kalfou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO DANAY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kay-Hay', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO DANAY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Maga', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO DANAY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Tchati Bali', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO DANAY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Wina ', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO DANAY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Yagoua', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO DANAY')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MAYO KANI',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Dziguilao', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO KANI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Guidiguis', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO KANI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kaelé', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO KANI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mindif', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO KANI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Moulvoudaye', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO KANI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Moutourwa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO KANI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Touloum', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO KANI')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                    [
                        'name' => 'MAYO SAVA',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'FAR NORTH')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Kolofata', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO SAVA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mora', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO SAVA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Tokombere', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO SAVA')->where('level_id', 3)->first()->id],
                        ]
                    ],
                ]
            ],
            [
                'name'=>'NORTH REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'children' => [
                    [
                        'name' => 'BENUE',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'NORTH REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Barndake', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BENUE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Basheo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BENUE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bibemi', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BENUE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Dembo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BENUE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Garoua Urbain', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BENUE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Garoua Rural', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BENUE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Gashiga', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BENUE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Lagdo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BENUE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngong', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BENUE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Pitoa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BENUE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Touroua', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BENUE')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MAYO LOUTI',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'NORTH REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Figuil', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO LOUTI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Guider', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO LOUTI')->where('level_id', 3)->first()->id],
                            ['name'=> 'mayo-Oulo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO LOUTI')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'FARO',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'NORTH REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Beka', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FARO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Poli urbain', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FARO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Poli rural', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FARO')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MAYO REY',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'NORTH REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Mandingring', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO REY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Tcholliré', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO REY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Touboro', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO REY')->where('level_id', 3)->first()->id],
                            ['name'=> 'Rey Bouba', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MAYO REY')->where('level_id', 3)->first()->id],
                        ]
                    ],
                ]
            ],
            [
                'name'=>'NORTH WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'children' => [
                    [
                        'name' => 'BUI',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Elak-Oku', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BUI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Jakiri', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BUI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kumbo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BUI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mbiami', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BUI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nkum', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BUI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Noni', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BUI')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'BOYO',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Belo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BOYO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bum', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BOYO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Fundong', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BOYO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Njinikom', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BOYO')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'DONGA MATUM',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Ako', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DONGA MATUM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Misaje', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DONGA MATUM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ndu', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DONGA MATUM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nkambe', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DONGA MATUM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nwa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DONGA MATUM')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MENCHUM',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Benakuma', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MENCHUM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Furawa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MENCHUM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Wum', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MENCHUM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Zhoa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MENCHUM')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MEZAM',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Bafut', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEZAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bali', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEZAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bamenda I', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEZAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bamenda II', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEZAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bamenda III', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEZAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Santa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEZAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Tubah', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEZAM')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MOMO',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Andek', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOMO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Batibo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOMO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mbengwi', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOMO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Njikwa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOMO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Widikum', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MOMO')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'NGOKETUNJA',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'NORTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Balikumbat', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NGOKETUNJA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Babessi', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NGOKETUNJA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ndop', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NGOKETUNJA')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    
                ]
            ],
            [
                'name'=>'SOUTH WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'children' => [
                    [
                        'name' => 'FAKO',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Buea', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FAKO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Tiko', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FAKO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Idinau', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FAKO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Muyuka', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FAKO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Limbe I', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FAKO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Limbe II', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FAKO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Limbe III', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'FAKO')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MEME',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Konye', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kumba I', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kumba II', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kumba III', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEME')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mbonge', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MEME')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'NDIAN',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Bamousso', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDIAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mundemba', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDIAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ekondo-Titi', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDIAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Idabato', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDIAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Isanguele', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDIAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kombo-Etindi', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDIAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kombo-Abedimo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDIAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Dikome Balue', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDIAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Toko', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDIAN')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'KUPE MANENGUBA',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Bangem', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'KUPE MANENGUBA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Tombel', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'KUPE MANENGUBA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nguti', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'KUPE MANENGUBA')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MANYU',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Mamfe', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MANYU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Akwaya', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MANYU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Eyumojock', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MANYU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Upper bayang', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MANYU')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'LEBIALEM',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'SOUTH WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Alou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEBIALEM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Fontem', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEBIALEM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Wabane', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'LEBIALEM')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    
                ]
            ],
            [
                'name'=>'SOUTH REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'children' => [
                    [
                        'name' => 'MVILA',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'SOUTH REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Biwong-Bane', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MVILA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Biwong-Bulu', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MVILA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ebolowa Urban', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MVILA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ebolowa Rural', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MVILA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Efoulan', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MVILA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mengong', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MVILA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mvangane', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MVILA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ngoulemakong', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MVILA')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                    [
                        'name' => 'DJA ET LOBO',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'SOUTH REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Sangmalima', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DJA ET LOBO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Djoum', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DJA ET LOBO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bengbis', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DJA ET LOBO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Zoétele', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DJA ET LOBO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Meyomessala', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DJA ET LOBO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Oveng', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DJA ET LOBO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mintom', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DJA ET LOBO')->where('level_id', 3)->first()->id],
                            ['name'=> 'Meyomessi', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'DJA ET LOBO')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                    [
                        'name' => 'OCEAN',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'SOUTH REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Akom II', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'OCEAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bibindi', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'OCEAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Campo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'OCEAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'kribi urban', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'OCEAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kribi Rural', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'OCEAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Lokundje', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'OCEAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Lolodorf', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'OCEAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mvengue', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'OCEAN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Niete', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'OCEAN')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                    [
                        'name' => 'NTEM',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'SOUTH REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Ambam', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NTEM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kye-Ossi', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NTEM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Olamze', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NTEM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Ma\'an', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NTEM')->where('level_id', 3)->first()->id],
                        ]
                    ],
                ]
            ],
            [
                'name'=>'WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'children' => [
                    [
                        'name' => 'BAMBOUTOUS',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Babadjou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BAMBOUTOUS')->where('level_id', 3)->first()->id],
                            ['name'=> 'Batcham', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BAMBOUTOUS')->where('level_id', 3)->first()->id],
                            ['name'=> 'Galim', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BAMBOUTOUS')->where('level_id', 3)->first()->id],
                            ['name'=> 'Mbouda', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'BAMBOUTOUS')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                    [
                        'name' => 'MIFI',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Bafoussam I', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MIFI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bafoussam II', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MIFI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bafoussam III', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MIFI')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                    [
                        'name' => 'HAUT-NKAM',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Bafang', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT-NKAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bakou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT-NKAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bana', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT-NKAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bandja', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT-NKAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Banwa', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT-NKAM')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kekem', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT-NKAM')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                    [
                        'name' => 'HAUT PLATEAU',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Baham', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT PLATEAU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bamendjou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT PLATEAU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bangou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT PLATEAU')->where('level_id', 3)->first()->id],
                            ['name'=> 'Batié', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'HAUT PLATEAU')->where('level_id', 3)->first()->id],
                        ]
                    ],
                    [
                        'name' => 'MENOUA',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Dschang Urban', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MENOUA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Dschang Rural', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MENOUA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Fokoue', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MENOUA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Fongo-Tongo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MENOUA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Nkong-Zem', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MENOUA')->where('level_id', 3)->first()->id],
                            ['name'=> 'Penka-Michel', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'MENOUA')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                    [
                        'name' => 'KOUNG-KHI',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Bayangam', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'KOUNG-KHI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Banjoun', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'KOUNG-KHI')->where('level_id', 3)->first()->id],
                            ['name'=> 'Demding', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'KOUNG-KHI')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                    [
                        'name' => 'NOUN',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Foumban', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NOUN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Foumbot', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NOUN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Kouptamo', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NOUN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Magba', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NOUN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Malantouen', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NOUN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Massangam', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NOUN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Njimom', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NOUN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bangourain', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NOUN')->where('level_id', 3)->first()->id],
                            ['name'=> 'Koutaba', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NOUN')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                    [
                        'name' => 'NDE',
                        'level_id' => 3,
                        'parent_id' => Zone::query()->where('name', 'WEST REGION')->where('level_id', 2)->id,
                        'children' => [
                            ['name'=> 'Bassamba', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bangante', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Tonga', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDE')->where('level_id', 3)->first()->id],
                            ['name'=> 'Bazou', 'level_id'  => 4, 'parent_id'=> Zone::query()->where('name', 'NDE')->where('level_id', 3)->first()->id],
                            
                        ]
                    ],
                ]
            ]
        ];

    }
}
