<?php


namespace Company\Service;

use Application\Service\AbstractAclService;


class Approval extends AbstractAclService
{

    /**
     * Get all pending approvals
     *
     * @return array ApprovalPending model
     */
    public function getPendingApprovals(){
        return $this->getApprovalMapper()->findPendingApprovals();
    }

    /**
     * Get the default resource ID.
     *
     * @return string
     */
    protected function getDefaultResourceId()
    {
        return 'approval';
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
    public function getApprovalMapper()
    {
        return $this->sm->get('company_mapper_approval');
    }
}
