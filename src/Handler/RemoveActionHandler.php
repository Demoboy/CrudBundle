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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class RemoveActionHandler
 *
 * @package KMJ\CrudBundle\Handler
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class RemoveActionHandler extends AbstractActionHandler
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
     * RemoveActionHandler constructor.
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
            AbstractCrud::ACTION_REMOVE => 'remove',
        ];
    }

    /**
     * Removes the specified model
     *
     * @param Request      $request
     * @param AbstractCrud $crud
     * @param              $model
     *
     * @return RedirectResponse|Response
     */
    public function remove(Request $request, AbstractCrud $crud, $model)
    {
        $action = AbstractCrud::ACTION_REMOVE;
        $templateData = [];

        $response = $crud->handleEvent($action, self::ACTION_BEFORE, $model, $templateData);

        if ($response) {
            return $response;
        }

        $this->em->remove($model);
        $this->em->flush();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->session->getBag('flashes')->add('success', $this->getFlashMessage($crud, $action));

        $response = $crud->handleEvent($action, self::ACTION_POST, $model, $templateData);

        if ($response) {
            return $response;
        }

        return $crud->redirectTo($action, $model);
    }
}