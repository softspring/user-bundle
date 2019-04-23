<?php

namespace Softspring\UserBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Softspring\UserBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntityPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SfsUserBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $basePath = realpath(__DIR__.'/Resources/config/doctrine-mapping/');

        $this->addRegisterMappingsPass($container, [$basePath.'/entities/base' => 'Softspring\UserBundle\Entity\Base'], 'sfs_user.user.load_user_default_mapping');
        $this->addRegisterMappingsPass($container, [$basePath.'/entities/invite' => 'Softspring\UserBundle\Entity\Invite'], 'sfs_user.user.load_user_invitation_default_mapping');
        $this->addRegisterMappingsPass($container, [$basePath.'/entities/history' => 'Softspring\UserBundle\Entity\History'], 'sfs_user.user.load_history_default_mapping');
        $this->addRegisterMappingsPass($container, [$basePath.'/model' => 'Softspring\User\Model']);

        $container->addCompilerPass(new ResolveDoctrineTargetEntityPass());
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $mappings
     * @param string|bool      $enablingParameter
     */
    private function addRegisterMappingsPass(ContainerBuilder $container, array $mappings, $enablingParameter = false)
    {
        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, ['sfs_user_em'], $enablingParameter));
    }
}