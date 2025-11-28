<?php

namespace App\Domain\Entities;

class FichaMatricula
{
    private ?int $codFichaMatricula;
    private int $gradoAMatricularse;
    private Estudiante $estudiante;
    private AntecedentesPersonales $antecedentesPersonales;
    private AntecedentesAcademicos $antecedentesAcademicos;
    private AntecedentesLocalidad $antecedentesLocalidad;
    private AntecedentesPie $antecedentesPie;
    private AntecedentesSalud $antecedentesSalud;
    private AntecedentesSociales $antecedentesSociales;
    private AntecedentesJunaeb $antecedentesJunaeb;
    private int $codPeriodoLectivo;
    private bool $matriculaNueva;
    private int $codEstadoFichaMatricula;
    private ?\DateTime $fechaPrematricula;
    private ?\DateTime $fechaMatricula;
    private bool $autorizacionUsoFotos;
    private bool $confirmacionDatosEntregados;
    private bool $enteradoEnvioReglamento;
    private array $familiares;
    private array $formacionGeneralOpciones;

    public function __construct(
        ?int $codFichaMatricula,
        int $gradoAMatricularse,
        bool $matriculaNueva,
        int $codEstadoFichaMatricula,
        Estudiante $estudiante,
        AntecedentesPersonales $antecedentesPersonales,
        AntecedentesAcademicos $antecedentesAcademicos,
        AntecedentesLocalidad $antecedentesLocalidad,
        AntecedentesPie $antecedentesPie,
        AntecedentesSalud $antecedentesSalud,
        AntecedentesSociales $antecedentesSociales,
        AntecedentesJunaeb $antecedentesJunaeb,
        int $codPeriodoLectivo,
        array $familiares = [],
        array $formacionGeneralOpciones = [],
        ?\DateTime $fechaPrematricula = null,
        ?\DateTime $fechaMatricula = null,
        bool $autorizacionUsoFotos = false,
        bool $confirmacionDatosEntregados = false,
        bool $enteradoEnvioReglamento = false
    ) {
        $this->codFichaMatricula = $codFichaMatricula;
        $this->gradoAMatricularse = $gradoAMatricularse;
        $this->matriculaNueva = $matriculaNueva;
        $this->codEstadoFichaMatricula = $codEstadoFichaMatricula;
        $this->estudiante = $estudiante;
        $this->antecedentesPersonales = $antecedentesPersonales;
        $this->antecedentesAcademicos = $antecedentesAcademicos;
        $this->antecedentesLocalidad = $antecedentesLocalidad;
        $this->antecedentesPie = $antecedentesPie;
        $this->antecedentesSalud = $antecedentesSalud;
        $this->antecedentesSociales = $antecedentesSociales;
        $this->antecedentesJunaeb = $antecedentesJunaeb;
        $this->codPeriodoLectivo = $codPeriodoLectivo;
        $this->familiares = $familiares;
        $this->formacionGeneralOpciones = $formacionGeneralOpciones;
        $this->fechaPrematricula = $fechaPrematricula;
        $this->fechaMatricula = $fechaMatricula;
        $this->autorizacionUsoFotos = $autorizacionUsoFotos;
        $this->confirmacionDatosEntregados = $confirmacionDatosEntregados;
        $this->enteradoEnvioReglamento = $enteradoEnvioReglamento;
    }

    public function getCodFichaMatricula(): ?int
    {
        return $this->codFichaMatricula;
    }

    public function getEstudiante(): Estudiante
    {
        return $this->estudiante;
    }

    public function getAntecedentesPersonales(): AntecedentesPersonales
    {
        return $this->antecedentesPersonales;
    }

    public function getAntecedentesAcademicos(): AntecedentesAcademicos
    {
        return $this->antecedentesAcademicos;
    }

    public function getAntecedentesLocalidad(): AntecedentesLocalidad
    {
        return $this->antecedentesLocalidad;
    }

    public function getAntecedentesPie(): AntecedentesPie
    {
        return $this->antecedentesPie;
    }

