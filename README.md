# PHP Template Engine
Developed to help display dynamic variables using custom tag elements in html files

## Usage
### Custom Attributes
* tb:attribute="variable_name"
#### PHP
```php
    require_once '../core/TemplateBuilder.php';
    $templateBuilder = new TemplateBuilder();
    // $templateBuilder -> setVariable("message", isset($_POST['message']) ? $_POST['message'] : "");
    $templateBuilder -> setVariable("message", "Hello World");
    $templateBuilder -> render('...');
```
#### HTML
```html
    <input type="text" name="message" tb:value="message" placeholder="Enter Message"/>
```
#### OUTPUT
```html
    <input type="text" name="message" value="Hello World" placeholder="Enter Message"/>
```

### Custom Tags
* tb:include attr=""
#### PHP
```php
    require_once '../core/TemplateBuilder.php';
    $templateBuilder = new TemplateBuilder();
    $templateBuilder -> setVariable("header", "../view/header.html");
    $templateBuilder -> setVariable("contents", "../view/contents.html");
    $templateBuilder -> setVariable("footer", "../view/footer.html");
    $templateBuilder -> render('../layout/default.html');
```
#### HTML
```html
    <body>
        <tb:include file="header"/>
        <tb:include file="contents"/>
        <tb:include file="footer"/>
    </body>
```
#### OUTPUT
```html
    <body>
        <!-- view/header.html contents -->
        <!-- view/contents.html contents -->
        <!-- view/footer.html contents -->
    </body>
```

* tb:print var=""
#### PHP
```php
    class Model {
        private $name;
        function __construct() {
            $this -> name = 'Model : Hello World!';
        }
        function setName($par) {
            $this -> name = $par;
        }
        function getName() {
            return $this -> name;
        }
    }

    require_once '../core/TemplateBuilder.php';
    $templateBuilder = new TemplateBuilder();
    $templateBuilder -> setVariable("model", new Model());
    $templateBuilder -> render('...');
```
#### HTML
```html
    <contents>
        <span><tb:print var="model.name"/></span>
    </contents>
```
#### OUTPUT
```html
    <contents>
        <span>Model : Hello World!</span>
    </contents>
```

* tb:set var="" val=""
* tb:if var=""
* tb:for item="" var=""
* tb:jscript
