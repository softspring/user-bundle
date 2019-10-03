<?php

namespace Softspring\UserBundle;

class SfsUserEvents
{
    /**
     * @Event("Softspring\UserBundle\Event\UserEvent")
     */
    const USER_CREATED = 'sfs_user.user.created';

    /**
     * @Event("Softspring\UserBundle\Event\UserEvent")
     */
    const USER_INVITED = 'sfs_user.user.invited';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const REGISTER_INITIALIZE = 'sfs_user.register.initialize' ;

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const REGISTER_FORM_VALID = 'sfs_user.register.form_valid' ;

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const REGISTER_SUCCESS = 'sfs_user.register.success' ;

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const REGISTER_FORM_INVALID = 'sfs_user.register.form_invalid' ;

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const INVITATION_ACCEPT = 'sfs_user.invitation.accept' ;

    /**
     * @Event("Softspring\UserBundle\Event\FormEvent")
     */
    const INVITATION_FORM_VALID = 'sfs_user.invitation.form_valid' ;

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const INVITATION_ACCEPTED = 'sfs_user.invitation.accepted' ;

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const INVITATION_FORM_INVALID = 'sfs_user.invitation.form_valid' ;

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const CONFIRMATION_SUCCESS = 'sfs_user.confirmation.success';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const CONFIRMATION_FAILED = 'sfs_user.confirmation.failed';

    /**
     * @Event("Softspring\UserBundle\Event\UserEvent")
     */
    const LOGIN_IMPLICIT = 'sfs_user.login.implicit' ;

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const LOGIN_ATTEMPT = 'sfs_user.login.attempt' ;

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const PREFERENCES_UPDATED = 'sfs_user.preferences.updated';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const PREFERENCES_FORM_VALID = 'sfs_user.preferences.form_valid';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const PREFERENCES_FORM_INVALID = 'sfs_user.preferences.form_invalid';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const CHANGE_EMAIL_UPDATED = 'sfs_user.change_email.updated';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const CHANGE_EMAIL_FORM_VALID = 'sfs_user.change_email.form_valid';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const CHANGE_EMAIL_FORM_INVALID = 'sfs_user.change_email.form_invalid';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const CHANGE_PASSWORD_UPDATED = 'sfs_user.change_password.updated';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const CHANGE_PASSWORD_FORM_VALID = 'sfs_user.change_password.form_valid';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const CHANGE_PASSWORD_FORM_INVALID = 'sfs_user.change_password.form_invalid';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const CHANGE_USERNAME_UPDATED = 'sfs_user.change_username.updated';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const CHANGE_USERNAME_FORM_VALID = 'sfs_user.change_username.form_valid';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const CHANGE_USERNAME_FORM_INVALID = 'sfs_user.change_username.form_invalid';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseEvent")
     */
    const RESET_REQUEST_INITIALIZE = 'sfs_user.reset_request.initialize';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const RESET_REQUEST_FORM_VALID = 'sfs_user.reset_request.form_valid';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const RESET_REQUEST_DO_REQUEST = 'sfs_user.reset_request.do_request';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const RESET_REQUEST_FORM_INVALID = 'sfs_user.reset_request.form_invalid';

    /**
     * @Event("Softspring\UserBundle\Event\ViewEvent")
     */
    const RESET_REQUEST_VIEW = 'sfs_user.reset_request.view';

    /**
     * @Event("Softspring\UserBundle\Event\ViewEvent")
     */
    const RESET_REQUESTED_VIEW = 'sfs_user.reset_done.view';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseEvent")
     */
    const RESET_INITIALIZE = 'sfs_user.reset.initialize';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const RESET_FORM_VALID = 'sfs_user.reset.form_valid';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const RESET_SUCCESS = 'sfs_user.reset.success';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const RESET_FORM_INVALID = 'sfs_user.reset.form_invalid';

    /**
     * @Event("Softspring\UserBundle\Event\ViewEvent")
     */
    const RESET_VIEW = 'sfs_user.reset.view';

    /**
     * @Event("Softspring\UserBundle\Event\ViewEvent")
     */
    const RESET_SUCCESS_VIEW = 'sfs_user.reset_success.view';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const ADMIN_USERS_DELETE_INITIALIZE = 'sfs_user.admin.users.delete_initialize';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const ADMIN_USERS_DELETE_FORM_VALID = 'sfs_user.admin.users.delete_form_valid';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseUserEvent")
     */
    const ADMIN_USERS_DELETE_SUCCESS = 'sfs_user.admin.users.delete_success';

    /**
     * @Event("Softspring\UserBundle\Event\GetResponseFormEvent")
     */
    const ADMIN_USERS_DELETE_FORM_INVALID = 'sfs_user.admin.users.delete_form_invalid';

    /**
     * @Event("Softspring\AdminBundle\Event\ViewEvent")
     */
    const ADMIN_USERS_DELETE_VIEW = 'sfs_user.admin.users.delete_view';
}