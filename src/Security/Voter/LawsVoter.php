<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Laws;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Yaml\Yaml;

class LawsVoter extends Voter
{
    private $security;
    private $parameters;

    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->parameters = Yaml::parseFile(__dir__.'/../../../config/config.yaml')['parameters']['authorizations']['laws'];
    }
    
    protected function supports($attribute, $laws)
    {
        return in_array($attribute, ['laws.submit', 'laws.submit_with_review', 'laws.call_vote', 'laws.review']);
    }

    protected function voteOnAttribute($attribute, $laws, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!($user instanceof User)) {
            $user = new User();
        }

        if (!($laws instanceof Laws)) {
            $laws = new Laws();
            $laws->setUser($user);
        }

        switch ($attribute) {
            case 'laws.submit':
                $this->canSubmit($user, $laws);
                break;
            case 'laws.submit_with_review':
                $this->canSubmit($user, $laws, true);
                break;
            case 'laws.call_vote':
                $this->canCallVote($user, $laws);
                break;
            case 'laws.review':
                $this->canReview($user, $laws);
                break;
        }

        return false;
    }

    private function canSubmit(User $user, Laws $laws, bool $with_review = false)
    {
        if (!$with_review) {
            if ($this->parameters['submit'] == 'ALL') {
                return true;
            } elseif ($this->parameters['submit'] == 'NONE') {
                return false;
            } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
                return true;
            }

            if ($this->security->isGranted($this->parameters['submit'])) {
                return true;
            }
        } else {
            if ($this->parameters['submit_with_review'] == 'ALL') {
                return true;
            } elseif ($this->parameters['submit_with_review'] == 'NONE') {
                return false;
            } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
                return true;
            }

            if ($this->security->isGranted($this->parameters['submit_with_review'])) {
                return true;
            }
        }

        return false;
    }

    private function canCallVote(User $user, Laws $laws)
    {
        if ($this->parameters['call_vote'] == 'ALL') {
            return true;
        } elseif ($this->parameters['call_vote'] == 'NONE') {
            return false;
        } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
            return true;
        }
        
        if ($this->security->isGranted($this->parameters['call_vote'])) {
            return true;
        }

        return false;
    }

    private function canReview(User $user, Laws $laws)
    {
        foreach ($laws->getUser()->getRoles() as $role) {
            if (!$this->security->isGranted($role) || \in_array($role, $user->getRoles())) {
                return false;
            }
        }

        if ($this->parameters['review'] == 'ALL') {
            return true;
        } elseif ($this->parameters['review'] == 'NONE') {
            return false;
        } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
            return true;
        }
        
        if ($this->security->isGranted($this->parameters['review'])) {
            return true;
        }

        return false;
    }
}
