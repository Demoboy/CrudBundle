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

namespace KMJ\CrudBundle\Handler;


use Doctrine\ORM\EntityManagerInterface;
use KMJ\CrudBundle\Crud\AbstractCrud;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AddEditActionHandler
 *
 * @package KMJ\CrudBundle\Handler
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class AddEditActionHandler extends AbstractActionHandler
{

    /**
     * @var FormFactoryInterface
     */
    private $form;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * AddEditActionHandler constructor.
     *
     * @param FormFactoryInterface $form
     * @param EntityManagerInterface $em
     * @param SessionInterface $session
     * @param Environment $twig
     */
    public function __construct(
        FormFactoryInterface $form,
        EntityManagerInterface $em,
        SessionInterface $session,
        Environment $twig
    )
    {
        $this->form = $form;
        $this->em = $em;
        $this->session = $session;
        $this->twig = $twig;
    }


    /**
     * {@inheritDoc}
     */
    public function getActions(): array
    {
        return [
            AbstractCrud::ACTION_ADD => 'handleAdd',
            AbstractCrud::ACTION_EDIT => 'handleEdit',
        ];
    }


    /**
     * Handles an add action.
     *
     * @param Request $request
     * @param AbstractCrud $crud
     * @param $model
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handleAdd(Request $request, AbstractCrud $crud, $model): Response
    {
        return $this->do($request, $crud, $model, $crud::ACTION_ADD);
    }

    /**
     * Handles the process of adding and editing a new instance of the modal to the database. This function will
     * provide callbacks to AbstractCrud::handleEvent so that key functionality may be manipulated.
     *
     * @param Request $request
     * @param AbstractCrud $crud
     * @param              $model
     *
     * @param string $action
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function do(Request $request, AbstractCrud $crud, $model, string $action): Response
    {
        $templateData = [];

        if ($model === null) {
            $class = $crud->getModelClass();
            $model = new $class();
        }

        $form = null;

        $response = $crud->handleEvent($action, self::ACTION_BEFORE, $model, $templateData, $form);

        if ($response) {
            return $response;
        }

        if (!$form instanceof Form) {
            $form = $this->form->create($crud->getFormType(), $model);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = $crud->handleEvent($action, self::ACTION_PRE_VALIDATE, $model, $templateData, $form);

            if ($response) {
                return $response;
            }

            if ($form->isValid()) {
                $response = $crud->handleEvent($action, self::ACTION_PERSIST, $model, $templateData, $form);

                if ($response) {
                    return $response;
                }

                $this->em->persist($model);
                $this->em->flush();

                $response = $crud->handleEvent($action, self::ACTION_COMPLETED, $model, $templateData, $form);

                if ($response) {
                    return $response;
                }

                //set the session flash message for success and redirect
                /** @noinspection PhpUndefinedMethodInspection */
                $this->session->getBag('flashes')->add(
                    'crud_event',
                    [
                        'status' => 'success',
                        'message' => $this->getFlashMessage($crud, $action),
                    ]
                );

                return $crud->redirectTo($action, $model, $form);
            }
        }

        /** @noinspection SuspiciousAssignmentsInspection */
        $response = $crud->handleEvent($action, self::ACTION_POST, $model, $templateData, $form);

        if ($response) {
            return $response;
        }

        if (!$template = $crud->getTemplate($action)) {
            $template = $this->getTemplate($crud, $action);
        }

        return new Response(
            $this->twig->render(
                $template,
                array_merge(
                    $templateData,
                    [
                        $crud->getTemplateVar(false) => $model,
                        'form' => $form->createView(),
                    ]
                )
            )
        );
    }


    /**
     * Handles an edit action.
     *
     * @param Request $request
     * @param AbstractCrud $crud
     * @param $model
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handleEdit(Request $request, AbstractCrud $crud, $model): Response
    {
        return $this->do($request, $crud, $model, $crud::ACTION_EDIT);
    }


}