<?php

namespace SmartOSC\CustomOptions\Ui\DataProvider\Catalog\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product\Image;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Framework\Locale\CurrencyInterface;
use SmartOSC\CustomOptions\Model\OptionsFactory;
use SmartOSC\CustomOptions\Model\ResourceModel\Options\CollectionFactory ;
use Symfony\Component\Debug\Debug;

class CustomOptions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions
{
    /**
     * @var \SmartOSC\CustomOptions\Model\OptionsFactory
     */
    protected $optionsFactory;
    /**
     * @var \SmartOSC\CustomOptions\Model\ResourceModel\Options\CollectionFactory
     */
    protected $optionsCollectionFactory;
    
    public function __construct(
        \Magento\Catalog\Model\Locator\LocatorInterface $locator,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionsConfig,
        \Magento\Catalog\Model\Config\Source\Product\Options\Price $productOptionsPrice,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Stdlib\ArrayManager $arrayManager,
        OptionsFactory $optionsFactory,
        CollectionFactory $optionsCollectionFactory
    ) {
        parent::__construct($locator, $storeManager, $productOptionsConfig, $productOptionsPrice, $urlBuilder, $arrayManager);
        $this->optionsFactory = $optionsFactory;
        $this->optionsCollectionFactory = $optionsCollectionFactory;
    }
    
    const FIELD_IMAGE_VIEW_NAME = 'image';
    const FIELD_IMAGE_UPLOAD_NAME = 'image_upload';
    const FIELD_THUMB_COLOR_NAME = 'thumb_color';
    const FIELD_DISPLAY_MODE_NAME = 'display_mode';
    const FIELD_IS_DEFAULT_NAME = 'is_default';
    
    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        $options = [];
        $productOptions = $this->locator->getProduct()->getOptions() ?: [];
        
        /** @var \Magento\Catalog\Model\Product\Option $option */
        foreach ($productOptions as $index => $option) {
            $optionData = $option->getData();
            $optionData[static::FIELD_IS_USE_DEFAULT] = !$option->getData(static::FIELD_STORE_TITLE_NAME);
            $options[$index] = $this->formatPriceByPath(static::FIELD_PRICE_NAME, $optionData);
            $values = $option->getValues() ?: [];
            
            foreach ($values as $value) {
                $value->setData(static::FIELD_IS_USE_DEFAULT, !$value->getData(static::FIELD_STORE_TITLE_NAME));
            }
            /** @var \Magento\Catalog\Model\Product\Option $value */
            foreach ($values as $value) {
                $options[$index][static::GRID_TYPE_SELECT_NAME][] = $this->formatPriceByPath(
                    static::FIELD_PRICE_NAME,
                    $value->getData()
                );
            }
    
            // NgocNH get custom option and merge to current options
            if(isset($options[$index][static::GRID_TYPE_SELECT_NAME])){
                foreach ($options[$index][static::GRID_TYPE_SELECT_NAME] as $key => $dbOption){
                    $aryCustomOptions =  $this->getCustomOptions($dbOption['option_id']);
            
                    if(!empty($aryCustomOptions)){
                        $options[$index][static::GRID_TYPE_SELECT_NAME][$key]+= $aryCustomOptions[$key];
                    }
                }
            }
        }

