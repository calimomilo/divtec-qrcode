<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qrcode extends Model
{

    public $modelNotFoundMessage = "qrcode not found";

    use HasFactory;

    /**
     * Validation des données
     * @return string[] qui contient les règles des propriétés
     */
    static function validateRules()
    {
        return [
            'nom'        => 'required|string',
            'image'      => 'required|string',
            'code_id'    => 'required|string',
            'lien_redir' => 'required|string'
        ];
    }

    public function stats()
    {
        return $this->hasMany(Stat::class);
    }

    /**
     * Liste des attributs modifiables
     *
     * @var array
     */
    protected $fillable = [
        'nom',
        'image',
        'code_id',
        'lien_redir',
        'utilisateur_id'
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
