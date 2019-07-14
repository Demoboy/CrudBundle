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

use DateTime;

/**
 * Class DateTimeBetweenFilter
 */
class DateTimeBetweenFilter
{

    /**
     * The start date of the filter
     *
     * @var DateTime|null
     */
    private $start;

    /**
     * The end date of the filter
     *
     * @var DateTime|null
     */
    private $end;

    /**
     * DateTimeBetweenFilter constructor.
     *
     * @param DateTime|null $start
     * @param DateTime|null $end
     */
    public function __construct(?DateTime $start = null, ?DateTime $end = null)
    {
        $this->start = $start;
        $this->end = $end;
    }


    /**
     * @return DateTime|null
     */
    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    /**
     * @param DateTime|null $start
     *
     * @return DateTimeBetweenFilter
     */
    public function setStart($start): DateTimeBetweenFilter
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    /**
     * @param DateTime|null $end
     *
     * @return DateTimeBetweenFilter
     */
    public function setEnd($end): DateTimeBetweenFilter
    {
        $this->end = $end;

        return $this;
    }


}