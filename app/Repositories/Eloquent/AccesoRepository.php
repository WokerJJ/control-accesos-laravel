<?php

namespace App\Repositories\Eloquent;

use App\Models\Acceso;
use App\Repositories\AccesoRepositoryInterface;
use TimWassenburg\RepositoryGenerator\Repository\BaseRepository;

/**
 * Class AccesoRepository.
 */
class AccesoRepository extends BaseRepository implements AccesoRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param Acceso $model
     */
    public function __construct(Acceso $model)
    {
        parent::__construct($model);
    }
}
