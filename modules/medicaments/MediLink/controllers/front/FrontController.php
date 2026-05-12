<?php

declare(strict_types=1);

require_once __DIR__ . '/../Controller.php';

abstract class FrontController extends Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../views/front/' . $view . '.php';
    }
}
