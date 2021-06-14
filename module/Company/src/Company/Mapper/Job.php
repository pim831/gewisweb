<?php

namespace Company\Mapper;

use Company\Model\Job as JobModel;
use Doctrine\ORM\EntityManager;

/**
 * Mappers for jobs.
 *
 * NOTE: Jobs will be modified externally by a script. Modifications will be
 * overwritten.
 */
class Job
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
     * Saves all modified entities that are marked persistant
     *
     */
    public function save()
    {
        $this->em->flush();
    }

    /**
     *
     * Checks if $slugName is only used by object identified with $cid
     *
     * @param string $slugName The slugName to be checked
     * @param int $cid The id to ignore
     *
     */
    public function isSlugNameUnique($companySlug, $slugName, $jid, $category)
    {
        // A slug in unique if there is no other slug of the same category and same language
        $objects = $this->findJob([
            'companySlugName' => $companySlug,
            'jobSlug' => $slugName,
            'jobCategoryId' => $category,
        ]);
        foreach ($objects as $job) {
            // If the current job is in the database under the same slug, we can safely skip it
            if ($job->getId() == $jid) {
                continue;
            }
            return false;
        }

        return true;
    }

    /**
     * Inserts a job into a given package
     *
     * @param mixed $package
     */
    public function insertIntoPackage($package, $lang, $languageNeutralId)
    {
        $job = new JobModel($this->em);
        $job->setLanguage($lang);
        $job->setLanguageNeutralId($languageNeutralId);
        $job->setPackage($package);
        return $job;
    }

    public function findJobsWithoutCategory($lang)
    {
        $qb = $this->getRepository()->createQueryBuilder('j');
        $qb->select('j');
        $qb->where('j.category is NULL');
        $qb->andWhere('j.language=:lang');
        $qb->setParameter('lang', $lang);
        return $qb->getQuery()->getResult();
    }

    public function findAllActiveJobs($lang)
    {
        $qb = $this->getRepository()->createQueryBuilder('j');
        $qb->select('j');
        $qb->where('j.language=:lang');
        $qb->setParameter('lang', $lang);
        return $qb->getQuery()->getResult();
    }


    /**
     * Find the same job, but in the given language
     *
     */
    public function siblingId($jobId, $lang)
    {
        $objectRepository = $this->getRepository(); // From clause is integrated in this statement
        $qb = $objectRepository->createQueryBuilder('j')
            ->select('j.id')->where('j.languageNeutralId=:jobId')->andWhere('j.language=:language')
            ->setParameter('jobId', $jobId)
            ->setParameter('language', $lang);

        $ids = $qb->getQuery()->getResult();

        return $ids[0];
    }

    /**
     * Find all jobs identified by $jobSlugName that are owned by a company
     * identified with $companySlugName
     *
     * @param mixed $companySlugName
     * @param mixed $jobSlugName
     * @param mixed $category
     */
    public function findJob($dict)
    {
        $qb = $this->getRepository()->createQueryBuilder('j');
        $qb->select('j')->join('j.package', 'p')->join('p.company', 'c');
        if (array_key_exists('jobCategory', $dict) || array_key_exists('jobCategoryId', $dict)) {
            $qb->join('j.category', 'cat');
        }
        if (array_key_exists('jobSlug', $dict)) {
            $jobSlugName = $dict['jobSlug'];
            $qb->andWhere('j.slugName=:jobId');
            $qb->setParameter('jobId', $jobSlugName);
        }
        if (array_key_exists('languageNeutralId', $dict)) {
            $languageNeutralId = $dict['languageNeutralId'];
            $qb->andWhere('j.languageNeutralId=?1 OR j.id=?2');
            $qb->setParameter(1, $languageNeutralId);
            $qb->setParameter(2, $languageNeutralId);
        }

        if (array_key_exists('jobCategory', $dict)) {
            $category = $dict['jobCategory'];
            $qb->andWhere('cat.slug=:category');
            $qb->setParameter('category', $category);
        }
        if (array_key_exists('jobCategoryId', $dict)) {
            $category = $dict['jobCategoryId'];
            $qb->andWhere('cat.id=:category');
            $qb->setParameter('category', $category);
        }

        if (array_key_exists('language', $dict)) {
            $lang = $dict['language'];
            $qb->andWhere('j.language=:language');
            $qb->setParameter('language', $lang);
        }

        if (array_key_exists('companySlugName', $dict)) {
            $companySlugName = $dict['companySlugName'];
            $qb->andWhere('c.slugName=:companySlugName');
            $qb->setParameter('companySlugName', $companySlugName);
        }

        return $qb->getQuery()->getResult();
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
     * Deletes the jobs corresponding to the given language neutral id.
     *
     */
    public function deleteByLanguageNeutralId($jobId)
    {
        $jobs = $this->getRepository()->findBy(['languageNeutralId' => $jobId]);
        foreach ($jobs as $job) {
            $this->em->remove($job);
        }

        $this->em->flush();
    }

    /**
     * Get the repository for this mapper.
     *
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('Company\Model\Job');
    }

    // TODO: decide if we should make a separate mapper for this
    public function findSectorsById($id)
    {
        $objectRepository = $this->getSectorsRepository(); // From clause is integrated in this statement
        $qb = $objectRepository->createQueryBuilder('c')
            ->select('c')->where('c.id=:Id')
            ->setParameter('Id', $id);

        if ($qb->getQuery()->getResult()!= null) {
            return $qb->getQuery()->getResult()[0];
        }
        return null;
    }

    public function getSectorsRepository()
    {
        return $this->em->getRepository('Company\Model\JobSector');
    }

    // TODO: decide if we should make a separate mapper for this
    public function findCategoryById($id)
    {
        $objectRepository = $this->getCategoryRepository(); // From clause is integrated in this statement
        $qb = $objectRepository->createQueryBuilder('c')
            ->select('c')->where('c.id=:Id')
            ->setParameter('Id', $id);

        if ($qb->getQuery()->getResult()!= null) {
            return $qb->getQuery()->getResult()[0];
        }
        return null;
    }

    public function getCategoryRepository()
    {
        return $this->em->getRepository('Company\Model\JobCategory');
    }

    public function createObjectSelectConfig($targetClass, $property, $label, $name, $locale)
    {
        return [
            'name' => $name,
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'options' => [
                'label' => $label,
                'object_manager' => $this->em,
                'target_class' => $targetClass,
                'property' => $property,
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => ['language' => $locale],
                        // Use key 'orderBy' if using ORM
                        //'orderBy'  => ['lastname' => 'ASC'],

                    ],
                ],
            ]
            //'attributes' => [
            //'class' => 'form-control input-sm'
            //]
        ];
    }

    /**
     * Get the a job by it's id
     *
     * @return JobModel
     */
    public function findJobById($vacancy_id) {
        $qb = $this->getRepository()->createQueryBuilder('j');
        $qb->select('j');
        $qb->where('j.id =:vacancy_id');
        $qb->setParameter('vacancy_id', $vacancy_id);
        return $qb->getQuery()->getResult()[0];
    }

    /**
     * Find all vacancies in categories where companies have not highlighted a vacancy yet
     *
     * @param integer $companyId the id of the company who's
     * highlighted categories will be fetched.
     * @param array $alreadyHighlighted array of languageNeutralIds of categories where a company has a highlighted vacancy
     * @param string $locale the current language of the website
     *
     * @return array Company\Model\JobCategory.
     */
    public function findHighlightableVacancies($companyId, $alreadyHighlighted, $locale)
    {
        $objectRepository = $this->getRepository(); // From clause is integrated in this statement

        $qb = $objectRepository->createQueryBuilder('j');
        $qb -> select('j')
            ->distinct()
            ->join('j.package', 'h')
            ->join('j.category', 'jc')
            ->where('h.company = ?1')
            ->andWhere('j.language = ?2')
            ->andWhere('j.active = 1')
            ->setParameter(1, $companyId)
            ->setParameter(2, $locale);

        if (!empty($alreadyHighlighted)) {
            $qb->andWhere('jc.languageNeutralId NOT in (?3)')
                ->setParameter(3, $alreadyHighlighted);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all vacancies that may be highlighted
     *
     * @return array Company\Model\JobCategory.
     */
    public function getRandomVacancies($highlightIds, $category, $hours, $sector, $language) {
        $objectRepository = $this->getRepository(); // From clause is integrated in this statement

        $qb = $objectRepository->createQueryBuilder('j');
        $qb -> select('j.id')
            -> where('j.active = 1')
            -> andWhere('j.language = ?1')
            -> setParameter(1, $language)
            -> andWhere('j.hours = ?2')
            -> setParameter(2, $hours)
            -> andWhere('IDENTITY(j.sectors) = ?3')
            -> setParameter(3, $sector)
            -> andWhere('IDENTITY(j.category) = (?5)')
            ->setParameter(5, $category);

        if ($highlightIds!=NULL){
            $qb -> andWhere('j.id NOT IN (?4)')
                -> setParameter(4, $highlightIds);
        }


        return $qb->getQuery()->getResult();
    }
}
