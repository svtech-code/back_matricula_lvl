<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\CreateFichaMatriculaUseCase;
use App\Application\UseCases\VerificarPrematriculaUseCase;
use App\Application\DTOs\FichaMatriculaResponseDTO;
use App\Application\DTOs\VerificarPrematriculaResponseDTO;
use App\Presentation\Http\RequestInterface;
use App\Presentation\Http\ResponseInterface;
use App\Infrastructure\Services\EmailService;
use App\Infrastructure\Services\EmailDataMapper;
use Exception;

class FichaMatriculaController
{
    private CreateFichaMatriculaUseCase $createFichaMatriculaUseCase;
    private VerificarPrematriculaUseCase $verificarPrematriculaUseCase;
    private RequestInterface $request;
    private ResponseInterface $response;
    private EmailService $emailService;
    private EmailDataMapper $emailDataMapper;

    public function __construct(
        CreateFichaMatriculaUseCase $createFichaMatriculaUseCase,
        VerificarPrematriculaUseCase $verificarPrematriculaUseCase,
        RequestInterface $request,
        ResponseInterface $response,
        EmailService $emailService,
        EmailDataMapper $emailDataMapper
    ) {
        $this->createFichaMatriculaUseCase = $createFichaMatriculaUseCase;
        $this->verificarPrematriculaUseCase = $verificarPrematriculaUseCase;
        $this->request = $request;
        $this->response = $response;
        $this->emailService = $emailService;
        $this->emailDataMapper = $emailDataMapper;
    }

    public function create(): void
    {
        try {
            $data = $this->request->getData();

            $requiredFields = [
                'periodo_lectivo',
                'grado_a_matricularse',
                'cod_estado_ficha_matricula',
                'estudiante',
                'antecedentes_personales',
                'antecedentes_academicos',
                'antecedentes_localidad',
                'antecedentes_pie',
                'antecedentes_salud',
                'antecedentes_sociales',
                'antecedentes_junaeb',
                'autorizacion_uso_fotos',
                'confirmacion_datos_entregados',
                'enterado_envio_reglamento'
            ];

            foreach ($requiredFields as $field) {
                if (!isset($data->$field)) {
                    $this->response->json([
                        'success' => false,
                        'message' => "El campo {$field} es requerido"
                    ], 400);
                    return;
                }
            }

            $dataArray = json_decode(json_encode($data), true);

            $ficha = $this->createFichaMatriculaUseCase->execute($dataArray);

            $responseDTO = FichaMatriculaResponseDTO::fromEntity($ficha);

            $emailEnviado = false;
            $emailError = null;

            try {
                $emailEnviado = $this->enviarCorreoEnSegundoPlano($dataArray);
            } catch (Exception $emailException) {
                $emailError = $emailException->getMessage();
            }

            $this->response->json([
                'success' => true,
                'message' => 'Ficha de matrícula creada exitosamente',
                'data' => $responseDTO->toArray(),
                'email_enviado' => $emailEnviado,
                'email_error' => $emailError
            ], 201);

        } catch (Exception $e) {
            $this->response->json([
                'success' => false,
                'message' => 'Error al crear la ficha de matrícula: ' . $e->getMessage()
            ], 500);
        }
    }

    private function enviarCorreoEnSegundoPlano(array $dataArray): bool
    {
        if (!isset($dataArray['familiares']) || empty($dataArray['familiares'])) {
            throw new Exception("No se encontraron familiares en los datos de la ficha");
        }
        
        $datosCorreo = $this->emailDataMapper->mapearDatosParaCorreo($dataArray);
        
        if (!$datosCorreo) {
            throw new Exception("No se encontró apoderado titular con email válido");
        }

        $resultado = $this->emailService->enviarCorreoPrematricula(
            $datosCorreo['estudiante'],
            $datosCorreo['apoderado']
        );
        
        if (!$resultado) {
            throw new Exception("Error al enviar el correo electrónico. Verifique la configuración SMTP.");
        }

        return true;
    }

    public function verificar(): void
    {
        try {
            $queryParams = $this->request->getQueryParams();

            if (!isset($queryParams['rut']) || !isset($queryParams['periodo_lectivo']) || !isset($queryParams['estado'])) {
                $this->response->json([
                    'success' => false,
                    'message' => 'Los parámetros rut, periodo_lectivo y estado son requeridos'
                ], 400);
                return;
            }

            $runEstudiante = (int) $queryParams['rut'];
            $periodoLectivo = (int) $queryParams['periodo_lectivo'];
            $estadoFichaMatricula = (int) $queryParams['estado'];

            $result = $this->verificarPrematriculaUseCase->execute($runEstudiante, $periodoLectivo, $estadoFichaMatricula);

            if ($result === null) {
                $this->response->json([
                    'success' => false,
                    'message' => 'No se encontró una prematrícula para los parámetros proporcionados',
                    'existe_prematricula' => false
                ], 200);
                return;
            }

            $responseDTO = VerificarPrematriculaResponseDTO::fromArray($result);

            $this->response->json([
                'success' => true,
                'existe_prematricula' => true,
                'data' => $responseDTO->toArray()
            ], 200);
        } catch (Exception $e) {
            $this->response->json([
                'success' => false,
                'message' => 'Error al verificar la prematrícula: ' . $e->getMessage()
            ], 500);
        }
    }
}
