<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;

class ZoneUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crée la zone principale pour le Cameroun
        $cameroun = Zone::query()->create([
            'name' => 'Cameroun',
            'level_id' => 1,
            'latitude' => 7.3697, // Latitude du centre du Cameroun
            'longitude' => 12.3547, // Longitude du centre du Cameroun
            'geojson' => null, // Vous pouvez définir un GeoJSON général pour le Cameroun si nécessaire
        ]);

        // Définition des régions avec latitude, longitude et GeoJSON
        $regions = [
            [
                'name' => 'ADAMAWA',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'latitude' => 7.2656, // Remplacer par la latitude correcte de la région
                'longitude' => 13.5864, // Remplacer par la longitude correcte de la région
                'geojson' => '' // Remplacer par le GeoJSON correct
            ],
            [
                'name' => 'CENTER',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'latitude' => 4.4857,
                'longitude' => 11.7468,
                'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[...]}}'
            ],
            [
                'name' => 'EAST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'latitude' => 4.5671,
                'longitude' => 14.4357,
                'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[...]}}'
            ],
            [
                'name' => 'LITTORAL',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'latitude' => 4.0483,
                'longitude' => 9.7043,
                'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[...]}}'
            ],
            [
                'name' => 'FAR NORTH',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'latitude' => 11.2076,
                'longitude' => 14.7156,
                'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[...]}}'
            ],
            [
                'name' => 'NORTH REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'latitude' => 8.7711,
                'longitude' => 13.2924,
                'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[...]}}'
            ],
            [
                'name' => 'NORTH WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'latitude' => 6.3339,
                'longitude' => 10.6253,
                'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[...]}}'
            ],
            [
                'name' => 'SOUTH WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'latitude' => 4.3833,
                'longitude' => 9.1706,
                'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[...]}}'
            ],
            [
                'name' => 'SOUTH REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'latitude' => 2.8497,
                'longitude' => 11.5186,
                'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[...]}}'
            ],
            [
                'name' => 'WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
                'latitude' => 5.5403,
                'longitude' => 10.2417,
                'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[...]}}'
            ]
        ];

        // Mise à jour ou création des régions
        foreach ($regions as $region) {
            Zone::query()->updateOrCreate(
                ['name' => $region['name']], // Condition de correspondance
                $region // Données à insérer ou mettre à jour
            );
        }
    }
}
