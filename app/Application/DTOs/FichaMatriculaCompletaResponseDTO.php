<?php

namespace App\Application\DTOs;

use App\Domain\Entities\FichaMatricula;

class FichaMatriculaCompletaResponseDTO
{
    private int $codFichaMatricula;
    public array $antecedentesEstudiante;
    public array $antecedentesFamiliares;
    public array $antecedentesSociales;
    public array $antecedentesSalud;
    public array $antecedentesJunaeb;
    public array $formacionGeneral;

    public function __construct(FichaMatricula $fichaMatricula)
    {
        $estudiante = $fichaMatricula->getEstudiante();
        $antPersonales = $fichaMatricula->getAntecedentesPersonales();
        $antAcademicos = $fichaMatricula->getAntecedentesAcademicos();
        $antLocalidad = $fichaMatricula->getAntecedentesLocalidad();
        $antPie = $fichaMatricula->getAntecedentesPie();
        $antSalud = $fichaMatricula->getAntecedentesSalud();
        $antSociales = $fichaMatricula->getAntecedentesSociales();
        $antJunaeb = $fichaMatricula->getAntecedentesJunaeb();
        $familiares = $fichaMatricula->getFamiliares();
        $formacionGeneral = $fichaMatricula->getFormacionGeneralOpciones();

        // Guardar código de ficha matrícula
        $this->codFichaMatricula = $fichaMatricula->getCodFichaMatricula() ?? 0;

        // Obtener familiares por tipo
        $titular = null;
        $suplente = null;
        $madre = null;
        $padre = null;

        foreach ($familiares as $familiar) {
            if ($familiar->getEsTitular()) {
                $titular = $familiar;
            } elseif ($familiar->getEsSuplente()) {
                $suplente = $familiar;
            } elseif ($familiar->getCodTipoFamiliar() == 1) { // Cod 1 es padre
                $padre = $familiar;
            } elseif ($familiar->getCodTipoFamiliar() == 2) { // Cod 2 es madre
                $madre = $familiar;
            }
        }

        // Construir estructura de antecedentes estudiante
        $this->antecedentesEstudiante = [
            'grado_curso' => $fichaMatricula->getGradoAMatricularse(),
            'apellido1_estudiante' => $estudiante->getApellidoPaterno(),
            'apellido2_estudiante' => $estudiante->getApellidoMaterno() ?? '',
            'nombres_estudiante' => $estudiante->getNombres(),
            'nombre_social' => $estudiante->getNombreSocial() ?? '',
            'run_estudiante' => $estudiante->getRunEstudiante() . '-' . $estudiante->getDvRutEstudiante(),
            'fecha_nacimiento' => $estudiante->getFechaNacimiento()->format('Y-m-d'),
            'cod_genero' => $estudiante->getCodGenero(),
            'direccion' => $antLocalidad->getDireccion(),
            'referencia_direccion' => $antLocalidad->getReferenciaDireccion() ?? '',
            'comuna' => $antLocalidad->getComuna(),
            'telefono_estudiante' => $antPersonales->getNumeroTelefonico() ?? '',
            'nacionalidad' => $estudiante->getNacionalidad() ?? '',
            'telefono_emergencia' => $antPersonales->getNumeroTelefonicoEmergencia(),
            'email_estudiante' => $antPersonales->getEmail() ?? '',
            'persona_convive' => $antPersonales->getPersonaConvive() ?? '',
            'cursos_reprobados' => $antAcademicos->getCursosReprobados(),
            'colegio_procedencia' => $antAcademicos->getColegioProcedencia(),
            'curso_anterior' => $antAcademicos->getCursoPeriodoAnterior(),
            'sector_rural' => $antLocalidad->getViveSectorRural(),
            'acceso_internet' => $antLocalidad->getTieneAccesoInternet(),
            'talento_academico' => $antPersonales->getTalentosAcademicos() ?? '',
            'disciplina_practica' => $antPersonales->getDiciplinaPracticada() ?? '',
            'pertenecio_pie' => $antPie->getPertenecioPie(),
            'diagnostico_pie' => $antPie->getDiagnosticoPie() ?? '',
            'curso_pie' => $antPie->getCursoEstuvoPie() ?? '',
            'documentacion_pie' => $antPie->getTieneDocumentacionPie(),
            'colegio_estuvo_pie' => $antPie->getColegioEstuvoPie() ?? '',
            'pertenecio_sename' => $antPersonales->getPertenecePrograma_sename(),
        ];

        // Construir estructura de antecedentes familiares
        $this->antecedentesFamiliares = [
            // Titular
            'apellido1_titular' => $titular ? $titular->getApellidoPaterno() : '',
            'apellido2_titular' => $titular ? $titular->getApellidoMaterno() : '',
            'nombres_titular' => $titular ? $titular->getNombres() : '',
            'run_titular' => $titular ? $titular->getRunFamiliar() . '-' . $titular->getDvRunFamiliar() : '',
            'cod_tipo_familiar_titular' => $titular ? $titular->getCodTipoFamiliar() : 0,
            'direccion_titular' => $titular ? $titular->getDireccion() : '',
            'comuna_titular' => $titular ? $titular->getComuna() : '',
            'actividad_laboral_titular' => $titular ? $titular->getActividadLaboral() : '',
            'cod_escolaridad_titular' => $titular ? $titular->getCodEscolaridad() : 0,
            'lugar_trabajo_titular' => $titular ? $titular->getLugarTrabajo() : '',
            'mail_titular' => $titular ? $titular->getEmail() : '',
            'telefono_titular' => $titular ? $titular->getNumeroTelefonico() : '',

            // Suplente
            'apellido1_suplente' => $suplente ? $suplente->getApellidoPaterno() : '',
            'apellido2_suplente' => $suplente ? $suplente->getApellidoMaterno() : '',
            'nombres_suplente' => $suplente ? $suplente->getNombres() : '',
            'run_suplente' => $suplente ? $suplente->getRunFamiliar() . '-' . $suplente->getDvRunFamiliar() : '',
            'cod_tipo_familiar_suplente' => $suplente ? $suplente->getCodTipoFamiliar() : 0,
            'direccion_suplente' => $suplente ? $suplente->getDireccion() : '',
            'comuna_suplente' => $suplente ? $suplente->getComuna() : '',
            'actividad_laboral_suplente' => $suplente ? $suplente->getActividadLaboral() : '',
            'cod_escolaridad_suplente' => $suplente ? $suplente->getCodEscolaridad() : 0,
            'lugar_trabajo_suplente' => $suplente ? $suplente->getLugarTrabajo() : '',
            'mail_suplente' => $suplente ? $suplente->getEmail() : '',
            'telefono_suplente' => $suplente ? $suplente->getNumeroTelefonico() : '',

            // Madre
            'apellido1_madre' => $madre ? $madre->getApellidoPaterno() : '',
            'apellido2_madre' => $madre ? $madre->getApellidoMaterno() : '',
            'nombres_madre' => $madre ? $madre->getNombres() : '',
            'run_madre' => $madre ? $madre->getRunFamiliar() . '-' . $madre->getDvRunFamiliar() : '',
            'direccion_madre' => $madre ? $madre->getDireccion() : '',
            'comuna_madre' => $madre ? $madre->getComuna() : '',
            'actividad_laboral_madre' => $madre ? $madre->getActividadLaboral() : '',
            'cod_escolaridad_madre' => $madre ? $madre->getCodEscolaridad() : 0,
            'lugar_trabajo_madre' => $madre ? $madre->getLugarTrabajo() : '',
            'mail_madre' => $madre ? $madre->getEmail() : '',
            'telefono_madre' => $madre ? $madre->getNumeroTelefonico() : '',

            // Padre
            'apellido1_padre' => $padre ? $padre->getApellidoPaterno() : '',
            'apellido2_padre' => $padre ? $padre->getApellidoMaterno() : '',
            'nombres_padre' => $padre ? $padre->getNombres() : '',
            'run_padre' => $padre ? $padre->getRunFamiliar() . '-' . $padre->getDvRunFamiliar() : '',
            'direccion_padre' => $padre ? $padre->getDireccion() : '',
            'comuna_padre' => $padre ? $padre->getComuna() : '',
            'actividad_laboral_padre' => $padre ? $padre->getActividadLaboral() : '',
            'cod_escolaridad_padre' => $padre ? $padre->getCodEscolaridad() : 0,
            'lugar_trabajo_padre' => $padre ? $padre->getLugarTrabajo() : '',
            'mail_padre' => $padre ? $padre->getEmail() : '',
            'telefono_padre' => $padre ? $padre->getNumeroTelefonico() : '',
        ];

        // Construir estructura de antecedentes sociales
        $this->antecedentesSociales = [
            'personas_casa' => $antSociales->getNumeroPersonasCasa(),
            'dormitorios_casa' => $antSociales->getNumeroDormitorios(),
            'agua_potable' => $antSociales->getTieneAguaPotable(),
            'luz_electrica' => $antSociales->getTieneLuzElectrica(),
            'registro_social' => $antSociales->getPorcentajeSocialHogares(),
            'alcantarillado' => $antSociales->getTieneAlcantarillado(),
            'prevision_salud' => $antSociales->getPrevisionSalud(),
            'subsidio_familiar' => $antSociales->getSubsidioFamiliar(),
            'consultorio_atencion_primaria' => $antSociales->getConsultorioAtencionPrimaria(),
            'seguro_complementario' => $antSociales->getSeguroComplementarioSalud(),
            'institucion_seguro_complementario' => $antSociales->getInstitucionAtencionSeguro() ?? '',
        ];

        // Construir estructura de antecedentes salud
        $this->antecedentesSalud = [
            'enfermedad_cronica' => $antSalud->getEnfermedadDiagnosticada() ?? '',
            'documentacion_enfermedad' => $antSalud->getDocumentacionEnfermedades(),
            'medicamentos_indicados' => $antSalud->getMedicamentosIndicados() ?? '',
            'medicamentos_contraindicados' => $antSalud->getMedicamentosContraindicados() ?? '',
            'grupo_sanguineo' => $antSalud->getGrupoSanguineo() ?? '',
            'psicologo' => $antSalud->getAtendidoPsicologo(),
            'psiquiatra' => $antSalud->getAtendidoPsiquiatra(),
            'psicopedagogo' => $antSalud->getAtendidoPsicopedagogo(),
            'fonoaudiologo' => $antSalud->getAtendidoFonoaudiologo(),
            'otro' => $antSalud->getAtendidoOtro(),
            'en_tratamiento_especialista' => $this->tieneEvaluacionEspecialista($antSalud),
            'nombre_especialista' => $antSalud->getNombreEspecialista() ?? '',
            'especialidad' => $antSalud->getEspecialidad() ?? '',
        ];

        // Construir estructura de antecedentes junaeb
        $this->antecedentesJunaeb = [
            'alimentacion_escolar' => $antJunaeb->getBeneficioAlimentacion(),
            'chile_solidario' => $antJunaeb->getPerteneceChileSolidario(),
            'pertenece_etnia' => $antJunaeb->getEtniaPerteneciente() ?? '',
            'beca_indigena' => $antJunaeb->getBecaIndigena(),
            'beca_presidente' => $antJunaeb->getBecaPresidenteRepublica(),
        ];

        // Construir estructura de formación general
        $this->formacionGeneral = $this->mapearElectividades($formacionGeneral);
    }

