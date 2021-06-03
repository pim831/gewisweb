<?php

namespace Decision\Service;

use Application\Service\AbstractAclService;

use Decision\Model\vacancy as companyAccountModel;

use Zend\Http\Client as HttpClient;

class companyAccount extends AbstractAclService
{

    /**
     * Get all active vacancies of selected company.
     *
     * @param integer $packageID the package id of the company who's active
     * vacancies will be fetched.
     *
     * @return array Job model.
     */
    public function getActiveVacancies($packageID){
        return $this->getcompanyAccountMapper()->findactiveVacancies($packageID);
    }

    public function getCompanyInfo($id = null) {
        if (null === $id) {
            $id = $this->getRole();
        }
    }

    /**
     * Get the default resource ID.
     *
     * @return string
     */
    protected function getDefaultResourceId()
    {
        return 'companyAccount';
    }

    public function getCompany() {
        $companyservice = $this->sm->get('company_auth_service');
        return $companyservice->getIdentity();
    }

    public function getCompanyContactEmail() {
        return $this->getCompany()->getContactEmail();
    }

    /**
     * Get the Acl.
     *
     * @return Zend\Permissions\Acl\Acl
     */
    public function getAcl()
    {
        return $this->sm->get('decision_acl');
    }

    /**
     * Get the CompanyAccount mapper.
     *
     * @return \Decision\Mapper\CompanyAccount
     */
    public function getcompanyAccountMapper()
    {
        return $this->sm->get('decision_mapper_companyAccount');
    }
}
