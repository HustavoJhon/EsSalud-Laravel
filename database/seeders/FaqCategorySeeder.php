<?php

namespace Database\Seeders;

use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Afiliaciones', 'description' => 'Preguntas sobre afiliación de asegurados, cónyuges e hijos', 'icon' => 'people', 'sort_order' => 1],
            ['name' => 'Subsidio por Maternidad', 'description' => 'Información sobre subsidio por maternidad y descanso pre/postnatal', 'icon' => 'pregnant_woman', 'sort_order' => 2],
            ['name' => 'Subsidio por Lactancia', 'description' => 'Información sobre subsidio por lactancia y asignación familiar', 'icon' => 'child_care', 'sort_order' => 3],
            ['name' => 'Subsidio por Sepelio', 'description' => 'Información sobre subsidio por sepelio y gastos funerarios', 'icon' => 'church', 'sort_order' => 4],
            ['name' => 'Prestaciones Económicas', 'description' => 'Consultas sobre pagos, montos y cobros de subsidios', 'icon' => 'paid', 'sort_order' => 5],
            ['name' => 'Consultas Médicas', 'description' => 'Preguntas sobre citas, recetas, tratamientos y atención médica', 'icon' => 'local_hospital', 'sort_order' => 6],
            ['name' => 'Trámites y Documentos', 'description' => 'Consultas sobre procesos de trámites, requisitos y plazos', 'icon' => 'description', 'sort_order' => 7],
            ['name' => 'Cuenta y Perfil', 'description' => 'Gestión de cuenta, contraseña, datos personales y acceso', 'icon' => 'account_circle', 'sort_order' => 8],
            ['name' => 'Consultas Generales', 'description' => 'Otras preguntas frecuentes sobre EsSalud', 'icon' => 'help', 'sort_order' => 9],
        ];

        foreach ($categories as $cat) {
            FaqCategory::create($cat);
        }
    }
}
