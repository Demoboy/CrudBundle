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


/**
 * Class AbstractBasicPool
 *
 * @package KMJ\CrudBundle\Pool
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class AbstractBasicPool
{

    /**
     * @var array
     */
    protected $pool = [];

    /**
     * @return array
     */
    public function getPool(): array
    {
        return $this->pool;
    }

    /**
     * Adds the crud class into the pool.
     *
     * @param mixed $crud
     */
    public function addToPool($crud): void
    {
        $this->pool[] = $crud;
    }
}