<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Yaml\Yaml;

class CommentVoter extends Voter
{
    private $security;
    private $parameters;

    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->parameters = Yaml::parseFile(__dir__.'/../../../config/config.yaml')['parameters']['authorizations']['comment'];
    }
    
    protected function supports($attribute, $comment)
    {
        return in_array($attribute, ['comment.submit', 'comment.submit_with_review',
                'comment.edit', 'comment.edit_with_review', 'comment.edit_other', 'comment.edit_other_with_review',
                'comment.delete', 'comment.delete_with_review', 'comment.delete_other', 'comment.delete_other_with_review',
                'comment.review', 'comment.review_other']);
    }

    protected function voteOnAttribute($attribute, $comment, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!($user instanceof User)) {
            $user = new User();
        }

        if (!($comment instanceof Comment)) {
            $comment = new Comment();
            $comment->setUser($user);
        }

        switch ($attribute) {
            case 'comment.submit':
                return $this->canSubmit($user, $comment);
                break;
            case 'comment.submit_with_review':
                return $this->canSubmit($user, $comment, true);
                break;
            case 'comment.edit':
			case 'comment.edit_other':
                return $this->canEdit($user, $comment);
                break;
            case 'comment.edit_with_review':
			case 'comment.edit_other_with_review':
                return $this->canEdit($user, $comment, true);
                break;
            case 'comment.delete':
			case 'comment.delete_other':
                return $this->canDelete($user, $comment);
                break;
            case 'comment.delete_with_review':
			case 'comment.delete_other_with_review':
                return $this->canDelete($user, $comment, true);
                break;
            case 'comment.review':
                return $this->canReview($user, $comment);
                break;
            case 'comment.review_other':
                return $this->canReviewOther($user, $comment);
                break;
        }

        return false;
    }

    private function canSubmit(User $user, Comment $comment, bool $with_review = false)
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

    private function canEdit(User $user, Comment $comment, bool $with_review = false)
    {
        if ($user == $comment->getUser()) {
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
        } elseif ($user != $comment->getUser()) {
            foreach ($comment->getUser()->getRoles() as $role) {
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

    private function canDelete(User $user, Comment $comment, bool $with_review = false)
    {
        if ($user == $comment->getUser()) {
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
        } elseif ($user != $comment->getUser()) {
            foreach ($comment->getUser()->getRoles() as $role) {
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

    private function canReview(User $user, Comment $comment)
    {
        foreach ($comment->getUser()->getRoles() as $role) {
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

    private function canReviewOther(User $user, Comment $comment)
    {
        foreach ($comment->getUser()->getRoles() as $role) {
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
