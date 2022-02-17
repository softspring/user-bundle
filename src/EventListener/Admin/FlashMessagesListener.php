<?php

namespace Softspring\UserBundle\EventListener\Admin;

use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlashMessagesListener implements EventSubscriberInterface
{
    protected FlashBagInterface $flashBag;

    protected TranslatorInterface $translator;

    /**
     * FlashMessagesListener constructor.
     */
    public function __construct(FlashBagInterface $flashBag, TranslatorInterface $translator)
    {
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_SUCCESS => 'onResendConfirmationSuccess',
            SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_ERROR => 'onResendConfirmationError',
            SfsUserEvents::ADMIN_USERS_RESEND_CONFIRMATION_ALREADY_CONFIRMED => 'onResendConfirmationAlready',
        ];
    }

    public function onResendConfirmationSuccess(GetResponseUserEvent $event): void
    {
        $user = $event->getUser();
        $locale = $event->getRequest()->getLocale();

        $this->addFlash('success', 'admin_users.resend_confirmation.messages.success', [
            '%username%' => $user->getUsername(),
            '%email%' => $user instanceof UserWithEmailInterface ? $user->getEmail() : $user->getUsername(),
        ], 'sfs_user', $locale);
    }

    public function onResendConfirmationError(GetResponseUserEvent $event): void
    {
        $user = $event->getUser();
        $locale = $event->getRequest()->getLocale();

        $this->addFlash('error', 'admin_users.resend_confirmation.messages.error', [
            '%username%' => $user->getUsername(),
            '%email%' => $user instanceof UserWithEmailInterface ? $user->getEmail() : $user->getUsername(),
        ], 'sfs_user', $locale);
    }

    public function onResendConfirmationAlready(GetResponseUserEvent $event): void
    {
        $user = $event->getUser();
        $locale = $event->getRequest()->getLocale();

        $this->addFlash('warning', 'admin_users.resend_confirmation.messages.already_confirmed', [
            '%username%' => $user->getUsername(),
            '%email%' => $user instanceof UserWithEmailInterface ? $user->getEmail() : $user->getUsername(),
        ], 'sfs_user', $locale);
    }

    protected function addFlash(string $type, string $trans, array $transParams = [], string $domain = 'sfs_user', string $locale = 'en'): void
    {
        $this->flashBag->add($type, $this->translator->trans($trans, $transParams, $domain, $locale));
    }
}
