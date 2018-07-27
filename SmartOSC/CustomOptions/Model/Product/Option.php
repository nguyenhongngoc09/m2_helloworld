<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 25/07/2018
 * Time: 18:42
 */

namespace SmartOSC\CustomOptions\Model\Product;

use SmartOSC\CustomOptions\Api\Data\ProductCustomOptionInterface;
// use SmartOSC\CustomOptions\Model\ResourceModel\Options\CollectionFactory ;

class Option extends \Magento\Catalog\Model\Product\Option implements ProductCustomOptionInterface
{
    const KEY_PRODUCT_IMAGE = 'image';
    const KEY_PRODUCT_THUMB_COLOR = 'thumb_color';
    const KEY_PRODUCT_DISPLAY_MODE = 'display_mode';
    const KEY_PRODUCT_IS_DEFAULT = 'is_default';
    /**
     * @var \SmartOSC\CustomOptions\Model\ResourceModel\Options\CollectionFactory
     */
    protected $customOptionsCollectionFactory;
    /**
     * @var \SmartOSC\CustomOptions\Model\ResourceModel\Options\CollectionFactory
     */
    protected $optionsCollectionFactory;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Catalog\Model\Product\Option\Value $productOptionValue,
        \Magento\Catalog\Model\Product\Option\Type\Factory $optionFactory,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\Product\Option\Validator\Pool $validatorPool,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
        // CollectionFactory $optionsCollectionFactory
    ) {
        parent::__construct(
            $context, $registry, $extensionFactory, $customAttributeFactory, $productOptionValue,
            $optionFactory, $string, $validatorPool, $resource, $resourceCollection, $data
        );
        // $this->optionsCollectionFactory = $optionsCollectionFactory;
    }
    
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\SmartOSC\CustomOptions\Model\ResourceModel\Options::class);
        parent::_construct();
    }
    
    /**
     * Get group name of option by given option type
     *
     * @param string $type
     * @return string
     */
    public function getGroupByType($type = null)
    {
        if ($type === null) {
            $type = $this->getType();
        }
        
        $optionGroupsToTypes = [
            self::OPTION_TYPE_FIELD => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_AREA => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_FILE => self::OPTION_GROUP_FILE,
            self::OPTION_TYPE_DROP_DOWN => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_RADIO => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_CHECKBOX => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_MULTIPLE => self::OPTION_GROUP_SELECT,
            // custom options
            self::OPTION_TYPE_THUMB_GALLERY => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_THUMB_GALLERY_POPUP => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_THUMB_GALLERY_MULTI_SELECT => self::OPTION_GROUP_SELECT,
            // end custom options
            self::OPTION_TYPE_DATE => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_DATE_TIME => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_TIME => self::OPTION_GROUP_DATE,
        ];
        
        return isset($optionGroupsToTypes[$type]) ? $optionGroupsToTypes[$type] : '';
    }
    
    public function getExtraCustomOptions($optionId)
    {
        // $optionsCollectionFactory = $this->optionsCollectionFactory->create();
        // $optionsCollection = $optionsCollectionFactory->getAllOptionsByOptionId($optionId);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $optionsResource = $objectManager->create('\SmartOSC\CustomOptions\Model\ResourceModel\Options\Collection');
        $optionsCollection = $optionsResource->addFieldToFilter('option_id', array('eq' => $optionId));
        $aryOptions = [];
        foreach ($optionsCollection as $optionModel){
            $aryOptions[] = $optionModel->getData();
        }
    
        return $aryOptions;
    }
    
    
    // /**
    //  * Get option thumb color
    //  *
    //  * @return string
    //  */
    // public function getThumbColor()
    // {
    //     return $this->_getData(self::KEY_PRODUCT_THUMB_COLOR);
    // }
    //
    // /**
    //  * Set option thumb color
    //  *
    //  * @param string $thumbColor
    //  *
    //  * @return $this
    //  */
    // public function setThumbColor($thumbColor)
    // {
    //     return $this->setData(self::KEY_PRODUCT_THUMB_COLOR, $thumbColor);
    // }
    //
    // /**
    //  * Get option thumb color
    //  *
    //  * @return string
    //  */
    // public function getDisplayMode()
    // {
    //     return $this->_getData(self::KEY_PRODUCT_DISPLAY_MODE);
    // }
    //
    // /**
    //  * Set option thumb color
    //  *
    //  * @param string $thumbColor
    //  *
    //  * @return $this
    //  */
    // public function setDisplayMode($displayMode)
    // {
    //     return $this->setData(self::KEY_PRODUCT_DISPLAY_MODE, $displayMode);
    // }
    //
    // /**
    //  * Set option thumb color
    //  *
    //  * @param string $thumbColor
    //  *
    //  * @return $this
    //  */
    // public function setImage($image)
    // {
    //     return $this->setData(self::KEY_PRODUCT_IMAGE, $image);
    // }
    //
    // public function getImage()
    // {
    //     return $this->_getData(self::KEY_PRODUCT_IMAGE);
    // }
    //
    // public function setIsDeFault($isDefault)
    // {
    //     return $this->setData(self::KEY_PRODUCT_IS_DEFAULT, $isDefault);
    // }
    //
    // public function getIsDefault()
    // {
    //     return $this->_getData(self::KEY_PRODUCT_IS_DEFAULT);
    // }
}