<?php
echo "<!DOCTYPE html>
<html>
<head>
    <title>Diagnostic du projet</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #6c757d; padding-bottom: 5px; }
        .ok { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; border-left: 3px solid #6c757d; }
    </style>
</head>
<body>
    <h1>🔍 Diagnostic complet du projet</h1>";

// 1. Informations de base
echo "<div class='section'>";
echo "<h2>📁 Informations</h2>";
echo "<p><strong>Répertoire courant :</strong> " . __DIR__ . "</p>";
echo "<p><strong>PHP Version :</strong> " . phpversion() . "</p>";
echo "</div>";

// 2. Structure des dossiers
echo "<div class='section'>";
echo "<h2>📂 Structure du projet</h2>";

function scanDir2($dir, $prefix = '') {
    $result = '';
    $files = scandir($dir);
    foreach($files as $file) {
        if($file != '.' && $file != '..') {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if(is_dir($path)) {
                $result .= $prefix . "📁 $file\n";
                // CORRIGÉ: array() au lieu de []
                $importants = array('controllers', 'models', 'views', 'public');
                if(in_array($file, $importants)) {
                    $result .= scanDir2($path, $prefix . '  ');
                }
            } else {
                // CORRIGÉ: array() au lieu de []
                $fichiersImportants = array('AuthController.php', 'PatientController.php', 'index.php');
                if(in_array($file, $fichiersImportants)) {
                    $result .= $prefix . "📄 $file\n";
                }
            }
        }
    }
    return $result;
}

echo "<pre>" . scanDir2(__DIR__) . "</pre>";
echo "</div>";

// 3. Vérification des fichiers critiques
echo "<div class='section'>";
echo "<h2>🔑 Fichiers critiques</h2>";

// CORRIGÉ: array() au lieu de []
$filesToCheck = array(
    'app/controllers/AuthController.php',
    'app/controllers/PatientController.php',
    'app/models/User.php',
    'app/models/Patient.php',
    'app/views/auth/login.php',
    'app/views/patients/index.php',
    'app/public/index.php'
);

foreach($filesToCheck as $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    $status = $exists ? "<span class='ok'>✅ OK</span>" : "<span class='error'>❌ Manquant</span>";
    $size = $exists ? " (" . filesize($path) . " octets)" : "";
    echo "<p><strong>$file</strong> : $status $size</p>";
    
    if($exists && strpos($file, '.php') !== false) {
        $content = file_get_contents($path);
        if(strpos($content, '<?php') === 0) {
            echo "<p style='margin-left:20px'><span class='ok'>✓ Bon début de fichier</span></p>";
        } else {
            echo "<p style='margin-left:20px'><span class='error'>✗ Problème: ne commence pas par <?php</span></p>";
        }
    }
}
echo "</div>";

// 4. Vérification du fichier AuthController spécifiquement
echo "<div class='section'>";
echo "<h2>🔐 Vérification détaillée de AuthController</h2>";

$authPath = __DIR__ . '/app/controllers/AuthController.php';
if(file_exists($authPath)) {
    echo "<p><span class='ok'>✅ Fichier trouvé</span></p>";
    
    $content = file_get_contents($authPath);
    echo "<h4>Premières lignes :</h4>";
    $lines = explode("\n", $content);
    for($i = 0; $i < min(15, count($lines)); $i++) {
        $line = htmlspecialchars($lines[$i]);
        $highlight = '';
        if(strpos($lines[$i], 'namespace') !== false) $highlight = ' style="color:blue;font-weight:bold"';
        if(strpos($lines[$i], 'class') !== false) $highlight = ' style="color:green;font-weight:bold"';
        echo "<div$highlight>" . ($i+1) . ": " . $line . "</div>";
    }
    
    // Vérifier le namespace
    if(preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
        echo "<p><strong>Namespace trouvé :</strong> " . $matches[1] . "</p>";
        if($matches[1] == 'Controllers') {
            echo "<p><span class='ok'>✓ Namespace correct</span></p>";
        } else {
            echo "<p><span class='error'>✗ Namespace incorrect, devrait être 'Controllers'</span></p>";
        }
    } else {
        echo "<p><span class='error'>✗ Aucun namespace trouvé</span></p>";
    }
    
    // Vérifier la classe
    if(preg_match('/class\s+AuthController/', $content)) {
        echo "<p><span class='ok'>✓ Classe AuthController trouvée</span></p>";
    } else {
        echo "<p><span class='error'>✗ Classe AuthController non trouvée</span></p>";
    }
    
} else {
    echo "<p><span class='error'>❌ Fichier AuthController.php introuvable</span></p>";
}
echo "</div>";

// 5. Test d'URL
echo "<div class='section'>";
echo "<h2>🌐 Tests d'URL</h2>";
echo "<p>Essayez ces URLs :</p>";
echo "<ul>";
echo "<li><a href='/projet_medical/app/public/index.php?controller=auth&action=loginForm' target='_blank'>/projet_medical/app/public/index.php?controller=auth&action=loginForm</a></li>";
echo "<li><a href='/projet_medical/public/index.php?controller=auth&action=loginForm' target='_blank'>/projet_medical/public/index.php?controller=auth&action=loginForm</a></li>";
echo "<li><a href='/projet_medical/index.php?controller=auth&action=loginForm' target='_blank'>/projet_medical/index.php?controller=auth&action=loginForm</a></li>";
echo "</ul>";
echo "</div>";

echo "</body></html>";
?>