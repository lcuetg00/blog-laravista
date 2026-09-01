<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChoiceEnum;
use App\Traits\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table('usuarios_cvs')]
#[Fillable(['usuario_id', 'nombre'])]
class UsuarioCv extends Model
{
    use HasFactory, HasPublicUlid, SoftDeletes;

    // Usado por trans_choice en mensajes con :modelo (el CV → "creado")
    public const ChoiceEnum CHOICE = ChoiceEnum::MASCULINO;

    /**
     * Relación con el usuario propietario del CV.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relación con las secciones del CV, ordenadas por su posición.
     */
    public function secciones(): HasMany
    {
        return $this->hasMany(UsuarioCvSeccion::class)->orderBy('orden');
    }
}
