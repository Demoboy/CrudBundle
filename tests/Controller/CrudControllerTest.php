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

declare(strict_types = 1);

namespace KMJ\CrudBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use KMJ\CrudBundle\Controller\CrudController;
use KMJ\CrudBundle\Crud\AbstractCrud;
use KMJ\CrudBundle\Exception\CrudException;
use KMJ\CrudBundle\Pool\ActionHandlerPool;
use KMJ\CrudBundle\Pool\CrudPool;
use KMJ\CrudBundle\Tests\MockModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Class CrudControllerTest
 *
 * @package KMJ\CrudBundle\Tests\Controller
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class CrudControllerTest extends TestCase
{

    /**
     * Tests whether the function prevents a user from accessing an entity that a voter has denied access too.
     *
     * @throws CrudException
     */
    public function testCrudUserRestricted(): void
    {
        $controller = new CrudController(
            $this->mockEntityManager(null),
            $this->mockAuthChecker(true)
        );

        $this->expectException(AccessDeniedException::class);

        $controller->crud(
            $this->mockRequest(null),
            $this->mockCrudPool(true),
            $this->mockActionHandlerPool(true),
            AbstractCrud::ACTION_ADD,
            'mockModel'
        );
    }

    /**
     * Tests that the function can execute successfully.
     *
     * @throws CrudException
     */
    public function testSuccessfulExecution(): void
    {
        $controller = new CrudController(
            $this->mockEntityManager(null),
            $this->mockAuthChecker(false)
        );

        $response = $controller->crud(
            $this->mockRequest(null),
            $this->mockCrudPool(true),
            $this->mockActionHandlerPool(true),
            AbstractCrud::ACTION_ADD,
            'mockModel'
        );

        $this->assertSame($response->getStatusCode(), Response::HTTP_OK);
    }

    /**
     * Tests that the function throws a NotFoundHttpException when the model is not able to be located in the db.
     *
     * @throws CrudException
     */
    public function testUnableToLocateModelTest(): void
    {
        $controller = new CrudController(
            $this->mockEntityManager(null),
            $this->createMock(AuthorizationCheckerInterface::class)
        );

        $this->expectException(NotFoundHttpException::class);

        $controller->crud(
            $this->mockRequest(1233),
            $this->mockCrudPool(true),
            $this->mockActionHandlerPool(true),
            AbstractCrud::ACTION_EDIT,
            'mockModel'
        );
    }

    /**
     * Tests that the function throws exception if the Crud class cannot be located for a model.
     *
     * @throws CrudException
     */
    public function testCrudNotFound(): void
    {
        $controller = new CrudController(
            $this->mockEntityManager(null),
            $this->createMock(AuthorizationCheckerInterface::class)
        );

        $this->expectException(CrudException::class);

        $controller->crud(
            $this->mockRequest(null),
            $this->mockCrudPool(false),
            $this->mockActionHandlerPool(true),
            AbstractCrud::ACTION_EDIT,
            'mockModel'
        );
    }


    /**
     * Tests that an exception is throw if  the action handler is not found.
     *
     * @throws CrudException
     */
    public function testActionHandlerNotFound(): void
    {
        $controller = new CrudController(
            $this->mockEntityManager(null),
            $this->mockAuthChecker(false)
        );

        $this->expectException(CrudException::class);

        $controller->crud(
            $this->mockRequest(null),
            $this->mockCrudPool(true),
            $this->mockActionHandlerPool(false),
            AbstractCrud::ACTION_EDIT,
            'mockModel'
        );
    }


    /**
     * Provided a mock AuthorizationCheckerInterface.
     *
     * @param bool $denyAccess
     *
     * @return AuthorizationCheckerInterface
     */
    private function mockAuthChecker(bool $denyAccess): AuthorizationCheckerInterface
    {
        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $authChecker->method('isGranted')
            ->willReturn(!$denyAccess);

        return $authChecker;
    }

    /**
     * Provides a mock crud pool.
     *
     * @param bool $hasCrud
     *
     * @return CrudPool
     */
    private function mockCrudPool(bool $hasCrud): CrudPool
    {
        $mockCrudPool = $this->createMock(CrudPool::class);
        $method = $mockCrudPool->method('getCrudByName');

        if ($hasCrud) {
            $mockCrud = $this->createMock(AbstractCrud::class);

            $mockCrud->method('getModelClass')
                ->willReturn(MockModel::class);

            $method->willReturn($mockCrud);
        } else {
            $method->willReturn(null);
        }

        return $mockCrudPool;
    }

    /**
     * Returns a mock ActionHandlerPool. If hasHandler is a true, the mock object will have a basic action handler that
     * will return a simple Response object.
     *
     * @param bool $hasHandler
     *
     * @return ActionHandlerPool
     */
    private function mockActionHandlerPool(bool $hasHandler): ActionHandlerPool
    {
        $mockActionHandlerPool = $this->createMock(ActionHandlerPool::class);

        $method = $mockActionHandlerPool->method('handlerForAction');

        if ($hasHandler) {
            $method->willReturn(
                static function (): Response {
                    return new Response('Mock content');
                }
            );
        } else {
            $method->willReturn(null);
        }

        return $mockActionHandlerPool;
    }


    /**
     * Provides a mock request. If Id is set, the request gets an id parameter to simulate loading a model.
     *
     * @param int|null $id
     *
     * @return Request
     */
    private function mockRequest(?int $id): Request
    {
        $mockRequest = $this->createMock(Request::class);

        $mockRequest->method('getRequestFormat')
            ->willReturn('html');

        $mockAttributes = $this->createMock(ParameterBagInterface::class);

        if ($id) {
            $mockAttributes->method('get')
                ->willReturn(
                    [
                        'id' => $id,
                    ]
                );
        } else {
            $mockAttributes->method('get')
                ->willReturn(null);
        }

        $mockRequest->attributes = $mockAttributes;

        return $mockRequest;
    }


    /**
     * Provides a mock EntityManagerInterface. If id is an int, the entity manager will return a mock model with the id
     * set when the find method is called.
     *
     * @param int|null $id
     *
     * @return EntityManagerInterface
     */
    private function mockEntityManager(?int $id): EntityManagerInterface
    {
        $mockEm = $this->createMock(EntityManagerInterface::class);
        $mockRepository = $this->createMock(EntityRepository::class);

        if ($id) {
            $mockModel = new MockModel();
            $mockModel->setId($id);

            $mockRepository->method('find')
                ->willReturn($mockModel);
        }

        $mockEm->method('getRepository')
            ->willReturn($mockRepository);

        return $mockEm;
    }
}
