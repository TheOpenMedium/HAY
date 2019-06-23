<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Report;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReportVoter extends Voter
{
    protected function supports($attribute, $report)
    {
        return in_array($attribute, ['report.submit', 'report.review']);
    }

    protected function voteOnAttribute($attribute, $report, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!($user instanceof User)) {
            $user = new User();
        }

        if (!($report instanceof Report)) {
            $report = new Report();
            $report->setReporter($user);
        }

        switch ($attribute) {
            case 'report.submit':
                return $this->canSubmit($user, $report);
                break;
            case 'report.review':
                return $this->canReview($user, $report);
                break;
        }

        return false;
    }

    private function canSubmit(User $user, Report $report)
    {
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

        return false;
    }

    private function canReview(User $user, Report $report)
    {
        foreach ($report->getReportedUser()->getRoles() as $role) {
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
