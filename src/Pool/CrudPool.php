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


use KMJ\CrudBundle\Crud\AbstractCrud;

/**
 * Class CrudPool
 *
 * @package KMJ\CrudBundle\Pool
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class CrudPool extends AbstractBasicPool
{

    /**
     * Gets a crud by name
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function getCrudByName(string $name)
    {
        /** @var AbstractCrud $crud */
        foreach ($this->pool as $crud) {
            if ($crud->getName() === $name) {
                return $crud;
            }
        }

        return null;
    }

}