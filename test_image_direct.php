<?php
$imagePath = '/projet_medical/app/public/assets/images/background-login.jpg';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Image</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .test-box {
            width: 100%;
            height: 300px;
            background-image: url('<?php echo $imagePath; ?>');
            background-size: cover;
            background-position: center;
            border: 2px solid #333;
            margin-bottom: 20px;
        }
        .info {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h2>🔍 Test d'affichage de l'image</h2>
    
    <div class="info">
        <p><strong>Chemin testé :</strong> <code><?php echo $imagePath; ?></code></p>
        <p><strong>URL complète :</strong> <a href="http://localhost<?php echo $imagePath; ?>" target="_blank">http://localhost<?php echo $imagePath; ?></a></p>
    </div>
    
    <h3>Test avec background-image :</h3>
    <div class="test-box"></div>
    
    <h3>Image directement :</h3>
    <img src="<?php echo $imagePath; ?>" style="max-width: 100%; border: 1px solid #ddd;">
    
    <h3>Informations sur l'image :</h3>
    <?php
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
    if(file_exists($fullPath)) {
        $imageInfo = getimagesize($fullPath);
        echo "<p>✅ Fichier trouvé</p>";
        echo "<p>Dimensions : " . $imageInfo[0] . " x " . $imageInfo[1] . "</p>";
        echo "<p>Type MIME : " . $imageInfo['mime'] . "</p>";
        echo "<p>Taille : " . round(filesize($fullPath) / 1024, 2) . " Ko</p>";
    } else {
        echo "<p>❌ Fichier non trouvé à : " . $fullPath . "</p>";
    }
    ?>
</body>
</html>