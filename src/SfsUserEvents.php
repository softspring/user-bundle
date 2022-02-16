<?php

namespace Softspring\UserBundle;

class SfsUserEvents
{
    /** @Event("Softspring\UserBundle\Event\UserEvent") */
    public const USER_CREATED = 'sfs_user.user.created';

    /** @Event("Softspring\UserBundle\Event\UserEvent") */
    public const USER_INVITED = 'sfs_user.user.invited';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const REGISTER_INITIALIZE = 'sfs_user.register.initialize';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const REGISTER_FORM_VALID = 'sfs_user.register.form_valid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const REGISTER_SUCCESS = 'sfs_user.register.success';

    /** @Event("Softspring\UserBundle\Event\RegisterExceptionEvent") */
    public const REGISTER_EXCEPTION = 'sfs_user.register.exception';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const REGISTER_FORM_INVALID = 'sfs_user.register.form_invalid';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const REGISTER_VIEW = 'sfs_user.register.view';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const REGISTER_SUCCESS_VIEW = 'sfs_user.register.success.view';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const INVITATION_ACCEPT = 'sfs_user.invitation.accept';

    /** @Event("Softspring\CoreBundle\Event\FormEvent") */
    public const INVITATION_FORM_VALID = 'sfs_user.invitation.form_valid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const INVITATION_ACCEPTED = 'sfs_user.invitation.accepted';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const INVITATION_FORM_INVALID = 'sfs_user.invitation.form_valid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const CONFIRMATION_SUCCESS = 'sfs_user.confirmation.success';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const CONFIRMATION_VALID = 'sfs_user.confirmation.valid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const CONFIRMATION_FAILED = 'sfs_user.confirmation.failed';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const CONFIRMATION_VIEW = 'sfs_user.confirmation.view';

    /** @Event("Softspring\UserBundle\Event\UserEvent") */
    public const LOGIN_IMPLICIT = 'sfs_user.login.implicit';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const LOGIN_ATTEMPT = 'sfs_user.login.attempt';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const PREFERENCES_UPDATED = 'sfs_user.preferences.updated';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const PREFERENCES_FORM_VALID = 'sfs_user.preferences.form_valid';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const PREFERENCES_FORM_INVALID = 'sfs_user.preferences.form_invalid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const CHANGE_EMAIL_UPDATED = 'sfs_user.change_email.updated';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const CHANGE_EMAIL_FORM_VALID = 'sfs_user.change_email.form_valid';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const CHANGE_EMAIL_FORM_INVALID = 'sfs_user.change_email.form_invalid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const CHANGE_PASSWORD_UPDATED = 'sfs_user.change_password.updated';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const CHANGE_PASSWORD_FORM_VALID = 'sfs_user.change_password.form_valid';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const CHANGE_PASSWORD_FORM_INVALID = 'sfs_user.change_password.form_invalid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const CHANGE_USERNAME_UPDATED = 'sfs_user.change_username.updated';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const CHANGE_USERNAME_FORM_VALID = 'sfs_user.change_username.form_valid';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const CHANGE_USERNAME_FORM_INVALID = 'sfs_user.change_username.form_invalid';

    /** @Event("Softspring\UserBundle\Event\GetResponseEvent") */
    public const RESET_REQUEST_INITIALIZE = 'sfs_user.reset_request.initialize';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const RESET_REQUEST_FORM_VALID = 'sfs_user.reset_request.form_valid';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const RESET_REQUEST_DO_REQUEST = 'sfs_user.reset_request.do_request';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const RESET_REQUEST_FORM_INVALID = 'sfs_user.reset_request.form_invalid';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const RESET_REQUEST_VIEW = 'sfs_user.reset_request.view';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const RESET_REQUESTED_VIEW = 'sfs_user.reset_done.view';

    /** @Event("Softspring\UserBundle\Event\GetResponseEvent") */
    public const RESET_INITIALIZE = 'sfs_user.reset.initialize';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const RESET_FORM_VALID = 'sfs_user.reset.form_valid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const RESET_SUCCESS = 'sfs_user.reset.success';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const RESET_FORM_INVALID = 'sfs_user.reset.form_invalid';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const RESET_VIEW = 'sfs_user.reset.view';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const RESET_SUCCESS_VIEW = 'sfs_user.reset_success.view';

    /** @Event("Softspring\CoreBundle\Event\GetResponseRequestEvent") */
    public const ADMIN_USERS_LIST_INITIALIZE = 'sfs_user.admin.users.list_initialize';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_USERS_LIST_VIEW = 'sfs_user.admin.users.list_view';

    /** @Event("Softspring\CoreBundle\Event\GetResponseRequestEvent") */
    public const ADMIN_USERS_DETAILS_INITIALIZE = 'sfs_user.admin.users.details_initialize';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_USERS_DETAILS_VIEW = 'sfs_user.admin.users.details_view';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_USERS_DELETE_INITIALIZE = 'sfs_user.admin.users.delete_initialize';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const ADMIN_USERS_DELETE_FORM_VALID = 'sfs_user.admin.users.delete_form_valid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_USERS_DELETE_SUCCESS = 'sfs_user.admin.users.delete_success';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const ADMIN_USERS_DELETE_FORM_INVALID = 'sfs_user.admin.users.delete_form_invalid';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_USERS_DELETE_VIEW = 'sfs_user.admin.users.delete_view';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_USERS_UPDATE_INITIALIZE = 'sfs_user.admin.users.update_initialize';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const ADMIN_USERS_UPDATE_FORM_VALID = 'sfs_user.admin.users.update_form_valid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_USERS_UPDATE_SUCCESS = 'sfs_user.admin.users.update_success';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const ADMIN_USERS_UPDATE_FORM_INVALID = 'sfs_user.admin.users.update_form_invalid';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_USERS_UPDATE_VIEW = 'sfs_user.admin.users.update_view';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_USERS_PROMOTE_INITIALIZE = 'sfs_user.admin.users.promote_initialize';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_USERS_PROMOTE_SUCCESS = 'sfs_user.admin.users.promote_success';

