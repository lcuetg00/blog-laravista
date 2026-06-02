<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\ChoiceEnum;
use App\Traits\HasPublicUlid;
use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Table('usuarios')]
#[Hidden(['password', 'remember_token'])]
#[Fillable(['nombre', 'primer_apellido', 'segundo_apellido', 'email', 'password'])]
class Usuario extends Authenticatable
{
    /** @use HasFactory<UsuarioFactory> */
    use HasFactory, HasPublicUlid, HasRoles, Notifiable, SoftDeletes;

    // Usado por trans_choice en mensajes con :modelo
    public const ChoiceEnum CHOICE = ChoiceEnum::MASCULINO;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Devuelve el nombre completo del usuario uniendo nombre y apellidos, sin espacios sobrantes.
     */
    protected function nombreCompleto(): Attribute
    {
        return Attribute::make(
            get: fn (): string => trim(implode(' ', array_filter([
                $this->nombre,
                $this->primer_apellido,
                $this->segundo_apellido,
            ]))),
        );
    }
}
