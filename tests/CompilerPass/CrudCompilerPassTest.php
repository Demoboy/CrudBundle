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

namespace KMJ\CrudBundle\Test\CompilerPass;

use Exception;
use KMJ\CrudBundle\CompilerPass\CrudCompilerPass;
use KMJ\CrudBundle\Pool\ActionHandlerPool;
use KMJ\CrudBundle\Pool\CrudPool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CrudCompilerPassTest
 * @package KMJ\CrudBundle\Test\CompilerPass
 * @author Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class CrudCompilerPassTest extends TestCase
{

    /**
     * Tests the process method
     *
     * @throws Exception
     */
    public function testProcess(): void
    {
        $cb = new ContainerBuilder();
        $cb->register(CrudPool::class, CrudPool::class);
        $cb->register(ActionHandlerPool::class, ActionHandlerPool::class);

        $cb->register('mock_crud')
            ->addTag('kmj_crud.crud');

        $cb->register('mock_action_handler')
            ->addTag('kmj_crud.action_handler');

        $compiler = new CrudCompilerPass();
        $compiler->process($cb);

        $this->assertCount(1, $cb->getDefinition(CrudPool::class)->getMethodCalls(), 'Unable to register a crud');
        $this->assertCount(1, $cb->getDefinition(ActionHandlerPool::class)->getMethodCalls(), 'Unable to register action handlers');
    }
}
