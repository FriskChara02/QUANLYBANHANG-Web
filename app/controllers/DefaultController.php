<?php
class DefaultController
{
    public function index(){
        $file = __DIR__ . '/../views/product/pagemain.php';

        if (file_exists($file)) {
            require $file;
        } else {
            echo "Not Found";
        }
    }
}
?>
