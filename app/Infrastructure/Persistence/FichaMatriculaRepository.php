<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\FichaMatricula;
use App\Domain\Repositories\FichaMatriculaRepositoryInterface;
use PDO;
use Exception;

class FichaMatriculaRepository implements FichaMatriculaRepositoryInterface
{
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function create(FichaMatricula $fichaMatricula): ?int
    {
        $conn = $this->database->getConnection();

        try {
            if ($this->database->inTransaction()) {
                throw new \RuntimeException("Ya existe una transacción activa");
            }

            $this->database->beginTransaction();

            $codEstudiante = $this->insertEstudiante($conn, $fichaMatricula->getEstudiante());
            $codAntecedentesPersonales = $this->insertAntecedentesPersonales($conn, $fichaMatricula->getAntecedentesPersonales());
            $codAntecedentesAcademicos = $this->insertAntecedentesAcademicos($conn, $fichaMatricula->getAntecedentesAcademicos());
            $codAntecedentesLocalidad = $this->insertAntecedentesLocalidad($conn, $fichaMatricula->getAntecedentesLocalidad());
            $codAntecedentesPie = $this->insertAntecedentesPie($conn, $fichaMatricula->getAntecedentesPie());
            $codAntecedentesSalud = $this->insertAntecedentesSalud($conn, $fichaMatricula->getAntecedentesSalud());
            $codAntecedentesSociales = $this->insertAntecedentesSociales($conn, $fichaMatricula->getAntecedentesSociales());
            $codAntecedentesJunaeb = $this->insertAntecedentesJunaeb($conn, $fichaMatricula->getAntecedentesJunaeb());

            $codFichaMatricula = $this->insertFichaMatricula(
                $conn,
                $codEstudiante,
                $codAntecedentesPersonales,
                $codAntecedentesAcademicos,
                $codAntecedentesLocalidad,
                $codAntecedentesPie,
                $codAntecedentesSalud,
                $codAntecedentesSociales,
                $codAntecedentesJunaeb,
                $fichaMatricula->getCodPeriodoLectivo(),
                $fichaMatricula->getGradoAMatricularse(),
                $fichaMatricula->getMatriculaNueva(),
                $fichaMatricula->getCodEstadoFichaMatricula(),
                $fichaMatricula->getAutorizacionUsoFotos(),
                $fichaMatricula->getConfirmacionDatosEntregados(),
                $fichaMatricula->getEnteradoEnvioReglamento()
            );

            $this->insertFamiliares($conn, $codFichaMatricula, $fichaMatricula->getFamiliares());
            $this->insertFormacionGeneral($conn, $codFichaMatricula, $fichaMatricula->getFormacionGeneralOpciones());

            $this->database->commit();

            return $codFichaMatricula;
        } catch (Exception $e) {
            if ($this->database->inTransaction()) {
                $this->database->rollback();
            }
            throw new \RuntimeException("Error al crear ficha de matrícula: " . $e->getMessage());
        }
    }

