<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\MetricType;
use App\Models\ReportItem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ReportItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reports = Report::all();
        $metricTypes = MetricType::all();

        foreach ($reports as $report) {
            foreach ($metricTypes as $metricType) {
                ReportItem::create([
                    'report_id' => $report->id,
                    'sub_metric_type_id' => $metricType->id,
                    'description' => 'description',
                    'value' => rand(1, 100), // Just an example value, you may adjust it as needed
                ]);
            }
        }
    }
}
