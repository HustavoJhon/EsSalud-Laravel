<?php

namespace App\Services;

class OpenAIService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY', ''));
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
    }

    public function embedding(string $text): array
    {
        if (empty($this->apiKey)) {
            return $this->mockEmbedding();
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->post("{$this->baseUrl}/embeddings", [
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'text-embedding-3-small',
                'input' => $text,
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        return $body['data'][0]['embedding'];
    }

    public function chatCompletion(array $messages, array $context = []): string
    {
        if (empty($this->apiKey)) {
            return $this->mockChatResponse($messages);
        }

        $client = new \GuzzleHttp\Client();

        $systemMessage = "Eres un asistente virtual de EsSalud. Ayudas a los usuarios con preguntas sobre trámites, afiliación, subsidios, citas médicas, certificados, reembolsos y otros servicios. Sé preciso, profesional y amable. Responde siempre en español.";

        if (!empty($context)) {
            $contextStr = implode("\n\n", array_map(fn($c) => $c['content'] ?? $c, $context));
            $systemMessage .= "\n\nA continuación información de la base de conocimiento de EsSalud que puedes usar para responder:\n" . $contextStr;
            $systemMessage .= "\n\nImportante: Responde SOLO con la información proporcionada arriba. Si la información no responde exactamente a la pregunta del usuario, indícale amablemente que no tienes esa información y sugiérele consultar en la sección de Trámites o llamar a EsSalud al 411-8000. NO inventes montos, requisitos ni procedimientos.";
        } else {
            $systemMessage .= "\n\nNo tengo información específica sobre esta consulta. Responde amablemente indicando que no tienes información suficiente y sugiere al usuario consultar la sección de FAQ, Trámites, o llamar a EsSalud al 411-8000.";
        }

        array_unshift($messages, ['role' => 'system', 'content' => $systemMessage]);

        $response = $client->post("{$this->baseUrl}/chat/completions", [
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ],
        ]);

        $body = json_decode($response->getBody(), true);
        return $body['choices'][0]['message']['content'];
    }

    protected function mockEmbedding(): array
    {
        $hash = md5('embedding');
        $seed = hexdec(substr($hash, 0, 8));
        mt_srand($seed);
        $embedding = [];
        for ($i = 0; $i < 1536; $i++) {
            $embedding[] = mt_rand() / mt_getrandmax() * 2 - 1;
        }
        return $embedding;
    }

    protected function mockChatResponse(array $messages): string
    {
        $lastMessage = end($messages);
        $question = strtolower($lastMessage['content'] ?? '');

        $responses = [
            'afiliacion' => 'Para afiliarte a EsSalud necesitas presentar tu DNI vigente y el certificado de trabajo emitido por tu empleador. Puedes iniciar el trámite en línea desde la sección de Procedimientos seleccionando "Afiliación".',
            'lactancia' => 'El subsidio por lactancia es un beneficio económico que se otorga a la madre asegurada por el periodo de descanso por lactancia. El monto equivale al 100% de tu remuneración mensual durante 90 días. Para cobrarlo, debes presentar el certificado de nacimiento del menor, DNI de la madre y del menor, y solicitar el trámite en la sección Trámites > Subsidio por Lactancia.',
            'maternidad' => 'El subsidio por maternidad es un beneficio económico para madres aseguradas. El descanso prenatal es de 45 días y el postnatal de 45 días (90 en total), recibiendo el 100% de tu remuneración. Debes presentar el certificado médico de embarazo, DNI y solicitar el trámite en Procedimientos.',
            'incapacidad' => 'El subsidio por incapacidad temporal (ITT) se otorga cuando no puedes trabajar por enfermedad o accidente. Debes presentar el certificado médico que acredite la incapacidad, DNI, y solicitarlo en Trámites > Subsidio por Incapacidad Temporal. Recibirás un porcentaje de tu remuneración mientras dure la incapacidad, según evaluación médica.',
            'cita' => 'Puedes solicitar una cita médica ingresando a Trámites > Citas Médicas, donde seleccionarás la especialidad y fecha disponible.',
            'reembolso' => 'Para solicitar un reembolso debes presentar comprobantes de pago originales, informe médico y formulario FO-003. El plazo máximo es de 30 días hábiles.',
            'certificado' => 'Los certificados médicos se solicitan por el trámite "Certificados Médicos". Debes adjuntar DNI, solicitud formal e informe médico.',
            'subsanacion' => 'Cuando un trámite requiere subsanación, significa que debes corregir o completar información. Recibirás una notificación con las observaciones detalladas.',
        ];

        foreach ($responses as $key => $resp) {
            if (strpos($question, $key) !== false) {
                return $resp;
            }
        }

        return 'Gracias por tu consulta. Para ayudarte mejor, por favor especifica qué tipo de trámite o servicio de EsSalud necesitas. Puedo asistirte con afiliación, citas médicas, reembolsos, certificados, licencias y prestaciones por maternidad.';
    }
}