        return array_replace_recursive(
            $data,
            [
                $this->locator->getProduct()->getId() => [
                    static::DATA_SOURCE_DEFAULT => [
                        static::FIELD_ENABLE => 1,
                        static::GRID_OPTIONS_NAME => $options
                    ]
                ]
            ]
        );
    }
    
    /**
     * @param $optionId
     *
     * @return array
     */
    protected function getCustomOptions($optionId){
        $optionsCollectionFactory = $this->optionsCollectionFactory->create();
        $optionsCollection = $optionsCollectionFactory->addFieldToFilter('option_id', $optionId);
        $aryOptions = [];
        foreach ($optionsCollection as $optionModel){
            $aryOptions[] = $optionModel->getData();
        }
        
        return $aryOptions;
    }
    
    
    protected function getTypeFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Option Type'),
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'component' => 'Magento_Catalog/js/custom-options-type',
                        'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                        'selectType' => 'optgroup',
                        'dataScope' => static::FIELD_TYPE_NAME,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => $this->getProductOptionTypes(),
                        'disableLabel' => true,
                        'multiple' => false,
                        'selectedPlaceholders' => [
                            'defaultPlaceholder' => __('-- Please select --'),
                        ],
                        'validation' => [
                            'required-entry' => true
                        ],
                        'groupsConfig' => [
                            'text' => [
                                'values' => ['field', 'area'],
                                'indexes' => [
                                    static::CONTAINER_TYPE_STATIC_NAME,
                                    static::FIELD_PRICE_NAME,
                                    static::FIELD_PRICE_TYPE_NAME,
                                    static::FIELD_SKU_NAME,
                                    static::FIELD_MAX_CHARACTERS_NAME
                                ]
                            ],
                            'file' => [
                                'values' => ['file'],
                                'indexes' => [
                                    static::CONTAINER_TYPE_STATIC_NAME,
                                    static::FIELD_PRICE_NAME,
                                    static::FIELD_PRICE_TYPE_NAME,
                                    static::FIELD_SKU_NAME,
                                    static::FIELD_FILE_EXTENSION_NAME,
                                    static::FIELD_IMAGE_SIZE_X_NAME,
                                    static::FIELD_IMAGE_SIZE_Y_NAME
                                ]
                            ],
                            'select' => [
                                'values' => ['drop_down', 'radio', 'checkbox', 'multiple', 'thumb_gallery', 'thumb_gallery_popup', 'thumb_gallery_multi_select'],
                                'indexes' => [
                                    static::GRID_TYPE_SELECT_NAME,
                                ],
                            ],
                            'data' => [
                                'values' => ['date', 'date_time', 'time'],
                                'indexes' => [
                                    static::CONTAINER_TYPE_STATIC_NAME,
                                    static::FIELD_PRICE_NAME,
                                    static::FIELD_PRICE_TYPE_NAME,
                                    static::FIELD_SKU_NAME
                                ]
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Get config for grid for "select" types
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getSelectTypeGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Value'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'renderDefaultRecord' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => static::FIELD_SORT_ORDER_NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::FIELD_TITLE_NAME => $this->getTitleFieldConfig(10),
                        static::FIELD_PRICE_NAME => $this->getPriceFieldConfig(20),
                        static::FIELD_PRICE_TYPE_NAME => $this->getPriceTypeFieldConfig(30, ['fit' => true]),
                        static::FIELD_SKU_NAME => $this->getSkuFieldConfig(40),
                        
                        // SmartOSC config fields
                        static::FIELD_IMAGE_VIEW_NAME => $this->getImageViewFieldConfig(41),
                        static::FIELD_IMAGE_UPLOAD_NAME => $this->getImageUploadFieldConfig(42),
                        static::FIELD_THUMB_COLOR_NAME => $this->getThumbColorFieldConfig(43),
                        static::FIELD_DISPLAY_MODE_NAME => $this->getDisplayModeFieldConfig(44),
                        static::FIELD_IS_DEFAULT_NAME => $this->getIsDefaultFieldConfig(45),
                        // End SmartOSC config fields
                        
                        static::FIELD_SORT_ORDER_NAME => $this->getPositionFieldConfig(50),
                        static::FIELD_IS_DELETE => $this->getIsDeleteFieldConfig(60)
                    ]
                ]
            ]
        ];
    }
    
    protected function getDisplayModeFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Display Mode'),
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'dataScope' => static::FIELD_DISPLAY_MODE_NAME,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => [
                            ['value' => 'image', 'label' => 'Image'],
                            ['value' => 'color', 'label' => 'Color']
                        ]
                    ],
                ],
            ],
        ];
    }
    
    protected function getIsDefaultFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Is Default'),
                        'componentType' => Field::NAME,
                        'formElement' => Checkbox::NAME,
                        'dataScope' => static::FIELD_IS_DEFAULT_NAME,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'value' => '1',
                        'prefer' => 'toggle',
                        'valueMap' => [
                            'true' => '1',
                            'false' => '0'
                        ],
                    ],
                ],
            ],
        ];
    }
    
    protected function getImageViewFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Image'),
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => static::FIELD_IMAGE_VIEW_NAME,
                        'dataType' => Text::NAME,
                        'elementTmpl' => 'SmartOSC_CustomOptions/image-view',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }
    
    // protected function getImageUploadFieldConfig($sortOrder)
    // {
    //     return [
    //         'arguments' => [
    //             'data' => [
    //                 'config' => [
    //                     'label' => __('Upload'),
    //                     'componentType' => 'fileUploader',
    //                     // 'formElement' => 'fileUploader',
    //                     'elementTmpl' => 'Magento_Ui/js/form/element/file-uploader',
    //                     'previewTmpl' => 'Magento_Catalog/image-preview',
    //                     'dataScope' => static::FIELD_IMAGE_UPLOAD_NAME,
    //                     'dataType' => Text::NAME,
    //                     'sortOrder' => $sortOrder,
    //                     'source' => 'Options',
    //                     'uploaderConfig' => [
    //                         'url' => 'custom_options/image/upload'
    //                     ]
    //                 ],
    //             ],
    //         ],
    //     ];
    // }
    
    protected function getImageUploadFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        // 'label' => __('Upload'),
                        'componentType' => 'fileUploader',
                        'formElement' => 'fileUploader',
                        'elementTmpl' => 'Magento_Ui/js/form/element/file-uploader',
                        'previewTmpl' => 'Magento_Catalog/image-preview',
                        'dataScope' => static::FIELD_IMAGE_UPLOAD_NAME,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'source' => 'Options',
                        'uploaderConfig' => [
                            'url' => 'custom_options/image/upload'
                        ]
                    ],
                ],
            ],
        ];
    }
    
    protected function getThumbColorFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Thumb Color'),
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'component' => 'SmartOSC_CustomOptions/js/thumb-color',
                        'elementTmpl' => 'SmartOSC_CustomOptions/thumb-color-input',
                        'dataScope' => static::FIELD_THUMB_COLOR_NAME,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }
    
}