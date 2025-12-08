<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\CreateFichaMatriculaUseCase;
use App\Application\UseCases\VerificarPrematriculaUseCase;
use App\Application\UseCases\GetFichaMatriculaCompletaUseCase;
use App\Application\UseCases\GetEstudiantesByPeriodoLectivoUseCase;
use App\Application\UseCases\UpdateFichaMatriculaUseCase;
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
    private GetFichaMatriculaCompletaUseCase $getFichaMatriculaCompletaUseCase;
    private GetEstudiantesByPeriodoLectivoUseCase $getEstudiantesByPeriodoLectivoUseCase;
    private UpdateFichaMatriculaUseCase $updateFichaMatriculaUseCase;
    private RequestInterface $request;
    private ResponseInterface $response;
    private EmailService $emailService;
    private EmailDataMapper $emailDataMapper;

    public function __construct(
        CreateFichaMatriculaUseCase $createFichaMatriculaUseCase,
        VerificarPrematriculaUseCase $verificarPrematriculaUseCase,
        GetFichaMatriculaCompletaUseCase $getFichaMatriculaCompletaUseCase,
        GetEstudiantesByPeriodoLectivoUseCase $getEstudiantesByPeriodoLectivoUseCase,
        UpdateFichaMatriculaUseCase $updateFichaMatriculaUseCase,
        RequestInterface $request,
        ResponseInterface $response,
        EmailService $emailService,
        EmailDataMapper $emailDataMapper
    ) {
        $this->createFichaMatriculaUseCase = $createFichaMatriculaUseCase;
        $this->verificarPrematriculaUseCase = $verificarPrematriculaUseCase;
        $this->getFichaMatriculaCompletaUseCase = $getFichaMatriculaCompletaUseCase;
        $this->getEstudiantesByPeriodoLectivoUseCase = $getEstudiantesByPeriodoLectivoUseCase;
        $this->updateFichaMatriculaUseCase = $updateFichaMatriculaUseCase;
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

    public function getFichaCompleta(): void
    {
        try {
            $queryParams = $this->request->getQueryParams();

            // Permitir búsqueda por ID de ficha o por RUN + código de período
            if (isset($queryParams['id'])) {
                $codFichaMatricula = (int) $queryParams['id'];
                $fichaCompleta = $this->getFichaMatriculaCompletaUseCase->execute($codFichaMatricula);
            } elseif (isset($queryParams['rut']) && isset($queryParams['cod_periodo'])) {
                $runEstudiante = (int) $queryParams['rut'];
                $codPeriodoLectivo = (int) $queryParams['cod_periodo'];
                $fichaCompleta = $this->getFichaMatriculaCompletaUseCase->executeByEstudianteRunAndCode($runEstudiante, $codPeriodoLectivo);
            } else {
                $this->response->json([
                    'success' => false,
                    'message' => 'Debe proporcionar el parámetro id de ficha matrícula o los parámetros rut y cod_periodo'
                ], 400);
                return;
            }

            if (!$fichaCompleta) {
                $this->response->json([
                    'success' => false,
                    'message' => 'No se encontró la ficha de matrícula para los parámetros proporcionados'
                ], 404);
                return;
            }

            $this->response->json([
                'success' => true,
                'message' => 'Ficha de matrícula completa obtenida exitosamente',
                'data' => $fichaCompleta->toArray()
            ], 200);

        } catch (Exception $e) {
            $this->response->json([
                'success' => false,
                'message' => 'Error al obtener la ficha de matrícula completa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEstudiantesByPeriodoLectivo(): void
    {
        try {
            $queryParams = $this->request->getQueryParams();

            if (!isset($queryParams['cod_periodo_lectivo'])) {
                $this->response->json([
                    'success' => false,
                    'message' => 'El parámetro cod_periodo_lectivo es requerido'
                ], 400);
                return;
            }

            $codPeriodoLectivo = (int) $queryParams['cod_periodo_lectivo'];

            if ($codPeriodoLectivo <= 0) {
                $this->response->json([
                    'success' => false,
                    'message' => 'El parámetro cod_periodo_lectivo debe ser un número entero positivo'
                ], 400);
                return;
            }

            $runs = $this->getEstudiantesByPeriodoLectivoUseCase->execute($codPeriodoLectivo);

            if (empty($runs)) {
                $this->response->json([
                    'success' => true,
                    'message' => 'No se encontraron estudiantes para el período lectivo especificado',
                    'data' => [],
                    'total' => 0
                ], 200);
                return;
            }

            $this->response->json([
                'success' => true,
                'message' => 'Lista de RUN de estudiantes obtenida exitosamente',
                'data' => $runs,
                'total' => count($runs)
            ], 200);

        } catch (Exception $e) {
            $this->response->json([
                'success' => false,
                'message' => 'Error al obtener la lista de estudiantes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(): void
    {
        try {
            $queryParams = $this->request->getQueryParams();

            // Verificar que se proporcione el ID de la ficha matrícula
            if (!isset($queryParams['id'])) {
                $this->response->json([
                    'success' => false,
                    'message' => 'El parámetro id de ficha matrícula es requerido'
                ], 400);
                return;
            }

            $codFichaMatricula = (int) $queryParams['id'];

            if ($codFichaMatricula <= 0) {
                $this->response->json([
                    'success' => false,
                    'message' => 'El parámetro id debe ser un número entero positivo'
                ], 400);
                return;
            }

            // Obtener los datos del cuerpo de la petición
            $data = $this->request->getData();

            if (empty($data)) {
                $this->response->json([
                    'success' => false,
                    'message' => 'No se proporcionaron datos para actualizar'
                ], 400);
                return;
            }

            // Convertir los datos a array
            $dataArray = json_decode(json_encode($data), true);

            // Ejecutar la actualización
            $fichaActualizada = $this->updateFichaMatriculaUseCase->execute($codFichaMatricula, $dataArray);

            if (!$fichaActualizada) {
                $this->response->json([
                    'success' => false,
                    'message' => 'No se encontró la ficha de matrícula para actualizar'
                ], 404);
                return;
            }

            $this->response->json([
                'success' => true,
                'message' => 'Ficha de matrícula actualizada exitosamente',
                'data' => $fichaActualizada->toArray()
            ], 200);

        } catch (Exception $e) {
            $this->response->json([
                'success' => false,
                'message' => 'Error al actualizar la ficha de matrícula: ' . $e->getMessage()
            ], 500);
        }
    }
}
