<?php
/**
 * @author Alan Barber <alan@cadence-labs.com>
 */ 
class Cadence_Pinterest_Helper_Data extends Mage_Core_Helper_Abstract 
{
    public function isVisitorPixelEnabled()
    {
        return Mage::getStoreConfig("cadence_pinterest/visitor/enabled");
    }

    public function isConversionPixelEnabled()
    {
        return Mage::getStoreConfig("cadence_pinterest/conversion/enabled");
    }

    public function getVisitorPixelId()
    {
        return Mage::getStoreConfig("cadence_pinterest/visitor/pixel_id");
    }

    public function getConversionPixelId()
    {
        return Mage::getStoreConfig("cadence_pinterest/conversion/pixel_id");
    }
}