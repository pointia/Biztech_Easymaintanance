<?php   
    class Biztech_Easymaintanance_Block_Feedback extends Mage_Core_Block_Template
    {
        public function _prepareLayout()
        {    
            return parent::_prepareLayout();
        }

        public function getFeedback()     
        { 
            if (!$this->hasData('feedback')) {
                $this->setData('feedback', Mage::registry('feedback'));
            }
            return $this->getData('feedback');

        }

        public function addItem($type, $path)
        {
            $config = Mage::getStoreConfig('easymaintanance/contactus/active');
            if($config == "1")
            {
                $head = $this->getLayout()->getBlock('head');
                return $head->addItem($type,$path);
            }
        }
}