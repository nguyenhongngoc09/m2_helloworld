<?php
/**
 * Created by PhpStorm.
 * User: NgocNH
 * Date: 7/13/2018
 * Time: 2:38 PM
 */

namespace SmartOSC\Helloworld\Controller\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Json extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonFactory
     */
    private $_jsonResultFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param JsonFactory $jsonResultFactory
     */
    public function __construct(Context $context, JsonFactory $jsonResultFactory)
    {
        $this->_jsonResultFactory = $jsonResultFactory;
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $resultJson = $this->_jsonResultFactory->create();

        $resultJson->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_FORBIDDEN);
        $resultJson->setData(['error_message' => __('What are you doing here?')]);
        return $resultJson;
    }
}
