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
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

#[Table('usuarios_cvs_secciones')]
#[Fillable(['usuario_cv_id', 'titulo', 'descripcion', 'orden'])]
class UsuarioCvSeccion extends Model implements HasMedia
{
    use HasFactory, HasPublicUlid, InteractsWithMedia, SoftDeletes;

    // Usado por trans_choice en mensajes con :modelo (la sección → "creada")
    public const ChoiceEnum CHOICE = ChoiceEnum::FEMENINO;

    /** Colección de medialibrary para la galería de imágenes de la sección (varias imágenes, sin singleFile) */
    public const MEDIA_COLLECTION_GALLERY = 'gallery';

    /**
     * Registra la colección de medialibrary de la galería, admite varias imágenes por sección.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_GALLERY);
    }

    /**
     * Devuelve las imágenes de la galería de la sección.
     */
    public function galeria(): MediaCollection
    {
        return $this->getMedia(self::MEDIA_COLLECTION_GALLERY);
    }

    /**
     * Relación con el CV al que pertenece la sección.
     */
    public function usuarioCv(): BelongsTo
    {
        return $this->belongsTo(UsuarioCv::class);
    }
}
