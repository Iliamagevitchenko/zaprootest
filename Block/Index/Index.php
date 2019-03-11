<?php


namespace Test\Status\Block\Index;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Test\Status\Setup\InstallData;

class Index extends \Magento\Customer\Block\Account\Customer
{

    /** @var Session */
    protected $customerSession;

    /** @var CustomerRepositoryInterface */
    protected $customerRepository;

    /**
     * Index constructor.
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepository
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerRepository,
        Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        parent::__construct($context, $httpContext, $data);
    }


    /**
     * Get test_status attribute
     *
     * @return string|null
     */
    public function getTestStatus()
    {
        $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
        /** @var \Magento\Framework\Api\AttributeValue $customStatus */
        $status = $customer->getCustomAttribute(InstallData::STATUS_ATTR_CODE);
        if (isset($status)) {
            return $status->getValue();
        }
        return null;
    }
}
