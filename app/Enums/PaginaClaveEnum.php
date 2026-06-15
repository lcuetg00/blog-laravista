<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Catálogo de las claves estables de las páginas públicas (enlazan la fila de paginas con su vista). El valor string se persiste en paginas.clave.
 */
enum PaginaClaveEnum: string
{
    case HOME = 'home';

    case CREDITOS = 'creditos';

    case TECNOLOGIAS = 'tecnologias';

    case PROYECTOS = 'proyectos';

    case CONTACTO = 'contacto';

    case POLITICA_PRIVACIDAD = 'politica-privacidad';

    case TERMINOS_CONDICIONES = 'terminos-condiciones';

    /**
     * Indica si la página puede desactivarse desde el panel (el home debe estar siempre accesible, el resto sí se puede desactivar).
     */
    public function esDesactivable(): bool
    {
        return $this !== self::HOME;
    }
}
