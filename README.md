# PHP Template Engine
Developed to help display dynamic variables using custom tag elements in html files.

## Installing
include core/TemplateBuilder.php to project.

## Usage
Usage for custom tags and attributes.

### tb:attribute="variable_name"
PHP :
```php
    require_once '../core/TemplateBuilder.php';
    $templateBuilder = new TemplateBuilder();
    // $templateBuilder -> setVariable("message", isset($_POST['message']) ? $_POST['message'] : "");
    $templateBuilder -> setVariable("message", "Hello World");
    $templateBuilder -> render('...');
```
HTML :
```html
    <input type="text" name="message" tb:value="message" placeholder="Enter Message"/>
```
OUTPUT :
```html
    <input type="text" name="message" value="Hello World" placeholder="Enter Message"/>
```

### tb:include file="file_location"
PHP :
```php
    require_once '../core/TemplateBuilder.php';
    $templateBuilder = new TemplateBuilder();
    $templateBuilder -> setVariable("header", "../view/header.html");
    $templateBuilder -> setVariable("contents", "../view/contents.html");
    $templateBuilder -> setVariable("footer", "../view/footer.html");
    $templateBuilder -> render('../layout/default.html');
```
HTML :
```html
    <body>
        <tb:include file="header"/>
        <tb:include file="contents"/>
        <tb:include file="footer"/>
    </body>
```
OUTPUT :
```html
    <body>
        <!-- view/header.html contents -->
        <!-- view/contents.html contents -->
        <!-- view/footer.html contents -->
    </body>
```

### tb:print var="variable_name"
PHP :
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
HTML :
```html
    <contents>
        <span><tb:print var="model.name"/></span>
    </contents>
```
OUTPUT :
```html
    <contents>
        <span>Model : Hello World!</span>
    </contents>
```

### tb:set var="variable_name" val="variable_value"
HTML :
```html
    <contents>
        <tb:set var="say" val="Something"/>
        <span><tb:print var="say"/></span>
    </contents>
```
OUTPUT :
```html
    <contents>
        <span>Something</span>
    </contents>
```

### tb:if var="variable_name"
PHP :
```php
    require_once '../core/TemplateBuilder.php';
    $templateBuilder = new TemplateBuilder();
    $templateBuilder -> setVariable("say", "Something");
    $templateBuilder -> setVariable("error", false);
    $templateBuilder -> render('...');
```
HTML :
```html
    <contents>
        <tb:if var="say">
            <span class="message">Say <tb:print var="say"/></span>
        </tb:if>
        <tb:if var="error">
            <span class="error">show error</span>
        </tb:if>
    </contents>
```
OUTPUT :
```html
    <contents>
        <span class="message">Say Something</span>
    </contents>
```

### tb:for item="variable_array" var="variable_name"
PHP :
```php
    require_once '../core/TemplateBuilder.php';
    $templateBuilder = new TemplateBuilder();
    $templateBuilder -> setVariable("repeatLoop", array(1,2,3));
    $templateBuilder -> render('...');
```
HTML :
```html
    <contents>
        <tb:for item="repeatLoop" var="repeatVar">
            <span><tb:print var="repeatVar"/></span><br>
        </tb:for>
    </contents>
```
OUTPUT :
```html
    <contents>
        <span>1</span><br>
        <span>2</span><br>
        <span>3</span><br>
    </contents>
```

### tb:jscript - {{variable_name}}
PHP :
```php
    require_once '../core/TemplateBuilder.php';
    $templateBuilder = new TemplateBuilder();
    $templateBuilder -> setVariable("say", "Something");
    $templateBuilder -> render('...');
```
HTML :
```html
    <tb:jscript>
        var say = '{{say}}';
        console.log('Say ' + say);
    </tb:jscript>
```
OUTPUT :
```html
    <script type="text/javascript">
        var say = 'Something';
        console.log('Say ' + say);
    </script>
```