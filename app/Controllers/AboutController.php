<?php
declare(strict_types=1);

namespace App\Controllers;

final class AboutController
{
    public function index(): void
    {
        require dirname(__DIR__) . '/Views/customer/about.php';
    }
}
