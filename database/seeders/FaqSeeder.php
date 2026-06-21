<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $afiliacion = FaqCategory::where('name', 'Afiliación')->first();
        $citas = FaqCategory::where('name', 'Citas Médicas')->first();
        $reembolsos = FaqCategory::where('name', 'Reembolsos')->first();
        $certificados = FaqCategory::where('name', 'Certificados')->first();
        $generales = FaqCategory::where('name', 'Trámites Generales')->first();

        $faqs = [
            [
                'category_id' => $afiliacion->id,
                'question' => '¿Cómo me afilio a EsSalud?',
                'answer' => 'Para afiliarte a EsSalud debes presentar tu DNI vigente, certificado de trabajo o contrato laboral, y llenar la ficha de inscripción. Puedes iniciar el trámite en línea desde la sección de Procedimientos.',
                'keywords' => ['afiliar', 'inscribir', 'registro', 'nuevo asegurado'],
            ],
            [
                'category_id' => $afiliacion->id,
                'question' => '¿Qué documentos necesito para afiliarme?',
                'answer' => 'Necesitas: DNI vigente, certificado de trabajo emitido por tu empleador, una foto tamaño carnet, y el formulario de inscripción FO-001 debidamente llenado.',
                'keywords' => ['documentos', 'requisitos', 'papeles', 'inscripcion'],
            ],
            [
                'category_id' => $citas->id,
                'question' => '¿Cómo solicito una cita médica?',
                'answer' => 'Puedes solicitar una cita médica ingresando a la sección de Trámites, seleccionando "Citas Médicas", eligiendo la especialidad y fecha disponible, y confirmando la solicitud.',
                'keywords' => ['cita', 'consulta', 'medico', 'especialidad', 'agendar'],
            ],
            [
                'category_id' => $citas->id,
                'question' => '¿Cuánto demora la asignación de una cita?',
                'answer' => 'El tiempo de resolución para una cita médica es de máximo 10 días hábiles. En casos de emergencia, puedes acudir directamente al centro de salud más cercano.',
                'keywords' => ['demora', 'tiempo', 'espera', 'plazo'],
            ],
            [
                'category_id' => $reembolsos->id,
                'question' => '¿Cómo solicito un reembolso de gastos médicos?',
                'answer' => 'Debes presentar los comprobantes de pago originales, informe médico que justifique la atención, formulario de reembolso FO-003, y tu DNI vigente. El plazo máximo de resolución es de 30 días.',
                'keywords' => ['reembolso', 'devolucion', 'dinero', 'gastos'],
            ],
            [
                'category_id' => $certificados->id,
                'question' => '¿Cómo obtengo un certificado médico?',
                'answer' => 'Puedes solicitar un certificado médico a través del trámite "Certificados Médicos" en la plataforma. Debes adjuntar tu DNI, la solicitud formal y el informe médico correspondiente.',
                'keywords' => ['certificado', 'constancia', 'medico', 'salud'],
            ],
            [
                'category_id' => $generales->id,
                'question' => '¿Cómo puedo hacer seguimiento de mi trámite?',
                'answer' => 'Ingresa a "Mis Trámites" en el menú lateral. Allí verás todos tus trámites con su estado actual, historial de cambios y comentarios de los operadores.',
                'keywords' => ['seguimiento', 'estado', 'tracking', 'consulta'],
            ],
            [
                'category_id' => $generales->id,
                'question' => '¿Qué hago si mi trámite fue rechazado?',
                'answer' => 'Si tu trámite fue rechazado, recibirás una notificación con los motivos. Puedes iniciar un nuevo trámite corrigiendo las observaciones indicadas por el evaluador.',
                'keywords' => ['rechazo', 'denegado', 'apelar', 'rechazado'],
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
