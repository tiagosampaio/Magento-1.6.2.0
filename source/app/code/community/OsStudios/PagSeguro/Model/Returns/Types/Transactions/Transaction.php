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
	const PAYMENT_TYPE_CC_STRING = 'Cartão de Crédito';
    
    /**
     * 
     * Paid with Billet
     * @var (int)
     */
    const PAYMENT_TYPE_BILLET = 2;
	const PAYMENT_TYPE_BILLET_STRING = 'Boleto';
    
    /**
     * 
     * Paid with Online Debit (TEF)
     * @var (int)
     */
    const PAYMENT_TYPE_DEBIT = 3;
	const PAYMENT_TYPE_DEBIT_STRING = 'Pagamento Online';
    
    /**
     * 
     * Paid with PagSeguro Account Credits
     * @var (int)
     */
    const PAYMENT_TYPE_PAGSEGUROCREDIT = 4;
	const PAYMENT_TYPE_PAGSEGUROCREDIT_STRING = 'Pagamento';
    
    /**
     * 
     * Paid with Oi Paggo via Celphones
     * @var (int)
     */
    const PAYMENT_TYPE_OIPAGGO = 5;
    const PAYMENT_TYPE_OIPAGGO_STRING = 'Oi Paggo';
    
    /**
     * 
     * Status: Waiting for the payment
     * @var (int)
     */
    const STATUS_WAITING_PAYMENT = 1;
    const STATUS_WAITING_PAYMENT_STRING = 'Aguardando Pagto';
    
    /**
     * 
     * Status: Payment is being analysed
     * @var (int)
     */
    const STATUS_ANALYSIS = 2;
    const STATUS_ANALYSIS_STRING = 'Em Análise';
    
    /**
     * 
     * Status: The transaction was paid
     * @var (int)
     */
    const STATUS_PAID = 3;
    const STATUS_PAID_STRING = 'Aprovado';
    
    /**
     * 
     * Status: The transaction was paid and the money is available to shopper in PagSeguro 
     * @var (int)
     */
    const STATUS_AVAILABLE = 4;
    const STATUS_AVAILABLE_STRING = 'Completo';
    
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
	const STATUS_CANCELED_STRING = 'Cancelado';
    
    
    /**
     * 
     * Handle the transaction type
     * @var (int)
     */
    protected $_transactionType = self::PAGSEGURO_RETURN_TYPE_DEFAULT;
    
    
	/**
	 * Runs before process any transaction
	 */
	protected function _beforeProcessTransaction()
	{
		
	}
	
	
	/**
	 * Runs before process any transaction
	 */
	protected function _afterProcessTransaction()
	{
		
	}
    
    
    /**
     *
     * Sets the transaction type
     * @param (int) $transactionType
     * @return \OsStudios_PagSeguro_Model_Returns_Types_Transaction 
     */
    public function setTransactionType($transactionType = self::PAGSEGURO_RETURN_TYPE_DEFAULT)
    {
        $this->_transactionType = $transactionType;
        return $this;
    }
    
    
    
    
    
    
    public function setTransactionData($transaction = array())
    {
    	
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
                     ->setLastEventDate($transaction['lastEventDate'])
                     ;
                
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
            	
            	$this->setDate($transaction['DataTransacao'])
                     ->setReference($transaction['Referencia'])
                     ->setCode($transaction['TransacaoID'])
                     ->setType($transaction['TipoPagamento'])
                     ->setStatus($transaction['StatusTransacao'])
                     //->setStatus( self::STATUS_PAID )
            		;
            		
                break;
        }
        
        return $this;
    }
    
    /**
     * Process the current transaction
     */
    public function processTransaction()
    {
    	
    	$this->_beforeProcessTransaction();
    	
        $order = $this->loadOrderByIncrementId($this->getReference());
        
        if($order instanceof Mage_Sales_Model_Order) {
        	
        	$order->getPayment()->setPagseguroTransactionId($this->getCode())
		    					->setPagseguroPaymentMethod($this->getPaymentMethodType())
		    					->save();
        	
			$ordersModel = Mage::getModel('pagseguro/returns_orders');
			$ordersModel->setOrder($order);
		    					
            switch ($this->getStatus()) {
                case self::STATUS_PAID:
                case self::STATUS_PAID_STRING:
                case self::STATUS_AVAILABLE:
                case self::STATUS_AVAILABLE_STRING:
                    
                    $result = $ordersModel->processOrderApproved()->getResponse();
                    if($result === OsStudios_PagSeguro_Model_Returns_Orders::ORDER_NOTPROCESSED) {
                    	return false;
                    }
                    
                    break;
                case self::STATUS_CANCELED:
                case self::STATUS_CANCELED_STRING:
                case self::STATUS_RETURNED:
                    
                	$result = $ordersModel->processOrderCanceled()->getResponse();
                	if($result === OsStudios_PagSeguro_Model_Returns_Orders::ORDER_NOTPROCESSED) {
                		return false;
                	}
                	
                    break;
                case self::STATUS_WAITING_PAYMENT:
                case self::STATUS_WAITING_PAYMENT_STRING:
                case self::STATUS_ANALYSIS:
                case self::STATUS_ANALYSIS_STRING:
                case self::STATUS_IN_DISPUTE:
                default:
                    
                	$result = $ordersModel->processOrderWaiting()->getResponse();
            		if($result === OsStudios_PagSeguro_Model_Returns_Orders::ORDER_NOTPROCESSED) {
                		return false;
                	}
                	
                    break;
            }
            
            $ordersModel->unsetOrder();
            
            $this->_afterProcessTransaction();
            
            return true;
        }
    }
}
