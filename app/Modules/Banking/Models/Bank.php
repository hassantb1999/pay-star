<?php

namespace App\Modules\Banking\Models;

class Bank
{
    const PARSAIN = 'parsain';
    const AYANDE = 'ayande';
    const KESHAVARZI = 'keshavarzi';

    public static function getBanks(): array
    {
        return [
            self::PARSAIN,
            self::AYANDE,
            self::KESHAVARZI
        ];
    }
}
