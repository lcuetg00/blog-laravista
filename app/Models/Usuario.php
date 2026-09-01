<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\ChoiceEnum;
use App\Enums\OrdenacionColumnaEnum;
use App\Enums\UsuarioOrdenacionEnum;
use App\Traits\HasPublicUlid;
use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

#[Table('usuarios')]
#[Hidden(['password', 'remember_token'])]
#[Fillable(['nombre', 'primer_apellido', 'segundo_apellido', 'email', 'password'])]
class Usuario extends Authenticatable implements HasMedia
{
    /** @use HasFactory<UsuarioFactory> */
    use HasFactory, HasPublicUlid, HasRoles, InteractsWithMedia, Notifiable, SoftDeletes;

    // Usado por trans_choice en mensajes con :modelo
    public const ChoiceEnum CHOICE = ChoiceEnum::MASCULINO;

    /** Colección de medialibrary para la imagen de perfil (avatar) del usuario */
    public const MEDIA_COLLECTION_AVATAR = 'avatar';

    /**
     * Registra la colección de medialibrary del avatar, único fichero que reemplaza al anterior al subir uno nuevo.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_AVATAR)->singleFile();
    }

    /**
     * Devuelve la URL del avatar del usuario, o null si todavía no ha subido ninguno.
     */
    public function avatarUrl(): ?string
    {
        return $this->getFirstMediaUrl(self::MEDIA_COLLECTION_AVATAR) ?: null;
    }

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

    /**
     * Filtra por coincidencia parcial sobre el nombre completo concatenando nombre y apellidos con CONCAT_WS para tolerar apellidos nulos.
     */
    public function scopeByNombreCompleto(Builder $query, ?string $valor): Builder
    {
        // Si no llega valor o llega en blanco no filtramos
        if ($valor === null || trim($valor) === '') {
            return $query;
        }

        $busqueda = '%' . trim($valor) . '%';

        // Utilizado ese whereRaw para poder hacer la búsqueda con los 3 campos juntos
        // Eloquent con whereRaw previene de inyección y además tiene la consulta parametrizada
        // Por si acaso siempre que se pase algo, es conveniente filtrar los símbolos que se utilizan, esto se hace siempre en las requests
        return $query->whereRaw(
            "CONCAT_WS(' ', nombre, primer_apellido, segundo_apellido) LIKE ?",
            [$busqueda],
        );
    }

    /**
     * Filtra por coincidencia parcial sobre el email del usuario.
     */
    public function scopeByEmail(Builder $query, ?string $valor): Builder
    {
        // Si no llega valor o llega en blanco no filtramos
        if ($valor === null || trim($valor) === '') {
            return $query;
        }

        return $query->where('email', 'like', '%' . trim($valor) . '%');
    }

    /**
     * Aplica ORDER BY encadenados respetando el orden de las claves del array (clave URL → dirección).
     *
     * @param  array<string, string>  $ordenacion
     */
    public function scopeByOrdenacion(Builder $query, array $ordenacion): Builder
    {
        // Recorremos las claves en su orden de inserción para preservar la prioridad de la ordenación
        foreach ($ordenacion as $clave => $direccion) {
            $caso = UsuarioOrdenacionEnum::tryFrom((string) $clave);
            $dir = OrdenacionColumnaEnum::tryFrom((string) $direccion);

            // Si la clave o la dirección no son válidas saltamos esa entrada (defensa adicional sobre el FormRequest)
            if ($caso === null || $dir === null) {
                continue;
            }

            $query->orderBy($caso->getNombreColumna(), $dir->value);
        }

        return $query;
    }
}