    public function getAntecedentesSalud(): AntecedentesSalud
    {
        return $this->antecedentesSalud;
    }

    public function getAntecedentesSociales(): AntecedentesSociales
    {
        return $this->antecedentesSociales;
    }

    public function getAntecedentesJunaeb(): AntecedentesJunaeb
    {
        return $this->antecedentesJunaeb;
    }

    public function getCodPeriodoLectivo(): int
    {
        return $this->codPeriodoLectivo;
    }

    public function getFamiliares(): array
    {
        return $this->familiares;
    }

    public function getFormacionGeneralOpciones(): array
    {
        return $this->formacionGeneralOpciones;
    }

    public function setId(int $id): void
    {
        $this->codFichaMatricula = $id;
    }

    public function getId(): ?int
    {
        return $this->codFichaMatricula;
    }

    public function getPeriodoLectivo(): int
    {
        return $this->codPeriodoLectivo;
    }

    public function getGradoAMatricularse(): int
    {
        return $this->gradoAMatricularse;
    }

    public function getMatriculaNueva(): bool
    {
        return $this->matriculaNueva;
    }

    public function getCodEstadoFichaMatricula(): int
    {
        return $this->codEstadoFichaMatricula;
    }

    public function getFechaPrematricula(): ?\DateTime
    {
        return $this->fechaPrematricula;
    }

    public function getFechaMatricula(): ?\DateTime
    {
        return $this->fechaMatricula;
    }

    public function getAutorizacionUsoFotos(): bool
    {
        return $this->autorizacionUsoFotos;
    }

    public function getConfirmacionDatosEntregados(): bool
    {
        return $this->confirmacionDatosEntregados;
    }

    public function getEnteradoEnvioReglamento(): bool
    {
        return $this->enteradoEnvioReglamento;
    }

