# Integrate Oauth providers 

This bundle integrates with HWIOAuthBundle.

    composer require hwi/oauth-bundle php-http/guzzle6-adapter php-http/httplug-bundle

## Facebook

Configure your User entity with:

    use Softspring\UserBundle\Entity\Oauth\FacebookOauthTrait;
    use Softspring\UserBundle\Model\Oauth\FacebookOauthInterface;
    
    /**
     * @ORM\Entity
     */
    class User extends ... implements ..., FacebookOauthInterface
    {
        use FacebookOauthTrait;
    }
    
Configure sfs_user:

    # config/packages/sfs_user.yaml
    
    sfs_user:
        oauth:
            facebook:
                application_id: '%env(FACEBOOK_OAUTH_APPLICATION_ID)%'
                client_id: '%env(FACEBOOK_OAUTH_CLIENT_ID)%'
                client_secret: '%env(FACEBOOK_OAUTH_CLIENT_SECRET)%'
    
Configure hwi_bundle:

    # config/packages/hwi_oauth.yaml
    
    hwi_oauth:
        firewall_names: ["main"]
        resource_owners:
            facebook:
                type: "facebook"
                client_id: "%sfs_user.oauth.facebook.client_id%"
                client_secret: "%sfs_user.oauth.facebook.client_secret%"
                options:
                    display: "popup"
                    auth_type: "rerequest"
                    csrf: "true"

and your .env file:

    FACEBOOK_OAUTH_APPLICATION_ID=<facebook-application-id>
    FACEBOOK_OAUTH_CLIENT_ID=<facebook-client-id>
    FACEBOOK_OAUTH_CLIENT_SECRET=<facebook-client-secret>
    
Set the security configuration:

    # config/packages/security.yaml
    
    security:
        ...
        firewalls:
            main:
                pattern: ^/
                anonymous: ~
                form_login:
                    ...
                oauth:
                    # Declare the OAuth Callback URLs for every resource owner
                    # They will be added in the routing.yml file too later
                    resource_owners:
                        facebook: "/oauth/login-facebook"
                    ## Provide the original login path of your application (sfs_user route)
                    ## and the failure route when the authentication fails.
                    login_path: sfs_user_login
                    failure_path: sfs_user_login
                    # Inject a service that will be created in the step #6
                    oauth_user_provider:
                        service: sfs_user.oauth_provider


