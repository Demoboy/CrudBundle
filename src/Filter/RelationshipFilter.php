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

namespace KMJ\CrudBundle\Filter;


/**
 * Class RelationshipFilter
 *
 * @package KMJ\CrudBundle\Filter
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class RelationshipFilter extends LinkedFilter
{
    /**
     * The data to link to.
     *
     * @var mixed|null
     */
    private $model;

    /**
     * @return mixed|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed|null $model
     *
     * @return RelationshipFilter
     */
    public function setModel($model): RelationshipFilter
    {
        $this->model = $model;

        return $this;
    }

}