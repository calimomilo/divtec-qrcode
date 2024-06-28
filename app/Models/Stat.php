<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{

    static function validateRules()
    {
        return ['qrcode_id' => 'required'];
    }
    /**
     * Liste des attributs modifiables
     *
     * @var array
     */
    protected $fillable = [
        'qrcode_id'
    ];

    /**
     * Liste des attributs cachés
     * Seront exclus dans les réponses
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

}
