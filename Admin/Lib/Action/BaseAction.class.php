<?php
class BaseAction extends Action {
    public function verify_code() {
        $w = isset($_GET['w']) ? (int) $_GET['w'] : 50;
        $h = isset($_GET['h']) ? (int) $_GET['h'] : 30;
        import("ORG.Util.Image");
        Image::buildImageVerify(5, 5, 'png', $w, $h);
    }
}