<?php

namespace App\Enums;

/**
 * Usado por trans_choice para mensajes con :modelo.
 */
enum ChoiceEnum: int
{
    // Masculino: "Usuario creado correctamente."
    case MASCULINO = 1;

        // Femenino: "Categoría creada correctamente."
    case FEMENINO = 2;
}
