<?php
namespace CustomerLogin\Tracking\Block\Account;
use \Magento\Framework\View\Element\Template;
use \Magento\Customer\Model\Session;
use \CustomerLogin\Tracking\Block\Account\PreviousLogin;
class LoginHistoryPagination extends Template{
protected $customerSession;
protected $_loginHistory;
protected $_customerID;
protected $_collection; 
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
         \CustomerLogin\Tracking\Model\LoginHistory $loginHistory,
         PreviousLogin $collectoin,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_loginHistory = $loginHistory;
        $this->customerSession = $customerSession; 

    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('LoginHistory'));


        if ($this->getAllLoginTransactionsForFixedCustomer()) {
            $pager = $this->getLayout()->createBlock(
                '\Magento\Theme\Block\Html\Pager',
                'test.news.pager'
            )->setAvailableLimit(array(5=>5,10=>10,15=>15))->setShowPerPage(true)->setCollection(
                $this->getAllLoginTransactionsForFixedCustomer()
            );
            $this->setChild('pager', $pager);
            $this->getAllLoginTransactionsForFixedCustomer()->load();
        }
        return $this;
    }
   
   public function getAllLoginTransactionsForFixedCustomer(){
    $this->_collection = $this->getCustomCollection();
   //get values of current page
    $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
    //get values of current limit
    $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 1;
    // $this->_collection->addFieldToFilter('is_active',1);
    // $this->_collection->setOrder('title','ASC');
    $this->_collection->setPageSize($pageSize);
    $this->_collection->setCurPage($page);
    return $this->_collection;
    
   }
       public function getCustomCollection(){  

        $this->_customerID = $this->customerSession->getCustomer()->getId(); 
        return $this->_loginHistory->getCollection()->addFieldToFilter("customer_id", array("eq" => $this->_customerID));
    }
       public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

}