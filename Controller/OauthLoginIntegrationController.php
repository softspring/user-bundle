<?php

namespace Softspring\UserBundle\Controller;

use Softspring\CoreBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class OauthLoginIntegrationController extends AbstractController
{
    /**
     * @var array
     */
    protected $oauthServices;

    /**
     * OauthLoginIntegrationController constructor.
     *
     * @param array $oauthServices
     */
    public function __construct(array $oauthServices)
    {
        $this->oauthServices = $oauthServices;
    }

    /**
     * @return Response
     */
    public function facebookJs(): Response
    {
        if (empty($this->oauthServices['facebook']['application_id'])) {
            throw new \RuntimeException('Facebook oauth configuration is required');
        }

        return $this->render('@SfsUser/oauth_login_integration/facebook_oauth_script.js.twig', [
            'application_id' => $this->oauthServices['facebook']['application_id'],
        ]);
    }
}