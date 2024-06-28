<?php

namespace Database\Factories;

use App\Models\Qrcode;
use Illuminate\Database\Eloquent\Factories\Factory;


class QrcodeFactory extends Factory
{
    /**
     * Le nom du modèle correspondant.
     *
     * @var string
     */
    protected $model = Qrcode::class;

    /**
     * Définir l'état par défaut du modèle.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nom'        => $this->faker->sentence,
            'image'      => $this->faker->imageUrl,
            'code_id'    => $this->faker->uuid,
            'lien_redir' => $this->faker->url,
        ];
    }
}
