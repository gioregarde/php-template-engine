<?php

    require_once '../core/TemplateBuilder.php';

    $templateBuilder = new TemplateBuilder();
    $templateBuilder -> setVariable("header", "../view/header.html");
    $templateBuilder -> setVariable("contents", "../view/say.html");
    $templateBuilder -> setVariable("footer", "../view/footer.html");

    $templateBuilder -> setVariable("message", isset($_REQUEST['message']) ? $_REQUEST['message'] : "");
    $templateBuilder -> setVariable("repeat",  isset($_REQUEST['repeat']) ? $_REQUEST['repeat'] : 1);

    $templateBuilder -> setVariable("repeatLoop", isset($_REQUEST['repeat']) ? array_fill(0, $_REQUEST['repeat'], "") : array());

    $templateBuilder -> render('../layout/default.html');

?>