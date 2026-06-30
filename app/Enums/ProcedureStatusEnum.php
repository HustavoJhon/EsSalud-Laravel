<?php

namespace App\Enums;

enum ProcedureStatusEnum: string
{
    case BORRADOR = 'BORRADOR';
    case RADICADO = 'RADICADO';
    case PENDIENTE = 'PENDIENTE';
    case REVISION = 'REVISION';
    case EVALUACION = 'EVALUACION';
    case APROBADO = 'APROBADO';
    case RECHAZADO = 'RECHAZADO';
    case SUBSANACION = 'SUBSANACION';
    case CANCELADO = 'CANCELADO';

    public function label(): string
    {
        return match ($this) {
            self::BORRADOR => 'Borrador',
            self::RADICADO => 'Radicado',
            self::PENDIENTE => 'Pendiente',
            self::REVISION => 'En Revisión',
            self::EVALUACION => 'En Evaluación',
            self::APROBADO => 'Aprobado',
            self::RECHAZADO => 'Rechazado',
            self::SUBSANACION => 'Subsanación',
            self::CANCELADO => 'Cancelado',
        };
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::APROBADO, self::RECHAZADO, self::CANCELADO => true,
            default => false,
        };
    }

    public static function activeStatuses(): array
    {
        return [
            self::BORRADOR,
            self::RADICADO,
            self::PENDIENTE,
            self::REVISION,
            self::EVALUACION,
            self::SUBSANACION,
        ];
    }
}
