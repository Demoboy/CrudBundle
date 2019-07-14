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

namespace KMJ\CrudBundle\Pool;


use KMJ\CrudBundle\Handler\AbstractActionHandler;

/**
 * Class ActionHandlerPool
 *
 * @package KMJ\CrudBundle\Pool
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class ActionHandlerPool extends AbstractBasicPool
{
    /**
     *
     * @param $action
     *
     * @return callable|null
     */
    public function handlerForAction($action): ?callable
    {
        //reverse the array so that we start with the most recent handler added to the pool
        $actionHandlers = array_reverse($this->pool);

        /** @var AbstractActionHandler $actionHandler */
        foreach ($actionHandlers as $actionHandler) {
            $actions = $actionHandler->getActions();

            if (isset($actions[$action])) {
                return [$actionHandler, $actions[$action]];
            }
        }

        return null;
    }
}