<?php

namespace App\Twig;

use App\Controller\AuthorizationController;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AuthorizationExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_authorized', [AuthorizationController::class, 'isAuthorized']),
        ];
    }
}
