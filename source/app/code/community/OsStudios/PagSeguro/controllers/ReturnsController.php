<?php
/**
 * Os Studios PagSeguro Payment Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   payment
 * @package    OsStudios_PagSeguro
 * @copyright  Copyright (c) 2012 Os Studios (www.osstudios.com.br)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Tiago Sampaio <tiago.sampaio@osstudios.com.br>
 */

/**
 * 
 * PagSeguro Returns Controller
 *
 */

class OsStudios_PagSeguro_ReturnsController extends Mage_Core_Controller_Front_Action
{
    
    /**
     * Return PagSeguro Singleton Object
     *
     * @return OsStudios_PagSeguro_Model_Hpp
     */
    public function getPagSeguro()
    {
        return Mage::getSingleton('pagseguro/hpp');
    }
    
    
    /**
     * Return Checkout Object
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    
    /**
     * Return Order's Store ID
     * 
     */
    function getOrderStoreId($orderId) {
        return Mage::getModel('sales/order')->load($orderId)->getStoreId();
    }
    
    
    /**
     * Used for two functionalities:
     * 1) Redirect the response from PagSeguro to set success page after finishing an order in PagSeguro
     * 2) Receive and control the requests from the auto data returns from PagSeguro when update order statuses
     * 
     */
    public function returnAction()
    {
        $pagseguro = $this->getPagSeguro();
        $request = $this->getRequest();
        
        if ($request->isPost()) {
        	
            $post = $request->getPost();
        	
            // That is a $_POST. Process Automatic Return.
            $pagseguro->setPostData($request)
                      ->setOrder(Mage::getModel('sales/order')->loadByIncrementId($post['Referencia']))
                      ->processReturn();
            
        } else {
        	
            // That is a $_GET. Redirect to set page.
            $orderId = Mage::getSingleton("core/session")->getPagseguroOrderId();
            
            if ($orderId) {
                $storeId = $this->getOrderStoreId($orderId);
                
                if ($pagseguro->getConfigData('use_return_page_cms', $storeId)) {
                    $url = $pagseguro->getConfigData('return_page', $storeId);
                    Mage::getSingleton("core/session")->setPagseguroOrderId(null);
                } else {
                    $url = 'pagseguro/pay/success';
                }
            } else {
                $url = '';
            }
            $this->_redirect($url);
            
        }
    }
}