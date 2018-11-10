<?php
namespace CustomerLogin\Tracking\Model\ResourceModel;
class LoginHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('customer_login_history', 'id');
    }
}