<?php

namespace App\Models;

use Database\Factories\ProfesseurFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id_prof
 * @property string $nom
 * @property string $prenom
 * @property string $email
 * @property string|null $telephone
 * @property string $mot_de_passe
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['nom', 'prenom', 'email', 'telephone', 'mot_de_passe'])]
#[Hidden(['mot_de_passe'])]
class Professeur extends Model
{
    /** @use HasFactory<ProfesseurFactory> */
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_prof';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mot_de_passe' => 'hashed',
        ];
    }
}
