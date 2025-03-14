<?php

namespace Vendor\PrivacyPolicy\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session as CustomerSession;

class Accept extends Action
{
    protected $customerRepository;
    protected $resultJsonFactory;
    protected $customerSession;
    
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerRepository,
        JsonFactory $resultJsonFactory,
        CustomerSession $customerSession
    ) {
        $this->customerRepository = $customerRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        try {
            $customerId = $this->customerSession->getCustomerId();
            $customerData = $this->customerRepository->getById($customerId);
            $customerData->setCustomAttribute('privacy_policy_accepted', 1);
            $this->customerRepository->save($customerData);
            $this->customerSession->unsShowPrivacyModal();
            $result = ['success' => true];
        } catch (\Exception $e) {
            $result = ['success' => false, 'error' => $e->getMessage()];
        }
        return $resultJson->setData($result);
    }
}
