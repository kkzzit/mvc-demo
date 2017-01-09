<?php
class InventoryController extends Controller
{
    public function index()
    {
        $this->User->isNotLoggedThrowLogin();

        $money = Inventory::getMoney($this->User->IDX);

        $equipped = [];
        $itemlist = [];

        foreach (Inventory::getInventory($this->User->IDX) as $item)
        {
            if ($item['itemStatus'] & 1)
            {
                $equipped[$item['itemSlot']] = $item;
            }
            else
            {
                array_push($itemlist, $item);
            }
        }

        require APP_VIEW . 'header.php';
        require APP_VIEW . 'inventory/inventory.php';
        require APP_VIEW . 'footer.php';
    }

    public function useitem($itemidx)
    {
        $this->User->isNotLoggedEXIT();

        Inventory::useItem($this->User->IDX, is_numeric($itemidx) ? $itemidx : 0);

        Redirect::to('inventory');
    }
}
