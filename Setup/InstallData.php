<?php
namespace Vendor\PrivacyPolicy\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    protected $customerSetupFactory;
    
    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        
        $customerSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'privacy_policy_accepted',
            [
                'type'                      => 'int',
                'label'                     => 'Política de Privacidade Aceita',
                'input'                     => 'boolean',
                'required'                  => false,
                'default'                   => 0,
                'visible'                   => true,
                'user_defined'              => false,
                'system'                    => 0,
                'is_used_in_grid'           => true,
                'is_visible_in_grid'        => true,
                'is_filterable_in_grid'     => true,
                'is_searchable_in_grid'     => true,
            ]
        );
        
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'privacy_policy_accepted');
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $customerSetup->getDefaultAttributeGroupId('customer'));
        $attribute->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit'
        ]);
        $attribute->save();
        $setup->endSetup();
    }
}
