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

namespace KMJ\CrudBundle\Form\Type;

use KMJ\CrudBundle\Filter\RelationshipFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class RelationshipFilterType
 *
 * @package KMJ\CrudBundle\Form\Type
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class RelationshipFilterType extends AbstractType
{
    use LinkedFilterTypeTrait;


    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return EntityType::class;
    }

    /**
     *
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mappingCallback = $options['query_builder_mapping'];
        $tableAlias = $options['table_alias'];

        $builder->addModelTransformer(
            new CallbackTransformer(
                static function (RelationshipFilter $relationshipFilter) {
                    if ($relationshipFilter === null) {
                        return null;
                    }

                    return $relationshipFilter->getModel();
                },
                static function ($model) use ($mappingCallback, $tableAlias) {
                    if ($model === null || count($model) === 0) {
                        return null;
                    }

                    $relationshipFilter = new RelationshipFilter($mappingCallback, $tableAlias);
                    $relationshipFilter->setModel($model);

                    return $relationshipFilter;
                }
            )
        );
    }
}