    /** @Event("Softspring\CoreBundle\Event\GetResponseRequestEvent") */
    public const ADMIN_ADMINISTRATORS_LIST_INITIALIZE = 'sfs_user.admin.administrators.list_initialize';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_ADMINISTRATORS_LIST_VIEW = 'sfs_user.admin.administrators.list_view';

    /** @Event("Softspring\CoreBundle\Event\GetResponseRequestEvent") */
    public const ADMIN_ADMINISTRATORS_DETAILS_INITIALIZE = 'sfs_user.admin.administrators.details_initialize';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_ADMINISTRATORS_DETAILS_VIEW = 'sfs_user.admin.administrators.details_view';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_ADMINISTRATORS_DEMOTE_INITIALIZE = 'sfs_user.admin.administrators.demote_initialize';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_ADMINISTRATORS_DEMOTE_SUCCESS = 'sfs_user.admin.administrators.demote_success';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_ADMINISTRATORS_DELETE_INITIALIZE = 'sfs_user.admin.administrators.delete_initialize';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const ADMIN_ADMINISTRATORS_DELETE_FORM_VALID = 'sfs_user.admin.administrators.delete_form_valid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_ADMINISTRATORS_DELETE_SUCCESS = 'sfs_user.admin.administrators.delete_success';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const ADMIN_ADMINISTRATORS_DELETE_FORM_INVALID = 'sfs_user.admin.administrators.delete_form_invalid';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_ADMINISTRATORS_DELETE_VIEW = 'sfs_user.admin.administrators.delete_view';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_ADMINISTRATORS_UPDATE_INITIALIZE = 'sfs_user.admin.administrators.update_initialize';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const ADMIN_ADMINISTRATORS_UPDATE_FORM_VALID = 'sfs_user.admin.administrators.update_form_valid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_ADMINISTRATORS_UPDATE_SUCCESS = 'sfs_user.admin.administrators.update_success';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const ADMIN_ADMINISTRATORS_UPDATE_FORM_INVALID = 'sfs_user.admin.administrators.update_form_invalid';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_ADMINISTRATORS_UPDATE_VIEW = 'sfs_user.admin.administrators.update_view';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_ADMINISTRATORS_INVITE_INITIALIZE = 'sfs_user.admin.administrators.invite_initialize';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const ADMIN_ADMINISTRATORS_INVITE_FORM_VALID = 'sfs_user.admin.administrators.invite_form_valid';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_ADMINISTRATORS_INVITE_SUCCESS = 'sfs_user.admin.administrators.invite_success';

    /** @Event("Softspring\CoreBundle\Event\GetResponseFormEvent") */
    public const ADMIN_ADMINISTRATORS_INVITE_FORM_INVALID = 'sfs_user.admin.administrators.invite_form_invalid';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_ADMINISTRATORS_INVITE_VIEW = 'sfs_user.admin.administrators.invite_view';

    /** @Event("Softspring\CoreBundle\Event\GetResponseRequestEvent") */
    public const ADMIN_INVITATIONS_LIST_INITIALIZE = 'sfs_user.admin.invitations.list_initialize';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_INVITATIONS_LIST_VIEW = 'sfs_user.admin.invitations.list_view';

    /** @Event("Softspring\CoreBundle\Event\GetResponseRequestEvent") */
    public const ADMIN_INVITATIONS_DETAILS_INITIALIZE = 'sfs_user.admin.invitations.details_initialize';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_INVITATIONS_DETAILS_VIEW = 'sfs_user.admin.invitations.details_view';

    public const ADMIN_INVITATIONS_CREATE_INITIALIZE = 'sfs_user.admin.invitations.create_initialize';
    public const ADMIN_INVITATIONS_CREATE_FORM_VALID = 'sfs_user.admin.invitations.create_form_valid';
    public const ADMIN_INVITATIONS_CREATE_SUCCESS = 'sfs_user.admin.invitations.create_success';
    public const ADMIN_INVITATIONS_CREATE_FORM_INVALID = 'sfs_user.admin.invitations.create_form_invalid';
    public const ADMIN_INVITATIONS_CREATE_VIEW = 'sfs_user.admin.invitations.create_view';

    /** @Event("Softspring\CoreBundle\Event\GetResponseRequestEvent") */
    public const ADMIN_ACCESS_HISTORY_LIST_INITIALIZE = 'sfs_user.admin.access_history.list_initialize';

    /** @Event("Softspring\CoreBundle\Event\ViewEvent") */
    public const ADMIN_ACCESS_HISTORY_LIST_VIEW = 'sfs_user.admin.access_history.list_view';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_USERS_RESEND_CONFIRMATION_INITIALIZE = 'sfs_user.admin.users.resend_confirmation_initialize';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_USERS_RESEND_CONFIRMATION_SUCCESS = 'sfs_user.admin.users.resend_confirmation_success';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_USERS_RESEND_CONFIRMATION_ERROR = 'sfs_user.admin.users.resend_confirmation_error';

    /** @Event("Softspring\UserBundle\Event\GetResponseUserEvent") */
    public const ADMIN_USERS_RESEND_CONFIRMATION_ALREADY_CONFIRMED = 'sfs_user.admin.users.resend_confirmation_already_confirmed';
}
