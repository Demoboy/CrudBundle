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

use KMJ\CrudBundle\Filter\DateTimeBetweenFilter;
use KMJ\CrudBundle\Filter\LinkedDateTimeBetweenFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LinkedDateTimeBetweenType
 *
 * @package KMJ\CrudBundle\Form\Type
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class LinkedDateTimeBetweenType extends AbstractType
{

    use LinkedFilterTypeTrait;

//<editor-fold desc="Getters and Setters">

    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return DateTimeBetweenFilterType::class;
    }
//</editor-fold>

    /**
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
                static function (?LinkedDateTimeBetweenFilter $deepLinkedDateTimeBetween) {
                    if ($deepLinkedDateTimeBetween === null) {
                        return null;
                    }

                    return $deepLinkedDateTimeBetween->getDates();
                },
                static function (?DateTimeBetweenFilter $model) use ($mappingCallback, $tableAlias) {
                    if ($model === null) {
                        return null;
                    }

                    $deepLinkedEntity = new LinkedDateTimeBetweenFilter($mappingCallback, $tableAlias, $model);

                    $deepLinkedEntity->setDates($model)
                        ->setMappingQbCallback($mappingCallback)
                        ->setTableAlias($tableAlias);

                    return $deepLinkedEntity;
                }
            )
        );
    }
}