<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ASEG = 'ASEG';
    case OPER = 'OPER';
    case SUPV = 'SUPV';
    case GESDOC = 'GESDOC';
    case SADM = 'SADM';

    public function label(): string
    {
        return match ($this) {
            self::ASEG => 'Asegurado',
            self::OPER => 'Operador',
            self::SUPV => 'Supervisor',
            self::GESDOC => 'Gestor Documental',
            self::SADM => 'Super Administrador',
        };
    }
}
