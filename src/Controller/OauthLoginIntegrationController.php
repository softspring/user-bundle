<?php

namespace Softspring\UserBundle\Controller;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class OauthLoginIntegrationController extends AbstractController
{
    protected array $oauthServices;

    public function __construct(array $oauthServices)
    {
        $this->oauthServices = $oauthServices;
    }

    public function facebookJs(): Response
    {
        if (!isset($this->oauthServices['facebook']['application_id']) || !$this->oauthServices['facebook']['application_id']) {
            throw new RuntimeException('Facebook oauth configuration is required');
        }

        return $this->render('@SfsUser/oauth_login_integration/facebook_oauth_script.js.twig', [
            'application_id' => $this->oauthServices['facebook']['application_id'],
        ]);
    }
}
