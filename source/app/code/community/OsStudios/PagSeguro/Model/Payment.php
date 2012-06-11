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

class OsStudios_PagSeguro_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    
    protected $_code  						= 'pagseguro';
    protected $_formBlockType 				= 'pagseguro/form';
    protected $_infoBlockType 				= 'pagseguro/info';
    
    protected $_canUseInternal 				= true;
    protected $_canUseForMultishipping 		= false;
    protected $_canCapture 					= true;
    
    protected $_order 						= null;
    
    const PAGSEGURO_LOG_FILENAME			= 'osstudios_pagseguro.log';
    
    const PAGSEGURO_STATUS_COMPLETE			= 'Completo';
    const PAGSEGURO_STATUS_WAITING_PAYMENT	= 'Aguardando Pagto';
    const PAGSEGURO_STATUS_APPROVED			= 'Aprovado';
    const PAGSEGURO_STATUS_ANALYSING		= 'Em Análise';
    const PAGSEGURO_STATUS_CANCELED			= 'Cancelado';
    const PAGSEGURO_STATUS_RETURNED			= 'Devolvido';
    
    /**
     * 
     * Set the $_POST information
     * 
     * @param Mage_Core_Controller_Request_Http $post
     */
    public function setPostData(Mage_Core_Controller_Request_Http $request)
    {    	
    	$post = $request->getPost();
    	
    	$this->setPost($post);
    	return $this;
    }
    
    /**
     *  Return Order
     *
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
        	return false;
        }
        return $this->_order;
    }

    /**
     * 
     *  Set Current Order
     *
     *  @param Mage_Sales_Model_Order $order
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
    	if(!$this->_order)
    	{
    		$this->_order = $order;
    	}
        return $this;
    }
    
    /**
     * 
     * Process before processReturn method.
     * 
     * @return OsStudios_PagSeguro_Model_Payment
     * 
     * @uses $this->log()
     */
    public function _beforeProcessReturn()
    {
    	$this->log('<!--[ '.Mage::helper('pagseguro')->__('Beginning of Return').' ]-->');
            
        // Saves $_POST Data
        $this->log('<!--[ '.Mage::helper('pagseguro')->__('Post Data').' ]-->');
        $this->log($this->getPost());
        $this->log('<!--[ '.Mage::helper('pagseguro')->__('End of Post Data').' ]-->');
        
        return $this;
    }
    
    /**
     * 
     * Process before processReturn method.
     * 
     * @return OsStudios_PagSeguro_Model_Payment
     * 
     * @uses $this->log()
     */
    public function _afterProcessReturn()
    {
    	$this->log('<!--[ '.Mage::helper('pagseguro')->__('Ending of Return').' ]-->');
		$this->log(' ----------- >> ----------- ');
		
		return $this;
    }
    
    /**
     * 
     * Registry any event/error log.
     * 
     * @return OsStudios_PagSeguro_Model_Payment
     * 
     * @param string $message
     * @param integer $level
     * @param string $file
     * @param bool $forceLog
     */
    public function log($message, $level = null, $file = self::PAGSEGURO_LOG_FILENAME, $forceLog = false) {
    	if($this->getConfigData('log_enable'))
    	{
	    	if( is_array($message) )
	    	{
	    		Mage::log($message, $level, $file, $forceLog);
	    	} else {
	    		Mage::log("PagSeguro: " . $message, $level, $file, $forceLog);
	    	}
    	}
    	
    	return $this;
    }
    
    /**
     * 
     * Returns the Current Store
     * 
	 * @return string
     */
    public function getStore()
    {
    	if($this->getOrder()) {
    		$store = $this->getOrder()->getStore();
    	} else {
    		$store = Mage::app()->getStore();
    	}
    	
    	return $store;
    }
    
    /**
     * 
     * Returns the URL for payments on PagSeguro
     * 
	 * @return string
     */ 
    public function getPagSeguroUrl()
    {
    	$url = $this->getConfigData('pagseguro_url', $this->getStore());
    	if(!$url) {
    		Mage::throwException( Mage::helper('pagseguro')->__('The PagSeguro URL could not be retrieved.') );
    	}
    	
    	return $url;
    }
    
    /**
     * 
     * Returns the Payment Notification URL of PagSeguro
     * 
	 * @return string
     */ 
    public function getPagSeguroNPIUrl()
    {
    	$url = $this->getConfigData('pagseguro_npi_url', $this->getStore());
    	if(!$url) {
    		Mage::throwException( Mage::helper('pagseguro')->__('The PagSeguro NPI URL could not be retrieved.') );
    	}
    	return $url;
    }
    
    /**
     * getPagSeguroBoletoUrl
     * 
     * Returns the URL to generate the billets of PagSeguro
     * 
	 * @param string $transactionId = PagSeguro Transaction ID
     * 
	 * @return string
     */ 
    public function getPagSeguroBoletoUrl($transactionId, $escapeHtml = true)
    {
    	$url = $this->getConfigData('pagseguro_billet_url', $this->getStore());
    	if(!$url) {
    		Mage::throwException( Mage::helper('pagseguro')->__('The PagSeguro Billet URL could not be retrieved.') );
    	}
    	
        $url .= '?resizeBooklet=n&code=' . $transactionId;
        if ($escapeHtml) {
            $url = Mage::helper("brunoassarisse_pagseguro")->escapeHtml($url);
        }
        return $url;
    }
    
	/**
	 * getOrderPlaceRedirectUrl
     * 
     * Cria a URL de redirecionamento ao PagSeguro, utilizando
     * o ID do pedido caso este seja informado
	 *
	 * @param int $orderId     ID pedido
	 *
	 * @return string
	 */
    public function getOrderPlaceRedirectUrl($orderId = 0)
	{
	   $params = array();
       $params['_secure'] = true;
       
	   if ($orderId != 0 && is_numeric($orderId)) {
	       $params['order_id'] = $orderId;
	   }
       
        return Mage::getUrl($this->getCode() . '/pay/redirect', $params);
    }
    
	/**
	 * createRedirectForm
     * 
     * Cria o formulário de redirecionamento ao PagSeguro
	 *
	 * @return string
     * 
     * @uses $this->getCheckoutFormFields()
	 */
    public function createRedirectForm()
    {
    	$form = new Varien_Data_Form();
        $form->setAction($this->getPagSeguroUrl())
            ->setId('pagseguro_checkout')
            ->setName('pagseguro_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        
        $fields = $this->getCheckoutFormFields();
        foreach ($fields as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }
        
        $submit_script = 'document.getElementById(\'pagseguro_checkout\').submit();';
        
		$html  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html .= '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="pt-BR">';
		$html .= '<head>';
		$html .= '<meta http-equiv="Content-Language" content="pt-br" />';
		$html .= '<meta name="language" content="pt-br" />';
		$html .= '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
		$html .= '<style type="text/css">';
		$html .= '* { font-family: Arial; font-size: 16px; line-height: 34px; text-align: center; color: #222222; }';
		$html .= 'small, a, a:link:visited:active, a:hover { font-size: 13px; line-height: normal; font-style: italic; }';
		$html .= 'a, a:link:visited:active { font-weight: bold; text-decoration: none; }';
		$html .= 'a:hover { font-weight: bold; text-decoration: underline; color: #555555; }';
		$html .= '</style>';
		$html .= '</head>';
		$html .= '<body onload="' . $submit_script . '">';
        $html .= 'Você será redirecionado ao <strong>PagSeguro</strong> em alguns instantes.<br />';
        $html .= '<small>Se a página não carregar, <a href="#" onclick="' . $submit_script . ' return false;">clique aqui</a>.</small>';
        $html .= $form->toHtml();
        $html .= '</body></html>';

        return utf8_decode($html);
        
    }
    
	/**
	 * getCheckoutFormFields
     * 
     * Gera os campos para o formulário de redirecionamento ao Pagseguro
	 *
	 * @return array
	 *
	 * @uses $this->getOrder()
	 */
    public function getCheckoutFormFields()
    {
        $order = $this->getOrder();
        
        // Utiliza endereço de cobrança caso produto seja virtual/para download
        $address = $order->getIsVirtual() ? $order->getBillingAddress() : $order->getShippingAddress();
        
        // Resgata CEP
        $cep = preg_replace('@[^\d]@', '', $address->getPostcode());
        
        // Dados de endereço
        if ($this->getConfigData('custom_address_model', $order->getStoreId())) {
            $endereco = $address->getStreet(1);
            $numero = $address->getStreet(2);
            $complemento = $address->getStreet(3);
            $bairro = $address->getStreet(4);
        } else {
            list($endereco, $numero, $complemento) = Mage::helper('pagseguro')->trataEndereco($address->getStreet(1));
            $bairro = $address->getStreet(2);
        }
        
        // Formata o telefone
        list($ddd, $telefone) = Mage::helper('pagseguro')->trataTelefone($address->getTelephone());
        
        // Monta os dados para o formulário
        $sArr = array(
                //'encoding'          => 'utf-8',
                'email_cobranca'    => $this->getConfigData('account_email', $order->getStoreId()),
                'Tipo'              => "CP",
                'Moeda'             => "BRL",
                'ref_transacao'     => $order->getRealOrderId(),
                'cliente_nome'      => $address->getFirstname() . ' ' . $address->getLastname(),
                'cliente_cep'       => $cep,
                'cliente_end'       => $endereco,
                'cliente_num'       => $numero,
                'cliente_compl'     => $complemento,
                'cliente_bairro'    => $bairro,
                'cliente_cidade'    => $address->getCity(),
                'cliente_uf'        => $address->getRegionCode(),
                'cliente_pais'      => $address->getCountry(),
                'cliente_ddd'       => $ddd,
                'cliente_tel'       => $telefone,
                'cliente_email'     => $order->getCustomerEmail(),
                );
        
        
        $i = 1;
        $items = $order->getAllVisibleItems();
        
		$shipping_amount = $order->getBaseShippingAmount();
        $tax_amount = $order->getBaseTaxAmount();
        $discount_amount = $order->getBaseDiscountAmount();
        
        $priceGrouping = $this->getConfigData('price_grouping', $order->getStoreId());
        $shippingPrice = $this->getConfigData('shipping_price', $order->getStoreId());
        
        if ($priceGrouping) {
            
            $order_total = $order->getBaseSubtotal() + $tax_amount + $discount_amount;
            if ($shippingPrice == 'grouped') {
                $order_total += $shipping_amount;
            }
            $item_descr = $order->getStoreName(2) . " - Pedido " . $order->getRealOrderId();
            $item_price = Mage::helper('pagseguro')->formatNumber($order_total);
            $sArr = array_merge($sArr, array(
                'item_descr_'.$i   => substr($item_descr, 0, 100),
                'item_id_'.$i      => $order->getRealOrderId(),
                'item_quant_'.$i   => 1,
                'item_valor_'.$i   => $item_price,
            ));
            $i++;
                
        } else {
            
            if ($items) {
                foreach ($items as $item) {
                    $item_price = 0;
                    $item_qty = $item->getQtyOrdered() * 1;
                    if ($children = $item->getChildrenItems()) {
                        foreach ($children as $child) {
                            $item_price += $child->getBasePrice() * $child->getQtyOrdered() / $item_qty;
                        }
                        $item_price = Mage::helper('pagseguro')->formatNumber($item_price);
                    }
                    if (!$item_price) {
        				$item_price = Mage::helper('pagseguro')->formatNumber($item->getBasePrice());
                    }
                    $sArr = array_merge($sArr, array(
                        'item_descr_'.$i   => substr($item->getName(), 0, 100),
                        'item_id_'.$i      => substr($item->getSku(), 0, 100),
                        'item_quant_'.$i   => $item_qty,
                        'item_valor_'.$i   => $item_price,
                    ));
                    $i++;
                }
            }
            
            if ($tax_amount > 0) {
                $tax_amount = Mage::helper('pagseguro')->formatNumber($tax_amount);
                $sArr = array_merge($sArr, array(
                    'item_descr_'.$i   => "Taxa",
                    'item_id_'.$i      => "taxa",
                    'item_quant_'.$i   => 1,
                    'item_valor_'.$i   => $tax_amount,
                ));
                $i++;
            }
                
            if ($discount_amount != 0) {
                $discount_amount = Mage::helper('pagseguro')->formatNumber($discount_amount);
                if (preg_match("/^1\.[23]/i", Mage::getVersion())) {
                    $discount_amount = -$discount_amount;
                }
                $sArr = array_merge($sArr, array(
                    'extras'   => $discount_amount,
                ));
            }
                
        }
        
        if ($shipping_amount > 0) {
            $shipping_amount = Mage::helper('pagseguro')->formatNumber($shipping_amount);
            switch ($shippingPrice) {
                case 'grouped':
                    if ($priceGrouping) {
                        break;
                    }
                case 'product':
                    // passa o valor do frete como um produto
                    $sArr = array_merge($sArr, array(
                        'item_descr_'.$i   => substr($order->getShippingDescription(), 0, 100),
                        'item_id_'.$i      => "frete",
                        'item_quant_'.$i   => 1,
                        'item_valor_'.$i   => $shipping_amount,
                    ));
                    $i++;
                    break;
                    
                case 'separated':
                default:
                    // passa o valor do frete separadamente
                    $sArr = array_merge($sArr, array('item_frete_1' => $shipping_amount));
                    
            }
        }
        
        $rArr = array();
        foreach ($sArr as $k => $v) {
            // troca caractere '&' por 'e'
            $value =  str_replace("&", "e", $v);
            $rArr[$k] =  $value;
        }
        
        return $rArr;
    }

	/**
	 * _confirma
	 *
	 * Faz a parte Server-Side, verificando os dados junto ao PagSeguro
	 *
	 * @param array $post Dados vindos no POST do PagSeguro
	 *
	 * @return boolean
	 */
	protected function _confirma($post) 
	{
        $resp = '';
		$confirma = false;
        
        $post['encoding'] = 'utf-8';
        $post['Comando'] = 'validar';
		$post['Token']   = $this->getConfigData('token', $this->getOrder()->getStoreId());
        
		if (!empty($post)) {
            
            $client = new Zend_Http_Client($this->getPagSeguroNPIUrl());
            
            if ($this->getConfigData('use_curl', $this->getOrder()->getStoreId())) {
                $adapter = new Zend_Http_Client_Adapter_Curl();
                $client->setAdapter($adapter);
                $adapter->setConfig(array(
                    'timeout' => 30,
                    'curloptions' => array(
                        CURLOPT_SSL_VERIFYPEER => false
                    )
                ));
            }
            
            try {
                   
                $client->setMethod(Zend_Http_Client::POST);
                $client->setParameterPost($post);
                
                $content = $client->request();
                $resp = $content->getBody();
                $resp = 'VERIFICADO';
            
            } catch (Exception $e) {
                $this->log("ERRO: " . $e->getMessage());
            }
            
            $confirma = (strcmp($resp, 'VERIFICADO') == 0);
            
		}
        $this->log("Resposta de confirmacao: $resp");
		return $confirma;
	}

	/**
	 * processReturn
	 *
	 * Checks and authenticates the data received and, in success case, call the process order method
     * 
	 * @return bool
	 *
	 * @uses $this->_confirma()
	 * @uses $this->processPagSeguroNPI()
	 */
	function processReturn()
	{
		$post = $this->getPost();
        
        $this->_beforeProcessReturn();
        
		$confirma = $this->_confirma($post);

		if ($confirma) {
			$itens = array ('VendedorEmail', 	'TransacaoID', 	'Referencia', 	'TipoFrete', 	'ValorFrete',	'Anotacao',			'DataTransacao',	'TipoPagamento',
							'StatusTransacao', 	'CliNome', 		'CliEmail', 	'CliEndereco', 	'CliNumero',	'CliComplemento',	'CliBairro',		'CliCidade',
							'CliEstado',		'CliCEP',		'CliTelefone',	'NumItens',		'Extras',
					 );
			
			foreach ($itens as $item) {
				if (!isset($post[$item])) {
					$post[$item] = '';
				}
				if (in_array($item, array('ValorFrete', 'Extras'))) {
					$post[$item] = str_replace(',', '.', $post[$item]);
				}
			}
            
			$total = 0;
			
			for ($i = 1; $i <= $post['NumItens']; $i++) {
				$total += Mage::helper('pagseguro')->convertNumber($post["ProdValor_{$i}"]) * $post["ProdQuantidade_{$i}"];
			}
            
			$total += Mage::helper('pagseguro')->convertNumber($post['ValorFrete']);
            
            if (preg_match("/^-/i", $post['Extras'])) {
			    $total -= Mage::helper('pagseguro')->convertNumber($post['Extras']);
            } else {
                $total += Mage::helper('pagseguro')->convertNumber($post['Extras']);
			}
            
			$this->log(Mage::helper('pagseguro')->__('Confirmation Success!'));
            $this->processPagSeguroNPI($post['StatusTransacao'], $post['TransacaoID'], $post['TipoPagamento'], $total);
		} else {
		    $this->log(Mage::helper('pagseguro')->__('Confirmation Denied...'));
		}
		
		$this->_afterProcessReturn();
		
        return $confirma;
	}

	/**
     * 
     * Process the received information and updates the order
	 *
	 * @param string $status 		= Payment Status
	 * @param string $transacaoID 	= PagSeguro Transaction ID
	 * @param string $tipoPagamento = Payment Method That Was Used
	 * @param float $valorTotal 	= Grand Total
     * 
     * @uses $this->getOrder()
	 */
    public function processPagSeguroNPI($status, $transacaoID, $tipoPagamento, $valorTotal)
    {
        $order = $this->getOrder();
        
        $this->log(Mage::helper('pagseguro')->__('Order #%s: %s', $order->getRealOrderId(), $status));
        
        if ($order->getId()) {
	    
	    if ($order->getPayment()->getMethod() == $this->getCode()) {
        
		$valorPedido = (float) $order->getBase_grand_total();
		if (function_exists('bccomp')) {
		    // Compares numbers with float dots, 2 decimal and returns 0 if they are equals.
		    $valoresCoincidentes = bccomp($valorPedido, $valorTotal, 2);
		} else {
		    $valoresCoincidentes = (number_format($valorPedido, 2, '.', '') == number_format($valorTotal, 2, '.', '')) ? 0 : 1;
		}
		
		if ($valoresCoincidentes == 0) {
		    
		    // Updates the transaction information
		    $order->getPayment()->setPagseguroTransactionId($transacaoID)
		    					->setPagseguroPaymentMethod($tipoPagamento)
		    					->save();
		    
		    $changeTo = "";
			    
		    // Checking the status of the order sent by PagSeguro
		    if (in_array(trim($status), array(self::PAGSEGURO_STATUS_COMPLETE, self::PAGSEGURO_STATUS_APPROVED))) {
				
		    	if ($order->canUnhold()) {
				    $order->unhold();
				}
				
				if ($order->canInvoice()) {
				    $changeTo = Mage_Sales_Model_Order::STATE_PROCESSING;
				    
				    $invoice = $order->prepareInvoice();
				    $invoice->register()->pay();
				    $invoice_msg = Mage::helper('pagseguro')->__('Payment confirmed (%s). PagSeguro Transaction: %s.', $tipoPagamento, $transacaoID);
				    $invoice->addComment($invoice_msg, true);
				    $invoice->sendEmail(true, $invoice_msg);
				    $invoice->setEmailSent(true);
				    
				    Mage::getModel('core/resource_transaction')
				       		->addObject($invoice)
				       		->addObject($invoice->getOrder())
				       		->save();
				       		
				    $comment = Mage::helper('pagseguro')->__('Invoice #%s created.', $invoice->getIncrementId());
				    $order->setState($changeTo, true, $comment, $notified = true);
				    
				    $this->log(Mage::helper('pagseguro')->__('Invoice Created!'));
				    
				} else {
				    // When invoice not be created run this block of code
				    $this->log(Mage::helper('pagseguro')->__('Invoice Not Created!'));
				}
		    } else {
			
		    	// Order is not complete yet. Let's process it...
				if (in_array(trim($status), array(self::PAGSEGURO_STATUS_CANCELED, self::PAGSEGURO_STATUS_RETURNED))) {
				    
				    if (trim($status) == self::PAGSEGURO_STATUS_RETURNED) {
						$order_msg = Mage::helper('pagseguro')->__('Payment Returned.');
						$comment_add = true;
						foreach ($order->getAllStatusHistory() as $status) {
						    if (strpos($status->getComment(), $order_msg) !== false) {
								$comment_add = false;
								break;
						    }
						}
						if ($comment_add) {
						    if (method_exists($order, "addStatusHistoryComment")) {
								$order->addStatusHistoryComment($order_msg, false)->setIsCustomerNotified(true);
						    } elseif (method_exists($order, "addStatusToHistory")) {
								$order->addStatusToHistory($order->getStatus(), $order_msg, true);
						    }
						}
				    } else {
						$order_msg = Mage::helper('pagseguro')->__('Payment Canceled.');
				    }
				    
				    // Canceled Order
				    if ($order->canUnhold()) {
						$order->unhold();
				    }
				    if ($order->canCancel()) {
						$changeTo = Mage_Sales_Model_Order::STATE_CANCELED;
						$order->getPayment()->setMessage($order_msg);
						$order->cancel();
				    }
				    
				} else {
				    
				    // Waiting/Analyzing/Waiting Payment (Billet)
				    if ($order->canHold()) {
						$changeTo = Mage_Sales_Model_Order::STATE_HOLDED;
						$comment = $status.' - '.$tipoPagamento;
						
						$order->setHoldBeforeState($order->getState());
						$order->setHoldBeforeStatus($order->getStatus());
						$order->setState($changeTo, true, $comment, $notified = false);
				    }
				    
				}
			
		    }
		    
		    if ($changeTo != "") {
				$this->log(Mage::helper('pagseguro')->__('Updated Order Status: %s.', $order->getState()));
		    }
		    $order->save();
		    
		} else {
		    $this->log(Mage::helper('pagseguro')->__('ERROR: O value received is different from the stored one (STORED VALUE: %s / RECEIVED VALUE: %s).', $valorPedido, $valorTotal));
		}
	    } else {
			$this->log(Mage::helper('pagseguro')->__('ERROR: This order was not placed with this payment method.'));
	    }
	} else {
            $this->log(Mage::helper('pagseguro')->__('ERROR: The order reference was not found.'));
        }
    }
}
