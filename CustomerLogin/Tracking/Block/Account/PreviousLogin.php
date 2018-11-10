<?php
namespace CustomerLogin\Tracking\Block\Account;
use \Magento\Framework\View\Element\Template;
class PreviousLogin extends Template{
protected $customerSession;
protected $_loginHistory;
protected $_customerID;
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
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_loginHistory = $loginHistory;
        $this->customerSession = $customerSession;

    }

    public function getLastLoginTransactionInformationBeforeCurrentLogin(){

		if ($this->checkIfCustomerLoggedIn()) {
		$this->_customerID = $this->customerSession->getCustomer()->getId(); 
		if ($this->checkIfThisIsFirstLogin()) {
			return  $this->getMessageForFirstLogin();
		}
		else{
			return $this->getFirstItemOfPreviouslyCurrentLoginHistoryCollection();
		}
		}
		else{
			return $this->getErrorMessageWhenCustomerIsNotLoggedIn();
		}
		
    	
   		// var_dump($this->_customerID); 


    }
    public function checkIfCustomerLoggedIn(){
    	return $this->customerSession->isLoggedIn();
    }
    public function checkIfThisIsFirstLogin(){
    	//when logged from browser manually
 	 return $this->getCountOfCustomerLogin()==1 ;
 	}
 	//timesOfCustomerIsLoggedIn
    public function getCountOfCustomerLogin(){
    	return count($this->getFirstItemOfPreviouslyCurrentLoginHistoryCollection());
    } 
    
    public function getFirstItemOfPreviouslyCurrentLoginHistoryCollection(){
        // return 

        $this->_customerID = $this->customerSession->getCustomer()->getId(); 
       $collection = $this->getCustomCollection()->addFieldToSelect("*")->addFieldToFilter("id", array("neq" => $this->getLastLoginId()))->setOrder("login_time","DESC");
        return $collection->getFirstItem();
        
    } 
    public function getPreviouslyOfCurrentLoginId(){
       return $this->getFirstItemOfPreviouslyCurrentLoginHistoryCollection()->getId();
    }
    public function getPreviouslyOfCurrentLoginIpAddress(){
       return $this->getFirstItemOfPreviouslyCurrentLoginHistoryCollection()->getIpAddress();
    }
    public function getPreviouslyOfCurrentLoginUserAgent(){
       return $this->getFirstItemOfPreviouslyCurrentLoginHistoryCollection()->getUserAgent();
    }
    public function getPreviouslyOfCurrentLoginTime(){
       return $this->getFirstItemOfPreviouslyCurrentLoginHistoryCollection()->getLoginTime();
    }


    public function getLastLoginId(){

        // $this->_customerID = $this->customerSession->getCustomer()->getId(); 
       return  $this->getCustomCollection()->addFieldToSelect("*")->getLastItem()->getId();
    }
 
    public function getCustomCollection(){ 
    	return $this->_loginHistory->getCollection()->addFieldToFilter("customer_id", array("eq" => $this->_customerID));
    }
    public function getErrorMessageWhenCustomerIsNotLoggedIn(){
    	//may be add manager message
    	return "Customer is not logged in.";
    }
 	

 	public function getMessageForFirstLogin(){
 		return 'This is your first login in website, so there are no previous logins';
 	}
}
