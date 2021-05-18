<?php

namespace Decision\Controller;

use Company\Service\Company as CompanyService;
use Doctrine\DBAL\Schema\View;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class companyaccountController extends AbstractActionController
{
    public function IndexAction()
    {
        if (!$this->getCompanyAccountService()->isAllowed('view')) {
            $translator = $this->getCompanyAccountService()->getTranslator();
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You are not allowed to view this page')
            );
        }
        return new ViewModel();
    }


    public function dummyAction(){
        return new ViewModel();
    }


    public function profileAction() {
        return new ViewModel();
    }
    public function settingsAction() {
        return new ViewModel();
    }


    public function vacanciesAction(){
        return new ViewModel();
    }

    public function editVacancyAction() {
        return new ViewModel();
    }


    /**
     * Action that allows adding a job
     *
     */
    public function createVacancyAction()
    {
        // Get useful stuff
        $companyService = $this->getCompanyService();
        $companyForm = $companyService->getJobFormCompany();


        $company = $this->getCompanyAccountService()->getCompany()->getCompanyAccount();
        $companyName = $company->getName();
        $packageId = $company->getJobPackageId();
        if($packageId == null) {
            $translator = $this->getCompanyAccountService()->getTranslator();
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You do not have a package to create vacancies.')
            );
        }
        // Handle incoming form results
        $request = $this->getRequest();


        if ($request->isPost()) {
            // Check if data is valid, and insert when it is
            $job = $companyService->createJob(
                $packageId,
                $request->getPost(),
                $request->getFiles()
            );

            if ($job) {

                //Send approval email to admin
                $email = $this->getDecisionEmail();
                $email->sendApprovalMail($company);

                // Redirect to edit page
                return $this->redirect()->toRoute(
                    'companyaccount/vacancies'
                );
            }
        }

        // Initialize the form
        $companyForm->setAttribute(
            'action',
            $this->url()->fromRoute(
                'companyaccount/vacancies/createvacancy'
            )
        );

        // Initialize the view
        $vm = new ViewModel([
            'form' => $companyForm,
            'languages' => $this->getLanguageDescriptions(),
        ]);

        return $vm;
    }

    private function getLanguageDescriptions()
    {
        $companyService = $this->getCompanyService();
        $languages = $companyService->getLanguages();
        $languageDictionary = [];
        foreach ($languages as $key) {
            $languageDictionary[$key] = $companyService->getLanguageDescription($key);
        }

        return $languageDictionary;
    }


    /**
     * Method that returns the service object for the company module.
     *
     * @return CompanyService
     */
    protected function getCompanyService()
    {
        return $this->getServiceLocator()->get('company_service_company');
    }

    /**
     * Get the company service.
     *
     * @return Decision\Service\CompanyAccount
     */
    public function getCompanyAccountService()
    {
        return $this->getServiceLocator()->get('decision_service_companyaccount');
    }

    /**
     * Method that returns the service object for the company module.
     *
     * @return DesicionEmail
     */
    protected function getDecisionEmail()
    {
        return $this->getServiceLocator()->get('decision_service_decisionEmail');
    }
}
