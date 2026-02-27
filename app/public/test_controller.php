<?php
echo "<h2>Vérification des contrôleurs</h2>";

$controllersDir = dirname(__DIR__) . '/controllers/';
echo "<p>Dossier controllers : " . $controllersDir . "</p>";

if(is_dir($controllersDir)) {
    $files = scandir($controllersDir);
    echo "<h3>Fichiers trouvés :</h3>";
    echo "<ul>";
    foreach($files as $file) {
        if($file != '.' && $file != '..') {
            echo "<li>" . $file . "</li>";
        }
    }
    echo "</ul>";
    
    if(file_exists($controllersDir . 'AuthController.php')) {
        echo "<p style='color:green'>✅ AuthController.php existe</p>";
    } else {
        echo "<p style='color:red'>❌ AuthController.php n'existe pas</p>";
    }
} else {
    echo "<p style='color:red'>❌ Le dossier controllers n'existe pas</p>";
}
?>