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

declare(strict_types=1);

namespace KMJ\CrudBundle\Form\Type;

use KMJ\CrudBundle\Exception\CrudException;
use KMJ\CrudBundle\Filter\DateTimeBetweenFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DateTimeBetweenFilterType
 *
 * @package KMJ\CrudBundle\Form\Type
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
class DateTimeBetweenFilterType extends AbstractType
{

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @throws CrudException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        switch ($options['type']) {
            case 'date':
                $type = DateType::class;
                break;
            case 'datetime':
                $type = DateTimeType::class;
                break;
            default:
                throw new CrudException("Unknown type {$options['type']}");
        }

        $builder
            ->add(
                'start',
                $type,
                [
                    'label' => $options['start_label'],
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'html5' => false,
                    'help' => $options['start_help'],
                    'required' => $options['required'],
                    'attr' => [
                        'class' => 'date-picker',
                    ],
                ]
            )
            ->add(
                'end',
                $type,
                [
                    'label' => $options['end_label'],
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'help' => $options['end_help'],
                    'html5' => false,
                    'required' => $options['required'],
                    'attr' => [
                        'class' => 'date-picker',
                    ],
                ]
            );
    }

    /**
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'type' => 'date',
                'required' => false,
                'data_class' => DateTimeBetweenFilter::class,
                'start_help' => null,
                'start_label' => 'Start date',
                'end_label' => 'End date',
                'end_help' => null,
            ]
        );

        $resolver->setAllowedTypes('end_help', ['null', 'string']);
        $resolver->setAllowedTypes('start_help', ['null', 'string']);
        $resolver->setAllowedTypes('required', ['bool']);
        $resolver->setAllowedValues('type', ['date', 'datetime']);
    }
}