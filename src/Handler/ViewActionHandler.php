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
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\SerializerInterface;
use KMJ\CrudBundle\Crud\AbstractCrud;
use KMJ\CrudBundle\Exception\CrudException;
use KMJ\CrudBundle\Filter\AbstractModelFilter;
use KMJ\CrudBundle\Repository\FilterRepository;
use ReflectionException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use function count;

/**
 * Class ViewActionHandler
 *
 * @package KMJ\CrudBundle\Handler
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class ViewActionHandler extends AbstractActionHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $form;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ViewActionHandler constructor.
     *
     * @param EntityManagerInterface $em
     * @param Environment $twig
     * @param FormFactoryInterface $form
     * @param SerializerInterface $serializer
     */
    public function __construct(
        EntityManagerInterface $em,
        Environment $twig,
        FormFactoryInterface $form,
        SerializerInterface $serializer
    )
    {
        $this->em = $em;
        $this->twig = $twig;
        $this->form = $form;
        $this->serializer = $serializer;
    }


    /**
     * {@inheritDoc}
     */
    public function getActions(): array
    {
        return [
            AbstractCrud::ACTION_VIEW => 'view',
        ];
    }

    /**
     * Handles the view action.
     *
     * @param Request $request
     * @param AbstractCrud $crud
     *
     * @return Response
     * @throws CrudException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ReflectionException
     */
    public function view(Request $request, AbstractCrud $crud): Response
    {
        $action = AbstractCrud::ACTION_VIEW;
        /** @var EntityRepository $repository */
        $repository = $this->em->getRepository($crud->getModelClass());

        if (!$repository instanceof FilterRepository) {
            throw new CrudException('Repository must implement filter');
        }

        $models = $templateData = [];

        $response = $crud->handleEvent($action, self::ACTION_BEFORE, $models, $templateData);

        if ($response) {
            return $response;
        }

        //models could have been created during the event.
        if (count($models) === 0) {
            $filterName = $request->attributes->get('_route_params')['filter'] ?? null;
            $filterClass = $crud->getFilterClass();
            $filter = new $filterClass();

            $masterFilter = new $filterClass();

            if (!$masterFilter instanceof AbstractModelFilter) {
                throw new CrudException('Filter must implement AbstractModelFilter');
            }

            if (
                ($filterFormType = $crud->getFilterFormType())
                && $crud->shouldShowFilterForm($filterName)
            ) {
                $filterClass = $crud->getFilterClass();

                if ($filterName !== null) {

                    $filter = $masterFilter->getConfiguredFilters()[$filterName];
                }

                $filterForm = $this->form->create($filterFormType, $filter);
                $filterForm->handleRequest($request);

                if (!$filterForm->isSubmitted() || ($filterForm->isSubmitted() && $filterForm->isValid())) {
                    $models = $repository->filter($filter);
                } elseif (!$filterForm->isValid()) {
                    if ($filterName) {
                        $filter = $masterFilter->getConfiguredFilters()[$filterName];
                    } else {
                        $filter = new $filterClass();
                    }
                }
            }

            $models = $repository->filter($filter);
        }

        $response = $crud->handleEvent($action, self::ACTION_POST, $models, $templateData);

        if ($response) {
            return $response;
        }

        if ($request->getRequestFormat() === 'json') {
            $response = new JsonResponse();

            $response->setJson(
                $this->serializer->serialize(
                    [
                        'status' => 'success',
                        $crud->getTemplateVar(true) => $models,
                    ],
                    'json'
                )
            );

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
                        $crud->getTemplateVar(true) => $models,
                        'filter_form' => isset($filterForm) ? $filterForm->createView() : null,
                    ]
                )
            )
        );
    }

}