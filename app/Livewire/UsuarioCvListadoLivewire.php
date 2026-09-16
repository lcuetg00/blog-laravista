<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\UsuarioCvOrdenacionEnum;
use App\Livewire\Concerns\LivewireOrdenacionTrait;
use App\Models\Usuario;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class UsuarioCvListadoLivewire extends Component
{
    use LivewireOrdenacionTrait;
    use WithPagination;

    /** Usuario cuyos CVs se listan */
    #[Locked]
    public Usuario $usuario;

    /**
     * Inicializa el componente con el usuario indicado.
     */
    public function mount(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    /**
     * Renderiza el listado paginado de CVs del usuario
     */
    public function render(): View
    {
        return view('livewire.usuario-cv-listado-livewire', [
            'cvs' => $this->usuario->usuariosCvs()
                ->byOrdenacion($this->ordenacion)
                ->paginate(10),
        ]);
    }

    /**
     * Enum con las claves de ordenación válidas para el listado de CVs.
     * Esto es para el trait de ordenación, para que compruebe que es uno de los valores
     * por los que se puede ordenar
     */
    private function ordenacionEnum(): string
    {
        return UsuarioCvOrdenacionEnum::class;
    }

    /**
     * Forzamos un nuevo render tras guardar/eliminar un CV o sus secciones en los modales hijos (los datos se recalculan en render()).
     */
    #[On('cv-guardado')]
    #[On('cv-eliminado')]
    #[On('secciones-actualizadas')]
    public function refrescar(): void
    {
        //
    }
}
