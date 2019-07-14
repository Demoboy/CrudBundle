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

namespace KMJ\CrudBundle\Crud;


use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AbstractCrud
 *
 * @package KMJ\CrudBundle\Crud
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
abstract class AbstractCrud
{
    public const ACTION_ADD = 'add';
    public const ACTION_EDIT = 'edit';
    public const ACTION_VIEW = 'view';
    public const ACTION_DETAILS = 'details';
    public const ACTION_REMOVE = 'remove';
    public const ACTION_ENABLE = 'enable';
    public const ACTION_DISABLE = 'disable';

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * AbstractCrud constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * The model class to use for the crud
     *
     * @return string
     */
    abstract public function getModelClass(): string;

    /**
     * The actions that should be allowed for the crud.
     *
     * @return array
     */
    abstract public function getActions(): array;

    /**
     * The form type to use for add/edit operations. Can return null if those actions are not used or are overridden.
     *
     * @return string
     */
    abstract public function getFormType(): ?string;

    /**
     * Gets the form type for the filter. If null, the filter cannot be changed from default.
     *
     * @return string|null
     */
    public function getFilterFormType(): ?string
    {
        return null;
    }

    /**
     * Determines if the route should get the 'expose' option added it to it. This is generally used for FOS Js Routing
     *
     * @param string $action
     * @param string|null $filter
     * @return bool
     */
    public function shouldExposeRoute(string $action, ?string $filter = null): bool
    {
        return false;
    }

    /**
     * Provides a redirect response for after a successful crud operation
     *
     * @param string        $action
     * @param               $model
     * @param FormInterface $form
     *
     * @return RedirectResponse
     */
    abstract public function redirectTo(string $action, $model, ?FormInterface $form = null): RedirectResponse;

    /**
     * Returns the name of the variable to use when injecting into the template.
     *
     * @param bool $multi
     *
     * @return string
     */
    abstract public function getTemplateVar(bool $multi): string;

    /**
     * Gets the template needed for rendering the view.
     *
     * @param string $action
     *
     * @return string|null
     */
    public function getTemplate(string $action): ?string
    {
        return null;
    }

    /**
     * Returns the name of the crud based on the name of the model class used.
     *
     * @return string
     */
    public function getName(): string
    {
        /** @noinspection ReturnFalseInspection */
        $class = substr(strrchr($this->getModelClass(), "\\"), 1);

        $snakeCasedName = '';

        $len = strlen($class);
        for ($i = 0; $i < $len; ++$i) {
            if ($i !== 0 && ctype_upper($class[$i])) {
                $snakeCasedName .= '_'.strtolower($class[$i]);
            } else {
                $snakeCasedName .= strtolower($class[$i]);
            }
        }

        return $snakeCasedName;
    }

    /**
     * During times of the default actions for the crud controller, the handle event method is called allowing
     * modification, (or overriding), the model and the form. The function must return null, unless a redirect or a
     * custom render response is needed.
     *
     * @param string    $action
     * @param string    $timeline
     * @param           $model
     * @param array     $templateData
     * @param Form|null $form
     *
     * @return Response|null
     */
    public function handleEvent(
        string $action,
        string $timeline,
        &$model,
        array &$templateData,
        ?Form $form = null
    ): ?Response {
        return null;
    }

    /**
     * The namespace to use when auto generating template names. This can return null if no namespace is needed
     *
     * @return string|null
     */
    public function getTemplateNamespace(): ?string
    {
        return null;
    }

    /**
     * Gets a callable object that can be used to provide a response for the action. This can be used to provide custom
     * actions as provided by the crud class.
     *
     * @param string $action
     *
     * @return callable|null
     */
    public function getClosureForAction(string $action): ?callable
    {
        return null;
    }

    /**
     * The filter class to use when using view. If the function returns null, the filtering functionality is disabled
     *
     * @return null|string
     */
    abstract public function getFilterClass(): ?string;

    /**
     * Determines if the route parameter should show the filter form.
     *
     * @param string|null $filterName
     *
     * @return bool
     */
    public function shouldShowFilterForm(?string $filterName): bool
    {
        return true;
    }

    /**
     * Gets the route params and the needed requirement for the url. Returning null assumes no parameters required for
     * route. Route requirements are provided in the form of an array [ param => requirement ].
     *
     * @param string $action
     *
     * @return array|null
     */
    public function getRouteParamsForAction(string $action): ?array
    {
        switch ($action) {
            case self::ACTION_VIEW:
            case self::ACTION_ADD:
                return null;
            case self::ACTION_DETAILS:
            case self::ACTION_ENABLE:
            case self::ACTION_DISABLE:
            case self::ACTION_REMOVE:
            case self::ACTION_EDIT:
                return [
                    'id' => [
                        'requirements' => '\d+',
                    ],
                ];
        }

        return null;
    }
}