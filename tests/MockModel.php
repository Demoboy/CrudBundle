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

namespace KMJ\CrudBundle\Tests;

/**
 * Class MockModel
 *
 * @package KMJ\CrudBundle\Tests
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class MockModel
{
    private $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return MockModel
     */
    public function setId($id): MockModel
    {
        $this->id = $id;

        return $this;
    }

}