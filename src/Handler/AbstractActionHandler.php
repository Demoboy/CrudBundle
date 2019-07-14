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

namespace KMJ\CrudBundle\Handler;


use KMJ\CrudBundle\Crud\AbstractCrud;

/**
 * Class AbstractActionHandler
 *
 * @package KMJ\CrudBundle\Handler
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
abstract class AbstractActionHandler
{
    public const ACTION_BEFORE = 'before';
    public const ACTION_PRE_VALIDATE = 'validate';
    public const ACTION_PERSIST = 'persist';
    public const ACTION_COMPLETED = 'completed';
    public const ACTION_POST = 'post';

    /**
     * The actions that the handler will operate on. Array must be returned as
     *
     * [
     *      $actionName => 'methodToCall',
     * ]
     *
     * @return array
     */
    abstract public function getActions(): array;

    /**
     * The template to use for the view of the action. This is auto-generated based on information gleaned from the
     * crud class. If you need to change the template, use AbstractCrud::getTemplate()
     *
     * @param AbstractCrud $crud
     * @param string       $action
     *
     * @return string
     */
    protected function getTemplate(AbstractCrud $crud, string $action): string
    {
        $template = '';

        if ($namespace = $crud->getTemplateNamespace()) {
            $template = "@{$namespace}/";
        }

        $template .= "{$crud->getName()}/{$action}.html.twig";

        return $template;
    }


    /**
     * Builds the message key used when displaying the flash message. This key is expected to be run through
     * translations.
     *
     * @param AbstractCrud $crud
     * @param string       $action
     *
     * @return string
     */
    protected function getFlashMessage(AbstractCrud $crud, string $action): string
    {
        return "crud.{$crud->getName()}.{$action}";
    }

}