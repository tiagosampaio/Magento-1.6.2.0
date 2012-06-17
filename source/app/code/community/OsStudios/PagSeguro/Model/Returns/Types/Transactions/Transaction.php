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

class OsStudios_PagSeguro_Model_Returns_Types_Transactions_Transaction extends OsStudios_PagSeguro_Model_Returns
{
    
    /**
     * 
     * Paid with Credit Card
     * @var (int)
     */
    const PAYMENT_TYPE_CC = 1;

    /**
     * 
     * Paid with Billet
     * @var (int)
     */
    const PAYMENT_TYPE_BILLET = 2;

    /**
     * 
     * Paid with Online Debit (TEF)
     * @var (int)
     */
    const PAYMENT_TYPE_DEBIT = 3;

    /**
     * 
     * Paid with PagSeguro Account Credits
     * @var (int)
     */
    const PAYMENT_TYPE_PAGSEGUROCREDIT = 4;

    /**
     * 
     * Paid with Oi Paggo via Celphones
     * @var (int)
     */
    const PAYMENT_TYPE_OIPAGGO = 5;
    
    /**
     * 
     * Status: Waiting for the payment
     * @var (int)
     */
    const STATUS_WAITING_PAYMENT = 1;
    
    /**
     * 
     * Status: Payment is being analysed
     * @var (int)
     */
    const STATUS_ANALYSIS = 2;
    
    /**
     * 
     * Status: The transaction was paid
     * @var (int)
     */
    const STATUS_PAID = 3;
    
    /**
     * 
     * Status: The transaction was paid and the money is available to shopper in PagSeguro 
     * @var (int)
     */
    const STATUS_AVAILABLE = 4;
    
    /**
     * 
     * Status: A dispute was opened by customer
     * @var (int)
     */
    const STATUS_IN_DISPUTE = 5;
    
    /**
     * 
     * Status: The money was given back to customer
     * @var (int)
     */
    const STATUS_RETURNED = 6;
    
    /**
     * 
     * Status: The transaction was canceled for any reason
     * @var (int)
     */
    const STATUS_CANCELED = 7;
    
    /**
     * 
     * Handle the transaction type
     * @var (int)
     */
    protected $_transactionType = self::PAGSEGURO_RETURN_TYPE_DEFAULT;
    
    
    /**
     *
     * Sets the transaction type
     * @param (int) $transactionType
     * @return \OsStudios_PagSeguro_Model_Returns_Types_Transaction 
     */
    public function setTransactionType($transactionType)
    {
        $this->_transactionType = $transactionType;
        return $this;
    }
    
    
    public function __construct($transaction = array(), $transactionType = self::PAGSEGURO_RETURN_TYPE_DEFAULT)
    {
    	$this->setTransactionType($transactionType);
    	
        switch ($this->_transactionType) {
            case self::PAGSEGURO_RETURN_TYPE_API:
                break;
            case self::PAGSEGURO_RETURN_TYPE_CONSULT:
                
                $this->setDate($transaction['date'])
                     ->setReference($transaction['reference'])
                     ->setCode($transaction['code'])
                     ->setType($transaction['type'])
                     ->setStatus($transaction['status'])
                     //->setStatus( self::STATUS_PAID )
                     ->setGrossAmount($transaction['grossAmount'])
                     ->setDiscountAmount($transaction['discountAmount'])
                     ->setFeeAmount($transaction['feeAmount'])
                     ->setNetAmount($transaction['netAmount'])
                     ->setExtraAmount($transaction['extraAmount'])
                     ->setLastEventDate($transaction['lastEventDate']);
                
                switch ($transaction['paymentMethod']['type']) {
                    case self::PAYMENT_TYPE_CC:
                        $paymentType = $this->__('Credit Card');
                        break;
                    case self::PAYMENT_TYPE_BILLET:
                        $paymentType = $this->__('Billet');
                        break;
                    case self::PAYMENT_TYPE_DEBIT:
                        $paymentType = $this->__('Online Debit');
                        break;
                    case self::PAYMENT_TYPE_PAGSEGUROCREDIT:
                        $paymentType = $this->__('PagSeguro Credit');
                        break;
                    case self::PAYMENT_TYPE_OIPAGGO:
                        $paymentType = $this->__('Oi Paggo');
                        break;
                    default:
                        $paymentType = $this->__('Not Provided');
                        break;
                }
                
                $this->setPaymentMethodType($paymentType);
                
                break;
            case self::PAGSEGURO_RETURN_TYPE_DEFAULT:
            default:
                break;
        }
        
        return $this;
    }
    
    /**
     * Process the current transaction
     */
    public function processTransaction()
    {
        $order = $this->loadOrderByIncrementId($this->getReference());
        
        if($order instanceof Mage_Sales_Model_Order) {
        	
        	$order->getPayment()->setPagseguroTransactionId($this->getCode())
		    					->setPagseguroPaymentMethod($this->getPaymentMethodType())
		    					->save();
        	
			$ordersModel = Mage::getModel('pagseguro/returns_orders');
			$ordersModel->setOrder($order);
		    					
            switch ($this->getStatus()) {
                case self::STATUS_PAID:
                case self::STATUS_AVAILABLE:
                    
                    $result = $ordersModel->processOrderApproved()->getResponse();
                    if($result === OsStudios_PagSeguro_Model_Returns_Orders::ORDER_NOTPROCESSED) {
                    	return false;
                    }
                    
                    break;
                case self::STATUS_CANCELED:
                case self::STATUS_RETURNED:
                    
                	$result = $ordersModel->processOrderCanceled()->getResponse();
                	if($result === OsStudios_PagSeguro_Model_Returns_Orders::ORDER_NOTPROCESSED) {
                		return false;
                	}
                	
                    break;
                case self::STATUS_WAITING_PAYMENT:
                case self::STATUS_ANALYSIS:
                case self::STATUS_IN_DISPUTE:
                default:
                    
                	$result = $ordersModel->processOrderWaiting()->getResponse();
            		if($result === OsStudios_PagSeguro_Model_Returns_Orders::ORDER_NOTPROCESSED) {
                		return false;
                	}
                	
                    break;
            }
            
            return true;
        }
    }
}
