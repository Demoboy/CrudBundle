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

namespace KMJ\CrudBundle;

use KMJ\CrudBundle\CompilerPass\CrudCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KMJCrudBundle
 *
 * @package KMJ\CrudBundle
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class KMJCrudBundle extends Bundle
{
    /**
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CrudCompilerPass());
    }
}
