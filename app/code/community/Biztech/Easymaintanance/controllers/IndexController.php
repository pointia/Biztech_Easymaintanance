<?php

class Biztech_Easymaintanance_IndexController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_EMAIL_RECIPIENT  = 'easymaintanance/contactus/from_mail';
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_CONTACTS     = 'contacts/email/recipient_email';

    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Contact Us Form'));
        $this->renderLayout();
        return $this;
    }

    public function postnotifyAction(){
        $post = $this->getRequest()->getPost();


        if($post){
            $notifyname = $post['notifyuname'];
            $notifyemail = $post['notifymail'];
            // Check if user with the same email already exist in the database
            //TODO: Test
            $model = Mage::getModel( "easymaintanance/notification" )->load( $notifyemail, "email" );
            if($model->getId()){
                $notifydata = array(
                        'result' => 'error',
                        'mesage' => 'A user with same email address has already registered for Notification.'
                );
            }
            else{
                try{
                    // Enter details in the database
                    $model->setName( $notifyname );
                    $model->setEmail( $notifyemail );
                    $model->save();

                    $notifydata = array(
                            'result' => 'success',
                            'mesage' => 'You are registered successfully for Notification.'
                    );
                }

                catch (Exception $e) {
                    $message = $e->getMessage();
                    $notifydata =   array(
                            'result' => 'error',
                            'message' => $message ? $message : "Unable to submit your request. Please, try again later"
                    );

                }


            }
        }
        else{
            $notifydata =  array(
                    'result' => 'error',
                    'message' => "Unable to submit your request. Please, try again later"
                );
        }

        $this->getResponse()->setBody( Mage::helper("core")->jsonEncode($notifydata) );
        return;
    }

    public function postFeedbackAction()
    {
            $post = $this->getRequest()->getPost();
            if ($post) {
                $translate = Mage::getSingleton('core/translate');
                /* @var $translate Mage_Core_Model_Translate */
                $translate->setTranslateInline(false);
                try {
                    $postObject = new Varien_Object();
                    $post['feedbackdetails'] = nl2br($post['feedbackdetails']);
                    if($post['feedbackheard'] == null || $post['feedbackheard'] == '')
                    {
                        $post['feedbackheard'] = 'N/A';
                    }
                    $postObject->setData($post);
                    $this->validate( $post );

                    $store=Mage::app()->getStore();
                    $mailTemplate = Mage::getModel('core/email_template');
                    /* @var $mailTemplate Mage_Core_Model_Email_Template */



                    $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['feedback_email'])
                    ->sendTransactional(
                        'feedback_email_template',
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        $this->getRecipient(),
                        null,
                        array(
                            'data' => $postObject,
                            'store'=> $store
                        )
                    );

                    if (!$mailTemplate->getSentSuccess()) {
                        throw new Exception("Couldn't send Email");
                    }
                    $translate->setTranslateInline(true);
                    $notifydata = array(
                            'result' => 'success',
                            'message' => 'Your request has been sent'

                    );

                } catch (Exception $e) {
                    $message = $e->getMessage();
                    $notifydata = array(
                            'result' => 'error',
                            'message' => $message ? $message : "Unable to submit your request. Please, try again later"

                    );
                }

            }
            else {
                $notifydata = array(
                        'result' => 'error',
                        'message' => 'Unable to submit your request. Please, try again later'

                );

            }
        $this->getResponse()->setBody( Mage::helper("core")->jsonEncode($notifydata) );
        return;
        }

    protected function validate ( $post )
    {
        if(!empty($post['feedbackbuname'])){
            if (!Zend_Validate::is(trim($post['feedbackbuname']) , 'NotEmpty')) {
                throw new Exception("Name is empty");
            }
        }
        if (!Zend_Validate::is(trim($post['feedbackmail']), 'EmailAddress')) {
            throw new Exception("Email not valid");
        }
        if(!empty($post['feedbackdetails'])){
            if (!Zend_Validate::is(trim($post['feedbackdetails']) , 'NotEmpty')) {
                throw new Exception("Details are empty");
            }
        }
    }

    protected function getRecipient(){
        if(Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT)==""){
            return Mage::getStoreConfig(self::XML_PATH_EMAIL_CONTACTS);
        }else{
            return Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
        }
    }

    public function checkTimerAction() {

        $storeId = Mage::app()->getStore()->getStoreId();
        $isEnabled = Mage::getStoreConfig('easymaintanance/general/enabled', $storeId);
        $timerEnabled = Mage::getStoreConfig('easymaintanance/timer/timer_enabled', $storeId);
        $makesiteEnabled = Mage::getStoreConfig('easymaintanance/timer/site_enabled', $storeId);

        if ($isEnabled == 1 && $timerEnabled == 1 && $makesiteEnabled == 1) {


                $timerConfig = new Mage_Core_Model_Config();
                $timerConfig->saveConfig('easymaintanance/general/enabled', "0");
                $timerConfig->saveConfig('easymaintanance/timer/timer_enabled', "0");
                Mage::app()->getCacheInstance()->flush();

            echo true;
        }
    }

}
