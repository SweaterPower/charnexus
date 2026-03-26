<?php

namespace App\Security\Voter;

use App\Dictionary\CampaignRoleDictionary;
use App\Entity\Character;
use App\Entity\User;
use App\Repository\CampaignRoleRepository;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CharacterVoter extends Voter
{
    public function __construct(
        private readonly CampaignRoleRepository $roleRepository
    ) {}

    public const EDIT = 'character_edit';
    public const VIEW = 'character_view';
    public const DELETE = 'character_delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Character;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
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

    private function canView(Character $character, User $user, ?Vote $vote): bool
    {
        if ($character->getUser()->getId() === $user->getId()) {
            return true;
        }

        $role = $this->roleRepository->findOneById($user->getId(), $character->getCampaign()->getId());

        if ($role === null) {
            $vote?->addReason("The user {$user->getUsername()} is not a part of campaign {$character->getCampaign()->getId()}.");
            return false;
        }

        if ($role->getRole() !== CampaignRoleDictionary::ROLE_BLOCKED) {
            return true;
        }

        $vote->addReason("Character {$character->getId()} can not be viewed by user {$user->getUsername()}");

        return false;
    }

    private function canEdit(Character $character, User $user, ?Vote $vote): bool
    {
        if ($character->getUser()->getId() === $user->getId()) {
            return true;
        }

        $role = $this->roleRepository->findOneById($user->getId(), $character->getCampaign()->getId());

        if ($role === null) {
            $vote?->addReason("The user {$user->getUsername()} is not a part of campaign {$character->getCampaign()->getId()}.");
            return false;
        }

        if ($role->getRole() === CampaignRoleDictionary::ROLE_GAME_MASTER || $role->getRole() === CampaignRoleDictionary::ROLE_ASSISTANT) {
            return true;
        }

        $vote->addReason("Character {$character->getId()} can not be edited by user {$user->getUsername()}");

        return false;
    }

    private function canDelete(Character $character, User $user, ?Vote $vote): bool
    {
        if ($character->getUser()->getId() === $user->getId()) {
            return true;
        }

        $role = $this->roleRepository->findOneById($user->getId(), $character->getCampaign()->getId());

        if ($role === null) {
            $vote?->addReason("The user {$user->getUsername()} is not a part of campaign {$character->getCampaign()->getId()}.");
            return false;
        }

        if ($role->getRole() === CampaignRoleDictionary::ROLE_GAME_MASTER) {
            return true;
        }

        $vote->addReason("Character {$character->getId()} can not be deleted by user {$user->getUsername()}");

        return false;
    }
}
