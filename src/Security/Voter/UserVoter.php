<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const VIEW = 'USER_VIEW';
    public const ENVIAR_PAGAMENTO = 'ENVIAR_PAGAMENTO';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {

        if (!in_array($attribute, [self::VIEW, self::EDIT, self::ENVIAR_PAGAMENTO])) {
            return false;
        }

        // only vote on `User` objects
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var User $userEdited */
        $userEdited = $subject;

        return match ($attribute) {
            self::ENVIAR_PAGAMENTO => $this->podePagarFuncionarios($userEdited, $user),
            default => new \Exception('code not to be reached')
        };
    }

    private function podePagarFuncionarios($userEdited, $user)
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
    private function canEdit($userEdited, $user)
    {
        return $userEdited->getId() === $user->getId();
    }

    private function canView($userEdited, $user)
    {
        return $userEdited->getId() == $user->getId();
    }
}
