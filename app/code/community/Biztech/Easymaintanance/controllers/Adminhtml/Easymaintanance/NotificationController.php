<?php

    class Biztech_Easymaintanance_Adminhtml_Easymaintanance_NotificationController extends Mage_Adminhtml_Controller_action
    {

        const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
        const XML_PATH_EMAIL_RECIPIENT  = 'easymaintanance/contactus/from_mail';
        const XML_PATH_EMAIL_CONTACTS     = 'contacts/email/recipient_email';

        protected function _initAction() {
            $this->loadLayout()
            ->_setActiveMenu('notification/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Notifications Manager'), Mage::helper('adminhtml')->__('Notification Manager'));

            return $this;
        }   

        public function indexAction() { 

            $this->_initAction()
            ->renderLayout();
        }

        public function massDeleteAction() {
            $notificationIds = $this->getRequest()->getParam('notification');
            if(!is_array($notificationIds)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
            } else {
                try {
                    foreach ($notificationIds as $notificationId) {
                        $notification = Mage::getModel('easymaintanance/notification')->load($notificationId);
                        $notification->delete();
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                            'Total of %d record(s) were successfully deleted', count($notificationIds)
                        )
                    );
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            $this->_redirect('*/*/index');
        }

        public function massNotifyAction() {
            $notificationIds = $this->getRequest()->getParam('notification');
            if(!is_array($notificationIds)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
            } else {
                try {
                    
                    $translate = Mage::getSingleton('core/translate');

                    $translate->setTranslateInline(false);

                    $mailTemplate = Mage::getModel('core/email_template');
                    
                    $recipient=Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER);
                    $store=Mage::app()->getStore();

                    $notification_model = Mage::getModel('easymaintanance/notification');

                    foreach ($notificationIds as $notificationId) {        
                        $notification = $notification_model->load($notificationId);

                        $to[] = $notification->getEmail(); 
                       
                    }     

                    $email      =   "developer1.test@gmail.com";
                    $postObject = new Varien_Object();
                    $post = array();
                    $postObject->setData($post);

                    $error = false;
                    $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($recipient)
                    ->addBcc($to)
                    ->sendTransactional(
                        'easymaintanance_general_notification_template',
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        $recipient,
                        null,
                        array(
                            'data' => $postObject,
                            'store'=> $store
                        )
                    );
                    if (!$mailTemplate->getSentSuccess()) {
                        throw new Exception($error);
                    }
                    $translate->setTranslateInline(true);
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                            'Notification sent successfully to %d user(s)', count($notificationIds)
                        )
                    );

                } catch (Exception $e) {                       
                    if($e->getMessage() != ''){
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    }
                    else{
                        Mage::getSingleton('adminhtml/session')->addError('Notification Not sent. Please try again later');
                    }
                } 

            }

            $this->_redirect('*/*/');  
        }

        public function exportCsvAction()
        {
            $fileName   = 'notification.csv';
            $content    = $this->getLayout()->createBlock('easymaintanance/adminhtml_notification_grid')
            ->getCsv();

            $this->_sendUploadResponse($fileName, $content);
        }

        public function exportXmlAction()
        {
            $fileName   = 'notification.xml';
            $content    = $this->getLayout()->createBlock('easymaintanance/adminhtml_notification_grid')
            ->getXml();

            $this->_sendUploadResponse($fileName, $content);
        }     

        protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
        {
            $response = $this->getResponse();
            $response->setHeader('HTTP/1.1 200 OK','');
            $response->setHeader('Pragma', 'public', true);
            $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
            $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
            $response->setHeader('Last-Modified', date('r'));
            $response->setHeader('Accept-Ranges', 'bytes');
            $response->setHeader('Content-Length', strlen($content));
            $response->setHeader('Content-type', $contentType);
            $response->setBody($content);
            $response->sendResponse();
            die;
        }

}
