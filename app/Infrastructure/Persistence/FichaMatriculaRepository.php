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

    public function findByIdWithAllDetails(int $codFichaMatricula): ?FichaMatricula
    {
        $conn = $this->database->getConnection();

        // Consulta principal para obtener la ficha de matrícula completa por ID
        $query = "
            SELECT 
                -- Ficha Matrícula
                fm.cod_ficha_matricula,
                fm.grado_a_matricularse,
                fm.matricula_nueva,
                fm.cod_periodo_lectivo,
                fm.cod_estado_ficha_matricula,
                fm.fecha_prematricula,
                fm.fecha_matricula,
                fm.autorizacion_uso_fotos,
                fm.confirmacion_datos_entregados,
                fm.enterado_envio_reglamento,
                
                -- Estudiante
                e.cod_estudiante,
                e.run_estudiante,
                e.dv_rut_estudiante,
                e.nombres as estudiante_nombres,
                e.nombre_social,
                e.apellido_paterno as estudiante_apellido_paterno,
                e.apellido_materno as estudiante_apellido_materno,
                e.fecha_nacimiento,
                e.nacionalidad,
                e.cod_genero,
                
                -- Antecedentes Personales
                ap.cod_antecedentes_personales,
                ap.numero_telefonico,
                ap.numero_telefonico_emergencia,
                ap.email,
                ap.persona_convive,
                ap.talentos_academicos,
                ap.diciplina_practicada,
                ap.pertenece_programa_sename,
                
                -- Antecedentes Académicos
                aa.cod_antecedentes_academicos,
                aa.colegio_procedencia,
                aa.cursos_reprobados,
                aa.curso_periodo_anterior,
                
                -- Antecedentes Localidad
                al.cod_antecedentes_localidad,
                al.direccion,
                al.referencia_direccion,
                al.comuna,
                al.vive_sector_rural,
                al.tiene_acceso_internet,
                
                -- Antecedentes PIE
                apie.cod_antecedentes_pie,
                apie.pertenecio_pie,
                apie.diagnostico_pie,
                apie.curso_estuvo_pie,
                apie.tiene_documentacion_pie,
                apie.colegio_estuvo_pie,
                
                -- Antecedentes Salud
                asalud.cod_antecedentes_salud,
                asalud.enfermedad_diagnosticada,
                asalud.documentacion_enfermedades,
                asalud.medicamentos_indicados,
                asalud.medicamentos_contraindicados,
                asalud.grupo_sanguineo,
                asalud.atendido_psicologo,
                asalud.atendido_psiquiatra,
                asalud.atendido_psicopedagogo,
                asalud.atendido_fonoaudiologo,
                asalud.atendido_otro,
                asalud.nombre_especialista,
                asalud.especialidad,
                
                -- Antecedentes Sociales
                asoc.cod_antecedentes_sociales,
                asoc.numero_personas_casa,
                asoc.numero_dormitorios,
                asoc.tiene_agua_potable,
                asoc.tiene_luz_electrica,
                asoc.porcentaje_social_hogares,
                asoc.tiene_alcantarillado,
                asoc.prevision_salud,
                asoc.subsidio_familiar,
                asoc.seguro_complementario_salud,
                asoc.institucion_atencion_seguro,
                asoc.consultorio_atencion_primaria,
                
                -- Antecedentes Junaeb
                aj.cod_antecedentes_junaeb,
                aj.beneficio_alimentacion,
                aj.pertenece_chile_solidario,
                aj.etnia_perteneciente,
                aj.beca_indigena,
                aj.beca_presidente_republica
                
            FROM ficha_matricula fm
            INNER JOIN estudiante e ON fm.cod_estudiante = e.cod_estudiante
            INNER JOIN antecedentes_personales ap ON fm.cod_antecedentes_personales = ap.cod_antecedentes_personales
            INNER JOIN antecedentes_academicos aa ON fm.cod_antecedentes_academicos = aa.cod_antecedentes_academicos
            INNER JOIN antecedentes_localidad al ON fm.cod_antecedentes_localidad = al.cod_antecedentes_localidad
            INNER JOIN antecedentes_pie apie ON fm.cod_antecedentes_pie = apie.cod_antecedentes_pie
            INNER JOIN antecedentes_salud asalud ON fm.cod_antecedentes_salud = asalud.cod_antecedentes_salud
            INNER JOIN antecedentes_sociales asoc ON fm.cod_antecedentes_sociales = asoc.cod_antecedentes_sociales
            INNER JOIN antecedentes_junaeb aj ON fm.cod_antecedentes_junaeb = aj.cod_antecedentes_junaeb
            WHERE fm.cod_ficha_matricula = :cod_ficha_matricula
            LIMIT 1
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute([':cod_ficha_matricula' => $codFichaMatricula]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        // Obtener familiares asociados a esta ficha
        $familiares = $this->obtenerFamiliaresByFicha($conn, $codFichaMatricula);
        
        // Obtener formación general
        $formacionGeneral = $this->obtenerFormacionGeneralByFicha($conn, $codFichaMatricula);

        // Construir la entidad FichaMatricula con todos los datos
        return $this->construirFichaMatriculaCompleta($result, $familiares, $formacionGeneral);
    }

    public function findByEstudianteAndPeriodoCodigo(int $runEstudiante, int $codPeriodoLectivo): ?FichaMatricula
    {
        $conn = $this->database->getConnection();

        // Consulta principal para obtener la ficha de matrícula completa
        $query = "
            SELECT 
                -- Ficha Matrícula
                fm.cod_ficha_matricula,
                fm.grado_a_matricularse,
                fm.matricula_nueva,
                fm.cod_periodo_lectivo,
                fm.cod_estado_ficha_matricula,
                fm.fecha_prematricula,
                fm.fecha_matricula,
                fm.autorizacion_uso_fotos,
                fm.confirmacion_datos_entregados,
                fm.enterado_envio_reglamento,
                
                -- Estudiante
                e.cod_estudiante,
                e.run_estudiante,
                e.dv_rut_estudiante,
                e.nombres as estudiante_nombres,
                e.nombre_social,
                e.apellido_paterno as estudiante_apellido_paterno,
                e.apellido_materno as estudiante_apellido_materno,
                e.fecha_nacimiento,
                e.nacionalidad,
                e.cod_genero,
                
                -- Antecedentes Personales
                ap.cod_antecedentes_personales,
                ap.numero_telefonico,
                ap.numero_telefonico_emergencia,
                ap.email,
                ap.persona_convive,
                ap.talentos_academicos,
                ap.diciplina_practicada,
                ap.pertenece_programa_sename,
                
                -- Antecedentes Académicos
                aa.cod_antecedentes_academicos,
                aa.colegio_procedencia,
                aa.cursos_reprobados,
                aa.curso_periodo_anterior,
                
                -- Antecedentes Localidad
                al.cod_antecedentes_localidad,
                al.direccion,
                al.referencia_direccion,
                al.comuna,
                al.vive_sector_rural,
                al.tiene_acceso_internet,
                
                -- Antecedentes PIE
                apie.cod_antecedentes_pie,
                apie.pertenecio_pie,
                apie.diagnostico_pie,
                apie.curso_estuvo_pie,
                apie.tiene_documentacion_pie,
                apie.colegio_estuvo_pie,
                
                -- Antecedentes Salud
                asalud.cod_antecedentes_salud,
                asalud.enfermedad_diagnosticada,
                asalud.documentacion_enfermedades,
                asalud.medicamentos_indicados,
                asalud.medicamentos_contraindicados,
                asalud.grupo_sanguineo,
                asalud.atendido_psicologo,
                asalud.atendido_psiquiatra,
                asalud.atendido_psicopedagogo,
                asalud.atendido_fonoaudiologo,
                asalud.atendido_otro,
                asalud.nombre_especialista,
                asalud.especialidad,
                
                -- Antecedentes Sociales
                asoc.cod_antecedentes_sociales,
                asoc.numero_personas_casa,
                asoc.numero_dormitorios,
                asoc.tiene_agua_potable,
                asoc.tiene_luz_electrica,
                asoc.porcentaje_social_hogares,
                asoc.tiene_alcantarillado,
                asoc.prevision_salud,
                asoc.subsidio_familiar,
                asoc.seguro_complementario_salud,
                asoc.institucion_atencion_seguro,
                asoc.consultorio_atencion_primaria,
                
                -- Antecedentes Junaeb
                aj.cod_antecedentes_junaeb,
                aj.beneficio_alimentacion,
                aj.pertenece_chile_solidario,
                aj.etnia_perteneciente,
                aj.beca_indigena,
                aj.beca_presidente_republica
                
            FROM ficha_matricula fm
            INNER JOIN estudiante e ON fm.cod_estudiante = e.cod_estudiante
            INNER JOIN antecedentes_personales ap ON fm.cod_antecedentes_personales = ap.cod_antecedentes_personales
            INNER JOIN antecedentes_academicos aa ON fm.cod_antecedentes_academicos = aa.cod_antecedentes_academicos
            INNER JOIN antecedentes_localidad al ON fm.cod_antecedentes_localidad = al.cod_antecedentes_localidad
            INNER JOIN antecedentes_pie apie ON fm.cod_antecedentes_pie = apie.cod_antecedentes_pie
            INNER JOIN antecedentes_salud asalud ON fm.cod_antecedentes_salud = asalud.cod_antecedentes_salud
            INNER JOIN antecedentes_sociales asoc ON fm.cod_antecedentes_sociales = asoc.cod_antecedentes_sociales
            INNER JOIN antecedentes_junaeb aj ON fm.cod_antecedentes_junaeb = aj.cod_antecedentes_junaeb
            WHERE e.run_estudiante = :run_estudiante
              AND fm.cod_periodo_lectivo = :cod_periodo_lectivo
            LIMIT 1
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':run_estudiante' => $runEstudiante,
            ':cod_periodo_lectivo' => $codPeriodoLectivo
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        // Obtener familiares asociados a esta ficha
        $familiares = $this->obtenerFamiliaresByFicha($conn, $result['cod_ficha_matricula']);
        
        // Obtener formación general
        $formacionGeneral = $this->obtenerFormacionGeneralByFicha($conn, $result['cod_ficha_matricula']);

        // Construir la entidad FichaMatricula con todos los datos
        return $this->construirFichaMatriculaCompleta($result, $familiares, $formacionGeneral);
    }

    public function verificarPrematricula(int $runEstudiante, int $periodoLectivo, int $estadoFichaMatricula): ?array
    {
        $conn = $this->database->getConnection();

        $query = "SELECT fm.cod_estado_ficha_matricula, fm.fecha_prematricula,
                  fm.grado_a_matricularse, e.nombres, e.apellido_paterno, e.apellido_materno
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

    public function findEstudiantesByPeriodoLectivo(int $codPeriodoLectivo): array
    {
        $conn = $this->database->getConnection();

        $query = "SELECT 
                    e.run_estudiante
                  FROM ficha_matricula fm
                  INNER JOIN estudiante e ON fm.cod_estudiante = e.cod_estudiante
                  WHERE fm.cod_periodo_lectivo = :cod_periodo_lectivo
                  ORDER BY e.run_estudiante";

        $stmt = $conn->prepare($query);
        $stmt->execute([':cod_periodo_lectivo' => $codPeriodoLectivo]);
        
        $runs = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $runs[] = (int) $row['run_estudiante'];
        }

        return $runs;
    }

    private function obtenerFamiliaresByFicha(PDO $conn, int $codFichaMatricula): array
    {
        $query = "
            SELECT 
                f.cod_familiar,
                f.run_familiar,
                f.dv_run_familiar,
                f.nombres,
                f.apellido_paterno,
                f.apellido_materno,
                f.direccion,
                f.comuna,
                f.actividad_laboral,
                f.cod_escolaridad,
                f.lugar_trabajo,
                f.email,
                f.numero_telefonico,
                tf.cod_tipo_familiar,
                tf.descripcion_familiar,
                fpfe.es_titular,
                fpfe.es_suplente
            FROM familiar_por_ficha_estudiante fpfe
            INNER JOIN familiar f ON fpfe.cod_familiar = f.cod_familiar
            INNER JOIN tipo_familiar tf ON fpfe.cod_tipo_familiar = tf.cod_tipo_familiar
            WHERE fpfe.cod_ficha_matricula = :cod_ficha_matricula
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute([':cod_ficha_matricula' => $codFichaMatricula]);

        $familiares = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $familiares[] = $row;
        }

        return $familiares;
    }

    private function obtenerFormacionGeneralByFicha(PDO $conn, int $codFichaMatricula): array
    {
        $query = "
            SELECT 
                fgo.cod_fg_opciones,
                fgo.nombre_asignatura,
                fgo.categoria
            FROM ficha_fg_seleccion ffs
            INNER JOIN formacion_general_opciones fgo ON ffs.cod_fg_opciones = fgo.cod_fg_opciones
            WHERE ffs.cod_ficha_matricula = :cod_ficha_matricula
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute([':cod_ficha_matricula' => $codFichaMatricula]);

        $formacionGeneral = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $formacionGeneral[] = $row;
        }

        return $formacionGeneral;
    }

    private function construirFichaMatriculaCompleta(array $data, array $familiares, array $formacionGeneral): FichaMatricula
    {
        // Construir las entidades necesarias usando los datos de la consulta
        $estudiante = new \App\Domain\Entities\Estudiante(
            null,
            $data['run_estudiante'],
            $data['dv_rut_estudiante'],
            $data['estudiante_nombres'],
            $data['nombre_social'],
            $data['estudiante_apellido_paterno'],
            $data['estudiante_apellido_materno'],
            new \DateTime($data['fecha_nacimiento']),
            $data['nacionalidad'],
            $data['cod_genero']
        );

        $antPersonales = new \App\Domain\Entities\AntecedentesPersonales(
            null,
            $data['numero_telefonico'],
            $data['numero_telefonico_emergencia'],
            $data['email'],
            $data['persona_convive'],
            $data['talentos_academicos'],
            $data['diciplina_practicada'],
            $this->convertirBoolean($data['pertenece_programa_sename'])
        );

        $antAcademicos = new \App\Domain\Entities\AntecedentesAcademicos(
            null,
            $data['colegio_procedencia'],
            $data['cursos_reprobados'],
            $data['curso_periodo_anterior']
        );

        $antLocalidad = new \App\Domain\Entities\AntecedentesLocalidad(
            null,
            $data['direccion'],
            $data['referencia_direccion'],
            $data['comuna'],
            $this->convertirBoolean($data['vive_sector_rural']),
            $this->convertirBoolean($data['tiene_acceso_internet'])
        );

        $antPie = new \App\Domain\Entities\AntecedentesPie(
            null,
            $this->convertirBoolean($data['pertenecio_pie']),
            $data['diagnostico_pie'],
            $data['curso_estuvo_pie'],
            $this->convertirBoolean($data['tiene_documentacion_pie']),
            $data['colegio_estuvo_pie']
        );

        $antSalud = new \App\Domain\Entities\AntecedentesSalud(
            null,
            $data['enfermedad_diagnosticada'],
            $this->convertirBoolean($data['documentacion_enfermedades']),
            $data['medicamentos_indicados'],
            $data['medicamentos_contraindicados'],
            $data['grupo_sanguineo'],
            $this->convertirBoolean($data['atendido_psicologo']),
            $this->convertirBoolean($data['atendido_psiquiatra']),
            $this->convertirBoolean($data['atendido_psicopedagogo']),
            $this->convertirBoolean($data['atendido_fonoaudiologo']),
            $this->convertirBoolean($data['atendido_otro']),
            $data['nombre_especialista'],
            $data['especialidad']
        );

        $antSociales = new \App\Domain\Entities\AntecedentesSociales(
            null,
            $data['numero_personas_casa'],
            $data['numero_dormitorios'],
            $this->convertirBoolean($data['tiene_agua_potable']),
            $this->convertirBoolean($data['tiene_luz_electrica']),
            $data['porcentaje_social_hogares'],
            $this->convertirBoolean($data['tiene_alcantarillado']),
            $data['prevision_salud'],
            $this->convertirBoolean($data['subsidio_familiar']),
            $this->convertirBoolean($data['seguro_complementario_salud']),
            $data['institucion_atencion_seguro'],
            $data['consultorio_atencion_primaria']
        );

        $antJunaeb = new \App\Domain\Entities\AntecedentesJunaeb(
            null,
            $this->convertirBoolean($data['beneficio_alimentacion']),
            $data['etnia_perteneciente'],
            $this->convertirBoolean($data['beca_indigena']),
            $this->convertirBoolean($data['beca_presidente_republica']),
            $this->convertirBoolean($data['pertenece_chile_solidario'])
        );

        // Construir familiares
        $familiaresEntidades = [];
        foreach ($familiares as $familiar) {
            $familiarEntity = new \App\Domain\Entities\Familiar(
                null,
                $familiar['run_familiar'],
                $familiar['dv_run_familiar'],
                $familiar['nombres'],
                $familiar['apellido_paterno'],
                $familiar['apellido_materno'],
                $familiar['direccion'],
                $familiar['comuna'],
                $familiar['actividad_laboral'],
                $familiar['cod_escolaridad'],
                $familiar['lugar_trabajo'],
                $familiar['email'],
                $familiar['numero_telefonico'],
                $familiar['cod_tipo_familiar'],
                $this->convertirBoolean($familiar['es_titular']),
                $this->convertirBoolean($familiar['es_suplente'])
            );
            $familiaresEntidades[] = $familiarEntity;
        }

        // Construir la ficha matrícula completa
        $fichaMatricula = new \App\Domain\Entities\FichaMatricula(
            $data['cod_ficha_matricula'],
            $data['grado_a_matricularse'],
            $this->convertirBoolean($data['matricula_nueva']),
            $data['cod_estado_ficha_matricula'],
            $estudiante,
            $antPersonales,
            $antAcademicos,
            $antLocalidad,
            $antPie,
            $antSalud,
            $antSociales,
            $antJunaeb,
            $data['cod_periodo_lectivo'],
            $familiaresEntidades,
            $formacionGeneral,
            $data['fecha_prematricula'] ? new \DateTime($data['fecha_prematricula']) : null,
            $data['fecha_matricula'] ? new \DateTime($data['fecha_matricula']) : null,
            $this->convertirBoolean($data['autorizacion_uso_fotos']),
            $this->convertirBoolean($data['confirmacion_datos_entregados']),
            $this->convertirBoolean($data['enterado_envio_reglamento'])
        );

        return $fichaMatricula;
    }

    private function convertirBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            return $value === 'true' || $value === '1' || strtolower($value) === 't';
        }
        
        if (is_numeric($value)) {
            return (int)$value === 1;
        }
        
        return false;
    }

    public function updateFichaMatriculaCompleta(int $codFichaMatricula, array $data): bool
    {
        $conn = $this->database->getConnection();

        try {
            if ($this->database->inTransaction()) {
                throw new \RuntimeException("Ya existe una transacción activa");
            }

            $this->database->beginTransaction();

            // Obtener los códigos de los antecedentes de esta ficha
            $fichaInfo = $this->obtenerCodigosAntecedentes($conn, $codFichaMatricula);
            if (!$fichaInfo) {
                throw new \RuntimeException("Ficha de matrícula no encontrada");
            }

            // Actualizar estudiante si hay datos
            if (isset($data['antecedentes_estudiante'])) {
                $this->updateEstudiante($conn, $fichaInfo['cod_estudiante'], $data['antecedentes_estudiante']);
            }

            // Actualizar antecedentes personales
            if (isset($data['antecedentes_estudiante']) || isset($data['antecedentes_salud'])) {
                $this->updateAntecedentesPersonales($conn, $fichaInfo['cod_antecedentes_personales'], 
                    $data['antecedentes_estudiante'] ?? []);
            }

            // Actualizar antecedentes académicos
            if (isset($data['antecedentes_estudiante'])) {
                $this->updateAntecedentesAcademicos($conn, $fichaInfo['cod_antecedentes_academicos'], 
                    $data['antecedentes_estudiante']);
            }

            // Actualizar antecedentes localidad
            if (isset($data['antecedentes_estudiante'])) {
                $this->updateAntecedentesLocalidad($conn, $fichaInfo['cod_antecedentes_localidad'], 
                    $data['antecedentes_estudiante']);
            }

            // Actualizar antecedentes PIE
            if (isset($data['antecedentes_estudiante'])) {
                $this->updateAntecedentesPie($conn, $fichaInfo['cod_antecedentes_pie'], 
                    $data['antecedentes_estudiante']);
            }

            // Actualizar antecedentes salud
            if (isset($data['antecedentes_salud'])) {
                $this->updateAntecedentesSalud($conn, $fichaInfo['cod_antecedentes_salud'], 
                    $data['antecedentes_salud']);
            }

            // Actualizar antecedentes sociales
            if (isset($data['antecedentes_sociales'])) {
                $this->updateAntecedentesSociales($conn, $fichaInfo['cod_antecedentes_sociales'], 
                    $data['antecedentes_sociales']);
            }

            // Actualizar antecedentes junaeb
            if (isset($data['antecedentes_junaeb'])) {
                $this->updateAntecedentesJunaeb($conn, $fichaInfo['cod_antecedentes_junaeb'], 
                    $data['antecedentes_junaeb']);
            }

            // Actualizar familiares
            if (isset($data['antecedentes_familiares'])) {
                $this->updateFamiliares($conn, $codFichaMatricula, $data['antecedentes_familiares']);
            }

            // Actualizar formación general
            if (isset($data['formacion_general'])) {
                $this->updateFormacionGeneral($conn, $codFichaMatricula, $data['formacion_general']);
            }

            $this->database->commit();

            return true;
        } catch (Exception $e) {
            if ($this->database->inTransaction()) {
                $this->database->rollback();
            }
            throw new \RuntimeException("Error al actualizar ficha de matrícula: " . $e->getMessage());
        }
    }

    private function obtenerCodigosAntecedentes(PDO $conn, int $codFichaMatricula): ?array
    {
        $query = "SELECT 
                    cod_estudiante,
                    cod_antecedentes_personales,
                    cod_antecedentes_academicos,
                    cod_antecedentes_localidad,
                    cod_antecedentes_pie,
                    cod_antecedentes_salud,
                    cod_antecedentes_sociales,
                    cod_antecedentes_junaeb
                  FROM ficha_matricula
                  WHERE cod_ficha_matricula = :cod_ficha_matricula";

        $stmt = $conn->prepare($query);
        $stmt->execute([':cod_ficha_matricula' => $codFichaMatricula]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function updateEstudiante(PDO $conn, int $codEstudiante, array $data): void
    {
        $campos = [];
        $valores = [];

        if (array_key_exists('nombres_estudiante', $data)) {
            $campos[] = 'nombres = :nombres';
            $valores[':nombres'] = $data['nombres_estudiante'];
        }

        if (array_key_exists('nombre_social', $data)) {
            $campos[] = 'nombre_social = :nombre_social';
            $valores[':nombre_social'] = $data['nombre_social'];
        }

        if (array_key_exists('apellido1_estudiante', $data)) {
            $campos[] = 'apellido_paterno = :apellido_paterno';
            $valores[':apellido_paterno'] = $data['apellido1_estudiante'];
        }

        if (array_key_exists('apellido2_estudiante', $data)) {
            $campos[] = 'apellido_materno = :apellido_materno';
            $valores[':apellido_materno'] = $data['apellido2_estudiante'];
        }

        if (array_key_exists('fecha_nacimiento', $data)) {
            $campos[] = 'fecha_nacimiento = :fecha_nacimiento';
            $valores[':fecha_nacimiento'] = $data['fecha_nacimiento'];
        }

        if (array_key_exists('nacionalidad', $data)) {
            $campos[] = 'nacionalidad = :nacionalidad';
            $valores[':nacionalidad'] = $data['nacionalidad'];
        }

        if (array_key_exists('cod_genero', $data)) {
            $campos[] = 'cod_genero = :cod_genero';
            $valores[':cod_genero'] = $data['cod_genero'];
        }

        if (!empty($campos)) {
            $query = "UPDATE estudiante SET " . implode(', ', $campos) . " WHERE cod_estudiante = :cod_estudiante";
            $valores[':cod_estudiante'] = $codEstudiante;

            $stmt = $conn->prepare($query);
            $stmt->execute($valores);
        }
    }

    private function updateAntecedentesPersonales(PDO $conn, int $codAntecedentes, array $data): void
    {
        $campos = [];
        $valores = [];

        if (array_key_exists('telefono_estudiante', $data)) {
            $campos[] = 'numero_telefonico = :numero_telefonico';
            $valores[':numero_telefonico'] = $data['telefono_estudiante'];
        }

        if (array_key_exists('telefono_emergencia', $data)) {
            $campos[] = 'numero_telefonico_emergencia = :numero_telefonico_emergencia';
            $valores[':numero_telefonico_emergencia'] = $data['telefono_emergencia'];
        }

        if (array_key_exists('email_estudiante', $data)) {
            $campos[] = 'email = :email';
            $valores[':email'] = $data['email_estudiante'];
        }

        if (array_key_exists('persona_convive', $data)) {
            $campos[] = 'persona_convive = :persona_convive';
            $valores[':persona_convive'] = $data['persona_convive'];
        }

        if (array_key_exists('talento_academico', $data)) {
            $campos[] = 'talentos_academicos = :talentos_academicos';
            $valores[':talentos_academicos'] = $data['talento_academico'];
        }

        if (array_key_exists('disciplina_practica', $data)) {
            $campos[] = 'diciplina_practicada = :diciplina_practicada';
            $valores[':diciplina_practicada'] = $data['disciplina_practica'];
        }

        if (array_key_exists('pertenecio_sename', $data)) {
            $campos[] = 'pertenece_programa_sename = :pertenece_programa_sename';
            $valores[':pertenece_programa_sename'] = $data['pertenecio_sename'] ? 'true' : 'false';
        }

        if (!empty($campos)) {
            $query = "UPDATE antecedentes_personales SET " . implode(', ', $campos) . " WHERE cod_antecedentes_personales = :cod_antecedentes";
            $valores[':cod_antecedentes'] = $codAntecedentes;

            $stmt = $conn->prepare($query);
            $stmt->execute($valores);
        }
    }

    private function updateAntecedentesAcademicos(PDO $conn, int $codAntecedentes, array $data): void
    {
        $campos = [];
        $valores = [];

        if (isset($data['colegio_procedencia'])) {
            $campos[] = 'colegio_procedencia = :colegio_procedencia';
            $valores[':colegio_procedencia'] = $data['colegio_procedencia'];
        }

        if (isset($data['cursos_reprobados'])) {
            $campos[] = 'cursos_reprobados = :cursos_reprobados';
            $valores[':cursos_reprobados'] = $data['cursos_reprobados'];
        }

        if (isset($data['curso_anterior'])) {
            $campos[] = 'curso_periodo_anterior = :curso_periodo_anterior';
            $valores[':curso_periodo_anterior'] = $data['curso_anterior'];
        }

        if (!empty($campos)) {
            $query = "UPDATE antecedentes_academicos SET " . implode(', ', $campos) . " WHERE cod_antecedentes_academicos = :cod_antecedentes";
            $valores[':cod_antecedentes'] = $codAntecedentes;

            $stmt = $conn->prepare($query);
            $stmt->execute($valores);
        }
    }

    private function updateAntecedentesLocalidad(PDO $conn, int $codAntecedentes, array $data): void
    {
        $campos = [];
        $valores = [];

        if (isset($data['direccion'])) {
            $campos[] = 'direccion = :direccion';
            $valores[':direccion'] = $data['direccion'];
        }

        if (isset($data['referencia_direccion'])) {
            $campos[] = 'referencia_direccion = :referencia_direccion';
            $valores[':referencia_direccion'] = $data['referencia_direccion'];
        }

        if (isset($data['comuna'])) {
            $campos[] = 'comuna = :comuna';
            $valores[':comuna'] = $data['comuna'];
        }

        if (isset($data['sector_rural'])) {
            $campos[] = 'vive_sector_rural = :vive_sector_rural';
            $valores[':vive_sector_rural'] = $data['sector_rural'] ? 'true' : 'false';
        }

        if (isset($data['acceso_internet'])) {
            $campos[] = 'tiene_acceso_internet = :tiene_acceso_internet';
            $valores[':tiene_acceso_internet'] = $data['acceso_internet'] ? 'true' : 'false';
        }

        if (!empty($campos)) {
            $query = "UPDATE antecedentes_localidad SET " . implode(', ', $campos) . " WHERE cod_antecedentes_localidad = :cod_antecedentes";
            $valores[':cod_antecedentes'] = $codAntecedentes;

            $stmt = $conn->prepare($query);
            $stmt->execute($valores);
        }
    }

    private function updateAntecedentesPie(PDO $conn, int $codAntecedentes, array $data): void
    {
        $campos = [];
        $valores = [];

        if (isset($data['pertenecio_pie'])) {
            $campos[] = 'pertenecio_pie = :pertenecio_pie';
            $valores[':pertenecio_pie'] = $data['pertenecio_pie'] ? 'true' : 'false';
        }

        if (isset($data['diagnostico_pie'])) {
            $campos[] = 'diagnostico_pie = :diagnostico_pie';
            $valores[':diagnostico_pie'] = $data['diagnostico_pie'];
        }

        if (isset($data['curso_pie'])) {
            $campos[] = 'curso_estuvo_pie = :curso_estuvo_pie';
            $valores[':curso_estuvo_pie'] = $data['curso_pie'];
        }

        if (isset($data['documentacion_pie'])) {
            $campos[] = 'tiene_documentacion_pie = :tiene_documentacion_pie';
            $valores[':tiene_documentacion_pie'] = $data['documentacion_pie'] ? 'true' : 'false';
        }

        if (isset($data['colegio_estuvo_pie'])) {
            $campos[] = 'colegio_estuvo_pie = :colegio_estuvo_pie';
            $valores[':colegio_estuvo_pie'] = $data['colegio_estuvo_pie'];
        }

        if (!empty($campos)) {
            $query = "UPDATE antecedentes_pie SET " . implode(', ', $campos) . " WHERE cod_antecedentes_pie = :cod_antecedentes";
            $valores[':cod_antecedentes'] = $codAntecedentes;

            $stmt = $conn->prepare($query);
            $stmt->execute($valores);
        }
    }

    private function updateAntecedentesSalud(PDO $conn, int $codAntecedentes, array $data): void
    {
        $campos = [];
        $valores = [];

        if (isset($data['enfermedad_cronica'])) {
            $campos[] = 'enfermedad_diagnosticada = :enfermedad_diagnosticada';
            $valores[':enfermedad_diagnosticada'] = $data['enfermedad_cronica'];
        }

        if (isset($data['documentacion_enfermedad'])) {
            $campos[] = 'documentacion_enfermedades = :documentacion_enfermedades';
            $valores[':documentacion_enfermedades'] = $data['documentacion_enfermedad'] ? 'true' : 'false';
        }

        if (isset($data['medicamentos_indicados'])) {
            $campos[] = 'medicamentos_indicados = :medicamentos_indicados';
            $valores[':medicamentos_indicados'] = $data['medicamentos_indicados'];
        }

        if (isset($data['medicamentos_contraindicados'])) {
            $campos[] = 'medicamentos_contraindicados = :medicamentos_contraindicados';
            $valores[':medicamentos_contraindicados'] = $data['medicamentos_contraindicados'];
        }

        if (isset($data['grupo_sanguineo'])) {
            $campos[] = 'grupo_sanguineo = :grupo_sanguineo';
            $valores[':grupo_sanguineo'] = $data['grupo_sanguineo'];
        }

        if (isset($data['psicologo'])) {
            $campos[] = 'atendido_psicologo = :atendido_psicologo';
            $valores[':atendido_psicologo'] = $data['psicologo'] ? 'true' : 'false';
        }

        if (isset($data['psiquiatra'])) {
            $campos[] = 'atendido_psiquiatra = :atendido_psiquiatra';
            $valores[':atendido_psiquiatra'] = $data['psiquiatra'] ? 'true' : 'false';
        }

        if (isset($data['psicopedagogo'])) {
            $campos[] = 'atendido_psicopedagogo = :atendido_psicopedagogo';
            $valores[':atendido_psicopedagogo'] = $data['psicopedagogo'] ? 'true' : 'false';
        }

        if (isset($data['fonoaudiologo'])) {
            $campos[] = 'atendido_fonoaudiologo = :atendido_fonoaudiologo';
            $valores[':atendido_fonoaudiologo'] = $data['fonoaudiologo'] ? 'true' : 'false';
        }

        if (isset($data['otro'])) {
            $campos[] = 'atendido_otro = :atendido_otro';
            $valores[':atendido_otro'] = $data['otro'] ? 'true' : 'false';
        }

        if (isset($data['nombre_especialista'])) {
            $campos[] = 'nombre_especialista = :nombre_especialista';
            $valores[':nombre_especialista'] = $data['nombre_especialista'];
        }

        if (isset($data['especialidad'])) {
            $campos[] = 'especialidad = :especialidad';
            $valores[':especialidad'] = $data['especialidad'];
        }

        if (!empty($campos)) {
            $query = "UPDATE antecedentes_salud SET " . implode(', ', $campos) . " WHERE cod_antecedentes_salud = :cod_antecedentes";
            $valores[':cod_antecedentes'] = $codAntecedentes;

            $stmt = $conn->prepare($query);
            $stmt->execute($valores);
        }
    }

    private function updateAntecedentesSociales(PDO $conn, int $codAntecedentes, array $data): void
    {
        $campos = [];
        $valores = [];

        if (isset($data['personas_casa'])) {
            $campos[] = 'numero_personas_casa = :numero_personas_casa';
            $valores[':numero_personas_casa'] = $data['personas_casa'];
        }

        if (isset($data['dormitorios_casa'])) {
            $campos[] = 'numero_dormitorios = :numero_dormitorios';
            $valores[':numero_dormitorios'] = $data['dormitorios_casa'];
        }

        if (isset($data['agua_potable'])) {
            $campos[] = 'tiene_agua_potable = :tiene_agua_potable';
            $valores[':tiene_agua_potable'] = $data['agua_potable'] ? 'true' : 'false';
        }

        if (isset($data['luz_electrica'])) {
            $campos[] = 'tiene_luz_electrica = :tiene_luz_electrica';
            $valores[':tiene_luz_electrica'] = $data['luz_electrica'] ? 'true' : 'false';
        }

        if (isset($data['registro_social'])) {
            $campos[] = 'porcentaje_social_hogares = :porcentaje_social_hogares';
            $valores[':porcentaje_social_hogares'] = $data['registro_social'];
        }

        if (isset($data['alcantarillado'])) {
            $campos[] = 'tiene_alcantarillado = :tiene_alcantarillado';
            $valores[':tiene_alcantarillado'] = $data['alcantarillado'] ? 'true' : 'false';
        }

        if (isset($data['prevision_salud'])) {
            $campos[] = 'prevision_salud = :prevision_salud';
            $valores[':prevision_salud'] = $data['prevision_salud'];
        }

        if (isset($data['subsidio_familiar'])) {
            $campos[] = 'subsidio_familiar = :subsidio_familiar';
            $valores[':subsidio_familiar'] = $data['subsidio_familiar'] ? 'true' : 'false';
        }

        if (isset($data['seguro_complementario'])) {
            $campos[] = 'seguro_complementario_salud = :seguro_complementario_salud';
            $valores[':seguro_complementario_salud'] = $data['seguro_complementario'] ? 'true' : 'false';
        }

        if (isset($data['institucion_seguro_complementario'])) {
            $campos[] = 'institucion_atencion_seguro = :institucion_atencion_seguro';
            $valores[':institucion_atencion_seguro'] = $data['institucion_seguro_complementario'];
        }

        if (isset($data['consultorio_atencion_primaria'])) {
            $campos[] = 'consultorio_atencion_primaria = :consultorio_atencion_primaria';
            $valores[':consultorio_atencion_primaria'] = $data['consultorio_atencion_primaria'];
        }

        if (!empty($campos)) {
            $query = "UPDATE antecedentes_sociales SET " . implode(', ', $campos) . " WHERE cod_antecedentes_sociales = :cod_antecedentes";
            $valores[':cod_antecedentes'] = $codAntecedentes;

            $stmt = $conn->prepare($query);
            $stmt->execute($valores);
        }
    }

    private function updateAntecedentesJunaeb(PDO $conn, int $codAntecedentes, array $data): void
    {
        $campos = [];
        $valores = [];

        if (isset($data['alimentacion_escolar'])) {
            $campos[] = 'beneficio_alimentacion = :beneficio_alimentacion';
            $valores[':beneficio_alimentacion'] = $data['alimentacion_escolar'] ? 'true' : 'false';
        }

        if (isset($data['chile_solidario'])) {
            $campos[] = 'pertenece_chile_solidario = :pertenece_chile_solidario';
            $valores[':pertenece_chile_solidario'] = $data['chile_solidario'] ? 'true' : 'false';
        }

        if (isset($data['pertenece_etnia'])) {
            $campos[] = 'etnia_perteneciente = :etnia_perteneciente';
            $valores[':etnia_perteneciente'] = $data['pertenece_etnia'];
        }

        if (isset($data['beca_indigena'])) {
            $campos[] = 'beca_indigena = :beca_indigena';
            $valores[':beca_indigena'] = $data['beca_indigena'] ? 'true' : 'false';
        }

        if (isset($data['beca_presidente'])) {
            $campos[] = 'beca_presidente_republica = :beca_presidente_republica';
            $valores[':beca_presidente_republica'] = $data['beca_presidente'] ? 'true' : 'false';
        }

        if (!empty($campos)) {
            $query = "UPDATE antecedentes_junaeb SET " . implode(', ', $campos) . " WHERE cod_antecedentes_junaeb = :cod_antecedentes";
            $valores[':cod_antecedentes'] = $codAntecedentes;

            $stmt = $conn->prepare($query);
            $stmt->execute($valores);
        }
    }

    private function updateFamiliares(PDO $conn, int $codFichaMatricula, array $data): void
    {
        // Formato legacy - Actualizar familiares existentes por tipo
        if (isset($data['run_titular']) && !empty($data['run_titular'])) {
            $this->updateFamiliarByRun($conn, $data['run_titular'], $data, 'titular');
        }
        
        if (isset($data['run_suplente']) && !empty($data['run_suplente'])) {
            $this->updateFamiliarByRun($conn, $data['run_suplente'], $data, 'suplente');
        }
        
        if (isset($data['run_madre']) && !empty($data['run_madre'])) {
            $this->updateFamiliarByRun($conn, $data['run_madre'], $data, 'madre');
        }
        
        if (isset($data['run_padre']) && !empty($data['run_padre'])) {
            $this->updateFamiliarByRun($conn, $data['run_padre'], $data, 'padre');
        }

        // NUEVO: Actualizar familiares usando el formato mejorado
        if (isset($data['actualizar_familiares']) && is_array($data['actualizar_familiares'])) {
            $this->actualizarFamiliaresMejorado($conn, $data['actualizar_familiares']);
        }

        // NUEVO: Agregar familiares
        if (isset($data['agregar_familiares']) && is_array($data['agregar_familiares'])) {
            $this->agregarNuevosFamiliares($conn, $codFichaMatricula, $data['agregar_familiares']);
        }
        
        // NUEVO: Eliminar familiares
        if (isset($data['eliminar_familiares']) && is_array($data['eliminar_familiares'])) {
            $this->eliminarFamiliaresPorRun($conn, $codFichaMatricula, $data['eliminar_familiares']);
        }
    }

    private function updateFamiliarByRun(PDO $conn, string $run, array $data, string $tipo): void
    {
        // Extraer RUN y DV
        $runParts = explode('-', $run);
        if (count($runParts) !== 2) return;
        
        $runNumerico = (int)$runParts[0];
        $dv = $runParts[1];

        $campos = [];
        $valores = [':run_familiar' => $runNumerico, ':dv_run_familiar' => $dv];

        if (isset($data["nombres_$tipo"])) {
            $campos[] = 'nombres = :nombres';
            $valores[':nombres'] = $data["nombres_$tipo"];
        }

        if (isset($data["apellido1_$tipo"])) {
            $campos[] = 'apellido_paterno = :apellido_paterno';
            $valores[':apellido_paterno'] = $data["apellido1_$tipo"];
        }

        if (isset($data["apellido2_$tipo"])) {
            $campos[] = 'apellido_materno = :apellido_materno';
            $valores[':apellido_materno'] = $data["apellido2_$tipo"];
        }

        if (isset($data["direccion_$tipo"])) {
            $campos[] = 'direccion = :direccion';
            $valores[':direccion'] = $data["direccion_$tipo"];
        }

        if (isset($data["actividad_laboral_$tipo"])) {
            $campos[] = 'actividad_laboral = :actividad_laboral';
            $valores[':actividad_laboral'] = $data["actividad_laboral_$tipo"];
        }

        if (isset($data["lugar_trabajo_$tipo"])) {
            $campos[] = 'lugar_trabajo = :lugar_trabajo';
            $valores[':lugar_trabajo'] = $data["lugar_trabajo_$tipo"];
        }

        if (isset($data["mail_$tipo"])) {
            $campos[] = 'email = :email';
            $valores[':email'] = $data["mail_$tipo"];
        }

        if (isset($data["telefono_$tipo"])) {
            $campos[] = 'numero_telefonico = :numero_telefonico';
            $valores[':numero_telefonico'] = $data["telefono_$tipo"];
        }

        if (!empty($campos)) {
            $query = "UPDATE familiar SET " . implode(', ', $campos) . 
                    " WHERE run_familiar = :run_familiar AND dv_run_familiar = :dv_run_familiar";

            $stmt = $conn->prepare($query);
            $stmt->execute($valores);
        }
    }

    /**
     * Agrega nuevos familiares a una ficha matrícula
     */
    private function agregarNuevosFamiliares(PDO $conn, int $codFichaMatricula, array $nuevosFamiliares): void
    {
        foreach ($nuevosFamiliares as $familiarData) {
            // Validar que tenga los campos mínimos requeridos
            if (!$this->validarDatosFamiliar($familiarData)) {
                throw new \RuntimeException("Datos incompletos para el familiar con RUN: " . ($familiarData['run_familiar'] ?? 'no especificado'));
            }

            // Crear objeto Familiar desde array
            $familiar = $this->crearFamiliarDesdeArray($familiarData);
            
            // Insertar o actualizar familiar
            $codFamiliar = $this->insertFamiliar($conn, $familiar);
            
            // Crear relación con la ficha
            $this->insertFamiliarPorFicha($conn, $codFichaMatricula, $codFamiliar, $familiar);
        }
    }

    /**
     * Elimina familiares de una ficha matrícula por sus RUNs
     */
    private function eliminarFamiliaresPorRun(PDO $conn, int $codFichaMatricula, array $runsAEliminar): void
    {
        foreach ($runsAEliminar as $run) {
            if (empty($run)) continue;
            
            // Extraer RUN y DV
            $runParts = explode('-', $run);
            if (count($runParts) !== 2) {
                throw new \RuntimeException("Formato de RUN inválido: $run. Debe ser formato 12345678-9");
            }
            
            $runNumerico = (int)$runParts[0];
            $dv = $runParts[1];

            // Solo eliminar la relación, NO el familiar de la tabla principal
            // Esto permite que el familiar siga existiendo para otras fichas
            $query = "DELETE FROM familiar_por_ficha_estudiante 
                      WHERE cod_ficha_matricula = :cod_ficha_matricula 
                      AND cod_familiar = (
                          SELECT cod_familiar FROM familiar 
                          WHERE run_familiar = :run_familiar AND dv_run_familiar = :dv_run_familiar
                      )";
                      
            $stmt = $conn->prepare($query);
            $resultado = $stmt->execute([
                ':cod_ficha_matricula' => $codFichaMatricula,
                ':run_familiar' => $runNumerico,
                ':dv_run_familiar' => $dv
            ]);

            if (!$resultado) {
                throw new \RuntimeException("Error al eliminar familiar con RUN: $run");
            }
        }
    }

    /**
     * Valida que los datos del familiar tengan los campos mínimos requeridos
     */
    private function validarDatosFamiliar(array $familiarData): bool
    {
        $camposRequeridos = [
            'run_familiar', 'dv_run_familiar', 'nombres', 'apellido_paterno',
            'direccion', 'comuna', 'cod_escolaridad', 'lugar_trabajo',
            'email', 'numero_telefonico', 'cod_tipo_familiar'
        ];

        foreach ($camposRequeridos as $campo) {
            if (!isset($familiarData[$campo])) {
                return false;
            }
            
            // Para dv_run_familiar, permitir "0" como valor válido
            if ($campo === 'dv_run_familiar') {
                if (!is_string($familiarData[$campo]) && !is_numeric($familiarData[$campo])) {
                    return false;
                }
                continue;
            }
            
            // Para otros campos, usar empty() normalmente
            if (empty($familiarData[$campo])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Actualiza familiares usando el formato mejorado con array de objetos
     */
    private function actualizarFamiliaresMejorado(PDO $conn, array $familiares): void
    {
        foreach ($familiares as $familiarData) {
            if (!isset($familiarData['run_familiar']) || !isset($familiarData['dv_run_familiar'])) {
                continue; // Saltar si no tiene RUN completo
            }

            $runNumerico = (int)$familiarData['run_familiar'];
            $dv = (string)$familiarData['dv_run_familiar'];

            $campos = [];
            $valores = [':run_familiar' => $runNumerico, ':dv_run_familiar' => $dv];

            // Mapear todos los campos que pueden ser actualizados
            if (array_key_exists('nombres', $familiarData)) {
                $campos[] = 'nombres = :nombres';
                $valores[':nombres'] = $familiarData['nombres'];
            }

            if (array_key_exists('apellido_paterno', $familiarData)) {
                $campos[] = 'apellido_paterno = :apellido_paterno';
                $valores[':apellido_paterno'] = $familiarData['apellido_paterno'];
            }

            if (array_key_exists('apellido_materno', $familiarData)) {
                $campos[] = 'apellido_materno = :apellido_materno';
                $valores[':apellido_materno'] = $familiarData['apellido_materno'];
            }

            if (array_key_exists('direccion', $familiarData)) {
                $campos[] = 'direccion = :direccion';
                $valores[':direccion'] = $familiarData['direccion'];
            }

            if (array_key_exists('comuna', $familiarData)) {
                $campos[] = 'comuna = :comuna';
                $valores[':comuna'] = $familiarData['comuna'];
            }

            if (array_key_exists('actividad_laboral', $familiarData)) {
                $campos[] = 'actividad_laboral = :actividad_laboral';
                $valores[':actividad_laboral'] = $familiarData['actividad_laboral'];
            }

            if (array_key_exists('cod_escolaridad', $familiarData)) {
                $campos[] = 'cod_escolaridad = :cod_escolaridad';
                $valores[':cod_escolaridad'] = $familiarData['cod_escolaridad'];
            }

            if (array_key_exists('lugar_trabajo', $familiarData)) {
                $campos[] = 'lugar_trabajo = :lugar_trabajo';
                $valores[':lugar_trabajo'] = $familiarData['lugar_trabajo'];
            }

            if (array_key_exists('email', $familiarData)) {
                $campos[] = 'email = :email';
                $valores[':email'] = $familiarData['email'];
            }

            if (array_key_exists('numero_telefonico', $familiarData)) {
                $campos[] = 'numero_telefonico = :numero_telefonico';
                $valores[':numero_telefonico'] = $familiarData['numero_telefonico'];
            }

            // Solo ejecutar si hay campos para actualizar
            if (!empty($campos)) {
                $query = "UPDATE familiar SET " . implode(', ', $campos) . 
                        " WHERE run_familiar = :run_familiar AND dv_run_familiar = :dv_run_familiar";

                $stmt = $conn->prepare($query);
                $stmt->execute($valores);
            }
        }
    }

    /**
     * Crea un objeto Familiar desde un array de datos
     */
    private function crearFamiliarDesdeArray(array $data): \App\Domain\Entities\Familiar
    {
        return new \App\Domain\Entities\Familiar(
            null, // cod_familiar será asignado por la base de datos
            $data['run_familiar'],
            $data['dv_run_familiar'],
            $data['nombres'],
            $data['apellido_paterno'],
            $data['apellido_materno'] ?? null,
            $data['direccion'],
            $data['comuna'],
            $data['actividad_laboral'] ?? null,
            $data['cod_escolaridad'],
            $data['lugar_trabajo'],
            $data['email'],
            $data['numero_telefonico'],
            $data['cod_tipo_familiar'],
            $data['es_titular'] ?? false,
            $data['es_suplente'] ?? false
        );
    }

    private function updateFormacionGeneral(PDO $conn, int $codFichaMatricula, array $data): void
    {
        // Primero eliminar las selecciones actuales
        $deleteQuery = "DELETE FROM ficha_fg_seleccion WHERE cod_ficha_matricula = :cod_ficha_matricula";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->execute([':cod_ficha_matricula' => $codFichaMatricula]);

        // Mapear los datos a códigos de formación general
        $opciones = [];
        if (isset($data['visuales']) && $data['visuales']) {
            $opciones[] = 1; // Asumiendo que 1 es visuales
        }
        if (isset($data['musica']) && $data['musica']) {
            $opciones[] = 2; // Asumiendo que 2 es música
        }
        if (isset($data['etica']) && $data['etica']) {
            $opciones[] = 3; // Asumiendo que 3 es ética
        }
        if (isset($data['catolica']) && $data['catolica']) {
            $opciones[] = 4; // Asumiendo que 4 es religión católica
        }
        if (isset($data['evangelica']) && $data['evangelica']) {
            $opciones[] = 5; // Asumiendo que 5 es religión evangélica
        }

        // Insertar las nuevas selecciones
        foreach ($opciones as $opcion) {
            $insertQuery = "INSERT INTO ficha_fg_seleccion (cod_ficha_matricula, cod_fg_opciones) VALUES (:cod_ficha_matricula, :cod_fg_opciones)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->execute([
                ':cod_ficha_matricula' => $codFichaMatricula,
                ':cod_fg_opciones' => $opcion
            ]);
        }
    }
}
