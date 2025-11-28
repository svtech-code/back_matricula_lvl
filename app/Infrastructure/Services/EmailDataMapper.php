<?php

namespace App\Infrastructure\Services;

class EmailDataMapper
{
    private const TIPOS_FAMILIAR = [
        1 => 'Padre',
        2 => 'Madre',
        3 => 'Tutor legal',
        4 => 'Abuelo/a',
        5 => 'Tío/a',
        6 => 'Apoderado',
        7 => 'Hermano/a mayor',
        8 => 'Otros familiares',
        9 => 'Cuidador legal',
        10 => 'Padrastro/Madrastra'
    ];

    public function mapearDatosParaCorreo(array $fichaData): ?array
    {
        if (!isset($fichaData['familiares']) || !is_array($fichaData['familiares'])) {
            throw new \Exception('No se encontró el array de familiares en los datos de la ficha');
        }

        $apoderadoTitular = $this->encontrarApoderadoTitular($fichaData['familiares']);
        
        if (!$apoderadoTitular) {
            return null;
        }

        return [
            'estudiante' => $this->mapearEstudiante($fichaData['estudiante'], $fichaData['grado_a_matricularse']),
            'apoderado' => $this->mapearApoderado($apoderadoTitular)
        ];
    }

    private function encontrarApoderadoTitular(array $familiares): ?array
    {
        foreach ($familiares as $familiar) {
            if (isset($familiar['es_titular']) && $familiar['es_titular']) {
                if (empty($familiar['email'])) {
                    throw new \Exception('El apoderado titular no tiene un email registrado');
                }
                return $familiar;
            }
        }
        
        return null;
    }

    private function mapearEstudiante(array $estudiante, int $grado): array
    {
        return [
            'nombres' => $estudiante['nombres'] ?? '',
            'apellido_paterno' => $estudiante['apellido_paterno'] ?? '',
            'apellido_materno' => $estudiante['apellido_materno'] ?? '',
            'rut' => $this->formatearRut($estudiante['run_estudiante'] ?? 0, $estudiante['dv_rut_estudiante'] ?? ''),
            'grado_a_matricularse' => $grado . '°'
        ];
    }

    private function mapearApoderado(array $apoderado): array
    {
        return [
            'nombres' => $apoderado['nombres'] ?? '',
            'apellido_paterno' => $apoderado['apellido_paterno'] ?? '',
            'apellido_materno' => $apoderado['apellido_materno'] ?? '',
            'rut' => $this->formatearRut($apoderado['run_familiar'] ?? 0, $apoderado['dv_run_familiar'] ?? ''),
            'tipo_familiar' => $this->obtenerTipoFamiliar($apoderado['cod_tipo_familiar'] ?? 0),
            'email' => $apoderado['email'] ?? '',
            'nombre_completo' => trim(($apoderado['nombres'] ?? '') . ' ' . ($apoderado['apellido_paterno'] ?? ''))
        ];
    }

    private function formatearRut(int $run, string $dv): string
    {
        return $run . '-' . $dv;
    }

    private function obtenerTipoFamiliar(int $codigo): string
    {
        return self::TIPOS_FAMILIAR[$codigo] ?? 'No especificado';
    }
}
