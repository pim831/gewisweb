<?php

namespace Activity\Mapper;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use \Activity\Model\Activity as ActivityModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class SignupList
{
    /**
     * Doctrine entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $signupListId
     * @param $activityId
     *
     * @return array
     */
    public function getSignupListByIdAndActivity($signupListId, $activityId)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('a')
            ->from('Activity\Model\SignupList', 'a')
            ->where('a.id = :signupList')
            ->andWhere('a.activity = :activity')
            ->setParameter('signupList', $signupListId)
            ->setParameter('activity', $activityId);
        $result = $qb->getQuery()->getResult();

        return count($result) > 0 ? $result[0] : null;
    }

    public function getSignupListsOfActivity($activityId)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('a')
            ->from('Activity\Model\SignupList', 'a')
            ->where('a.activity = :activity')
            ->setParameter('activity', $activityId);
        $result = $qb->getQuery()->getResult();

        return $result;
    }
}