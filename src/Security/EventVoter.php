<?php
namespace App\Security;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class EventVoter extends Voter
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

        // only vote on Event objects inside this voter
        if (!$subject instanceof Event) {
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

       
        /** @var Event  */
        $event = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($event, $user);
            case self::EDIT:
                return $this->canEdit($event, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Event $event, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($event, $user)) {
            return true;
        }
    }

    private function canEdit(Event $event, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $event->getUser();
    }
}