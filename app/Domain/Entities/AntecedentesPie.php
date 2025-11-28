<?php

namespace App\Domain\Entities;

class AntecedentesPie
{
    private ?int $codAntecedentesPie;
    private bool $pertenecioPie;
    private ?string $diagnosticoPie;
    private ?string $cursoEstuvoPie;
    private bool $tieneDocumentacionPie;
    private ?string $colegioEstuvoPie;

    public function __construct(
        ?int $codAntecedentesPie,
        bool $pertenecioPie = false,
        ?string $diagnosticoPie = null,
        ?string $cursoEstuvoPie = null,
        bool $tieneDocumentacionPie = false,
        ?string $colegioEstuvoPie = null
    ) {
        $this->codAntecedentesPie = $codAntecedentesPie;
        $this->pertenecioPie = $pertenecioPie;
        $this->diagnosticoPie = $diagnosticoPie;
        $this->cursoEstuvoPie = $cursoEstuvoPie;
        $this->tieneDocumentacionPie = $tieneDocumentacionPie;
        $this->colegioEstuvoPie = $colegioEstuvoPie;
    }

    public function getCodAntecedentesPie(): ?int
    {
        return $this->codAntecedentesPie;
    }

    public function getPertenecioPie(): bool
    {
        return $this->pertenecioPie;
    }

    public function getDiagnosticoPie(): ?string
    {
        return $this->diagnosticoPie;
    }

    public function getCursoEstuvoPie(): ?string
    {
        return $this->cursoEstuvoPie;
    }

    public function getTieneDocumentacionPie(): bool
    {
        return $this->tieneDocumentacionPie;
    }

    public function getColegioEstuvoPie(): ?string
    {
        return $this->colegioEstuvoPie;
    }

    public function toArray(): array
    {
        return [
            'cod_antecedentes_pie' => $this->codAntecedentesPie,
            'pertenecio_pie' => $this->pertenecioPie,
            'diagnostico_pie' => $this->diagnosticoPie,
            'curso_estuvo_pie' => $this->cursoEstuvoPie,
            'tiene_documentacion_pie' => $this->tieneDocumentacionPie,
            'colegio_estuvo_pie' => $this->colegioEstuvoPie
        ];
    }
}
