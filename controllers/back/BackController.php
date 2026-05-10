<?php

declare(strict_types=1);

require_once __DIR__ . '/../Controller.php';

abstract class BackController extends Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../views/back/layouts/header.php';
        require __DIR__ . '/../../views/back/' . $view . '.php';
        require __DIR__ . '/../../views/back/layouts/footer.php';
    }
}
