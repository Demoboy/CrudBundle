<?php /**
 * This file is part of the KMJ Crud package.
 * Copyright (c) Kaelin Jacobson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2019
 */ /**
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


use KMJ\CrudBundle\Crud\AbstractCrud;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class DetailsActionHandler
 *
 * @package KMJ\CrudBundle\Handler
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class DetailsActionHandler extends AbstractActionHandler
{

    /**
     * @var Environment
     */
    private $twig;

    /**
     * DetailsActionHandler constructor.
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritDoc}
     */
    public function getActions(): array
    {
        return [
            AbstractCrud::ACTION_DETAILS => 'details',
        ];
    }

    /**
     * Handles the details action of the object
     *
     * @param Request      $request
     * @param AbstractCrud $crud
     * @param              $model
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function details(Request $request, AbstractCrud $crud, $model): Response
    {
        $action = AbstractCrud::ACTION_DETAILS;
        $templateData = [];

        $response = $crud->handleEvent($action, self::ACTION_POST, $model, $templateData);

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
                    ]
                )
            )
        );

    }
}