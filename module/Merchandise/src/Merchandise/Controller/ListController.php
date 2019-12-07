<?php

namespace Merchandise\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\View;

class ListController extends AbstractActionController {

    public function indexAction()
    {
        $items = $this->getItems();

        return array("items" => $items);
    }

    public function getItems() {
        return array(
            "tshirt-1" => array(
                "id" => "tshirt-1",
                "title" => "Wit t-shirt",
                "description" => "Tshirt is lekker comfortabel",
                "stock" => 10,
                "price" => 4.50
            ),
            "tshirt-2" => array(
                "id" => "tshirt-2",
                "title" => "Zwart t-shirt",
                "description" => "Tshirt is erg donker",
                "stock" => 5,
                "price" => 10.0
            ),
            "tshirt-3" => array(
                "id" => "tshirt-3",
                "title" => "Rood t-shirt",
                "description" => "Tshirt is lijkt erg op GEWIS!",
                "stock" => 200,
                "price" => 2.00
            )
        );
    }
}
