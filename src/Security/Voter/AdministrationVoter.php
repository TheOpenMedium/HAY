<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Yaml\Yaml;

class AdministrationVoter extends Voter
{
    private $security;
    private $parameters;
    private $slug = NULL;

    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->parameters = Yaml::parseFile(__dir__.'/../../../config/config.yaml')['parameters']['authorizations']['administration'];
    }
    
    protected function supports($attribute, $subject)
    {
        if ($attribute == 'administration.access' && $subject == NULL) {
            return true;
        }

        if ($attribute == 'administration.accessible' && gettype($subject) == 'string') {
            if ($this->parameters['accessible'][$subject]) {
                $this->slug = $subject;
                return true;
            } else {
                return false;
            }
        }

        if (substr($attribute, 0, 25) == 'administration.accessible' && $subject == NULL) {
            if ($this->parameters['accessible'][substr($attribute, 26)]) {
                $this->slug = substr($attribute, 26);
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!($user instanceof User)) {
            $user = new User();
        }

        if ($attribute == 'administration.access') {
            return $this->canAccess($user);
        }

        if ((substr($attribute, 0, 25) == 'administration.accessible') || ($attribute == 'administration.accessible')) {
            return $this->isAccessible($user);
        }

        return false;
    }

    private function canAccess(User $user)
    {
        if ($this->parameters['access'] == 'ALL') {
            return true;
        } elseif ($this->parameters['access'] == 'NONE') {
            return false;
        } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
            return true;
        }
        
        if ($this->security->isGranted($this->parameters['access'])) {
            return true;
        }

        return false;
    }

    private function isAccessible(User $user)
    {
        if ($this->parameters['accessible'][$this->slug] == 'ALL') {
            return true;
        } elseif ($this->parameters['accessible'][$this->slug] == 'NONE') {
            return false;
        } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
            return true;
        }
        
        if ($this->security->isGranted($this->parameters['accessible'][$this->slug])) {
            return true;
        }

        return false;
    }
}
