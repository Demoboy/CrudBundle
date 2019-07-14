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

namespace KMJ\CrudBundle\Filter;


/**
 * Class LinkedFilter
 *
 * @package KMJ\CrudBundle\Filter
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
abstract class LinkedFilter
{


    /**
     * The mapping to apply to the query builder to be able to reference the related data.
     *
     * @var callable|null
     */
    protected $mappingQbCallback;

    /**
     * The alas to use in the query builder for linking this data
     *
     * @var string|null
     */
    protected $tableAlias;

    /**
     * LinkedFilter constructor.
     *
     * @param callable|null $mappingQbCallback
     * @param string|null   $tableAlias
     */
    public function __construct(callable $mappingQbCallback, string $tableAlias)
    {
        $this->mappingQbCallback = $mappingQbCallback;
        $this->tableAlias = $tableAlias;
    }

    /**
     * @return callable|null
     */
    public function getMappingQbCallback(): ?callable
    {
        return $this->mappingQbCallback;
    }

    /**
     * @param callable|null $mappingQbCallback
     *
     * @return self
     */
    public function setMappingQbCallback($mappingQbCallback): self
    {
        $this->mappingQbCallback = $mappingQbCallback;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTableAlias(): ?string
    {
        return $this->tableAlias;
    }

    /**
     * @param null|string $tableAlias
     *
     * @return self
     */
    public function setTableAlias($tableAlias): self
    {
        $this->tableAlias = $tableAlias;

        return $this;
    }


}