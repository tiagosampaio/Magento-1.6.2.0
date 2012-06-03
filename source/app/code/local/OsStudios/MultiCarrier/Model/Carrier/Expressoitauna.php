<?php
class OsStudios_Multicarrier_Model_Carrier_Expressoitauna extends OsStudios_Multicarrier_Model_Carrier_Abstract
{
	
	protected $_code = 'expresso_itauna';
	
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		// skip if not enabled
        if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')){
            return false;
        }
        
        $result = Mage::getModel('shipping/rate_result');
        
        $handling = 0;
        
        if(Mage::getStoreConfig('carriers/'.$this->_code.'/handling') >0){
            $handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
    	}
        if(Mage::getStoreConfig('carriers/'.$this->_code.'/handling_type') == 'P' && $request->getPackageValue() > 0){
            $handling = $request->getPackageValue() * $handling;
        }
 
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/title'));
        
        /* Use method name */
        
        $method->setMethod('delivery');
        $method->setMethodTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/methodtitle'));
        $method->setCost($handling);
        $method->setPrice($handling);
        $result->append($method);
        
        return $result;
	}
	
}