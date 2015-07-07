<?php

    class Biztech_Easymaintanance_Block_Adminhtml_Notification_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    {
        protected function _prepareForm()
        {          
            $form = new Varien_Data_Form();
            $this->setForm($form);
            $fieldset = $form->addFieldset('notification_form', array('legend'=>Mage::helper('easymaintanance')->__('Notification information')));

            $fieldset->addField('name', 'text', array(
                    'label'     => Mage::helper('easymaintanance')->__('Name'),
                    'class'     => 'required-entry',
                    'required'  => true,
                    'name'      => 'name',
                ));

            $fieldset->addField('email', 'text', array(
                    'label'     => Mage::helper('easymaintanance')->__('Email'),
                    'class'     => 'required-entry',
                    'required'  => true,
                    'name'      => 'email',
                ));



            /*$fieldset->addField('filename', 'file', array(
            'label'     => Mage::helper('easymaintanance')->__('File'),
            'required'  => false,
            'name'      => 'filename',
            ));

            $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('easymaintanance')->__('Status'),
            'name'      => 'status',
            'values'    => array(
            array(
            'value'     => 1,
            'label'     => Mage::helper('easymaintanance')->__('Enabled'),
            ),

            array(
            'value'     => 2,
            'label'     => Mage::helper('easymaintanance')->__('Disabled'),
            ),
            ),
            ));

            $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('easymaintanance')->__('Content'),
            'title'     => Mage::helper('easymaintanance')->__('Content'),
            'style'     => 'width:700px; height:500px;',
            'wysiwyg'   => false,
            'required'  => true,
            ));*/

            if ( Mage::getSingleton('adminhtml/session')->getNotificationData() )
            {
                $form->setValues(Mage::getSingleton('adminhtml/session')->getNotificationData());
                Mage::getSingleton('adminhtml/session')->setNotificationData(null);
            } elseif ( Mage::registry('notification_data') ) {
                $form->setValues(Mage::registry('notification_data')->getData());
            }
            return parent::_prepareForm();
        }
}