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
 * Trait EnableableModelTrait
 *
 * @package KMJ\CrudBundle\Interfaces
 */
trait EnableableModelTrait
{
    /**
     * If the model is enabled or not
     *
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;

    /**
     * Returns true if the model is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Sets the enabled-ness of the model directly.
     *
     * @param bool $enable
     *
     * @return self
     */
    public function setEnabled(bool $enable): self
    {
        $this->enabled = $enable;

        return $this;
    }

    /**
     * Returns true if the model is disabled.
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return !$this->enabled;
    }
}