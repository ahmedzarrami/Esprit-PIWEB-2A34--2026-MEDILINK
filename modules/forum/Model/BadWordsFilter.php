<?php
/**
 * BadWordsFilter — Filtre de mots injurieux (Métier Avancé)
 * Censure les mots vulgaires/injurieux en les remplaçant par des étoiles '*'
 * Règle : 1 caractère = 1 étoile (ex: "idiot" → "*****")
 */
class BadWordsFilter {

    /**
     * Liste des mots injurieux / vulgaires en français
     * @var array
     */
    private static array $badWords = [
        // Insultes courantes
        'connard', 'connasse', 'con', 'conne',
        'idiot', 'idiote', 'imbécile', 'imbecile',
        'abruti', 'abrutie', 'crétin', 'crétine', 'cretin', 'cretine',
        'débile', 'debile',
        'enculé', 'encule', 'enculer',
        'salaud', 'salope', 'salop',
        'putain', 'pute', 'prostituée',
        'merde', 'merdeux', 'merdeuse', 'merdique',
        'bordel',
        'foutre', 'fouteur', 'fouteuse',
        'nique', 'niquer', 'niqué',
        'batard', 'bâtard', 'batarde', 'bâtarde',
        'ordure', 'ordures',
        'pourriture', 'pourri', 'pourrie',
        'enfoiré', 'enfoire', 'enfoirée',
        'fumier',
        'salopard', 'saloperie',
        'dégueulasse', 'degueulasse',
        'branleur', 'branleuse', 'branleur',
        'couillon', 'couillonne',
        'bouffon', 'bouffonne',
        'trouduc', 'trou du cul',
        'chier', 'chieur', 'chieuse',
        'gueuler', 'gueule', 'ta gueule',
        'ferme-la', 'ferme la',
        'dégage', 'degage',
        'cul', 'culotte',
        'emmerdeur', 'emmerdeuse', 'emmerder',
        'pétasse', 'petasse',
        'gogol', 'mongol',
        'taré', 'tarée', 'tare',
        'nul', 'nulle', 'nullard',
        'minable',
        'pouffiasse', 'poufiasse',
        'raclure',
        'charogne',
        'crevard', 'crevarde',
        'déchet', 'dechet',
        'vermine',
        'parasite',
    ];

    /**
     * Filtrer un texte en censurant les mots injurieux
     * Chaque caractère du mot interdit est remplacé par '*'
     * Insensible à la casse, gère les accents
     *
     * @param string $text Le texte à filtrer
     * @return string Le texte censuré
     */
    public static function filter(string $text): string {
        if (empty($text)) {
            return $text;
        }

        foreach (self::$badWords as $badWord) {
            // Échapper les caractères spéciaux regex
            $escaped = preg_quote($badWord, '/');
            // Remplacer le mot par des étoiles (même nombre de caractères)
            $text = preg_replace_callback(
                '/\b' . $escaped . '\b/iu',
                function ($matches) {
                    return str_repeat('*', mb_strlen($matches[0]));
                },
                $text
            );
        }

        return $text;
    }

    /**
     * Vérifier si un texte contient des mots injurieux
     *
     * @param string $text Le texte à vérifier
     * @return bool true si le texte contient des mots interdits
     */
    public static function containsBadWords(string $text): bool {
        return self::filter($text) !== $text;
    }

    /**
     * Obtenir la liste des mots injurieux détectés dans un texte
     *
     * @param string $text Le texte à analyser
     * @return array Liste des mots trouvés
     */
    public static function detectBadWords(string $text): array {
        $found = [];
        foreach (self::$badWords as $badWord) {
            $escaped = preg_quote($badWord, '/');
            if (preg_match('/\b' . $escaped . '\b/iu', $text)) {
                $found[] = $badWord;
            }
        }
        return $found;
    }
}
