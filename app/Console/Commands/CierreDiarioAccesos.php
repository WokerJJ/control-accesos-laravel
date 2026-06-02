<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Admin\AccesoAdminService;

class CierreDiarioAccesos extends Command
{
    protected $signature = 'accesos:cierre-diario';

    protected $description =
        'Cierra accesos abiertos y libera casilleros';

    public function handle(
        AccesoAdminService $service
    ): int {

        $total = $service->cierreDiario();

        $this->info(
            "Proceso completado. {$total} accesos cerrados."
        );

        return self::SUCCESS;
    }
}
