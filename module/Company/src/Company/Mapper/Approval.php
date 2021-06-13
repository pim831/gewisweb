<?php


namespace Company\Mapper;

use Company\Model\ApprovalModel\ApprovalPending;
use Company\Model\ApprovalModel\ApprovalProfile;
use Company\Model\ApprovalModel\ApprovalCompanyI18n;
use Company\Model\ApprovalModel\ApprovalVacancy;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class Approval
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

    public function persist($job)
    {
        $this->em->persist($job);
        $this->em->flush();
    }

    /**
     * Flush.
     */
    public function flush()
    {
        $this->em->flush();
    }

    /**
     * Saves all modified entities that are marked persistant
     *
     */
    public function save()
    {
        $this->em->flush();
    }

    /**
     * Find all pending approvals
     *
     * @return array ApprovalPending model
     */
    public function findPendingApprovals()
    {
        $builder = new ResultSetMappingBuilder($this->em);
        $builder->addRootEntityFromClassMetadata('Company\Model\ApprovalModel\ApprovalPending', 'ap');

        $select = $builder->generateSelectClause(['ap' => 't1']);
        $sql = "SELECT $select FROM ApprovalPending AS t1";
        $query = $this->em->createNativeQuery($sql, $builder);

        return $query->getResult();
    }

    public function rejectApproval($cId){
        $qb = $this->em->createQueryBuilder();
        $qb->update("Company\Model\ApprovalProfile", "ap");
        $qb->where("ap.company_id = $cId");
        $qb->set("ap.rejected", ":rejected");
        $qb->setParameter("rejected", "0");
        $qb->getQuery()->getResult();

        $qb = $this->em->createQueryBuilder();
        $qb->update("Company\Model\ApprovalCompanyl18n", "ap");
        $qb->where("ap.company_id = $cId");
        $qb->set("ap.rejected", ":rejected");
        $qb->setParameter("rejected", "0");
        $qb->getQuery()->getResult();

    }

    /**
     * Find the company with the given slugName.
     *
     * @param slugName The 'username' of the company to get.
     * @param asObject if yes, returns the company as an object in an array, otherwise returns the company as an array of an array
     *
     * @return An array of companies with the given slugName.
     */
    public function findEditableCompaniesBySlugName($slugName, $asObject)
    {

        $objectRepository = $this->getRepository(); // From clause is integrated in this statement
        $qb = $objectRepository->createQueryBuilder('c');
        $qb->select('c')->where('c.slugName=:slugCompanyName');
        $qb->setParameter('slugCompanyName', $slugName);
        $qb->setMaxResults(1);
        if ($asObject) {
            return $qb->getQuery()->getResult();
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Find the company with the given slugName.
     *
     * @param slugName The 'username' of the company to get.
     * @param asObject if yes, returns the company as an object in an array, otherwise returns the company as an array of an array
     *
     * @return An array of companies with the given slugName.
     */
    public function findEditableCompaniesBySlugName2($slugName, $asObject)
    {

        $builder = new ResultSetMappingBuilder($this->em);
        $builder->addRootEntityFromClassMetadata('Company\Model\ApprovalModel\ApprovalProfile', 'ci');

        $select = $builder->generateSelectClause(['ci' => 't1']);
        $sql = "SELECT $select FROM ApprovalProfile AS t1".
            " WHERE t1.slugName = '$slugName'";

        $query = $this->em->createNativeQuery($sql, $builder);
        return $query->getResult();
    }

    public function findApprovalCompanyI18($cId){
        $builder = new ResultSetMappingBuilder($this->em);
        $builder->addRootEntityFromClassMetadata('Company\Model\ApprovalModel\ApprovalCompanyI18n', 'ci');

        $select = $builder->generateSelectClause(['ci' => 't1']);
        $sql = "SELECT $select FROM ApprovalCompanyI18n AS t1".
            " WHERE t1.company_id = $cId";

        $query = $this->em->createNativeQuery($sql, $builder);
        return $query->getResult();
    }

    /**
     * Get the repository for this mapper.
     *
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('Company\Model\ApprovalModel\ApprovalAbstract');
    }

//    /**
//     * Saves all unsaved entities, that are marked persistent
//     *
//     */
//    public function save($profile)
//    {
//        $this->em->persist($profile);
//        $this->em->flush();
//    }

    /**
     * Get the a job by it's id
     *
     * @return ApprovalVacancy
     */
    public function findVacanciesByLanguageNeutralId($vacancy_id) {
        $qb = $this->getRepository()->createQueryBuilder('j');
        $qb->select('j');
        $qb->where('j.languageNeutralId =:vacancy_id');
        $qb->setParameter('vacancy_id', $vacancy_id);
        return $qb->getQuery()->getResult();
    }

    /**
     * Inserts a company into the datebase, and initializes the given
     * translations as empty translations for them
     *
     * @param mixed $languages
     */
    public function insert($languages)
    {
        $company = new ApprovalProfile($this->em);

        foreach ($languages as $language) {
            $translation = new ApprovalCompanyI18n($language, $company);
            if (is_null($translation->getLogo())) {
                $translation->setLogo('');
            }
            $this->em->persist($translation);
            $company->addTranslation($translation);
        }

        $this->em->persist($company);

        return $company;
    }

}