    public static function fromArray(array $data): self
    {
        $estudiante = new Estudiante(
            null,
            $data['estudiante']['run_estudiante'],
            $data['estudiante']['dv_rut_estudiante'],
            $data['estudiante']['nombres'],
            $data['estudiante']['nombre_social'] ?? null,
            $data['estudiante']['apellido_paterno'],
            $data['estudiante']['apellido_materno'] ?? null,
            new \DateTime($data['estudiante']['fecha_nacimiento']),
            $data['estudiante']['nacionalidad'] ?? null,
            $data['estudiante']['cod_genero']
        );

        $antPersonales = new AntecedentesPersonales(
            null,
            $data['antecedentes_personales']['numero_telefonico'] ?? null,
            $data['antecedentes_personales']['numero_telefonico_emergencia'],
            $data['antecedentes_personales']['email'] ?? null,
            $data['antecedentes_personales']['persona_convive'] ?? null,
            $data['antecedentes_personales']['talentos_academicos'] ?? null,
            $data['antecedentes_personales']['diciplina_practicada'] ?? null,
            $data['antecedentes_personales']['pertenece_programa_sename'] ?? false
        );

        $antAcademicos = new AntecedentesAcademicos(
            null,
            $data['antecedentes_academicos']['colegio_procedencia'],
            is_array($data['antecedentes_academicos']['cursos_reprobados']) 
                ? implode(', ', $data['antecedentes_academicos']['cursos_reprobados'])
                : $data['antecedentes_academicos']['cursos_reprobados'],
            $data['antecedentes_academicos']['curso_periodo_anterior']
        );

        $antLocalidad = new AntecedentesLocalidad(
            null,
            $data['antecedentes_localidad']['direccion'],
            $data['antecedentes_localidad']['referencia_direccion'] ?? null,
            $data['antecedentes_localidad']['comuna'],
            $data['antecedentes_localidad']['vive_sector_rural'] ?? false,
            $data['antecedentes_localidad']['tiene_acceso_internet'] ?? false
        );

        $antPie = new AntecedentesPie(
            null,
            $data['antecedentes_pie']['pertenecio_pie'] ?? false,
            $data['antecedentes_pie']['diagnostico_pie'] ?? null,
            $data['antecedentes_pie']['curso_estuvo_pie'] ?? null,
            $data['antecedentes_pie']['tiene_documentacion_pie'] ?? false,
            $data['antecedentes_pie']['colegio_estuvo_pie'] ?? null
        );

        $antSalud = new AntecedentesSalud(
            null,
            $data['antecedentes_salud']['enfermedad_diagnosticada'] ?? null,
            $data['antecedentes_salud']['documentacion_enfermedades'] ?? false,
            $data['antecedentes_salud']['medicamentos_indicados'] ?? null,
            $data['antecedentes_salud']['medicamentos_contraindicados'] ?? null,
            $data['antecedentes_salud']['grupo_sanguineo'] ?? null,
            $data['antecedentes_salud']['atendido_psicologo'] ?? false,
            $data['antecedentes_salud']['atendido_psiquiatra'] ?? false,
            $data['antecedentes_salud']['atendido_psicopedagogo'] ?? false,
            $data['antecedentes_salud']['atendido_fonoaudiologo'] ?? false,
            $data['antecedentes_salud']['atendido_otro'] ?? false,
            $data['antecedentes_salud']['nombre_especialista'] ?? null,
            $data['antecedentes_salud']['especialidad'] ?? null
        );

        $antSociales = new AntecedentesSociales(
            null,
            $data['antecedentes_sociales']['numero_personas_casa'],
            $data['antecedentes_sociales']['numero_dormitorios'],
            $data['antecedentes_sociales']['tiene_agua_potable'],
            $data['antecedentes_sociales']['tiene_luz_electrica'],
            $data['antecedentes_sociales']['porcentaje_social_hogares'],
            $data['antecedentes_sociales']['tiene_alcantarillado'],
            $data['antecedentes_sociales']['prevision_salud'],
            $data['antecedentes_sociales']['subsidio_familiar'],
            $data['antecedentes_sociales']['seguro_complementario_salud'],
            $data['antecedentes_sociales']['institucion_atencion_seguro'] ?? null,
            $data['antecedentes_sociales']['consultorio_atencion_primaria']
        );

        $antJunaeb = new AntecedentesJunaeb(
            null,
            $data['antecedentes_junaeb']['beneficio_alimentacion'] ?? false,
            $data['antecedentes_junaeb']['etnia_perteneciente'] ?? null,
            $data['antecedentes_junaeb']['beca_indigena'] ?? false,
            $data['antecedentes_junaeb']['beca_presidente_republica'] ?? false,
            $data['antecedentes_junaeb']['pertenece_chile_solidario'] ?? false
        );

        $familiares = [];
        if (isset($data['familiares']) && is_array($data['familiares'])) {
            foreach ($data['familiares'] as $familiarData) {
                $familiares[] = new Familiar(
                    null,
                    $familiarData['run_familiar'],
                    $familiarData['dv_run_familiar'],
                    $familiarData['nombres'],
                    $familiarData['apellido_paterno'],
                    $familiarData['apellido_materno'] ?? null,
                    $familiarData['direccion'],
                    $familiarData['comuna'],
                    $familiarData['actividad_laboral'] ?? null,
                    $familiarData['cod_escolaridad'],
                    $familiarData['lugar_trabajo'],
                    $familiarData['email'],
                    $familiarData['numero_telefonico'],
                    $familiarData['cod_tipo_familiar'],
                    $familiarData['es_titular'] ?? false,
                    $familiarData['es_suplente'] ?? false
                );
            }
        }

        $formacionGeneral = $data['formacion_general_opciones'] ?? [];

        return new self(
            null,
            $data['grado_a_matricularse'],
            $data['matricula_nueva'] ?? false,
            $data['cod_estado_ficha_matricula'],
            $estudiante,
            $antPersonales,
            $antAcademicos,
            $antLocalidad,
            $antPie,
            $antSalud,
            $antSociales,
            $antJunaeb,
            $data['periodo_lectivo'],
            $familiares,
            $formacionGeneral,
            null,
            null,
            $data['autorizacion_uso_fotos'],
            $data['confirmacion_datos_entregados'],
            $data['enterado_envio_reglamento']
        );
    }
}
