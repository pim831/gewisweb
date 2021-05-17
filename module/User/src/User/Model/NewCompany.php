<?php

namespace User\Model;

use DateTime;
use Company\Model\Company;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User model.
 *
 * @ORM\Entity
 */
class NewCompany
{
    /**
     * The membership number.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * The user's email address.
     *
     * @ORM\Column(type="string")
     */
    protected $contactEmail;

    /**
     * The user's activation code.
     *
     * @ORM\Column(type="string")
     */
    protected $code;

    /**
     * Registration attempt timestamp
     *
     * @ORM\Column(type="datetime",nullable=true)
     */
    protected $time;

    /**
     * User's company
     *
     * @ORM\OneToOne(targetEntity="Company\Model\Company")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    protected $company;

    /**
     * Generate an activation code for the user.
     *
     * @param int $length
     *
     * @return string
     */
    public function generateCode($length = 20)
    {
        $ret = '';
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        for ($i = 0; $i < $length; $i++) {
            $ret .= $alphabet[rand(0, strlen($alphabet) - 1)];
        }

        return $ret;
    }

    /**
     * Get the company id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get the user's email address.
     *
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Get the activation code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the registration time.
     *
     * @return DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Constructor.
     *
     * We can populate most values from a member model.
     *
     * @param Company $company
     */
    public function __construct(Company $company = null)
    {
        if (null !== $company) {
            $this->id = $company->getLidnr();
            $this->contactEmail = $company->getContactEmail();
            $this->company = $company;
        }
    }

    /**
     * Get the company.
     *
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }
}
