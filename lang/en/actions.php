<?php

declare(strict_types=1);

return [
    // Generic actions
    'back' => 'Back',
    'back_top' => 'Back to top',

    // Auth
    'sign_in' => 'Sign in',
    'sign_out' => 'Sign out',
    'forgot_password' => 'Forgot your password?',
    'show_password' => 'Show password',
    'hide_password' => 'Hide password',

    // CRUD - buttons
    'create' => 'Create',
    'edit' => 'Edit',
    'show' => 'View',
    'delete' => 'Delete',
    'manage_cvs' => 'Manage résumés',
    'save' => 'Save',
    'accept' => 'Accept',
    'cancel' => 'Cancel',
    'close' => 'Close',
    'export' => 'Export',
    'filter' => 'Filters',
    'filter_submit' => 'Search',
    'clear_filters' => 'Clear filters',
    'clear_ordenacion' => 'Clear sort',

    // Generic delete confirmation modal
    'delete_confirm_title' => 'Do you want to delete this record?',
    'delete_confirm_description' => 'The record will be deleted',

    // Flash messages (English does not inflect for gender; both variants are identical for key parity)
    'created' => '{1} :modelo created successfully.|{2} :modelo created successfully.',
    'updated' => '{1} :modelo updated successfully.|{2} :modelo updated successfully.',
    'deleted' => '{1} :modelo deleted successfully.|{2} :modelo deleted successfully.',
    'restored' => '{1} :modelo restored successfully.|{2} :modelo restored successfully.',

    // Generic error flash when an operation fails
    'generic_error' => 'An error occurred while processing the operation. Please try again.',

    // Configuration section flashes
    'settings_saved' => 'The settings have been saved successfully.',
    'cache_cleared' => 'The cache has been cleared successfully.',
    'views_cleared' => 'The compiled views have been removed successfully.',
    'maintenance_on' => 'The application is now in maintenance mode. You keep access to the panel.',
    'maintenance_off' => 'The application is up and running again.',
];
