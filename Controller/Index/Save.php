<?php


namespace Test\Status\Controller\Index;


use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Test\Status\Setup\InstallData;

class Save extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        Validator $formKeyValidator
    ) {
        $this->session = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * Execute save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $validFormKey = $this->formKeyValidator->validate($this->getRequest());

        if ($this->getRequest()->isPost() && $validFormKey) {
            $post = $this->getRequest()->getParam('test_status');
            if (isset($post)) {
                try {
                    $customer = $this->customerRepository->getById($this->session->getCustomerId());
                    $customer->setCustomAttribute(InstallData::STATUS_ATTR_CODE, $post);
                    $this->customerRepository->save($customer);
                    $this->messageManager->addSuccessMessage(__('Status was saved.'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Error during saving status/'));
                }
            }
        }

        return $resultRedirect->setPath('teststatus/index/index');
    }
}
