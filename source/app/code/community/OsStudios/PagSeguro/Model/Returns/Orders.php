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
 * @category   OsStudios
 * @package    OsStudios_PagSeguro
 * @copyright  Copyright (c) 2012 Os Studios (www.osstudios.com.br)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Tiago Sampaio <tiago.sampaio@osstudios.com.br>
 */

class OsStudios_PagSeguro_Model_Returns_Orders extends OsStudios_PagSeguro_Model_Abstract
{
   
	const ORDER_NOTPROCESSED = 0;
	const ORDER_PROCESSED = 1;
	
	protected $_order = null;
	protected $_response = self::ORDER_NOTPROCESSED;
	
	public function getResponse()
	{
		return $this->_response;
	}
	
	
	/**
	 * Runs before process any order
	 */
	protected function _beforeProcessOrder()
	{
		
	}
	
	
	/**
	 * Runs before process any order
	 */
	protected function _afterProcessOrder()
	{
		
	}
	
	
	/**
	 * Set current Order
	 * 
	 * @param Mage_Sales_Model_Order $order
	 * @return OsStudios_PagSeguro_Model_Orders
	 */
	public function setOrder(Mage_Sales_Model_Order $order)
	{
		$this->_order = $order;
		return $this;
	}
	
	
	/**
	 * Unset current order
	 * 
	 * @return OsStudios_PagSeguro_Model_Orders
	 */
	public function unsetOrder()
	{
		$this->_order = null;
		return $this;
	}
	
	
	/**
	 * Process canceled orders
	 * 
	 * @return OsStudios_PagSeguro_Model_Orders
	 */
	public function processOrderCanceled()
	{
		
		$this->_beforeProcessOrder();
		
		if(!$this->_order) {
			return $this;
		}
		
		$order = $this->_order;
		
		if ($order->canUnhold()) {
			$order->unhold();
		}
                    
		if($this->_order->canCancel()) {
            $state = Mage_Sales_Model_Order::STATE_CANCELED;
            $status = Mage_Sales_Model_Order::STATE_CANCELED;
            $comment = $this->__('Order was canceled by PagSeguro.');
                        
            $order->getPayment()->setMessage($comment)->save();
            $order->setState($state, $status, $comment, true)->save();
			$order->cancel();
			
			$this->_response = self::ORDER_PROCESSED;
		} else {
			$this->_response = self::ORDER_NOTPROCESSED;
		}
		
		$this->_afterProcessOrder();
		
		return $this;
	}
	
	
	/**
	 * Process approved orders
	 * 
	 * @return OsStudios_PagSeguro_Model_Orders
	 */
	public function processOrderApproved()
	{
		
		$this->_beforeProcessOrder();
		
		if(!$this->_order) {
			return $this;
		}
		
		$order = $this->_order;
		
		if($order->canUnhold()) {
        	$order->unhold();
		}
                    
		if($order->canInvoice()) {
                        
            $state = Mage_Sales_Model_Order::STATE_PROCESSING;
            $status = Mage_Sales_Model_Order::STATE_PROCESSING;
            $comment = Mage::helper('pagseguro')->__('Payment confirmed by PagSeguro (%s). PagSeguro Transaction: %s.', $this->getPaymentMethodType(), $this->getCode()) ;
            $notify = true;
            $visibleOnFront = true;
                        
            $invoice = $order->prepareInvoice();
            $invoice->register()->pay();
            $invoice->addComment($comment, $notify, $visibleOnFront)->save();
            $invoice->sendUpdateEmail($visibleOnFront, $comment);
            $invoice->setEmailSent(true);
                        
            Mage::getModel('core/resource_transaction')->addObject($invoice)
                                                       ->addObject($invoice->getOrder())
                                                       ->save();
                        
            $comment = Mage::helper('pagseguro')->__('Invoice #%s was created.', $invoice->getIncrementId());
            $order->setState($state, $status, $comment, true)->save();

            $this->_response = self::ORDER_PROCESSED;
		} else {
			$this->_response = self::ORDER_NOTPROCESSED;
		}
		
		$this->_afterProcessOrder();
		
		return $this;
	}
	
	
	/**
	 * Process approved orders
	 * 
	 * @return OsStudios_PagSeguro_Model_Orders
	 */
	public function processOrderWaiting()
	{
		
		$this->_beforeProcessOrder();
		
		if(!$this->_order) {
			return $this;
		}
		
		$order = $this->_order;
		
		if($order->canHold()) {
			$order->hold();
			
			$this->_response = self::ORDER_PROCESSED;
		} else {
			$this->_response = self::ORDER_NOTPROCESSED;
		}
		
		$this->_afterProcessOrder();
		
		return $this;
	}
}
