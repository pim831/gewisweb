<?php


namespace User\Model;

use Company\Model\Company;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Company model.
 *
 * @ORM\Table(name="CompanyUser")
 * @ORM\Entity
 */

class CompanyUser extends Model implements RoleInterface, ResourceInterface
{
    /**
     * The membership number.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * The company's contactEmail address.
     * @ORM\Column(type="string")
     */
    protected $contactEmail;

    /**
     * The company's password.
     *
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * Companies sessions
     *
     * @ORM\OneToMany(targetEntity="User\Model\Session", mappedBy="company")
     */
    protected $sessions;

    /**
     * Constructor
     */
    // TODO: comments
    public function __construct(NewCompany $newCompany = null)
    {
        if (null !== $newCompany) {
            $this->id = $newCompany->getId();
            $this->companyAccount = $newCompany->getCompany();
            $this->contactEmail = $newCompany->getContactEmail();
        }
    }

    /**
     * The corresponding member for this user.
     *
     * @ORM\OneToOne(targetEntity="Company\Model\Company", fetch="EAGER")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    protected $companyAccount;


    /**
     * Get the membership number.
     *
     * @return int
     */
    public function getLidnr()
    {
        return $this->id;
    }

    /**
     * Get the id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the company's contactEmailaddress.
     *
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Get the password hash.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the password hash.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $contactEmail
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * @param mixed $sessions
     */
    public function setSessions($sessions)
    {
        $this->sessions = $sessions;
    }

    /**
     * @return CompanyUser
     */
    public function getCompanyAccount()
    {
        return $this->companyAccount;
    }

    /**
     * @param CompanyUser $companyAccount
     */
    public function setCompanyAccount($companyAccount)
    {
        $this->companyAccount = $companyAccount;
    }

    /**
     * Get the company's role ID.
     *
     * @return string
     */
    public function getRoleId()
    {
        return 'company_user_' . $this->getLidnr();
    }

    /**
     * Get the company role name.
     *
     * @return array Role names
     */
    public function getRoleNames()
    {
        return ["company_user"];
    }

    /**
     * Get the company's resource ID.
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'companyUser';
    }

    /**
     * Updates this object with values in the form of getArrayCopy()
     *
     */
    public function exchangeArray($data)
    {
        $this->setContactEmail($this->updateIfSet($data['contactEmail'],''));
    }
}
