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

namespace KMJ\CrudBundle\Interfaces;

/**
 * Interface EnableableModel
 *
 * @package KMJ\CrudBundle\Interfaces
 */
interface EnableableModel
{
    /**
     * Returns true if the model is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Returns true if the model is disabled.
     *
     * @return bool
     */
    public function isDisabled(): bool;

    /**
     * Sets the enabled-ness of the model directly.
     *
     * @param bool $enable
     */
    public function setEnabled(bool $enable);

}