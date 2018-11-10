<?php
namespace CustomerLogin\Tracking\Block\Account;
class PreviousLogin extends \Magento\Framework\View\Element\Template{
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
         \Magento\Customer\Model\LoginHistory $loginHistory,
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
			 $this->getMessageForFirstLogin();
		}
		else{
			$this->getLastLoginTransactionsInformationForFixedCustomer();
		}
		}
		else{
			return $this->getErrorMessageWhenCustomerIsNotLoggedIn();
		}
		
    	
   		var_dump($customerID); 


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
    	return count($this->getLastLoginTransactionsInformationForFixedCustomer());
    }
    public function getLastLoginTransactionsInformationForFixedCustomer(){
    	$returnedCollection = $this->getCollection()->addAttributeToSelect("*")->addAttributeToFilter("customer_id", array("eq" => $this->_customerID))->load();//addAttributeToSort("login_time","DESC");
    	var_dump(count($returnedCollection));
    	return $returnedCollection; 
    
	}  
	public function getPreviousCurrentLloginTransaction(){
		$returnedCollection = $this->getLastLoginTransactionsInformationForFixedCustomer();
		return $returnedCollection[0];
	}
    public function getCollection(){
    	return $this->_loginHistory->getCollection();
    }
    public function getErrorMessageWhenCustomerIsNotLoggedIn(){
    	//may be add manager message
    	return "Customer is not logged in.";
    }
 	

 	public function getMessageForFirstLogin(){
 		return 'This is your first login in website, so there are no previous logins';
 	}
}
