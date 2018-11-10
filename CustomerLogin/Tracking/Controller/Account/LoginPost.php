<?php

namespace CustomerLogin\Tracking\Controller\Account;

use \Magento\Customer\Model\Account\Redirect as AccountRedirect;
use \Magento\Framework\App\Action\Context;
use \Magento\Customer\Model\Session;
use \Magento\Customer\Api\AccountManagementInterface;
use \Magento\Customer\Model\Url as CustomerUrl;
use \Magento\Framework\Exception\EmailNotConfirmedException;
use \Magento\Framework\Exception\AuthenticationException;
use \Magento\Framework\Data\Form\FormKey\Validator;
use CustomerLogin\Tracking\Model\LoginHistory;
use \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use  \Magento\Framework\ObjectManager\ObjectManager;
use \Magento\Framework\Stdlib\DateTime\DateTime;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoginPost extends \Magento\Customer\Controller\Account\LoginPost {
 
 /**
* @var \Tutorial\SimpleNews\Model\NewsFactory
*/
protected $formKeyValidator;
protected $resultRedirectFactory;
protected $customerAccountManagement;
protected $session;
protected $customerUrl;
protected $_loginHistory;
protected $_remoteAddress;
protected $_objectManager;
protected $_currentTimestamp;
/**
* @param Context $context
* @param Validator $formKeyValidator
*/
public function __construct(Context $context, Session $session, AccountManagementInterface $customerAccountManagement, CustomerUrl $customerUrl, Validator $formKeyValidator, AccountRedirect $resultRedirectFactory, LoginHistory $_loginHistory, RemoteAddress $remoteAddress, ObjectManager $objectManager, DateTime $currentTimestamp) {
$this->formKeyValidator = $formKeyValidator;
$this->resultRedirectFactory = $resultRedirectFactory;
$this->customerAccountManagement = $customerAccountManagement;
$this->session = $session;
$this->customerUrl = $customerUrl;
$this->_loginHistory = $_loginHistory;
$this->_remoteAddress = $remoteAddress;
$this->_objectManager = $objectManager;
$this->_currentTimestamp = $currentTimestamp;
parent::__construct($context, $session, $customerAccountManagement, $customerUrl, $formKeyValidator, $resultRedirectFactory,$remoteAddress, $objectManager, $currentTimestamp);
}

    public function execute() {
        if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('home');
            return $resultRedirect;
        }

        if ($this->getRequest()->isPost()) {
            
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                    $this->session->setCustomerDataAsLoggedIn($customer);
                    $this->session->regenerateId();
                    $this->saveLoginHistoryIntoModel();
                        // exit;
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath("logintracking/index/login"); // set this path to what you want your customer to go
                    // $resultRedirect->setPath("customer/account/loginPost"); 
                    return $resultRedirect;
                    

                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                            'This account is not confirmed.' .
                            ' <a href="%1">Click here</a> to resend confirmation email.', $value
                    );
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                } catch (AuthenticationException $e) {
                    $message = __('Invalid login or password.');
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    exit();//exception generated after runtime
                    $this->messageManager->addError(__('Invalid login or password.'));
                }
            } else {
                $this->messageManager->addError(__('A login and a password are required.'));
            }
        }  
    } 
    public function getIPAddress1(){

    }  

     public function getUserAgent(){
        return $_SERVER['HTTP_USER_AGENT'];
     }

     public function checkDeviceType(){
        var_dump($_SERVER);
        return stripos($_SERVER['HTTP_USER_AGENT'],"desktop")==false;
     }
     public function getIPAddress(){

        /** @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $a  */
       return $this->_objectManager->get('\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress')->getRemoteAddress();

     }
     public function getCurrentTimestamp(){
        return $this->_currentTimestamp->gmtDate();
     }
     public function saveLoginHistoryIntoModel(){
    $this->_loginHistory->setData('ip_address',$this->getIPAddress());
    $this->_loginHistory->setData('user_agent',$this->getUserAgent());
    $this->_loginHistory->setData('customer_id',$this->session->getCustomer()->getId());
    $this->_loginHistory->setData('login_time',$this->getCurrentTimestamp());
    $this->_loginHistory->save();
     } 
}