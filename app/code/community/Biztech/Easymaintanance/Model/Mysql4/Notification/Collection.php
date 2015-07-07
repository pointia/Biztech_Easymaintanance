<?php

    class Biztech_Easymaintanance_Model_Mysql4_Notification_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
    {
        public function _construct()
        {        
            parent::_construct();
            $this->_init('easymaintanance/notification');
        }
}