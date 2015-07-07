<?php

    class Biztech_Easymaintanance_Block_Adminhtml_Notification_Grid extends Mage_Adminhtml_Block_Widget_Grid
    {
        public function __construct()
        {
            parent::__construct();
            $this->setId('notificationGrid');
            $this->setDefaultSort('notification_id');
            $this->setDefaultDir('ASC');
            $this->setSaveParametersInSession(true);  
        }

        protected function _prepareCollection()
        {
            $collection = Mage::getModel('easymaintanance/notification')->getCollection();
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }

        protected function _prepareColumns()
        {
            $this->addColumn('notification_id', array(
                    'header'    => Mage::helper('easymaintanance')->__('ID'),
                    'align'     =>'right',
                    'width'     => '50px',
                    'index'     => 'notification_id',
                ));

            $this->addColumn('name', array(
                    'header'    => Mage::helper('easymaintanance')->__('Name'),
                    'align'     =>'left',
                    'index'     => 'name',
                ));

            $this->addColumn('email', array(
                    'header'    => Mage::helper('easymaintanance')->__('Email'),
                    'align'     =>'left',
                    'index'     => 'email',
                )); 


            $this->addExportType('*/*/exportCsv', Mage::helper('easymaintanance')->__('CSV'));
            $this->addExportType('*/*/exportXml', Mage::helper('easymaintanance')->__('XML'));

            return parent::_prepareColumns();
        }

        protected function _prepareMassaction()
        {
            $this->setMassactionIdField('notification_id');
            $this->getMassactionBlock()->setFormFieldName('notification');

            $this->getMassactionBlock()->addItem('notify', array(
                    'label'    => Mage::helper('easymaintanance')->__('Notify'),
                    'url'      => $this->getUrl('*/*/massNotify'),
                    'confirm'  => Mage::helper('easymaintanance')->__('Are you sure you want to send notification?')
                ));               

            $this->getMassactionBlock()->addItem('delete', array(
                    'label'    => Mage::helper('easymaintanance')->__('Delete'),
                    'url'      => $this->getUrl('*/*/massDelete'),
                    'confirm'  => Mage::helper('easymaintanance')->__('Are you sure you want to remove User?')
                ));                                      

            return $this;
        }

        public function getRowUrl($row)
        {
            return;
        }

}