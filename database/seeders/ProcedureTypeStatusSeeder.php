<?php

namespace Database\Seeders;

use App\Models\ProcedureStatus;
use App\Models\ProcedureType;
use Illuminate\Database\Seeder;

class ProcedureTypeStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'BORRADOR', 'name' => 'Borrador', 'is_terminal' => false],
            ['code' => 'RADICADO', 'name' => 'Radicado', 'is_terminal' => false],
            ['code' => 'EVALUACION', 'name' => 'En Evaluación', 'is_terminal' => false],
            ['code' => 'SUBSANACION', 'name' => 'Subsanación', 'is_terminal' => false],
            ['code' => 'APROBADO', 'name' => 'Aprobado', 'is_terminal' => true],
            ['code' => 'RECHAZADO', 'name' => 'Rechazado', 'is_terminal' => true],
            ['code' => 'CANCELADO', 'name' => 'Cancelado', 'is_terminal' => true],
        ];

        foreach ($statuses as $status) {
            ProcedureStatus::create($status);
        }

        $types = [
            [
                'code' => 'CITAS_MED',
                'name' => 'Citas Médicas',
                'description' => 'Solicitud de citas para consultas médicas generales y especializadas.',
                'requirements' => ['dni_vigente', 'historial_medico', 'seguro_vigente'],
                'max_days_resolution' => 10,
            ],
            [
                'code' => 'CERT_MED',
                'name' => 'Certificados Médicos',
                'description' => 'Emisión de certificados médicos, de salud y de discapacidad.',
                'requirements' => ['dni_vigente', 'solicitud_formal', 'informe_medico'],
                'max_days_resolution' => 7,
            ],
            [
                'code' => 'REEMBOLSO',
                'name' => 'Reembolso de Gastos',
                'description' => 'Solicitud de reembolso por gastos médicos realizados.',
                'requirements' => ['dni_vigente', 'comprobantes_pago', 'informe_medico', 'formulario_reembolso'],
                'max_days_resolution' => 30,
            ],
            [
                'code' => 'AFILIACION',
                'name' => 'Afiliación',
                'description' => 'Trámite de afiliación al seguro social de salud.',
                'requirements' => ['dni_vigente', 'certificado_trabajo', 'ficha_inscripcion'],
                'max_days_resolution' => 15,
            ],
            [
                'code' => 'LICENCIA',
                'name' => 'Licencia por Enfermedad',
                'description' => 'Solicitud de licencia laboral por enfermedad o accidente.',
                'requirements' => ['dni_vigente', 'certificado_medico', 'formulario_licencia'],
                'max_days_resolution' => 5,
            ],
            [
                'code' => 'MATERNIDAD',
                'name' => 'Prestaciones por Maternidad',
                'description' => 'Solicitud de subsidio por maternidad y lactancia.',
                'requirements' => ['dni_vigente', 'certificado_nacimiento', 'control_prenatal', 'formulario_subsidio'],
                'max_days_resolution' => 20,
            ],
        ];

        foreach ($types as $type) {
            ProcedureType::create($type);
        }
    }
}
