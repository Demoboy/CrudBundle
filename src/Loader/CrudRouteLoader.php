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

namespace KMJ\CrudBundle\Loader;


use KMJ\CrudBundle\Controller\CrudController;
use KMJ\CrudBundle\Crud\AbstractCrud;
use KMJ\CrudBundle\Exception\CrudException;
use KMJ\CrudBundle\Filter\AbstractModelFilter;
use KMJ\CrudBundle\Interfaces\EnableableModel;
use KMJ\CrudBundle\Pool\CrudPool;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class CrudRouteLoader
 *
 * @package KMJ\CrudBundle\Loader
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class CrudRouteLoader implements LoaderInterface
{
    /**
     * @var CrudPool
     */
    private $crudPool;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * Whether the loader had been run.
     *
     * @var bool
     */
    private $loaded = false;

    /**
     * CrudRouteLoader constructor.
     *
     * @param CrudPool        $crudPool
     * @param LoggerInterface $logger
     */
    public function __construct(CrudPool $crudPool, LoggerInterface $logger)
    {
        $this->crudPool = $crudPool;
        $this->logger = $logger;
    }


    /**
     * {@inheritDoc}
     */
    public function load($resource, $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new CrudException('Do not add the crud loader twice');
        }

        $routes = new RouteCollection();
        $cruds = $this->crudPool->getPool();

        /** @var AbstractCrud $crud */
        foreach ($cruds as $crud) {
            $modelName = $crud->getName();
            $modelUrl = str_replace('_', '-', $modelName);

            foreach ($crud->getActions() as $action) {
                $filter = null;

                if (in_array($action, [AbstractCrud::ACTION_ENABLE, AbstractCrud::ACTION_DISABLE], true)) {
                    $modelClass = $crud->getModelClass();

                    $model = new $modelClass();

                    if (!$model instanceof EnableableModel) {
                        $this->logger->warning(
                            "Unable to create enabled/disabled route for model {$modelClass} because it is not an instance of EnableableModel"
                        );
                        continue;
                    }
                }

                if ($filterClass = $crud->getFilterClass()) {
                    $filter = new $filterClass();

                    if (!$filter instanceof AbstractModelFilter) {
                        throw new CrudException('Filter must extend AbstractModelFilter');
                    }
                }

                if ($action === AbstractCrud::ACTION_VIEW && $filter !== null) {
                    foreach ($filter->getConfiguredFilters() as $name => $filter) {
                        $url = str_replace('_', '-', $name);
                        $path = "/{$modelUrl}/view/{$url}";

                        $route = new Route(
                            $path,
                            [
                                '_controller' => CrudController::class.':crud',
                                'action'      => $action,
                                'modelName'   => $modelName,
                                'filter'      => $name,
                                'filter_form' => true,
                            ],
                            [],
                            [
                                'expose' => $crud->shouldExposeRoute($action, $name),
                            ]
                        );

                        $routes->add("crud_{$modelName}_filter_{$name}", $route);
                    }
                }

                $requirements = [];
                $path = "/{$modelUrl}/{$action}";

                if ($parameters = $crud->getRouteParamsForAction($action)) {
                    foreach ($parameters as $param => $requirement) {
                        $path .= "/{{$param}}";

                        if (isset($requirement['requirements'])) {
                            $requirements[$param] = $requirement['requirements'];
                        }
                    }
                }

                $route = new Route(
                    $path,
                    [
                        '_controller' => CrudController::class.'::crud',
                        'action'      => $action,
                        'modelName'   => $modelName,
                    ],
                    $requirements,
                    [
                        'expose' => $crud->shouldExposeRoute($action),
                    ]
                );

                $routes->add("crud_{$modelName}_{$action}", $route);
            }
        }

        return $routes;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null): bool
    {
        return $type === 'crud';
    }

    /**
     * @inheritDoc
     */
    public function getResolver(): ?LoaderResolverInterface
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setResolver(LoaderResolverInterface $resolver): void
    {
    }
}