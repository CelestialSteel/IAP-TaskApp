<?php
$__autoload_paths = [
    __DIR__ . '/plugin/PHPMailer/vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
];
$__autoload_loaded = false;
foreach ($__autoload_paths as $__path) {
    if (file_exists($__path)) {
        require $__path;
        $__autoload_loaded = true;
        break;
    }
}
/*if (!$__autoload_loaded) {
    // Fallback to direct includes if composer autoloaders are not present
    require __DIR__ . '/plugin/PHPMailer/src/Exception.php';
    require __DIR__ . '/plugin/PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/plugin/PHPMailer/src/SMTP.php';
}
*/
// Load app configuration
require __DIR__ . '/conf.php';

// Directories to search for class files
$directory = array('global', 'layouts', 'Forms');

// PSR-0 style simple autoloader for this project
spl_autoload_register(function ($class_name) use ($directory) {
    $baseDir = __DIR__;
    $candidatesFor = function ($dir, $name) use ($baseDir) {
        $dirPath = $baseDir . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR;
        $names = [
            $name . '.php',                   // Exact
            ucfirst($name) . '.php',          // PascalCase
            rtrim($name, 's') . '.php',       // Singular
            ucfirst(rtrim($name, 's')) . '.php', // Singular + PascalCase
        ];
        return array_map(fn($n) => $dirPath . $n, $names);
    };

    foreach ($directory as $dir) {
        foreach ($candidatesFor($dir, $class_name) as $path) {
            if (file_exists($path) && is_readable($path)) {
                require_once $path;
                return true;
            }
        }
    }
    return false;
});

// Instantiate commonly used objects
try {
    $ObjSendMail = new SendMail();
    $ObjLayout = new layouts();
    $ObjForm = new forms();

    // Back-compat aliases
    $layout = $ObjLayout;
    $forms = $ObjForm;
} catch (Throwable $e) {
    error_log('Initialization error: ' . $e->getMessage());
    die('Initialization failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>
