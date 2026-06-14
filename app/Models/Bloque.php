<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BloqueTipoEnum;
use App\Traits\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Table('bloques')]
#[Fillable(['pagina_id', 'tipo', 'orden', 'campos'])]
// El contenido del bloque se traduce con Translatable de spatie: se guarda un bloque de campos por idioma ({es:{...}, en:{...}, ja:{...}})
#[Translatable(['campos'])]
class Bloque extends Model implements HasMedia
{
    use HasFactory, HasPublicUlid, HasTranslations, InteractsWithMedia, SoftDeletes;

    /** Colección de medialibrary para la imagen única de un bloque */
    public const MEDIA_COLLECTION_IMAGEN = 'imagen';

    /** Colección de medialibrary para el icono de un bloque */
    public const MEDIA_COLLECTION_ICONO = 'icono';

    /** Colección de medialibrary para la galería de imágenes de un bloque (SWIPER) */
    public const MEDIA_COLLECTION_GALLERY = 'gallery';

    /** Colección de medialibrary para los elementos imagen+etiqueta del carrusel giratorio */
    public const MEDIA_COLLECTION_CARRUSEL = 'carrusel';

    /**
     * Tipos de los atributos del modelo (tipo como enum; campos lo castea Translatable a array con el mapa {idioma: campos}).
     */
    protected function casts(): array
    {
        return [
            'tipo' => BloqueTipoEnum::class,
        ];
    }

    /**
     * Registra las colecciones de imágenes: imagen e icono únicos (singleFile) y la galería y el carrusel múltiples.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_IMAGEN)->singleFile();
        $this->addMediaCollection(self::MEDIA_COLLECTION_ICONO)->singleFile();
        $this->addMediaCollection(self::MEDIA_COLLECTION_GALLERY);
        $this->addMediaCollection(self::MEDIA_COLLECTION_CARRUSEL);
    }

    /**
     * Devuelve la URL de la imagen única del bloque, o null si no tiene ninguna.
     */
    public function imagenUrl(): ?string
    {
        return $this->getFirstMediaUrl(self::MEDIA_COLLECTION_IMAGEN) ?: null;
    }

    /**
     * Devuelve la URL del icono (imagen) del bloque, o null si no tiene ninguno.
     */
    public function iconoUrl(): ?string
    {
        return $this->getFirstMediaUrl(self::MEDIA_COLLECTION_ICONO) ?: null;
    }

    /**
     * Devuelve las imágenes de la galería ordenadas (cada Media lleva su alt traducible en custom properties).
     */
    public function galeria(): MediaCollection
    {
        return $this->getMedia(self::MEDIA_COLLECTION_GALLERY);
    }

    /**
     * Devuelve los elementos del carrusel ordenados (cada Media lleva su etiqueta traducible en custom properties).
     */
    public function itemsCarrusel(): MediaCollection
    {
        return $this->getMedia(self::MEDIA_COLLECTION_CARRUSEL);
    }

    /**
     * Relación con la página. Un bloque solo pertenece a una página
     */
    public function pagina(): BelongsTo
    {
        return $this->belongsTo(Pagina::class);
    }
}