    private function insertEstudiante(PDO $conn, $estudiante): int
    {
        $queryCheck = "SELECT cod_estudiante FROM estudiante 
                       WHERE run_estudiante = :run AND dv_rut_estudiante = :dv";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->execute([
            ':run' => $estudiante->getRunEstudiante(),
            ':dv' => $estudiante->getDvRutEstudiante()
        ]);

        $existing = $stmtCheck->fetchColumn();
        
        if ($existing) {
            $queryUpdate = "UPDATE estudiante 
                           SET nombres = :nombres,
                               nombre_social = :nombre_social,
                               apellido_paterno = :apellido_paterno,
                               apellido_materno = :apellido_materno,
                               fecha_nacimiento = :fecha_nacimiento,
                               nacionalidad = :nacionalidad,
                               cod_genero = :cod_genero
                           WHERE cod_estudiante = :cod_estudiante";
            
            $stmtUpdate = $conn->prepare($queryUpdate);
            $stmtUpdate->execute([
                ':nombres' => $estudiante->getNombres(),
                ':nombre_social' => $estudiante->getNombreSocial(),
                ':apellido_paterno' => $estudiante->getApellidoPaterno(),
                ':apellido_materno' => $estudiante->getApellidoMaterno(),
                ':fecha_nacimiento' => $estudiante->getFechaNacimiento()->format('Y-m-d'),
                ':nacionalidad' => $estudiante->getNacionalidad(),
                ':cod_genero' => $estudiante->getCodGenero(),
                ':cod_estudiante' => $existing
            ]);
            
            return $existing;
        }

        $query = "INSERT INTO estudiante 
                  (run_estudiante, dv_rut_estudiante, nombres, nombre_social, apellido_paterno, apellido_materno, fecha_nacimiento, nacionalidad, cod_genero) 
                  VALUES (:run, :dv, :nombres, :nombre_social, :apellido_paterno, :apellido_materno, :fecha_nacimiento, :nacionalidad, :cod_genero) 
                  RETURNING cod_estudiante";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':run' => $estudiante->getRunEstudiante(),
            ':dv' => $estudiante->getDvRutEstudiante(),
            ':nombres' => $estudiante->getNombres(),
            ':nombre_social' => $estudiante->getNombreSocial(),
            ':apellido_paterno' => $estudiante->getApellidoPaterno(),
            ':apellido_materno' => $estudiante->getApellidoMaterno(),
            ':fecha_nacimiento' => $estudiante->getFechaNacimiento()->format('Y-m-d'),
            ':nacionalidad' => $estudiante->getNacionalidad(),
            ':cod_genero' => $estudiante->getCodGenero()
        ]);

        return $stmt->fetchColumn();
    }

    private function insertAntecedentesPersonales(PDO $conn, $antecedentes): int
    {
        $query = "INSERT INTO antecedentes_personales 
                  (numero_telefonico, numero_telefonico_emergencia, email, persona_convive, talentos_academicos, diciplina_practicada, pertenece_programa_sename) 
                  VALUES (:numero_telefonico, :numero_telefonico_emergencia, :email, :persona_convive, :talentos_academicos, :diciplina_practicada, :pertenece_programa_sename) 
                  RETURNING cod_antecedentes_personales";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':numero_telefonico' => $antecedentes->getNumeroTelefonico(),
            ':numero_telefonico_emergencia' => $antecedentes->getNumeroTelefonicoEmergencia(),
            ':email' => $antecedentes->getEmail(),
            ':persona_convive' => $antecedentes->getPersonaConvive(),
            ':talentos_academicos' => $antecedentes->getTalentosAcademicos(),
            ':diciplina_practicada' => $antecedentes->getDiciplinaPracticada(),
            ':pertenece_programa_sename' => $antecedentes->getPertenecePrograma_sename() ? 'true' : 'false'
        ]);

        return $stmt->fetchColumn();
    }

    private function insertAntecedentesAcademicos(PDO $conn, $antecedentes): int
    {
        $query = "INSERT INTO antecedentes_academicos 
                  (colegio_procedencia, cursos_reprobados, curso_periodo_anterior) 
                  VALUES (:colegio_procedencia, :cursos_reprobados, :curso_periodo_anterior) 
                  RETURNING cod_antecedentes_academicos";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':colegio_procedencia' => $antecedentes->getColegioProcedencia(),
            ':cursos_reprobados' => $antecedentes->getCursosReprobados(),
            ':curso_periodo_anterior' => $antecedentes->getCursoPeriodoAnterior()
        ]);

        return $stmt->fetchColumn();
    }

    private function insertAntecedentesLocalidad(PDO $conn, $antecedentes): int
    {
        $query = "INSERT INTO antecedentes_localidad 
                  (direccion, referencia_direccion, comuna, vive_sector_rural, tiene_acceso_internet) 
                  VALUES (:direccion, :referencia_direccion, :comuna, :vive_sector_rural, :tiene_acceso_internet) 
                  RETURNING cod_antecedentes_localidad";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':direccion' => $antecedentes->getDireccion(),
            ':referencia_direccion' => $antecedentes->getReferenciaDireccion(),
            ':comuna' => $antecedentes->getComuna(),
            ':vive_sector_rural' => $antecedentes->getViveSectorRural() ? 'true' : 'false',
            ':tiene_acceso_internet' => $antecedentes->getTieneAccesoInternet() ? 'true' : 'false'
        ]);

        return $stmt->fetchColumn();
    }

    private function insertAntecedentesPie(PDO $conn, $antecedentes): int
    {
        $query = "INSERT INTO antecedentes_pie 
                  (pertenecio_pie, diagnostico_pie, curso_estuvo_pie, tiene_documentacion_pie, colegio_estuvo_pie) 
                  VALUES (:pertenecio_pie, :diagnostico_pie, :curso_estuvo_pie, :tiene_documentacion_pie, :colegio_estuvo_pie) 
                  RETURNING cod_antecedentes_pie";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':pertenecio_pie' => $antecedentes->getPertenecioPie() ? 'true' : 'false',
            ':diagnostico_pie' => $antecedentes->getDiagnosticoPie(),
            ':curso_estuvo_pie' => $antecedentes->getCursoEstuvoPie(),
            ':tiene_documentacion_pie' => $antecedentes->getTieneDocumentacionPie() ? 'true' : 'false',
            ':colegio_estuvo_pie' => $antecedentes->getColegioEstuvoPie()
        ]);

        return $stmt->fetchColumn();
    }

    private function insertAntecedentesSalud(PDO $conn, $antecedentes): int
    {
        $query = "INSERT INTO antecedentes_salud 
                  (enfermedad_diagnosticada, documentacion_enfermedades, medicamentos_indicados, medicamentos_contraindicados, 
                   grupo_sanguineo, atendido_psicologo, atendido_psiquiatra, atendido_psicopedagogo, atendido_fonoaudiologo, 
                   atendido_otro, nombre_especialista, especialidad) 
                  VALUES (:enfermedad_diagnosticada, :documentacion_enfermedades, :medicamentos_indicados, :medicamentos_contraindicados, 
                          :grupo_sanguineo, :atendido_psicologo, :atendido_psiquiatra, :atendido_psicopedagogo, :atendido_fonoaudiologo, 
                          :atendido_otro, :nombre_especialista, :especialidad) 
                  RETURNING cod_antecedentes_salud";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':enfermedad_diagnosticada' => $antecedentes->getEnfermedadDiagnosticada(),
            ':documentacion_enfermedades' => $antecedentes->getDocumentacionEnfermedades() ? 'true' : 'false',
            ':medicamentos_indicados' => $antecedentes->getMedicamentosIndicados(),
            ':medicamentos_contraindicados' => $antecedentes->getMedicamentosContraindicados(),
            ':grupo_sanguineo' => $antecedentes->getGrupoSanguineo(),
            ':atendido_psicologo' => $antecedentes->getAtendidoPsicologo() ? 'true' : 'false',
            ':atendido_psiquiatra' => $antecedentes->getAtendidoPsiquiatra() ? 'true' : 'false',
            ':atendido_psicopedagogo' => $antecedentes->getAtendidoPsicopedagogo() ? 'true' : 'false',
            ':atendido_fonoaudiologo' => $antecedentes->getAtendidoFonoaudiologo() ? 'true' : 'false',
            ':atendido_otro' => $antecedentes->getAtendidoOtro() ? 'true' : 'false',
            ':nombre_especialista' => $antecedentes->getNombreEspecialista(),
            ':especialidad' => $antecedentes->getEspecialidad()
        ]);

        return $stmt->fetchColumn();
    }

    private function insertAntecedentesSociales(PDO $conn, $antecedentes): int
    {
        $query = "INSERT INTO antecedentes_sociales 
                  (numero_personas_casa, numero_dormitorios, tiene_agua_potable, tiene_luz_electrica, porcentaje_social_hogares, 
                   tiene_alcantarillado, prevision_salud, subsidio_familiar, seguro_complementario_salud, 
                   institucion_atencion_seguro, consultorio_atencion_primaria) 
                  VALUES (:numero_personas_casa, :numero_dormitorios, :tiene_agua_potable, :tiene_luz_electrica, :porcentaje_social_hogares, 
                          :tiene_alcantarillado, :prevision_salud, :subsidio_familiar, :seguro_complementario_salud, 
                          :institucion_atencion_seguro, :consultorio_atencion_primaria) 
                  RETURNING cod_antecedentes_sociales";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':numero_personas_casa' => $antecedentes->getNumeroPersonasCasa(),
            ':numero_dormitorios' => $antecedentes->getNumeroDormitorios(),
            ':tiene_agua_potable' => $antecedentes->getTieneAguaPotable() ? 'true' : 'false',
            ':tiene_luz_electrica' => $antecedentes->getTieneLuzElectrica() ? 'true' : 'false',
            ':porcentaje_social_hogares' => $antecedentes->getPorcentajeSocialHogares(),
            ':tiene_alcantarillado' => $antecedentes->getTieneAlcantarillado() ? 'true' : 'false',
            ':prevision_salud' => $antecedentes->getPrevisionSalud(),
            ':subsidio_familiar' => $antecedentes->getSubsidioFamiliar() ? 'true' : 'false',
            ':seguro_complementario_salud' => $antecedentes->getSeguroComplementarioSalud() ? 'true' : 'false',
            ':institucion_atencion_seguro' => $antecedentes->getInstitucionAtencionSeguro(),
            ':consultorio_atencion_primaria' => $antecedentes->getConsultorioAtencionPrimaria()
        ]);

        return $stmt->fetchColumn();
    }

    private function insertAntecedentesJunaeb(PDO $conn, $antecedentes): int
    {
        $query = "INSERT INTO antecedentes_junaeb 
                  (beneficio_alimentacion, etnia_perteneciente, beca_indigena, beca_presidente_republica, pertenece_chile_solidario) 
                  VALUES (:beneficio_alimentacion, :etnia_perteneciente, :beca_indigena, :beca_presidente_republica, :pertenece_chile_solidario) 
                  RETURNING cod_antecedentes_junaeb";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':beneficio_alimentacion' => $antecedentes->getBeneficioAlimentacion() ? 'true' : 'false',
            ':etnia_perteneciente' => $antecedentes->getEtniaPerteneciente(),
            ':beca_indigena' => $antecedentes->getBecaIndigena() ? 'true' : 'false',
            ':beca_presidente_republica' => $antecedentes->getBecaPresidenteRepublica() ? 'true' : 'false',
            ':pertenece_chile_solidario' => $antecedentes->getPerteneceChileSolidario() ? 'true' : 'false'
        ]);

        return $stmt->fetchColumn();
    }

    private function insertFichaMatricula(
        PDO $conn,
        int $codEstudiante,
        int $codAntecedentesPersonales,
        int $codAntecedentesAcademicos,
        int $codAntecedentesLocalidad,
        int $codAntecedentesPie,
        int $codAntecedentesSalud,
        int $codAntecedentesSociales,
        int $codAntecedentesJunaeb,
        int $codPeriodoLectivo,
        int $gradoAMatricularse,
        bool $matriculaNueva,
        int $codEstadoFichaMatricula,
        bool $autorizacionUsoFotos,
        bool $confirmacionDatosEntregados,
        bool $enteradoEnvioReglamento
    ): int {
        $query = "INSERT INTO ficha_matricula
              (grado_a_matricularse, matricula_nueva, cod_estudiante, cod_antecedentes_personales, cod_antecedentes_academicos, cod_antecedentes_localidad,
               cod_antecedentes_pie, cod_antecedentes_salud, cod_antecedentes_sociales, cod_antecedentes_junaeb, cod_periodo_lectivo, cod_estado_ficha_matricula,
               autorizacion_uso_fotos, confirmacion_datos_entregados, enterado_envio_reglamento)
              VALUES (:grado_a_matricularse, :matricula_nueva, :cod_estudiante, :cod_antecedentes_personales, :cod_antecedentes_academicos, :cod_antecedentes_localidad,
              :cod_antecedentes_pie, :cod_antecedentes_salud, :cod_antecedentes_sociales, :cod_antecedentes_junaeb, :cod_periodo_lectivo, :cod_estado_ficha_matricula,
              :autorizacion_uso_fotos, :confirmacion_datos_entregados, :enterado_envio_reglamento)
              RETURNING cod_ficha_matricula";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':grado_a_matricularse' => $gradoAMatricularse,
            ':matricula_nueva' => $matriculaNueva ? 'true' : 'false',
            ':cod_estudiante' => $codEstudiante,
            ':cod_antecedentes_personales' => $codAntecedentesPersonales,
            ':cod_antecedentes_academicos' => $codAntecedentesAcademicos,
            ':cod_antecedentes_localidad' => $codAntecedentesLocalidad,
            ':cod_antecedentes_pie' => $codAntecedentesPie,
            ':cod_antecedentes_salud' => $codAntecedentesSalud,
            ':cod_antecedentes_sociales' => $codAntecedentesSociales,
            ':cod_antecedentes_junaeb' => $codAntecedentesJunaeb,
            ':cod_periodo_lectivo' => $codPeriodoLectivo,
            ':cod_estado_ficha_matricula' => $codEstadoFichaMatricula,
            ':autorizacion_uso_fotos' => $autorizacionUsoFotos ? 'true' : 'false',
            ':confirmacion_datos_entregados' => $confirmacionDatosEntregados ? 'true' : 'false',
            ':enterado_envio_reglamento' => $enteradoEnvioReglamento ? 'true' : 'false'
        ]);

        return $stmt->fetchColumn();
    }

    private function insertFamiliares(PDO $conn, int $codFichaMatricula, array $familiares): void
    {
        if (empty($familiares)) {
            return;
        }

        foreach ($familiares as $familiar) {
            $codFamiliar = $this->insertFamiliar($conn, $familiar);
            $this->insertFamiliarPorFicha($conn, $codFichaMatricula, $codFamiliar, $familiar);
        }
    }

    private function insertFamiliar(PDO $conn, $familiar): int
    {
        $queryCheck = "SELECT cod_familiar FROM familiar 
                       WHERE run_familiar = :run AND dv_run_familiar = :dv";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->execute([
            ':run' => $familiar->getRunFamiliar(),
            ':dv' => $familiar->getDvRunFamiliar()
        ]);

        $existing = $stmtCheck->fetchColumn();
        
        if ($existing) {
            $queryUpdate = "UPDATE familiar 
                           SET nombres = :nombres,
                               apellido_paterno = :apellido_paterno,
                               apellido_materno = :apellido_materno,
                               direccion = :direccion,
                               comuna = :comuna,
                               actividad_laboral = :actividad_laboral,
                               cod_escolaridad = :cod_escolaridad,
                               lugar_trabajo = :lugar_trabajo,
                               email = :email,
                               numero_telefonico = :numero_telefonico
                           WHERE cod_familiar = :cod_familiar";
            
            $stmtUpdate = $conn->prepare($queryUpdate);
            $stmtUpdate->execute([
                ':nombres' => $familiar->getNombres(),
                ':apellido_paterno' => $familiar->getApellidoPaterno(),
                ':apellido_materno' => $familiar->getApellidoMaterno(),
                ':direccion' => $familiar->getDireccion(),
                ':comuna' => $familiar->getComuna(),
                ':actividad_laboral' => $familiar->getActividadLaboral(),
                ':cod_escolaridad' => $familiar->getCodEscolaridad(),
                ':lugar_trabajo' => $familiar->getLugarTrabajo(),
                ':email' => $familiar->getEmail(),
                ':numero_telefonico' => $familiar->getNumeroTelefonico(),
                ':cod_familiar' => $existing
            ]);
            
            return $existing;
        }

        $query = "INSERT INTO familiar 
                  (run_familiar, dv_run_familiar, nombres, apellido_paterno, apellido_materno, direccion, comuna, 
                   actividad_laboral, cod_escolaridad, lugar_trabajo, email, numero_telefonico) 
                  VALUES (:run_familiar, :dv_run_familiar, :nombres, :apellido_paterno, :apellido_materno, :direccion, :comuna, 
                          :actividad_laboral, :cod_escolaridad, :lugar_trabajo, :email, :numero_telefonico) 
                  RETURNING cod_familiar";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':run_familiar' => $familiar->getRunFamiliar(),
            ':dv_run_familiar' => $familiar->getDvRunFamiliar(),
            ':nombres' => $familiar->getNombres(),
            ':apellido_paterno' => $familiar->getApellidoPaterno(),
            ':apellido_materno' => $familiar->getApellidoMaterno(),
            ':direccion' => $familiar->getDireccion(),
            ':comuna' => $familiar->getComuna(),
            ':actividad_laboral' => $familiar->getActividadLaboral(),
            ':cod_escolaridad' => $familiar->getCodEscolaridad(),
            ':lugar_trabajo' => $familiar->getLugarTrabajo(),
            ':email' => $familiar->getEmail(),
            ':numero_telefonico' => $familiar->getNumeroTelefonico()
        ]);

        return $stmt->fetchColumn();
    }

    private function insertFamiliarPorFicha(PDO $conn, int $codFichaMatricula, int $codFamiliar, $familiar): void
    {
        $query = "INSERT INTO familiar_por_ficha_estudiante 
                  (cod_ficha_matricula, cod_familiar, cod_tipo_familiar, es_titular, es_suplente) 
                  VALUES (:cod_ficha_matricula, :cod_familiar, :cod_tipo_familiar, :es_titular, :es_suplente)";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':cod_ficha_matricula' => $codFichaMatricula,
            ':cod_familiar' => $codFamiliar,
            ':cod_tipo_familiar' => $familiar->getCodTipoFamiliar(),
            ':es_titular' => $familiar->getEsTitular() ? 'true' : 'false',
            ':es_suplente' => $familiar->getEsSuplente() ? 'true' : 'false'
        ]);
    }

    private function insertFormacionGeneral(PDO $conn, int $codFichaMatricula, array $opciones): void
    {
        if (empty($opciones)) {
            return;
        }

        foreach ($opciones as $codOpcion) {
            $query = "INSERT INTO ficha_fg_seleccion 
                      (cod_ficha_matricula, cod_fg_opciones) 
                      VALUES (:cod_ficha_matricula, :cod_fg_opciones)";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':cod_ficha_matricula' => $codFichaMatricula,
                ':cod_fg_opciones' => $codOpcion
            ]);
        }
    }

    public function findById(int $codFichaMatricula): ?FichaMatricula
    {
        return null;
    }

    public function findAll(): array
    {
        return [];
    }

    public function verificarPrematricula(int $runEstudiante, int $periodoLectivo, int $estadoFichaMatricula): ?array
    {
        $conn = $this->database->getConnection();

        $query = "SELECT fm.cod_estado_ficha_matricula, fm.fecha_prematricula
                  FROM ficha_matricula fm
                  INNER JOIN estudiante e ON fm.cod_estudiante = e.cod_estudiante
                  WHERE e.run_estudiante = :run_estudiante
                    AND fm.cod_periodo_lectivo = :periodo_lectivo
                    AND fm.cod_estado_ficha_matricula = :estado_ficha_matricula
                  LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':run_estudiante' => $runEstudiante,
            ':periodo_lectivo' => $periodoLectivo,
            ':estado_ficha_matricula' => $estadoFichaMatricula
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    private function enviarCorreoNotificacionAsincrono(PDO $conn, int $codFichaMatricula, FichaMatricula $fichaMatricula): void
    {
        try {
            $familiares = $fichaMatricula->getFamiliares();
            
            $apoderadoTitular = null;
            foreach ($familiares as $familiar) {
                if ($familiar->getEsTitular()) {
                    $apoderadoTitular = $familiar;
                    break;
                }
            }

            if (!$apoderadoTitular) {
                error_log("No se encontró apoderado titular - no se enviará correo");
                return;
            }

            $queryTipoFamiliar = "SELECT descripcion_familiar FROM tipo_familiar WHERE cod_tipo_familiar = :cod_tipo_familiar";
            $stmtTipoFamiliar = $conn->prepare($queryTipoFamiliar);
            $stmtTipoFamiliar->execute([':cod_tipo_familiar' => $apoderadoTitular->getCodTipoFamiliar()]);
            $tipoFamiliar = $stmtTipoFamiliar->fetchColumn();

            $estudiante = $fichaMatricula->getEstudiante();

            $datosEstudiante = [
                'nombres' => $estudiante->getNombres(),
                'apellido_paterno' => $estudiante->getApellidoPaterno(),
                'apellido_materno' => $estudiante->getApellidoMaterno() ?? '',
                'rut' => $estudiante->getRunEstudiante() . '-' . $estudiante->getDvRutEstudiante(),
                'grado_a_matricularse' => $fichaMatricula->getGradoAMatricularse() . '°'
            ];

            $datosApoderado = [
                'nombres' => $apoderadoTitular->getNombres(),
                'apellido_paterno' => $apoderadoTitular->getApellidoPaterno(),
                'apellido_materno' => $apoderadoTitular->getApellidoMaterno() ?? '',
                'rut' => $apoderadoTitular->getRunFamiliar() . '-' . $apoderadoTitular->getDvRunFamiliar(),
                'tipo_familiar' => $tipoFamiliar ?? 'No especificado',
                'email' => $apoderadoTitular->getEmail(),
                'nombre_completo' => $apoderadoTitular->getNombres() . ' ' . $apoderadoTitular->getApellidoPaterno()
            ];

            $this->disparaEnvioCorreoAsync($datosEstudiante, $datosApoderado);
            
        } catch (Exception $e) {
            error_log("Error al disparar envío de correo: " . $e->getMessage());
        }
    }

    private function disparaEnvioCorreoAsync(array $datosEstudiante, array $datosApoderado): void
    {
        $url = ($_ENV['APP_URL'] ?? 'http://localhost') . '/send_email.php';
        
        $data = http_build_query([
            'estudiante' => json_encode($datosEstudiante),
            'apoderado' => json_encode($datosApoderado)
        ]);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        
        @curl_exec($ch);
        @curl_close($ch);
    }
}
