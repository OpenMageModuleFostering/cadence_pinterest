<?php
/**
 * @author Alan Barber <alan@cadence-labs.com>
 */
Class Cadence_Pinterest_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('cadence_pinterest');
    }

    /**
     * @param $data
     * @return $this
     */
    public function setAddToCart($data)
    {
        $this->setData('add_to_cart', $data);
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getAddToCart()
    {
        if ($this->hasAddToCart()) {
            $data = $this->getData('add_to_cart');
            $this->unsetData('add_to_cart');
            return $data;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function hasAddToCart()
    {
        return $this->hasData('add_to_cart');
    }
}