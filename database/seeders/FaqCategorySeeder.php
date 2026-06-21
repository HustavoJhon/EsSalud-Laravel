<?php

namespace Database\Seeders;

use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Afiliación', 'description' => 'Preguntas sobre afiliación al seguro', 'icon' => 'user-plus', 'sort_order' => 1],
            ['name' => 'Citas Médicas', 'description' => 'Preguntas sobre citas y consultas', 'icon' => 'calendar', 'sort_order' => 2],
            ['name' => 'Reembolsos', 'description' => 'Preguntas sobre reembolsos de gastos', 'icon' => 'dollar-sign', 'sort_order' => 3],
            ['name' => 'Certificados', 'description' => 'Preguntas sobre certificados médicos', 'icon' => 'file-text', 'sort_order' => 4],
            ['name' => 'Trámites Generales', 'description' => 'Preguntas generales sobre trámites', 'icon' => 'info', 'sort_order' => 5],
        ];

        foreach ($categories as $cat) {
            FaqCategory::create($cat);
        }
    }
}
