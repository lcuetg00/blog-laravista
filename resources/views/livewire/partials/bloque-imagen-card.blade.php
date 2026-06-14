{{-- Tarjeta de una imagen de bloque: imagen redondeada con franja inferior (secondary) que muestra el nombre del fichero y el botón que fija en Alpine el uuid/nombre y abre el modal de borrado compartido del bloque. Parámetros: $media, $modalBorrar (id del modal del bloque) --}}
<figure class="bloque-imagen-card rounded overflow-hidden border shadow mb-0">
    <img src="{{ $media->getUrl() }}" alt="" class="bloque-imagen-card-img" loading="lazy">
    <figcaption class="bloque-imagen-card-franja d-flex justify-content-between align-items-center gap-2 px-2 py-1">
        <span class="bloque-imagen-card-nombre text-white small" title="{{ $media->file_name }}">{{ $media->file_name }}</span>
        <button type="button" class="btn btn-sm btn-terciary flex-shrink-0" data-bs-toggle="modal"
            data-bs-target="#{{ $modalBorrar }}"
            x-on:click="borrarUuid = @js($media->uuid); borrarNombre = @js($media->file_name)"
            aria-label="{{ trans('fields.bloques.imagenes.borrar') }}">
            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
        </button>
    </figcaption>
</figure>
