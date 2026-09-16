<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChoiceEnum;
use App\Traits\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Table('usuarios_cvs_secciones')]
#[Fillable(['usuario_cv_id', 'titulo', 'descripcion', 'orden', 'sangria'])]
class UsuarioCvSeccion extends Model implements HasMedia
{
    use HasFactory, HasPublicUlid, InteractsWithMedia, SoftDeletes;

    // Usado por trans_choice en mensajes con :modelo (la sección → "creada")
    public const ChoiceEnum CHOICE = ChoiceEnum::FEMENINO;

    /** Colección de medialibrary para la galería de imágenes de la sección (varias imágenes, sin singleFile) */
    public const MEDIA_COLLECTION_GALLERY = 'gallery';

    /**
     * Ordena por id descendente por defecto en todas las queries del modelo (desempate cuando no se ordena explícitamente por 'orden').
     */
    protected static function booted(): void
    {
        static::addGlobalScope('ordenPorId', fn (Builder $query) => $query->orderByDesc('id'));
    }

    /**
     * Casts de los atributos del modelo.
     */
    protected function casts(): array
    {
        return [
            'sangria' => 'boolean',
        ];
    }

    /**
     * Registra la colección de medialibrary de la galería, admite varias imágenes por sección.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_GALLERY);
    }

    /**
     * Registra la conversión "pdf": miniatura en jpg (máxima compatibilidad con dompdf) que sustituye al original al mostrar la galería en el PDF del CV.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('pdf')
            ->fit(Fit::Contain, 480, 480)
            ->format('jpg')
            ->performOnCollections(self::MEDIA_COLLECTION_GALLERY);
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
