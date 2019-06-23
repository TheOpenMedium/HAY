<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    protected function supports($attribute, $post)
    {
        return in_array($attribute, ['post.submit', 'post.submit_with_review',
                'post.edit', 'post.edit_with_review', 'post.edit_other', 'post.edit_other_with_review',
                'post.delete', 'post.delete_with_review', 'post.delete_other', 'post.delete_other_with_review',
                'post.review', 'post.review_other', 'post.option_color', 'post.option_textsize']);
    }

    protected function voteOnAttribute($attribute, $post, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!($user instanceof User)) {
            $user = new User();
        }

        if (!($post instanceof Post)) {
            $post = new Post();
            $post->setUser($user);
        }

        switch ($attribute) {
            case 'post.submit':
                return $this->canSubmit($user, $post);
                break;
            case 'post.submit_with_review':
                return $this->canSubmit($user, $post, true);
                break;
            case ('post.edit' || 'post.edit_other_with_review'):
                return $this->canEdit($user, $post);
                break;
            case ('post.edit_with_review' || 'post.edit_other_with_review'):
                return $this->canEdit($user, $post, true);
                break;
            case ('post.delete' || 'post.delete_other'):
                return $this->canDelete($user, $post);
                break;
            case ('post.delete_with_review' || 'post.delete_other_with_review'):
                return $this->canDelete($user, $post, true);
                break;
            case 'post.review':
                return $this->canReview($user, $post);
                break;
            case 'post.review_other':
                return $this->canReviewOther($user, $post);
                break;
            case 'post.option_color':
                return $this->canOptionColor($user, $post);
                break;
            case 'post.option_textsize':
                return $this->canOptionTextSize($user, $post);
                break;
        }

        return false;
    }

    private function canSubmit(User $user, Post $post, bool $with_review = false)
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

    private function canEdit(User $user, Post $post, bool $with_review = false)
    {
        if ($user == $post->getUser()) {
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
        } elseif ($user != $post->getUser()) {
            foreach ($post->getUser()->getRoles() as $role) {
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

    private function canDelete(User $user, Post $post, bool $with_review = false)
    {
        if ($user == $post->getUser()) {
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
        } elseif ($user != $post->getUser()) {
            foreach ($post->getUser()->getRoles() as $role) {
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

    private function canReview(User $user, Post $post)
    {
        foreach ($post->getUser()->getRoles() as $role) {
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

    private function canReviewOther(User $user, Post $post)
    {
        foreach ($post->getUser()->getRoles() as $role) {
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

    private function canOptionColor(User $user, Post $post)
    {
        if ($this->parameters['option_color'] == 'ALL') {
            return true;
        } elseif ($this->parameters['potion_color'] == 'NONE') {
            return false;
        } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
            return true;
        }
        
        if ($this->security->isGranted($this->parameters['option_color'])) {
            return true;
        }

        return false;
    }

    private function canOptionTextSize(User $user, Post $post)
    {
        if ($this->parameters['option_textsize'] == 'ALL') {
            return true;
        } elseif ($this->parameters['potion_textsize'] == 'NONE') {
            return false;
        } elseif (in_array('ROLE_OWNER', $user->getRoles())) {
            return true;
        }
        
        if ($this->security->isGranted($this->parameters['option_textsize'])) {
            return true;
        }

        return false;
    }
}
