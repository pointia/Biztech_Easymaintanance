<?php
    class Biztech_Easymaintanance_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Widget_Grid_Container
    {
        public function __construct()
        {
            $this->_controller = 'adminhtml_notification';
            $this->_blockGroup = 'easymaintanance';
            $this->_headerText = Mage::helper('easymaintanance')->__('Notification Manager');
            $this->_addButtonLabel = Mage::helper('easymaintanance')->__('Add Notification');
            parent::__construct();
            $this->_removeButton('add');
        }
}