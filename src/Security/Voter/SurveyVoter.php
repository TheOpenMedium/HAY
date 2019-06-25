<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Survey;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Yaml\Yaml;

class SurveyVoter extends Voter
{
    private $security;
    private $parameters;

    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->parameters = Yaml::parseFile(__dir__.'/../../../config/config.yaml')['parameters']['authorizations']['survey'];
    }
    
    protected function supports($attribute, $survey)
    {
        return in_array($attribute, ['survey.submit', 'survey.submit_with_review',
                'survey.edit', 'survey.edit_with_review', 'survey.edit_other', 'survey.edit_other_with_review',
                'survey.delete', 'survey.delete_with_review', 'survey.delete_other', 'survey.delete_other_with_review',
                'survey.review', 'survey.review_other']);
    }

    protected function voteOnAttribute($attribute, $survey, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!($user instanceof User)) {
            $user = new User();
        }

        if (!($survey instanceof Survey)) {
            $survey = new Survey();
            $survey->setUser($user);
        }

        switch ($attribute) {
            case 'survey.submit':
                return $this->canSubmit($user, $survey);
                break;
            case 'survey.submit_with_review':
                return $this->canSubmit($user, $survey, true);
                break;
            case 'survey.edit':
			case 'survey.edit_other':
                return $this->canEdit($user, $survey);
                break;
            case 'survey.edit_with_review':
			case 'survey.edit_other_with_review':
                return $this->canEdit($user, $survey, true);
                break;
            case 'survey.delete':
			case 'survey.delete_other':
                return $this->canDelete($user, $survey);
                break;
            case 'survey.delete_with_review':
			case 'survey.delete_other_with_review':
                return $this->canDelete($user, $survey, true);
                break;
            case 'survey.review':
                return $this->canReview($user, $survey);
                break;
            case 'survey.review_other':
                return $this->canReviewOther($user, $survey);
                break;
        }

        return false;
    }

    private function canSubmit(User $user, Survey $survey, bool $with_review = false)
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

    private function canEdit(User $user, Survey $survey, bool $with_review = false)
    {
        if ($user == $survey->getUser()) {
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
        } elseif ($user != $survey->getUser()) {
            foreach ($survey->getUser()->getRoles() as $role) {
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

    private function canDelete(User $user, Survey $survey, bool $with_review = false)
    {
        if ($user == $survey->getUser()) {
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
        } elseif ($user != $survey->getUser()) {
            foreach ($survey->getUser()->getRoles() as $role) {
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

    private function canReview(User $user, Survey $survey)
    {
        foreach ($survey->getUser()->getRoles() as $role) {
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

    private function canReviewOther(User $user, Survey $survey)
    {
        foreach ($survey->getUser()->getRoles() as $role) {
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
