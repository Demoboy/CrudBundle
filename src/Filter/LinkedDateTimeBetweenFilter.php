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
 * Class LinkedDateTimeBetweenFilter
 *
 * @package KMJ\CrudBundle\Filter
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class LinkedDateTimeBetweenFilter extends LinkedFilter
{

    /**
     * @var DateTimeBetweenFilter|null
     */
    private $dates;

    /**
     * LinkedDateTimeBetweenFilter constructor.
     *
     * @param callable                   $mappingQbCallback
     * @param string                     $tableAlias
     * @param DateTimeBetweenFilter|null $dates
     */
    public function __construct(callable $mappingQbCallback, string $tableAlias, DateTimeBetweenFilter $dates = null)
    {
        parent::__construct($mappingQbCallback, $tableAlias);
        if (!$dates) {
            $this->dates = new DateTimeBetweenFilter();
        } else {
            $this->dates = $dates;
        }
    }

    /**
     * @return DateTimeBetweenFilter|null
     */
    public function getDates(): ?DateTimeBetweenFilter
    {
        return $this->dates;
    }

    /**
     * @param DateTimeBetweenFilter|null $dates
     *
     * @return LinkedDateTimeBetweenFilter
     */
    public function setDates(?DateTimeBetweenFilter $dates): LinkedDateTimeBetweenFilter
    {
        $this->dates = $dates;

        return $this;
    }


}