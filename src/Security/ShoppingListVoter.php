<?php
namespace App\Security;

use App\Entity\ShoppingList;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ShoppingListVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // only vote on ShoppingList objects inside this voter
        if (!$subject instanceof ShoppingList) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var ShoppingList  */
        $shoppingList = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($shoppingList, $user);
            case self::EDIT:
                return $this->canEdit($shoppingList, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(ShoppingList $shoppingList, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($shoppingList, $user)) {
            return true;
        }
    }

    private function canEdit(ShoppingList $shoppingList, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $shoppingList->getUser();
    }
}