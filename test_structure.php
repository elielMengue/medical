<?php
echo "<h2>🔍 Diagnostic de la structure</h2>";

$basePath = 'C:\wamp\www\projet_medical';
echo "<p><strong>Chemin de base:</strong> " . $basePath . "</p>";

// Vérifier les dossiers
$folders = [
    'app',
    'app/views',
    'app/views/admin',
    'app/controllers',
    'app/public'
];

echo "<h3>📁 Vérification des dossiers:</h3>";
echo "<ul>";
foreach($folders as $folder) {
    $path = $basePath . '/' . $folder;
    if(is_dir($path)) {
        echo "<li style='color:green'>✅ $folder - OK</li>";
    } else {
        echo "<li style='color:red'>❌ $folder - MANQUANT</li>";
        echo "<li style='color:orange'>→ À créer: mkdir('$path')</li>";
    }
}
echo "</ul>";

// Vérifier les fichiers
$files = [
    'app/views/admin/users.php',
    'app/controllers/AdminController.php'
];

echo "<h3>📄 Vérification des fichiers:</h3>";
echo "<ul>";
foreach($files as $file) {
    $path = $basePath . '/' . $file;
    if(file_exists($path)) {
        echo "<li style='color:green'>✅ $file - OK</li>";
    } else {
        echo "<li style='color:red'>❌ $file - MANQUANT</li>";
    }
}
echo "</ul>";

echo "<h3>📋 Contenu du dossier app/views/ (si existe):</h3>";
$viewsPath = $basePath . '/app/views';
if(is_dir($viewsPath)) {
    $items = scandir($viewsPath);
    echo "<ul>";
    foreach($items as $item) {
        if($item != '.' && $item != '..') {
            $type = is_dir($viewsPath . '/' . $item) ? '📁' : '📄';
            echo "<li>$type $item</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>Le dossier views n'existe pas</p>";
}
?>