    private function mapearElectividades(array $formacionGeneral): array
    {
        $electividades = [
            'visuales' => false,
            'musica' => false,
            'etica' => false,
            'catolica' => false,
            'evangelica' => false
        ];

        // Mapeo de las opciones de formación general según tu esquema
        foreach ($formacionGeneral as $opcion) {
            $nombreAsignatura = strtolower($opcion['nombre_asignatura'] ?? '');
            
            if (strpos($nombreAsignatura, 'artes visuales') !== false || strpos($nombreAsignatura, 'visuales') !== false) {
                $electividades['visuales'] = true;
            } elseif (strpos($nombreAsignatura, 'música') !== false || strpos($nombreAsignatura, 'musica') !== false) {
                $electividades['musica'] = true;
            } elseif (strpos($nombreAsignatura, 'ética') !== false || strpos($nombreAsignatura, 'etica') !== false) {
                $electividades['etica'] = true;
            } elseif (strpos($nombreAsignatura, 'religión católica') !== false || strpos($nombreAsignatura, 'catolica') !== false) {
                $electividades['catolica'] = true;
            } elseif (strpos($nombreAsignatura, 'religión evangélica') !== false || strpos($nombreAsignatura, 'evangelica') !== false) {
                $electividades['evangelica'] = true;
            }
        }

        return $electividades;
    }

    private function tieneEvaluacionEspecialista($antSalud): bool
    {
        return $antSalud->getAtendidoPsicologo() ||
               $antSalud->getAtendidoPsiquiatra() ||
               $antSalud->getAtendidoPsicopedagogo() ||
               $antSalud->getAtendidoFonoaudiologo() ||
               $antSalud->getAtendidoOtro();
    }

    private function calcularEdad(\DateTime $fechaNacimiento): int
    {
        return $fechaNacimiento->diff(new \DateTime())->y;
    }

    private function obtenerNombreMes(int $numeroMes): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $meses[$numeroMes] ?? 'Enero';
    }



    public function toArray(): array
    {
        return [
            'cod_ficha_matricula' => $this->codFichaMatricula,
            'antecedentes_estudiante' => $this->antecedentesEstudiante,
            'antecedentes_familiares' => $this->antecedentesFamiliares,
            'antecedentes_sociales' => $this->antecedentesSociales,
            'antecedentes_salud' => $this->antecedentesSalud,
            'antecedentes_junaeb' => $this->antecedentesJunaeb,
            'formacion_general' => $this->formacionGeneral
        ];
    }
}