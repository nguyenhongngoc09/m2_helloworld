<?php
/**
 * Created by PhpStorm.
 * User: NgocNH
 * Date: 7/13/2018
 * Time: 10:58 AM
 */

namespace SmartOSC\Helloworld\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    private $_pageResultFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $pageResultFactory
     */
    public function __construct(Context $context, PageFactory $pageResultFactory)
    {
        $this->_pageResultFactory = $pageResultFactory;
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
        $resultPage = $this->_pageResultFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Hello World'));

        return $resultPage;
    }
}
