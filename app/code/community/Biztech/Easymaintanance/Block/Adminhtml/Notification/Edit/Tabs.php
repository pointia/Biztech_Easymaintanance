<?php

    class Biztech_Easymaintanance_Block_Adminhtml_Notification_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
    {

        public function __construct()
        {
            parent::__construct();
            $this->setId('notification_tabs');
            $this->setDestElementId('edit_form');
            $this->setTitle(Mage::helper('easymaintanance')->__('Notification Information'));
        }

        protected function _beforeToHtml()
        {
            $this->addTab('form_section', array(
                    'label'     => Mage::helper('easymaintanance')->__('Notification Information'),
                    'title'     => Mage::helper('easymaintanance')->__('Notification Information'),
                    'content'   => $this->getLayout()->createBlock('easymaintanance/adminhtml_notification_edit_tab_form')->toHtml(),
                ));

            return parent::_beforeToHtml();
        }
}