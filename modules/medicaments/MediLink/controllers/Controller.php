<?php

declare(strict_types=1);

abstract class Controller
{
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
