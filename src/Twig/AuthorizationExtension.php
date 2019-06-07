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
        if ($user_roles != NULL) {
            $user_roles = $user_roles->getRoles();
        } else {
            $user_roles = ["ROLE_ANONYMOUS"];
        }
        if (in_array("ROLE_OWNER", $user_roles)) {
            return True;
        }
        $yaml = Yaml::parseFile(__dir__.'/../../config/authorizations.yaml');
        $role_hierarchy = Yaml::parseFile(__dir__.'/../../config/packages/security.yaml')['security']['role_hierarchy'];
        $next = $user_roles;
        $roles = [];
        while (isset($next[0])) {
            if (!in_array($next[0], $roles)) {
                $roles[] = $next[0];
                if (isset($role_hierarchy[$next[0]])) {
                    $next_role = (gettype($role_hierarchy[$next[0]]) == 'array') ? $role_hierarchy[$next[0]] : [$role_hierarchy[$next[0]]];
                    $next = \array_merge($next, $next_role);
                }
            }
            array_splice($next, 0, 1);
        }
        if ($max_iteration != NULL) {
            \array_splice($roles, $max_iteration);
        }
        foreach ($roles as $role) {
            if (isset($yaml[$role][$category][$authorization])) {
                // var == var
                if ($yaml[$role][$category][$authorization] == $value) {
                    return True;
                }
                // [var, foo] == var
                if (gettype($yaml[$role][$category][$authorization]) == 'array' && gettype($value) == 'string') {
                    if (in_array($value, $yaml[$role][$category][$authorization])) {
                        return True;
                    }
                }
                // var == [var, bar]
                if (gettype($yaml[$role][$category][$authorization]) == 'string' && gettype($value) == 'array') {
                    if (in_array($yaml[$role][$category][$authorization], $value)) {
                        return True;
                    }
                }
                // [var, foo] == [var, bar]
                if (gettype($yaml[$role][$category][$authorization]) == 'array' && gettype($value) == 'array') {
                    if (!empty(array_intersect($yaml[$role][$category][$authorization], $value))) {
                        return True;
                    }
                }
            }
        }
        return False;
    }
}
