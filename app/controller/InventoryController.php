<?php
class InventoryController extends Controller
{
    public function index()
    {
        $this->User->isNotLoggedThrowLogin();
        
        $this->newModel('Inventory');
        
        $money = Inventory::returnMoney($this->User->IDX);
        $itemlist = Inventory::listItems($this->User->IDX);
        
        require APP_VIEW . 'header.php';
        require APP_VIEW . 'inventory/inventory.php';
        require APP_VIEW . 'footer.php';
    }
}