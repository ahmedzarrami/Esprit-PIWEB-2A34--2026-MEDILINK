<?php
/**
 * Configuration de l'IA pour MediLink
 */
return [
    'openrouter_api_key' => $_ENV['OPENROUTER_API_KEY'] ?? getenv('OPENROUTER_API_KEY') ?? ''
];
