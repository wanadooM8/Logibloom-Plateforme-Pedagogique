<?php

namespace Database\Factories;

use App\Models\Professeur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Professeur>
 */
class ProfesseurFactory extends Factory
{
    /**
     * The model the factory corresponds to.
     *
     * @var class-string<Professeur>
     */
    protected $model = Professeur::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $motDePasse;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->phoneNumber(),
            'mot_de_passe' => static::$motDePasse ??= Hash::make('password'),
        ];
    }
}
