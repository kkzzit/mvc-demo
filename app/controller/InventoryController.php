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

    public function useitem()
    {
        $this->User->isNotLoggedEXIT();

        $itemidx = isset($_GET['item']) && is_numeric($_GET['item']) ? $_GET['item'] : 0;

        Inventory::useItem($this->User->IDX, $itemidx);

        Redirect::to('inventory');
    }
}
