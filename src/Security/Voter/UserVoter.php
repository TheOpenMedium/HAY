<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Yaml\Yaml;

class UserVoter extends Voter
{
    private $security;
    private $parameters;

    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->parameters = Yaml::parseFile(__dir__.'/../../../config/config.yaml')['parameters']['authorizations']['user'];
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['user.submit', 'user.submit_with_review',
            'user.edit', 'user.edit_with_review', 'user.edit_other', 'user.edit_other_with_review',
            'user.delete', 'user.delete_with_review', 'user.delete_other', 'user.delete_other_with_review',
            'user.friend_request', 'user.review', 'user.review_other']);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!($user instanceof User)) {
            $user = new User();
        }

        if (!($subject instanceof User)) {
            $subject = $user;
        }

        switch ($attribute) {
            case 'user.submit':
                return $this->canSubmit($user, $subject);
                break;
            case 'user.submit_with_review':
                return $this->canSubmit($user, $subject, true);
                break;
            case ('user.edit' || 'user.edit_other'):
                return $this->canEdit($user, $subject);
                break;
            case ('user.edit_with_review' || 'user.edit_other_with_review'):
                return $this->canEdit($user, $subject, true);
                break;
            case ('user.delete' || 'user.delete_other'):
                return $this->canDelete($user, $subject);
                break;
            case ('user.delete_with_review' || 'user.delete_other_with_review'):
                return $this->canDelete($user, $subject, true);
                break;
            case 'user.friend_request':
                return $this->canFriendRequest($user, $subject);
                break;
            case 'user.review':
                return $this->canReview($user, $subject);
                break;
            case 'user.review_other':
                return $this->canReviewOther($user, $subject);
                break;
        }

        return false;
    }

    private function canSubmit(User $user, User $subject, bool $with_review = false)
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

    private function canEdit(User $user, User $subject, bool $with_review = false)
    {
        if ($user == $subject) {
            if (!$with_review) {
                if ($this->parameters['edit'] == 'ALL') {
                    return true;
                } elseif ($this->parameters['edit'] == 'NONE') {
                    return false;
                } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
                    return true;
                }

                if ($this->security->isGranted($this->parameters['edit'])) {
                    return true;
                }
            } else {
                if ($this->parameters['edit_with_review'] == 'ALL') {
                    return true;
                } elseif ($this->parameters['edit_with_review'] == 'NONE') {
                    return false;
                } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
                    return true;
                }

                if ($this->security->isGranted($this->parameters['edit_with_review'])) {
                    return true;
                }
            }
        } elseif ($user != $subject) {
            foreach ($subject->getRoles() as $role) {
                if (!$this->security->isGranted($role) || \in_array($role, $user->getRoles())) {
                    return false;
                }
            }

            if (!$with_review) {
                if ($this->parameters['edit_other'] == 'ALL') {
                    return true;
                } elseif ($this->parameters['edit_other'] == 'NONE') {
                    return false;
                } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
                    return true;
                }

                if ($this->security->isGranted($this->parameters['edit_other'])) {
                    return true;
                }
            } else {
                if ($this->parameters['edit_other_with_review'] == 'ALL') {
                    return true;
                } elseif ($this->parameters['edit_other_with_review'] == 'NONE') {
                    return false;
                } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
                    return true;
                }

                if ($this->security->isGranted($this->parameters['edit_other_with_review'])) {
                    return true;
                }
            }
        }

        return false;
    }

    private function canDelete(User $user, User $subject, bool $with_review = false)
    {
        if ($user == $subject) {
            if (!$with_review) {
                if ($this->parameters['delete'] == 'ALL') {
                    return true;
                } elseif ($this->parameters['delete'] == 'NONE') {
                    return false;
                } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
                    return true;
                }

                if ($this->security->isGranted($this->parameters['edit'])) {
                    return true;
                }
            } else {
                if ($this->parameters['delete_with_review'] == 'ALL') {
                    return true;
                } elseif ($this->parameters['delete_with_review'] == 'NONE') {
                    return false;
                } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
                    return true;
                }

                if ($this->security->isGranted($this->parameters['delete_with_review'])) {
                    return true;
                }
            }
        } elseif ($user != $subject) {
            foreach ($subject->getRoles() as $role) {
                if (!$this->security->isGranted($role) || \in_array($role, $user->getRoles())) {
                    return false;
                }
            }

            if (!$with_review) {
                if ($this->parameters['delete_other'] == 'ALL') {
                    return true;
                } elseif ($this->parameters['delete_other'] == 'NONE') {
                    return false;
                } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
                    return true;
                }

                if ($this->security->isGranted($this->parameters['delete_other'])) {
                    return true;
                }
            } else {
                if ($this->parameters['delete_other_with_review'] == 'ALL') {
                    return true;
                } elseif ($this->parameters['delete_other_with_review'] == 'NONE') {
                    return false;
                } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
                    return true;
                }

                if ($this->security->isGranted($this->parameters['delete_other_with_review'])) {
                    return true;
                }
            }
        }

        return false;
    }

    private function canFriendRequest(User $user, User $subject)
    {
        if ($this->parameters['friend_request'] == 'ALL') {
            return true;
        } elseif ($this->parameters['friend_request'] == 'NONE') {
            return false;
        } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
            return true;
        }

        if ($this->security->isGranted($this->parameters['friend_request'])) {
            return true;
        }

        return false;
    }

    private function canReview(User $user, User $subject)
    {
        foreach ($subject->getRoles() as $role) {
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

    private function canReviewOther(User $user, User $subject)
    {
        foreach ($subject->getRoles() as $role) {
            if (!$this->security->isGranted($role) || \in_array($role, $user->getRoles())) {
                return false;
            }
        }
        
        if ($this->parameters['review_other'] == 'ALL') {
            return true;
        } elseif ($this->parameters['review_other'] == 'NONE') {
            return false;
        } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
            return true;
        }
        
        if ($this->security->isGranted($this->parameters['review_other'])) {
            return true;
        }

        return false;
    }
}
