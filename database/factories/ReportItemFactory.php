<?php

namespace Database\Factories;

use App\Models\ReportItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportItemFactory extends Factory
{
    protected $model = ReportItem::class;

    public function definition()
    {
        return [
            'report_id' => \App\Models\Report::factory(),
            'metric_type_id' => $this->faker->randomNumber(),
            'value' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
