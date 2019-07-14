<?php /**
 * This file is part of the KMJ Crud package.
 * Copyright (c) Kaelin Jacobson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2019
 */ /** @noinspection PhpUnusedParameterInspection */
declare(strict_types = 1);

namespace KMJ\CrudBundle\Handler;


use Doctrine\ORM\EntityManagerInterface;
use KMJ\CrudBundle\Crud\AbstractCrud;
use KMJ\CrudBundle\Interfaces\EnableableModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class EnableDisableActionHandler
 *
 * @package KMJ\CrudBundle\Handler
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class EnableDisableActionHandler extends AbstractActionHandler
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * EnableDisableActionHandler constructor.
     *
     * @param EntityManagerInterface $em
     * @param SessionInterface       $session
     */
    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    /**
     * The actions that the handler will operate on. Array must be returned as
     *
     * [
     *      $actionName => 'methodToCall',
     * ]
     *
     * @return array
     */
    public function getActions(): array
    {
        return [
            AbstractCrud::ACTION_ENABLE  => 'enable',
            AbstractCrud::ACTION_DISABLE => 'disable',
        ];
    }


    /**
     * Enables the provided model
     *
     * @param Request         $request
     * @param AbstractCrud    $crud
     * @param EnableableModel $model
     *
     * @return Response
     */
    public function enable(Request $request, AbstractCrud $crud, EnableableModel $model): Response
    {
        $action = AbstractCrud::ACTION_ENABLE;
        $response = $this->toggle($action, $crud, $model);

        if ($response) {
            return $response;
        }

        return $crud->redirectTo($action, $model);
    }

    /**
     * Disables the provided model.
     *
     * @param Request         $request
     * @param AbstractCrud    $crud
     * @param EnableableModel $model
     *
     * @return Response
     */
    public function disable(Request $request, AbstractCrud $crud, EnableableModel $model): Response
    {
        $action = AbstractCrud::ACTION_DISABLE;
        $response = $this->toggle($action, $crud, $model);

        if ($response) {
            return $response;
        }

        return $crud->redirectTo($action, $model);
    }


    /**
     * Toggles the models enabled-ness based on the action (ACTION_ENABLE = enable model).
     *
     * @param string          $action
     * @param AbstractCrud    $crud
     * @param EnableableModel $model
     *
     * @return Response|null
     */
    private function toggle(string $action, AbstractCrud $crud, EnableableModel $model): ?Response
    {
        $templateData = [];

        $response = $crud->handleEvent($action, self::ACTION_BEFORE, $model, $templateData);

        if ($response) {
            return $response;
        }

        $model->setEnabled($action === AbstractCrud::ACTION_ENABLE);

        $this->em->persist($model);
        $this->em->flush();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->session->getBag('flashes')->add('success', $this->getFlashMessage($crud, $action).'.success');

        $response = $crud->handleEvent($action, self::ACTION_POST, $model, $templateData);

        if ($response) {
            return $response;
        }

        return null;
    }
}