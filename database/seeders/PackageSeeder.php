<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periods = [
            'Month' => 1,
            'Quarter' => 3,
            'Half' => 6,
            'Annual' => 12,
        ];

        $basePackages = [
            [
                'name_fr' => 'Package National',
                'name_en' => 'National Package',
                'level' => 'National',
                'base_price' => 50000, // Base price per month
                'description_fr' => json_encode([
                    'Annonces illimitées pour le territoire national',
                    'Messagerie de masse illimitée',
                    'Collaboration des parties prenantes',
                    'Assistance IA (à venir)',
                    'Services de support',
                    'Demande de simulations de risques pour les emplacements',
                    'Tableau de bord de données personnalisable'
                ]),
                'description_en' => json_encode([
                    'Unlimited ads for national territory', 
                    'Unlimited mass messaging', 
                    'Stakeholder collaboration', 
                    'AI assistance (upcoming)', 
                    'Support services', 
                    'Request hazard simulations for locations', 
                    'Customizable data dashboard'
                ]),
                'is_active' => true,
            ],
            [
                'name_fr' => 'Package Regional',
                'name_en' => 'Regional Package',
                'level' => 'Regional',
                'base_price' => 35000, // Base price per month
                'description_fr' => json_encode([
                    'Annonces illimitées (régionales)',
                    'Messagerie de masse illimitée',
                    'Assistant IA (à venir)',
                    'Services de support',
                    'Demande de simulations de risques pour les emplacements',
                    'Collaboration des parties prenantes'
                ]),
                'description_en' => json_encode([
                    'Unlimited ads (regional)', 
                    'Unlimited mass messaging', 
                    'AI assistant (upcoming)', 
                    'Support services', 
                    'Request hazard simulations for locations', 
                    'Stakeholder collaboration'
                ]),
                'is_active' => true,
            ],
            [
                'name_fr' => 'Package Divisionnel',
                'name_en' => 'Divisional Package',
                'level' => 'Divisional',
                'base_price' => 25000, // Base price per month
                'description_fr' => json_encode([
                    'Annonces illimitées pour la division enregistrée',
                    'Messagerie de masse (Divisionnelle)',
                    'Services de support',
                    'Simulations de risques pour les emplacements (divisionnels)'
                ]),
                'description_en' => json_encode([
                    'Unlimited ads for registered division',
                    'Mass messaging (Divisional)', 
                    'Support services', 
                    'Hazard simulations for locations (divisional)'
                ]),
                'is_active' => false,
            ],
            [
                'name_fr' => 'Package Subdivisionnel',
                'name_en' => 'Subdivisional Package',
                'level' => 'Subdivisional',
                'base_price' => 15000, // Base price per month
                'description_fr' => json_encode([
                    'Messages localisés en masse',
                    'Annonces illimitées pour la localisation enregistrée',
                    'Simulations de risques de danger',
                    'Services de support pour la planification des risques'
                ]),
                'description_en' => json_encode([
                    'Location-based mass messaging',
                    'Unlimited ads for registered location', 
                    'Hazard risks simulations', 
                    'Support services for hazard planning'
                ]),
                'is_active' => true,
            ]
        ];

        $packages = [];

        foreach ($basePackages as $basePackage) {
            foreach ($periods as $period => $months) {
                $packages[] = array_merge($basePackage, [
                    'price' => $basePackage['base_price'] * $months, // Adjust price based on period
                    'periodicity' => $period,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        DB::table('packages')->insert($packages);
    }
}