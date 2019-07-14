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

namespace KMJ\CrudBundle\Form\Type;


use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Trait LinkedFilterTypeTrait
 *
 * @package KMJ\CrudBundle\Form\Type
 */
trait LinkedFilterTypeTrait
{

    /**
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        /** @noinspection PhpUndefinedClassInspection */
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'query_builder_mapping' => static function (QueryBuilder $qb) {
                    return $qb;
                },
                'table_alias'           => null,
                'empty_data'            => null,
            ]
        );

        $resolver->setAllowedTypes('query_builder_mapping', ['callable']);
        $resolver->setAllowedTypes('table_alias', ['string', 'null']);
    }
}