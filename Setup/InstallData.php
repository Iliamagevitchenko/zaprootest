<?php

namespace Test\Status\Setup;

use Magento\Eav\Model\ResourceModel\Entity\Attribute as AttributeResource;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    const STATUS_ATTR_CODE = 'test_status';

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $attributeSetFactory;

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var AttributeResource
     */
    private $attributeResource;

    /**
     * InstallData constructor.
     * @param AttributeSetFactory $attributeSetFactory
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeResource $attributeResource
     */
    public function __construct(
        AttributeSetFactory $attributeSetFactory,
        CustomerSetupFactory $customerSetupFactory,
        AttributeResource $attributeResource
    ) {
        $this->attributeSetFactory = $attributeSetFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeResource = $attributeResource;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()
            ->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            self::STATUS_ATTR_CODE,
            [
                'type' => 'varchar',
                'label' => 'Status',
                'input' => 'text',
                'unique' => false,
                'required' => false,
                'default' => null,
                'sort_order' => 100,
                'system' => false,
                'position' => 100,
                'visible' => true,
                'visible_on_front' => true,
            ]
        );

        $attribute = $customerSetup->getEavConfig()
            ->getAttribute(Customer::ENTITY, self::STATUS_ATTR_CODE)
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer'],
            ]);

        $this->attributeResource->save($attribute);

        $setup->endSetup();
    }
}
