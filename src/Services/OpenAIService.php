<?php
// src/Service/OpenAIService.php
namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\FutTCronicas;
use App\Entity\FutUEquiposRivales;

class OpenAIService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    /**
     * Genera un texto expandido usando GPT-4.
     *
     * @param string $inputText El texto original que debe ser expandido.
     * @return string|null El texto expandido o null en caso de error.
     */
    public function generateCronicaPartido(FutTCronicas $cronica, $partido): ?string
    {
        // Extrae los datos necesarios de la crÃ³nica
        $mvp = $cronica->getMvp();
        $mvpSemana = $cronica->getMvpSemana();
        $goleadores = [
            ['nombre' => $cronica->getGoleador1(), 'goles' => $cronica->getNumeroGoles1()],
            ['nombre' => $cronica->getGoleador2(), 'goles' => $cronica->getNumeroGoles2()],
            ['nombre' => $cronica->getGoleador3(), 'goles' => $cronica->getNumeroGoles3()],
            ['nombre' => $cronica->getGoleador4(), 'goles' => $cronica->getNumeroGoles4()],
            ['nombre' => $cronica->getGoleador5(), 'goles' => $cronica->getNumeroGoles5()],
            ['nombre' => $cronica->getGoleador6(), 'goles' => $cronica->getNumeroGoles6()],
            ['nombre' => $cronica->getGoleador7(), 'goles' => $cronica->getNumeroGoles7()],
        ];

        // Filtra los goleadores nulos
        $goleadoresFiltrados = array_filter($goleadores, function ($goleador) {
            return !is_null($goleador['nombre']);
        });

        // Construye el texto con los datos extraÃ­dos
        $goleadoresTexto = implode(', ', array_map(function ($g) {
            return "{$g['nombre']} ({$g['goles']})";
        }, $goleadoresFiltrados));

        // Extrae los datos del partido
        $nombreEquipo = $partido->getIdEquipo()->getEquipo();
        $nombreRival = $partido->getIdRival()->getRival();
        $competicion = $partido->getCompeticion();
        $grupo = $partido->getGrupo();
        $jornada = $partido->getJornada();
        $resultadoLocal = $partido->getResultadoLocal();
        $resultadoVisitante = $partido->getResultadoVisitante();
        $local = $partido->getLocal() == 'si';
        
        $resultadoPartido = $local
        ? "$resultadoLocal-$resultadoVisitante $nombreEquipo"
        : "$resultadoVisitante-$resultadoLocal $nombreRival";
        
        $textoDatosPartido = "PARTIDO: ".($local ? "$nombreEquipo $resultadoLocal-$resultadoVisitante $nombreRival" : "$nombreRival $resultadoVisitante-$resultadoLocal $nombreEquipo") . "\n"
             . "COMPETICION: $competicion $grupo JORNADA $jornada";

        $textoCompleto =    $textoDatosPartido."\n".
                            "TEXTO DE LA CRONICA: {$cronica->getTextoCronica()}\n" .
                            "GOLEADORES: $goleadoresTexto\n" .
                            "MVP DEL PARTIDO: $mvp\n" .
                            "MVP DE LA SEMANA: $mvpSemana";
        $systemMessage = ""
                . "TAREA: Redacta crónicas profesionales de partidos de fútbol a partir de textos facilitados por el entrenador del equipo "
                . "CONTEXTO: Eres la persona encargada de las Redes Sociales y WEB de la Agrupación Deportiva Colmenar Viejo (A.D. Colmenar Viejo) o ADCV. Debes facilitar crónicas profesionales a partir de un texto facilitado por el entrenador. En un grupo de Whatsapp, los entrenadores del equipo van introduciendo los datos de sus partidos (Partido, resultado, crónica del partido escrita por ellos, goleadores y mejores jugadores del partido). De este texto, quiero obtener una crónica profesional. "
                . "EJEMPLO: De lo que te pase, este tiene que ser el resultado final."
                . "- Equipo ADCV resultado RIVAL (Si te pasan primero el rival, es porque somos visitantes, sería al revés)"
                . "- un titulo sobre el contenido de la cronica que generas"
                . "- La cronica del partido que generas. La crónica deberá de contener todos los datos que te he pasado, incluidos si los hay, mvps, mejores y goleadores"
                . "PERSONA: Eres un Gestor de Contenidos y Redes Sociales con mas de 10 años de experiencia "
                . "FORMATO: El formato me lo darás siempre tipo texto. "
                . "TONO: Usa términos futbolísticos en una escala de 0 a 10 un 8. La crónica tiene que estar bien escrita, muy importante corregir las faltas de ortografía y que estén todas las tildes, Haz referencia a lo que ha escrito el entrenador pero con mas estilo, respetando siempre al rival y al arbitro. "
                . "DETALLES A TENER EN CUENTA: "
                . "- Cuando en un jugador pongan el puesto DC, es Delantero Centro, no Defensa Central. "
                . "- Cuando te ponga la crónica del entrenador, es para que me des tu la crónica con las indicaciones de este GPT. "
                . "- Cuando escriban ADCV, siempre deberás referirte al equipo como 'el Colmenar'."
                . "- ";

        $userMessage = "Aqui tienes los detalles del partido. Usa las instrucciones proporcionadas para generar una cronica de alta calidad. $textoCompleto";

        $url = 'https://api.openai.com/v1/chat/completions';
        $payload = [
            //'model' => 'gpt-3.5-turbo',
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemMessage
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage
                ]
            ],
            'max_tokens' => 4096, //Máximo para GPT3 4096 - //'max_tokens' => 8192, //Máximo para GPT4
            'temperature' => 1.0,
            'top_p' => 0.2,
            'n' => 1,
            'presence_penalty' => 2.0,
        ];

        $response = $this->client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        $data = $response->toArray();

        return $data['choices'][0]['message']['content'] ?? null;
    }

}