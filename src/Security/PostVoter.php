<?php
namespace App\Security;

use App\Entity\Star;
use App\Entity\AppUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PostVoter extends Voter
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

        // only vote on Star objects inside this voter
        if (!$subject instanceof Star) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $appUser = $token->getUser();

        if (!$appUser instanceof AppUser) {
            // the user must be logged in; if not, deny access
            return false;
        }

       
        /** @var Star  */
        $star = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($star, $appUser);
            case self::EDIT:
                return $this->canEdit($star, $appUser);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Star $star, AppUser $appUser)
    {
        // if they can edit, they can view
        if ($this->canEdit($star, $appUser)) {
            return true;
        }
    }

    private function canEdit(Star $star, AppUser $appUser)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $appUser === $star->getAppUser();
    }
}