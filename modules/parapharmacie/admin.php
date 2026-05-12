<?php
/**
 * Parapharmacie - point d'entree admin.
 * Gate role + delegue a la vue back office.
 */
require_once __DIR__ . '/../../config/session.php';
require_role('Administrateur');

require __DIR__ . '/view/back/admin.php';
