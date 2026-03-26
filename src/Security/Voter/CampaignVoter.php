<?php

namespace App\Security\Voter;

use App\Dictionary\CampaignRoleDictionary;
use App\Entity\Campaign;
use App\Entity\User;
use App\Repository\CampaignRoleRepository;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CampaignVoter extends Voter
{
    public function __construct(
        private readonly CampaignRoleRepository $roleRepository
    ) {}

    public const VIEW = 'campaign_view';
    public const EDIT = 'campaign_edit';
    public const DELETE = 'campaign_delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Campaign;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            $vote?->addReason('The user is not logged in.');
            return false;
        }

        return match($attribute) {
            self::VIEW => $this->canView($subject, $user, $vote),
            self::EDIT => $this->canEdit($subject, $user, $vote),
            self::DELETE => $this->canDelete($subject, $user, $vote),
            default => throw new LogicException('This code should not be reached!')
        };
    }

    private function canView(Campaign $campaign, User $user, ?Vote $vote): bool
    {
        $role = $this->roleRepository->findOneById($user->getId(), $campaign->getId());

        if ($role !== null && $role->getRole() !== CampaignRoleDictionary::ROLE_BLOCKED) {
            return true;
        }

        $vote?->addReason("The user {$user->getUsername()} is not a part of campaign {$campaign->getId()}.");

        return false;
    }

    private function canEdit(Campaign $campaign, User $user, ?Vote $vote): bool
    {
        $role = $this->roleRepository->findOneById($user->getId(), $campaign->getId());

        if ($role === null) {
            $vote?->addReason("The user {$user->getUsername()} is not a part of campaign {$campaign->getId()}.");
            return false;
        }

        if ($role->getRole() === CampaignRoleDictionary::ROLE_GAME_MASTER || $role->getRole() === CampaignRoleDictionary::ROLE_ASSISTANT) {
            return true;
        }

        $vote?->addReason("The user {$user->getUsername()} is not a GM or Assistant of campaign {$campaign->getId()}.");

        return false;
    }

    private function canDelete(Campaign $campaign, User $user, ?Vote $vote): bool
    {
        $role = $this->roleRepository->findOneById($user->getId(), $campaign->getId());

        if ($role === null) {
            $vote?->addReason("The user {$user->getUsername()} is not a part of campaign {$campaign->getId()}.");
            return false;
        }

        if ($role->getRole() === CampaignRoleDictionary::ROLE_GAME_MASTER) {
            return true;
        }

        $vote?->addReason("The user {$user->getUsername()} is not a GM of campaign {$campaign->getId()}.");

        return false;
    }
}
