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

        $systemMessage .= "\n\nUsa la conversación anterior para entender el contexto de las preguntas del usuario.";

        if (!empty($context)) {
            $contextStr = implode("\n\n", array_map(fn($c) => $c['content'] ?? $c, $context));
            $systemMessage .= "\n\nInformación de la base de conocimiento de EsSalud:\n" . $contextStr;
            $systemMessage .= "\n\nResponde basándote en esta información cuando sea relevante. Si la información no corresponde a la pregunta, indícalo amablemente y sugiere consultar Trámites o llamar al 411-8000. NO inventes montos, requisitos ni procedimientos.";
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
        srand($seed);
        $embedding = [];
        for ($i = 0; $i < 1536; $i++) {
            $embedding[] = rand() / getrandmax() * 2 - 1;
        }
        return $embedding;
    }

    protected function mockChatResponse(array $messages): string
    {
        $lastMessage = end($messages);
        $question = strtolower($lastMessage['content'] ?? '');

        // Check previous bot message for context (follow-up questions)
        $prevBotMessage = '';
        if (count($messages) >= 2) {
            for ($i = count($messages) - 2; $i >= 0; $i--) {
                if (($messages[$i]['role'] ?? '') === 'assistant') {
                    $prevBotMessage = strtolower($messages[$i]['content'] ?? '');
                    break;
                }
            }
        }

        $patterns = [
            '/\b(afilia|afiliar|afiliacion).*conyuge|conyuge.*(afilia|afiliar|afiliacion)\b/i' => 'Para afiliar a tu cónyuge a EsSalud como derechohabiente necesitas: (1) DNI del titular y del cónyuge, (2) Partida de matrimonio vigente (original o copia certificada), (3) Declaración jurada de no contar con otro seguro. Puedes iniciar el trámite en la sección Trámites > Afiliación de Derechohabientes. El cónyuge tendrá cobertura en EsSalud sin costo adicional siempre que estés en estado activo como asegurado.',
            '/\b(afilia|afiliar|afiliacion)\b/i' => 'Para afiliarte a EsSalud necesitas presentar tu DNI vigente y el certificado de trabajo emitido por tu empleador. Puedes iniciar el trámite en línea desde la sección de Procedimientos seleccionando "Afiliación". Si deseas afiliar a un derechohabiente (cónyuge, hijos), necesitas también los DNI de ellos y el documento que acredite el vínculo familiar (partida de matrimonio, acta de nacimiento).',
            '/\b(cobro|como lo cobro|como cobro|pago|cobrarlo|cobrar)\b.*\b(lactancia|lactante|subsidio)\b|\b(lactancia|lactante|subsidio)\b.*\b(cobro|pago|cobrar)\b/i' => 'El subsidio por lactancia es de S/ 820.00 por cada hijo nacido vivo. Lo cobras de dos maneras: si diste a luz en un hospital de EsSalud, el pago es automático ("Cero Trámite") y te depositan sin que hagas nada. Si fue en clínica particular, debes presentar el Formulario 1040 en EsSalud o por la plataforma VIVA, y luego te pagan.',
            '/\b(monto|cuanto es|cuanto pagan|cuanto dan|cantidad|cual es el monto|de cuanto)\b.*\b(lactancia|lactante|subsidio)\b|\b(lactancia|lactante|subsidio)\b.*\b(monto|cuanto|pagar|cantidad|importe)\b/i' => 'El monto del subsidio por lactancia es de S/ 820.00 por cada hijo nacido vivo. Si tienes mellizos o gemelos, el monto se duplica: S/ 1,640.00 en total. El pago se realiza automáticamente si diste a luz en un hospital de EsSalud ("Cero Trámite"), o previa presentación del Formulario 1040 si fue en clínica particular.',
            '/\b(por cada hijo|cada hijo|por hijo|mellizos|gemelos|dos hijos)\b/i' => 'Si tienes mellizos, gemelos o más de un hijo, el subsidio por lactancia se duplica: S/ 1,640.00 en total (S/ 820.00 por cada hijo). Debes presentar las partidas de nacimiento de cada menor para el trámite.',
            '/\b(requisito|documento|necesito|necesito presentar|que necesito)\b.*\b(lactancia|lactante)\b|\b(lactancia|lactante)\b.*\b(requisito|documento|necesito)\b/i' => 'Para cobrar el subsidio por lactancia (S/ 820.00 por hijo) necesitas: (1) certificado de nacimiento del menor, (2) DNI de la madre y del menor, (3) solicitud en Trámites > Subsidio por Lactancia. Si diste a luz en un hospital de EsSalud, el pago puede ser automático ("Cero Trámite"). Si fue en clínica particular, presenta el Formulario 1040.',
            '/\b(lactancia|lactante|amamantar)\b/i' => 'El subsidio por lactancia es de S/ 820.00 por cada hijo nacido vivo. Se paga por cada hijo: si son mellizos o gemelos, son S/ 1,640.00. El pago es automático si diste a luz en un hospital de EsSalud ("Cero Trámite"). Si fue en clínica particular, debes presentar el Formulario 1040 en EsSalud o por VIVA.',
            '/\b(maternidad|embarazo|gestante|prenatal|postnatal)\b/i' => 'El subsidio por maternidad es un beneficio económico para madres aseguradas. El descanso prenatal es de 45 días y el postnatal de 45 días (90 en total), recibiendo el 100% de tu remuneración. Debes presentar el certificado médico de embarazo, DNI y solicitar el trámite en Procedimientos.',
            '/\b(incapacidad|itt|enfermedad|accidente)\b/i' => 'El subsidio por incapacidad temporal (ITT) se otorga cuando no puedes trabajar por enfermedad o accidente. Debes presentar el certificado médico que acredite la incapacidad, DNI, y solicitarlo en Trámites > Subsidio por Incapacidad Temporal. Recibirás un porcentaje de tu remuneración mientras dure la incapacidad, según evaluación médica.',
            '/\b(cita|consultorio|medico|medica|consulta|atencion)\b/i' => 'Puedes solicitar una cita médica ingresando a Trámites > Citas Médicas, donde seleccionarás la especialidad y fecha disponible.',
            '/\b(reembolso|devolucion)\b/i' => 'Para solicitar un reembolso debes presentar comprobantes de pago originales, informe médico y formulario FO-003. El plazo máximo es de 30 días hábiles.',
            '/\b(certificado)\b/i' => 'Los certificados médicos se solicitan por el trámite "Certificados Médicos". Debes adjuntar DNI, solicitud formal e informe médico.',
            '/\b(subsanacion|subsanar)\b/i' => 'Cuando un trámite requiere subsanación, significa que debes corregir o completar información. Recibirás una notificación con las observaciones detalladas.',
        ];

        foreach ($patterns as $regex => $resp) {
            if (preg_match($regex, $question)) {
                return $resp;
            }
        }

        return 'Gracias por tu consulta. Para ayudarte mejor, por favor especifica qué tipo de trámite o servicio de EsSalud necesitas. Puedo asistirte con afiliación, citas médicas, reembolsos, certificados, licencias y prestaciones por maternidad.';
    }
}
