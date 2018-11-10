<?php

namespace CustomerLogin\Tracking\Controller\Index; 

class Index extends \Magento\Framework\App\Action\Action
{

protected $resultPageFactory; 

public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {

	parent::__construct($context);

	$this->resultPageFactory = $resultPageFactory; 
	
}


public function execute()
{

	$resultPage = $this->resultPageFactory->create();

	$resultPage->getConfig()->getTitle()->set(__('Customer Login Tracking'));

	return $resultPage;

}

}