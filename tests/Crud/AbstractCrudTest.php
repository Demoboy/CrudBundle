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

namespace KMJ\CrudBundle\Tests\Crud;

use KMJ\CrudBundle\Crud\AbstractCrud;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AbstractCrudTest
 * @package KMJ\CrudBundle\Tests\Crud
 * @author Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class AbstractCrudTest extends TestCase
{

    /**
     * Tests getName
     */
    public function testGetName(): void
    {
        $crud = $this->init();

        $crud->method('getModelClass')
            ->willReturn('KMJ\CrudBundle\TestAbstractCrud');

        $this->assertSame($crud->getName(), 'test_abstract_crud');
    }

    /**
     * Initialized an AbstractCrud object.
     *
     * @return MockObject
     */
    private function init(): MockObject
    {
        $router = $this->createMock(RouterInterface::class);
        return $this->getMockForAbstractClass(AbstractCrud::class, [$router], '', true, true, true, ['getModelClass']);
    }
}
