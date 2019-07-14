<?php
/**
 * This file is part of the KMJ Crud package.
 * Copyright (c) Kaelin Jacobson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2019
 */

declare(strict_types=1);

namespace KMJ\CrudBundle\CompilerPass;


use KMJ\CrudBundle\Pool\ActionHandlerPool;
use KMJ\CrudBundle\Pool\CrudPool;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CrudCompilerPass
 *
 * @package KMJ\CrudBundle\CompilerPass
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class CrudCompilerPass implements CompilerPassInterface
{

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): bool
    {
        $this->addCruds($container);
        $this->addActionHandlers($container);

        return true;
    }

    /**
     * Locates all services that have been tagged as a Crud and adds them to a CrudPool so that they can be located at
     * runtime.
     *
     * @param ContainerBuilder $container
     */
    private function addCruds(ContainerBuilder $container): void
    {
        $def = $container->findDefinition(CrudPool::class);

        foreach ($container->findTaggedServiceIds('kmj_crud.crud') as $id => $crud) {
            $def->addMethodCall('addToPool', [new Reference($id)]);
        }
    }

    /**
     * Locates all services tagged as ActionHandlers then adds them to a ActionHandlerPool instance so that they can be
     * located at runtime
     *
     * @param ContainerBuilder $container
     */
    private function addActionHandlers(ContainerBuilder $container): void
    {
        $def = $container->findDefinition(ActionHandlerPool::class);

        foreach ($container->findTaggedServiceIds('kmj_crud.action_handler') as $id => $actionHandler) {
            $def->addMethodCall('addToPool', [new Reference($id)]);
        }
    }
}