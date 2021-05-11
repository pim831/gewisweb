<?php

namespace User\Mapper;

use User\Model\NewCompany as NewCompanyModel;
use Doctrine\ORM\EntityManager;

class NewCompany
{
    /**
     * Doctrine entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get the new user by code.
     *
     * @param string $code
     *
     * @return NewCompanyModel
     */
    public function getByCode($code)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('nc')
            ->from('User\Model\NewCompany', 'nc')
            ->where('nc.code = ?1');
        $qb->setParameter(1, $code);
        $qb->setMaxResults(1);

        $res = $qb->getQuery()->getResult();
        return empty($res) ? null : $res[0];
    }

    /**
     * Get the repository for this mapper.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('User\Model\NewCompany');
    }
}