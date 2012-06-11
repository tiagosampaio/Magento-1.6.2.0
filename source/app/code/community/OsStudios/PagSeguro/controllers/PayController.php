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
 * PagSeguro Payment Controller
 *
 */

class OsStudios_PagSeguro_PayController extends Mage_Core_Controller_Front_Action
{
    
    /**
     * Return PagSeguro Singleton Object
     *
     * @return OsStudios_PagSeguro_Model_Payment
     */
    public function getPagSeguro()
    {
        return Mage::getSingleton('pagseguro/payment');
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
     * Redirect Customer to PagSeguro Checkout Page
     *
     */
    public function redirectAction()
    {
        $pagseguro = $this->getPagSeguro();
        $session = $this->getCheckout();
        $orderId = $this->getRequest()->getParam('order_id');
        
        if (empty($orderId)) {
            $orderId = $session->getLastOrderId();
            $session->clear(); //Cleans the cart session
        }

        $order = Mage::getModel('sales/order')->load($orderId);
        
        if ($order->getId()) {
        
            // Envia email de confirmação ao cliente
            if(!$order->getEmailSent()) {
            	$order->sendNewOrderEmail();
    			$order->setEmailSent(true);
    			$order->save();
            }
            
            $order_redirect = false;
            
            // Checks if Payment Method is really the PagSeguro Method 
            if ($order->getPayment()->getMethod() == $pagseguro->getCode()) {
                
                switch ($order->getState()) {
                    
                    case Mage_Sales_Model_Order::STATE_NEW:
						// Stores Order ID in session and display redirect form to PagSeguro
                        Mage::getSingleton("core/session")->setPagseguroOrderId($orderId);
                        
                        $html = $pagseguro->setOrder($order)->createRedirectForm();
                        
                        $this->getResponse()->setHeader("Content-Type", "text/html; charset=ISO-8859-1", true);
                        $this->getResponse()->setBody($html);
                        break;
                        
                    case Mage_Sales_Model_Order::STATE_HOLDED:
						// Redirect to Print Billet Page
                        if ($order->getPayment()->getPagseguroPaymentMethod() == "Boleto") {
                            $this->_redirectUrl($pagseguro->getPagSeguroBoletoUrl($order->getPayment()->getPagseguroTransactionId(), false));
                            break;
                        }
                        
                    default:
						// Redirect to Order's Page
                        $order_redirect = true;
                        break;
                }
                
            } else {
                $order_redirect = true;
            }
            
            if ($order_redirect) {
                $params = array();
                $params['_secure'] = true;
                $params['order_id'] = $orderId;
                $this->_redirect('sales/order/view', $params);
            }
            
        } else {
            $this->_redirect('');
        }
    }

    /**
     * Ação utilizada para duas finalidades:
     * - Redirecionar para a página de sucesso configurada, quando o comprador retorna à loja
     * - Receber e controlar as requisições do retorno automático de dados
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
                    $url = $pagseguro->getCode() . '/pay/success';
                }
            } else {
                $url = '';
            }
            $this->_redirect($url);
            
        }
    }

    /**
     * Exibe informações de conclusão do pagamento
     * 
     */
    public function successAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!empty($orderId)) {
            Mage::getSingleton("core/session")->setPagseguroOrderId($orderId);
        } else {
            $orderId = Mage::getSingleton("core/session")->getPagseguroOrderId();
        }
        
        if ($orderId) {
            $storeId = $this->getOrderStoreId($orderId);
            
            $pagseguro = $this->getPagSeguro();
            if ($pagseguro->getConfigData('use_return_page_cms', $storeId)) {
                $this->_redirect($pagseguro->getConfigData('return_page', $storeId));
            } else {
                $this->loadLayout();
                $this->renderLayout();
            }
            
            Mage::getSingleton("core/session")->setPagseguroOrderId(null);
        } else {
            $this->_redirect('');
        }
    }
    

    /**
     * Retorna bloco de parcelamento de acordo
     * com a mudança de preço do produto.
     * 
     */
    public function installmentsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}