<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\Yaml\Yaml;

class AuthorizationExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_authorized', [$this, 'isAuthorized']),
        ];
    }

    public function isAuthorized($user_roles, $category, $authorization, $value = True, $max_iteration = NULL)
    {
        return true;
    }
}
