<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Departamento
 *
 * @property int    $id
 * @property string $nombre
 */
class Departamento extends Model
{
    protected $table = 'departamento';

    protected $fillable = ['nombre'];

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class);
    }
}
