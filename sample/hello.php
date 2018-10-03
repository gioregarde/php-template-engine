<?php

    require_once '../core/TemplateBuilder.php';
    require_once '../model/Model.php';
    require_once '../model/SubModel.php';

    $templateBuilder = new TemplateBuilder();
    $templateBuilder -> setVariable("header", "../view/header.html");
    $templateBuilder -> setVariable("contents", "../view/hello.html");
    $templateBuilder -> setVariable("footer", "../view/footer.html");
    $templateBuilder -> setVariable("model", new Model());

    $templateBuilder -> render('../layout/default.html');

?>