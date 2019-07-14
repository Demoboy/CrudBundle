<?php /**
 * This file is part of the KMJ Crud package.
 * Copyright (c) Kaelin Jacobson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2019
 */ /** @noinspection PhpUnusedParameterInspection */
declare(strict_types = 1);

namespace KMJ\CrudBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use KMJ\CrudBundle\Filter\AbstractModelFilter;
use KMJ\CrudBundle\Filter\DateTimeBetweenFilter;
use KMJ\CrudBundle\Filter\LinkedDateTimeBetweenFilter;
use KMJ\CrudBundle\Filter\LinkedFilter;
use KMJ\CrudBundle\Filter\RelationshipFilter;
use ReflectionClass;
use ReflectionException;
use Traversable;
use function count;

/**
 * Class FilterRepository
 *
 * @package KMJ\CrudBundle\Repository
 * @author  Kaelin Jacobson <kaelinjacobson@gmail.com>
 */
abstract class FilterRepository extends EntityRepository
{

    /**
     *
     * @param array $filter
     *
     * @return QueryBuilder
     * @throws ReflectionException
     */
    public function getFilterQb(array $filter): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');

        $reflectionClass = new ReflectionClass($this->getClassName());

        $this->defaultJoins($qb);

        foreach ($filter as $key => $option) {
            if (!$option instanceof LinkedFilter) {
                try {
                    $reflectionClass->getProperty($key);
                } catch (ReflectionException $exc) {
                    continue;
                }
            }

            if (is_array($option) || $option instanceof Traversable) {
                if (count($option) === 0) {
                    continue;
                }

                $this->filterArray($qb, $key, $option);
            } elseif ($option instanceof DateTimeBetweenFilter) {
                $this->filterDateTime($qb, $key, $option);
            } elseif ($option instanceof RelationshipFilter) {
                $this->filterDeepLinkedEntity($qb, $option);
            } elseif ($option instanceof LinkedDateTimeBetweenFilter) {
                $this->filterLinkedDataDateTimeBetween($qb, $key, $option);
            } elseif ($option !== null) {
                $this->filterPlain($qb, $key, $option);
            }
        }

        $this->addFilterOrderBy($qb);

        /** @noinspection ReturnNullInspection */
        $maxResults = $this->maxResults();

        if ($maxResults !== null) {
            $qb->setMaxResults($maxResults);
        }

        return $qb;
    }

    /**
     *
     * @param AbstractModelFilter $filter
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function filter(AbstractModelFilter $filter)
    {
        return $this->getFilterQb($filter->toArray())->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder                $qb
     * @param string                      $property
     * @param LinkedDateTimeBetweenFilter $option
     */
    public function filterLinkedDataDateTimeBetween(
        QueryBuilder $qb,
        string $property,
        LinkedDateTimeBetweenFilter $option
    ): void {
        $option->getMappingQbCallback()($qb);
        $this->filterDateTime($qb, $property, $option->getDates(), $option->getTableAlias());
    }

    /**
     *
     * @param QueryBuilder $qb
     */
    protected function defaultJoins(QueryBuilder $qb): void //@noinspection PhpUnusedParameterInspection
    {
    }

    /**
     * @return null|int
     */
    protected function maxResults(): ?int
    {
        return null;
    }

    /**
     *
     * @return array
     */
    protected function orderByFields(): array
    {
        return [];
    }

    /**
     *
     * @param QueryBuilder $qb
     */
    private function addFilterOrderBy(QueryBuilder $qb): void
    {
        $orderByFields = $this->orderByFields();

        if (count($orderByFields) !== 0) {
            foreach ($orderByFields as $field => $order) {
                /** @noinspection ReturnFalseInspection */
                if (strpos($field, '.') === false) {
                    $field = 'a.'.$field;
                }

                $qb->orderBy($field, $order);
            }
        }
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $property
     * @param              $array
     */
    private function filterArray(QueryBuilder $qb, string $property, $array): void
    {
        $inArray = [];
        foreach ($array as $option) {
            if (is_object($option)) {
                if (method_exists($option, 'getId')) {
                    $inArray[] = $option->getId();
                } else {
                    $inArray[] = (string) $option;
                }
            } else {
                $inArray[] = $option;
            }
        }

        $paramKey = $this->parameterKey();

        $qb->andWhere($qb->expr()->in('a.'.$property, ":array_{$paramKey}"))
            ->setParameter("array_{$paramKey}", $inArray);
    }

    /**
     * Handles filtering a DateTimeBetweenFilter to insert a query statement for the provided property
     *
     * @param QueryBuilder          $qb
     * @param string                $property
     * @param DateTimeBetweenFilter $option
     * @param string                $alias
     */
    private function filterDateTime(
        QueryBuilder $qb,
        string $property,
        DateTimeBetweenFilter $option,
        $alias = 'a'
    ): void {
        if ($option->getStart()) {
            /** @noinspection NotOptimalIfConditionsInspection */
            if ($option->getEnd()) {
                $qb->andWhere(
                    $qb->expr()->between($alias.'.'.$property, ':start_date_'.$property, ':end_date_'.$property)
                );
                $qb->setParameter('start_date_'.$property, $option->getStart()->format('Y-m-d'));
                $qb->setParameter('end_date_'.$property, $option->getEnd()->format('Y-m-d'));
            } else {
                $qb->andWhere($qb->expr()->gte($alias.'.'.$property, ':start_date_'.$property));
                $qb->setParameter('start_date_'.$property, $option->getStart()->format('Y-m-d'));
            }
        } elseif ($option->getEnd()) {
            $qb->andWhere($qb->expr()->lte($alias.'.'.$property, ':end_date_'.$property));
            $qb->setParameter('end_date_'.$property, $option->getEnd()->format('Y-m-d'));
        }
    }

    /**
     *
     * @param QueryBuilder       $qb
     * @param RelationshipFilter $option
     */
    private function filterDeepLinkedEntity(QueryBuilder $qb, RelationshipFilter $option): void
    {
        $option->getMappingQbCallback()($qb);
        $inArray = [];
        $entity = $option->getModel();


        if ((is_array($entity) || $entity instanceof Traversable) && count($entity) !== 0) {
            foreach ($entity as $opt) {
                if (is_object($opt)) {
                    if (method_exists($opt, 'getId')) {
                        $inArray[] = $opt->getId();
                    } else {
                        $inArray[] = (string) $opt;
                    }
                } else {
                    $inArray[] = $opt;
                }
            }
        } elseif (is_object($option->getModel()) && method_exists($option->getModel(), 'getId')) {
            /** @noinspection NullPointerExceptionInspection */
            $inArray[] = $option->getModel()->getId();
        } else {
            $inArray[] = $option->getModel();
        }

        $paramKey = $this->parameterKey();

        $qb->andWhere($qb->expr()->in($option->getTableAlias().'.id', ":de_array_{$paramKey}"))
            ->setParameter("de_array_{$paramKey}", $inArray);
    }

    /**
     *
     * @param QueryBuilder $qb
     * @param              $property
     * @param              $value
     */
    private function filterPlain(QueryBuilder $qb, $property, $value): void
    {
        $qb->andWhere($qb->expr()->eq('a.'.$property, ':option_'.$property));
        $qb->setParameter('option_'.$property, $value);
    }

    /**
     *
     * @return bool|string
     */
    private function parameterKey()
    {
        return substr(md5(microtime()), 0, 5);
    }
}