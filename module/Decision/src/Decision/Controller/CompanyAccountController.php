<?php

namespace Decision\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Form\Form as Form;
use Zend\Validator\File\IsImage;

class companyaccountController extends AbstractActionController
{
    public $MSG;

    public function indexAction()
    {
        $decisionService = $this->getServiceLocator()->get('decision_service_decision');
        $company = "TestA";

        return new ViewModel([
            //fetch the active vacancies of the logged in company
            'vacancies' => $this->getcompanyAccountService()->getActiveVacancies($company),
            'company' => $company
        ]);
    }

    public function banneruploadAction(){
        // Get useful stuff
        $companyService = $this->getCompanyService();
        $company = $this->getCompanyAccountService()->getCompany()->getCompanyAccount();
        $companyName = $company->getName();
        global $MSG;

        // Get Zend validator
        $image_validator = new IsImage();

        // Get form
        $packageForm = $companyService->getPackageForm('banner');

        // Handle incoming form results
        $request = $this->getRequest();
        if ($request->isPost()) {
            $files = $request->getFiles();
            $post = $request->getPost();
            $post['published'] = 0;

            // Check if valid timespan is selected
            if(new \DateTime($post['expirationDate']) > new \DateTime($post['startDate'])){
                // Check if the upload file is an image
                if ($image_validator->isValid($files['banner'])) {
                    $image = $files['banner'];
                    // Check if the size of the image is 90x728
                    if ($this->checkImageSize($image, $packageForm)) {
                        // Check if Company has enough credits and subtract them if so
                        if ($this->deductCredits($post, $company, $companyService)) {
                            // Upload the banner to database and redirect to Companypanel
                            if ($companyService->insertPackageForCompanySlugNameByData(
                                $companyName,
                                $request->getPost(),
                                $image
                            )) {
                                return $this->redirect()->toRoute(
                                    'companyaccount'
                                );
                            }
                        }
                    } else {
                        // TODO Implement cropping tool (Could)
                    }
                } else {
                    $MSG = "Please submit an image.";
                }
            } else {
                $MSG = "Please make sure the expirationdate is after the startingdate.";
            }
            echo $this->function_alert($MSG);
        }

        // Initialize the form
        $packageForm->setAttribute(
            'action',
            $this->url()->fromRoute(
                'companyaccount/bannerupload'
            )
        );

        return new ViewModel([
            'form' => $packageForm
        ]);
    }

    public function function_alert($msg){
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }

    public function deductCredits($post, $company, $companyService) {
        global $MSG;
        $ban_start = new \DateTime($post['startDate']);
        $ban_end = new \DateTime($post['expirationDate']);
        $ban_days = $ban_end->diff($ban_start)->format("%a");

        $ban_credits = $company->getBannerCredits();
        if ($ban_credits >= $ban_days ){
            $ban_credits = $ban_credits - $ban_days;            //deduct banner credits based on days scheduled

            $company->setBannerCredits($ban_credits);           //set new credits
            $ban_credits = $company->getBannerCredits();
            $companyService->saveCompany();
            return true;
        }
        $MSG = "The amount of credits needed is: " . $ban_days . ". The amount you have is: " . $ban_credits . ".";
        return false;
    }

    public function checkImageSize($image, $packageForm) {
        global $MSG;
        list($image_width, $image_height) = getimagesize($image['tmp_name']);

        if ($image_height != 90 ||
        $image_width != 728) {
            $MSG = "The image you submitted does not have the right dimensions. " .
                "The dimensions of your image are " . $image_height . " x " . $image_width .
                ". The dimensions of the image should be 90 x 728.";
            return false;
        }
        return true;
    }

    public function dummyAction(){
        return new ViewModel();
    }

    public function profileAction() {
        return new ViewModel();
    }


    public function test(){
        return "test";
    }

    public function settingsAction() {
        $company = "COmpany";
        $companyInfo = $this->getSettingsService()->getCompanyInfo($company);
        $companyPackageInfo = $this->getSettingsService()->getCompanyPackageInfo($companyInfo[0]->getId());

        return new ViewModel([
            'companyPackageInfo' => $companyPackageInfo,
            'companyInfo'  => $companyInfo,
            'settingsService' => $this->getSettingsService()
        ]);
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
     *
     */
    public function createVacancyAction()
    {
        // Get useful stuff
        $companyService = $this->getCompanyService();
        $companyForm = $companyService->getJobFormCompany();

        // Get parameters
//        $companyName = $this->params('slugCompanyName');
//        $packageId = $this->params('packageId');
        $companyName = 'Phillips';
        $packageId = 2;


//        $company = $this->identity()->getMember();

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
                // Redirect to edit page
                return $this->redirect()->toRoute(
                    'companyAccount/vacancies'
                );
            }
        }

        // TODO: change redirect after company has been created.

        // Initialize the form
        $companyForm->setAttribute(
            'action',
            $this->url()->fromRoute(
                'admin_company/editCompany/editPackage/addJob',
                [
                    'slugCompanyName' => $companyName,
                    'packageId' => $packageId
                ]
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
     * Method that returns the service object for the company module.
     *
     * @return Decision\Service\Settings
     */
    protected function getSettingsService()
    {
        return $this->getServiceLocator()->get('decision_service_settings');
    }

    /**
     * Get the CompanyAccount service.
     *
     * @return Decision\Service\CompanyAccount
     */
    public function getcompanyAccountService()
    {
        return $this->getServiceLocator()->get('decision_service_companyAccount');
    }

}
