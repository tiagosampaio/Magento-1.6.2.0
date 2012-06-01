<?php
class OsStudios_MultiCarrier_Model_Carriers_Braspress extends OsStudios_MultiCarrier_Model_Abstract
{
    
    protected $_code = 'braspress';
    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        
        if( !Mage::getStoreConfig('carriers/'.$this->_code.'/active') ) {
            return false;
        }
        
        $result = Mage::getModel('shipping/rate_result');
        $handling = 0;
        
        $method = Mage::getModel( 'shipping/rate_result_method' );
        $method->setCarrier( $this->_code );
        $method->setCarrierTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/title'));
        
        $method->setMethod('delivery');
        $method->setMethodTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/methodtitle'));
        $method->setCost( $handling );
        $method->setPrice( $handling );
        $result->append($method);
        
        return $result;
        
    }
    
}
