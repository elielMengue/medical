<?php
// Test pour vérifier les données POST envoyées par le formulaire
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "📨 Données POST reçues :\n";
    echo "========================\n\n";
    
    echo "Champs reçus :\n";
    foreach($_POST as $key => $value) {
        echo "- $key : '$value'\n";
    }
    
    echo "\n🕐 Vérification du format date/heure :\n";
    if(isset($_POST['date_heure'])) {
        echo "- date_heure reçu : '{$_POST['date_heure']}'\n";
        
        // Tester la conversion
        try {
            $date_heure = new DateTime($_POST['date_heure']);
            echo "- Date extraite : " . $date_heure->format('Y-m-d') . "\n";
            echo "- Heure extraite : " . $date_heure->format('H:i:s') . "\n";
            echo "✅ Conversion réussie\n";
        } catch(Exception $e) {
            echo "❌ Erreur de conversion : " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ date_heure non trouvé dans POST\n";
    }
    
    echo "\n🔍 Vérification des champs requis :\n";
    $required_fields = ['patient_id', 'infirmier_id', 'type_soin', 'date_heure'];
    foreach($required_fields as $field) {
        if(isset($_POST[$field]) && !empty($_POST[$field])) {
            echo "✅ $field : OK\n";
        } else {
            echo "❌ $field : MANQUANT ou VIDE\n";
        }
    }
    
} else {
    echo "📝 Formulaire de test pour simuler la soumission\n";
    echo "==============================================\n\n";
    ?>
    
    <form method="POST" action="">
        <h3>Test de soumission de formulaire</h3>
        
        <label>Patient ID:</label>
        <input type="text" name="patient_id" value="1" required><br><br>
        
        <label>Infirmier ID:</label>
        <input type="text" name="infirmier_id" value="4" required><br><br>
        
        <label>Type de soin:</label>
        <input type="text" name="type_soin" value="Pansement" required><br><br>
        
        <label>Date et heure:</label>
        <input type="datetime-local" name="date_heure" value="<?php echo date('Y-m-d\TH:i'); ?>" required><br><br>
        
        <label>Description:</label>
        <textarea name="description">Test de description</textarea><br><br>
        
        <button type="submit">Tester la soumission</button>
    </form>
    
    <?php
}
?>