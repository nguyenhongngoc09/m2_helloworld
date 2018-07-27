<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 25/07/2018
 * Time: 14:09
 */

namespace SmartOSC\CustomOptions\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SmartOSC\CustomOptions\Model\OptionsFactory;

class SaveProductCustomOptionsObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \SmartOSC\CustomOptions\Model\OptionsFactory
     */
    protected $optionsFactory;
    
    public function __construct(
        RequestInterface $request,
        OptionsFactory $optionsFactory
    ) {
        $this->request = $request;
        $this->optionsFactory = $optionsFactory;
    }
    
    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $saveProductController = $observer->getData('controller');
        $product = $observer->getData('product');
        
        $productOptionsStored = $product->getOptions();
        
        $data = $this->request->getParams();
    
        if (isset($data['product']['options'])) {
            $optionsModel = $this->optionsFactory->create();
        
            foreach ($data['product']['options'] as $key => $option) {
                if (isset($option['values'])) {
                    foreach ($option['values'] as $customOpt) {
                        $dataOption = [
                            'option_id' => null !== $productOptionsStored[$key]->getData('option_id') ?
                                        $productOptionsStored[$key]->getData('option_id') : $option['option_id'],
                            'type' => $option['type'],
                            'image' => isset($customOpt['image_upload'][0]['url']) ?
                                        $customOpt['image_upload'][0]['url'] : $customOpt['image'],
                            'thumb_color' => $customOpt['thumb_color'],
                            'display_mode' => $customOpt['display_mode'],
                            'is_default' => $customOpt['is_default'],
                            'sort_order' => $customOpt['sort_order']
                        ];
        
                        if (isset($customOpt['id'])) {
                            $dataOption['id'] = $customOpt['id'];
                        }
        
                        try {
                            $optionsModel->setData($dataOption);
                            $optionsModel->save();
        
                        } catch (\Exception $e) {
                            $saveProductController->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                            $saveProductController->messageManager->addErrorMessage($e->getMessage());
                        }
    
                    }
                }
        
            }
        }
    }
    
}