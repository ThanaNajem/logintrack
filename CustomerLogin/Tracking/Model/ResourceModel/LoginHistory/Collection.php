<?php
namespace CustomerLogin\Tracking\Model\ResourceModel\LoginHistory;
/**
 * Subscription Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('CustomerLogin\Tracking\Model\LoginHistory', 'CustomerLogin\Tracking\Model\ResourceModel\LoginHistory');
    }
}