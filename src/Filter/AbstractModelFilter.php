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


use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class AbstractModelFilter
 *
 * @package KMJ\CrudBundle\Filter
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
abstract class AbstractModelFilter
{
    /**
     * Gets any pre-configured filters. The filters must be returned in an array matching
     *
     * [
     *      'filter_name' => $filter,
     * ]
     *
     * @return array|null
     */
    abstract public function getConfiguredFilters(): ?array;


    /**
     * Converts the object into an array.
     *
     * @return array
     * @throws ReflectionException
     */
    public function toArray(): array
    {
        $rc = new ReflectionClass($this);
        $array = [];

        /** @var ReflectionProperty $property */
        foreach ($rc->getProperties() as $property) {
            $property->setAccessible(true);

            if ($value = $property->getValue($this)) {
                $array[$property->getName()] = $value;
            }
        }

        return $array;
    }

}