<?php /**
 * This file is part of the KMJ Crud package.
 * Copyright (c) Kaelin Jacobson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2019
 */
/** @noinspection PhpUnusedPrivateMethodInspection */
declare(strict_types = 1);

namespace KMJ\CrudBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use KMJ\CrudBundle\Crud\AbstractCrud;
use KMJ\CrudBundle\Exception\CrudException;
use KMJ\CrudBundle\Pool\ActionHandlerPool;
use KMJ\CrudBundle\Pool\CrudPool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class CrudController
 *
 * @package KMJ\CrudBundle\Controller
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
final class CrudController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * CrudController constructor.
     *
     * @param EntityManagerInterface        $em
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function __construct(EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker)
    {
        $this->em = $em;
        $this->authChecker = $authChecker;
    }

    /**
     * @param Request           $request
     *
     * @param CrudPool          $crudPool
     * @param ActionHandlerPool $actionHandlerPool
     * @param string            $action
     * @param string            $modelName
     *
     * @return Response
     * @throws CrudException
     */
    public function crud(
        Request $request,
        CrudPool $crudPool,
        ActionHandlerPool $actionHandlerPool,
        string $action,
        string $modelName
    ): Response {
        /** @var AbstractCrud $crud */
        $crud = $crudPool->getCrudByName($modelName);

        if (!$crud) {
            throw new CrudException("Unable to locate crud {$crud}");
        }

        $modelClass = $crud->getModelClass();
        $model = new $modelClass();

        if (
            isset($request->attributes->get('_route_params')['id'])
            && $id = $request->attributes->get('_route_params')['id']
        ) {
            $model = $this->em->getRepository($crud->getModelClass())->find($id);

            if (!$model) {
                throw new NotFoundHttpException(
                    "Unable to locate {$crud->getModelClass()} with id of {$id}"
                );
            }
        }

        if (!$this->authChecker->isGranted(strtoupper($action), $model)) {
            throw new AccessDeniedException(
                "User does not have access to {$action} for {$crud->getModelClass()}"
            );
        }

        $closure = $actionHandlerPool->handlerForAction($action);

        if (!$closure) {
            throw new CrudException("Unable to determine handler for action {$action}");
        }

        //get the entity for the action
        return $closure($request, $crud, $model);
    }
}