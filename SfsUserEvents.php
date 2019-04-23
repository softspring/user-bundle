<?php

namespace Softspring\UserBundle;

class SfsUserEvents
{
    /**
     * @Event("Softspring\User\Event\UserEvent")
     */
    const USER_CREATED = 'sfs_user.user.created';

    /**
     * @Event("Softspring\User\Event\UserEvent")
     */
    const USER_INVITED = 'sfs_user.user.invited';

    /**
     * @Event("Softspring\User\Event\GetResponseUserEvent")
     */
    const REGISTER_INITIALIZE = 'sfs_user.register.initialize' ;

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const REGISTER_FORM_VALID = 'sfs_user.register.form_valid' ;

    /**
     * @Event("Softspring\User\Event\GetResponseUserEvent")
     */
    const REGISTER_SUCCESS = 'sfs_user.register.success' ;

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const REGISTER_FORM_INVALID = 'sfs_user.register.form_invalid' ;

    /**
     * @Event("Softspring\User\Event\GetResponseUserEvent")
     */
    const INVITATION_ACCEPT = 'sfs_user.invitation.accept' ;

    /**
     * @Event("Softspring\User\Event\FormEvent")
     */
    const INVITATION_FORM_VALID = 'sfs_user.invitation.form_valid' ;

    /**
     * @Event("Softspring\User\Event\GetResponseUserEvent")
     */
    const INVITATION_ACCEPTED = 'sfs_user.invitation.accepted' ;

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const INVITATION_FORM_INVALID = 'sfs_user.invitation.form_valid' ;

    /**
     * @Event("Softspring\User\Event\UserEvent")
     */
    const LOGIN_IMPLICIT = 'sfs_user.login.implicit' ;

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const LOGIN_ATTEMPT = 'sfs_user.login.attempt' ;

    /**
     * @Event("Softspring\User\Event\GetResponseUserEvent")
     */
    const PREFERENCES_UPDATED = 'sfs_user.preferences.updated';

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const PREFERENCES_FORM_VALID = 'sfs_user.preferences.form_valid';

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const PREFERENCES_FORM_INVALID = 'sfs_user.preferences.form_invalid';

    /**
     * @Event("Softspring\User\Event\GetResponseUserEvent")
     */
    const CHANGE_EMAIL_UPDATED = 'sfs_user.change_email.updated';

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const CHANGE_EMAIL_FORM_VALID = 'sfs_user.change_email.form_valid';

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const CHANGE_EMAIL_FORM_INVALID = 'sfs_user.change_email.form_invalid';

    /**
     * @Event("Softspring\User\Event\GetResponseUserEvent")
     */
    const CHANGE_PASSWORD_UPDATED = 'sfs_user.change_password.updated';

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const CHANGE_PASSWORD_FORM_VALID = 'sfs_user.change_password.form_valid';

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const CHANGE_PASSWORD_FORM_INVALID = 'sfs_user.change_password.form_invalid';

    /**
     * @Event("Softspring\User\Event\GetResponseUserEvent")
     */
    const CHANGE_USERNAME_UPDATED = 'sfs_user.change_username.updated';

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const CHANGE_USERNAME_FORM_VALID = 'sfs_user.change_username.form_valid';

    /**
     * @Event("Softspring\User\Event\GetResponseFormEvent")
     */
    const CHANGE_USERNAME_FORM_INVALID = 'sfs_user.change_username.form_invalid';
}