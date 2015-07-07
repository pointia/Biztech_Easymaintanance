<?php

class Biztech_Easymaintanance_Model_Observer
{

    const XML_PATH_EMAIL_SENDER = 'contacts/email/sender_email_identity';


    public function initControllerRouters ( $request )
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        if ($this->isEnabled( $storeId ) == 1) {
            if ($this->isMaintenance( $storeId )) {
                $IPs = $this->getAllowedIps( $storeId );
                $currentIP = $this->getCurrentIp();
                $adminIp = $this->getAdminIp( $storeId );
                if ($currentIP === $adminIp) {
                    $this->createLog( 'Access granted for admin with IP: ' . $currentIP . ' and store ' . $storeId, $storeId );
                } else {
                    if (!in_array( $currentIP, $IPs )) {
                        $this->createLog( 'Access denied  for IP: ' . $currentIP . ' and store ' . $storeId, $storeId );

                        $html = Mage::getSingleton( 'core/layout' )->createBlock( 'core/template' )->setTemplate( 'easymaintanance/easymaintanance.phtml' )->toHtml();

                        if ('' !== $html) {
                            Mage::getSingleton( 'core/session', array( 'name' => 'front' ) );
                            $response = $request->getEvent()->getFront()->getResponse();
                            $response->setHeader( 'HTTP/1.1', '503 Service Temporarily Unavailable' );
                            $response->setHeader( 'Status', '503 Service Temporarily Unavailable' );
                            $response->setHeader( 'Retry-After', '5000' );
                            $response->setBody( $html );
                            $response->sendHeaders();
                            $response->outputBody();
                        }
                        exit();
                    } else {
                        $this->createLog( 'Access granted for IP: ' . $currentIP . ' and store ' . $storeId, $storeId );
                    }
                }
            }
        }
    }

    protected function getAdminIp ( $storeId )
    {
        $adminIp = null;
        $allowForAdmin = Mage::getStoreConfig( 'easymaintanance/general/allowforadmin', $storeId );
        if ($allowForAdmin == 1) {
            Mage::getSingleton( 'core/session', array( 'name' => 'adminhtml' ) );
            $adminSession = Mage::getSingleton( 'admin/session' );
            if ($adminSession->isLoggedIn()) {
                $adminIp = $adminSession['_session_validator_data']['remote_addr'];
            }
        }
        return $adminIp;
    }

    protected function getAllowedIps ( $storeId )
    {
        $allowedIPs = Mage::getStoreConfig( 'easymaintanance/general/allowedIPs', $storeId );
        $allowedIPs = preg_replace( '/ /', '', $allowedIPs );
        $IPs = array();
        if ('' !== trim( $allowedIPs )) {
            $IPs = explode( ',', $allowedIPs );
        }
        return $IPs;
    }

    protected function getCurrentIp ()
    {
        $ips = Mage::helper( "core/http" )->getRemoteAddr();
        $ips = explode( ",", $ips );
        return reset( $ips );
    }

    protected function isEnabled ( $storeId )
    {
        return Mage::getStoreConfig( 'easymaintanance/general/enabled', $storeId );
    }

    protected function isMaintenance ( $storeId )
    {
        $adminFrontName = Mage::getConfig()->getNode( 'admin/routers/adminhtml/args/frontName' );
        $redirect_url = array_map( 'trim', explode( "\n", Mage::getStoreConfig( 'easymaintanance/general/redirecturl', $storeId ) ) );
        $area = Mage::app()->getRequest()->getOriginalPathInfo();
        if (preg_match( '/' . $adminFrontName . '/', $area )) {
            return false;
        }
        if (preg_match( '/postFeedback/', $area )) {
            return false;
        }
        if (preg_match( '/postnotify/', $area )) {
            return false;
        }
        if (in_array( $area, $redirect_url )) {
            return false;
        }
        if (preg_match( '/checkTimer/', $area )) {
            return false;
        }
        if (Mage::app()->getRequest()->getBaseUrl() == "/downloader") {
            return false;
        }

        return true;
    }

    private function createLog ( $text, $storeId = null, $zendLevel = Zend_Log::DEBUG )
    {
        $logFile = trim( Mage::getStoreConfig( 'easymaintanance/general/logFileName', $storeId ) );
        if ('' === $logFile) {
            $logFile = 'easymaintenance.log';
        }
        Mage::log( $text, $zendLevel, $logFile );
    }

    public function timeralert ()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $hour = Mage::getStoreConfig( 'easymaintanance/timer/timer_hour', $storeId );
        $min = Mage::getStoreConfig( 'easymaintanance/timer/timer_min', $storeId );

        if ($this->isEnabled( $storeId ) == 1) {

            $time1 = strtotime( Mage::getStoreConfig( 'easymaintanance/timer/timer_date', $storeId ) . " " . $hour . ":" . $min . ":" . "00" );
            $time2 = strtotime( date( "m/d/Y H:i:s", Mage::getModel( 'core/date' )->timestamp( time() ) ) );

            $minutes_diff = (int)round( abs( $time1 - $time2 ) / 60 );

            $alert_min = Mage::getStoreConfig( 'easymaintanance/timer/timer_alert', $storeId );

            if ($minutes_diff <= $alert_min) {
                $fromEmail = Mage::getStoreConfig( self::XML_PATH_EMAIL_SENDER );
                $toEmail = Mage::getStoreConfig( 'easymaintanance/timer/timer_email' );
                $message = Mage::getStoreConfig( 'easymaintanance/timer/timer_email_template' );
                $subject = "Timer Alert";

                try {
                    $mail = new Zend_Mail();
                    $mail->setFrom( $fromEmail );
                    $mail->addTo( $toEmail );
                    $mail->setSubject( $subject );
                    $mail->setBodyHtml( $message );
                    $mail->send();

                } catch (Exception $e) {
                    echo $e->getMassage();
                }
            }
        }
    }
}