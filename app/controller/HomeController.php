<?php
class HomeController extends Controller
{
    public function index() {
        require APP_VIEW . 'header.php';
        require APP_VIEW . 'home/home.php';
        require APP_VIEW . 'footer.php';
    }
}
?>
