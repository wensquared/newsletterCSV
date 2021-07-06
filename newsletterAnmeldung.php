<?php
    
    /*
        Das Formular sollte inhaltlich wie folgt überprüft werden: 

        Anrede: Pflichtfeld
        Vorname: Pflichtfeld, mindestens 2 Zeichen, maximal 50 Zeichen
        Nachname: Pflichtfeld, mindestens 2 Zeichen, maximal 50 Zeichen
        E-Mail: Pflichtfeld, E-Mail
        Datenschutz: Pflichtfeld

        Liegt Fehler vor, dann sollen die entsprechenden Fehlermeldungen ausgegeben werden.
        Liegt kein Fehler vor, dann sollen die Daten in eine Datei (CSV) gespeichert werden.

        Optional: Die E-Mail Adresse in der CSV Datei soll eindeutig sein.

    */

    require_once './app/lib/Session.php';

    function html_isInvalid_check($val,$name){
        if(is_object($val) && $val->isValid($name)) {
            echo 'is-invalid';
        }
    }

    function print_errmsg($val,$name){
        if (is_object($val) && $val->isValid($name)) {
            echo $val->isValid($name);
        }
    }

    $sess = app\lib\Session::init();
    if (!empty($_POST)) {
        if (!isset($_POST['_token']) ||$_POST['_token'] != $sess->getCsrf()) {
            $error = 'Datenübertragung fehlgeschlagen';
        }
        else {
            require_once './app/lib/Validation.php';
            $val = new app\lib\Validation;

            $val->setElement('anrede',$_POST['anrede'])->required()->arrayCheck(['Frau','Herr','Divers'],'Anredeform wurde falsch übermittelt.');
            $val->setElement('vorname',$_POST['vorname'])->required()->min(2)->max(50);
            $val->setElement('nachname',$_POST['nachname'])->required()->min(2)->max(50);
            $val->setElement('email',$_POST['email'])->required()->email();
            $val->setElement('datenschutz',$_POST['datenschutz'])->required()->arrayCheck(['Datenschutz gelesen'],'Datenschutzauswahl konnte nicht übermittelt werden.');
        
            $csvFolder = __DIR__.'/newsletter/';
            $csvFile = $csvFolder.'newsletter.csv';

            if( !is_dir($csvFolder)){
                mkdir($csvFolder,0755,true);
            }

            if(file_exists($csvFile) && (!$val->isError())){
                $file = fopen($csvFile,'r');
                while (($data = fgetcsv($file,1000,';')) !== false) {
                    if (strtolower($data[3]) == strtolower($_POST['email'])) {
                        $err_email = 'Email schon vorhanden!';
                        break;
                    }
                }
                fclose($file);
            }
        
            if (!$val->isError() && empty($err_email)) {
                $file = fopen($csvFile,'a');
                if ($file !== false) {
                    unset($_POST['_token']);
                    fputcsv($file,$_POST,';');
                    $success = 'Die Daten wurden gespeichert';
                    unset($_POST);
                }
                else {
                    $error = 'Datei kann nicht geöffnet werden!';
                }
                fclose($file);
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Formular</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <style>
        #wrapper{
            max-width: 700px;
            margin:30px auto;
        }
    </style>
</head>
<body>
  <div id="wrapper" class="container-fluid">
    <h1>Newsletter Anmeldung</h1>
    <?php
        if (!empty($error)) {
            echo '<div style="color:red">'.$error.'</div>';
        }
        if( isset($success) ){
            echo '<div style="color:green; margin-bottom:10px;">'.$success.'</div>';
        } 
        if (!empty($err_email)) {
            echo '<div style="color:red">'.$err_email.'</div>';
        }
    ?>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="form-group">
            <div class="form-check form-check-inline <?php html_isInvalid_check($val,'anrede');?>">
                <input class="form-check-input" type="radio" name="anrede" id="w" value="Frau" 
                    <?php
                        if(isset($_POST['anrede']) && $_POST['anrede'] == 'Frau') echo 'checked';
                    ?>
                >
                <label class="form-check-label" for="w">Frau</label>
                </div>
                <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="anrede" id="m" value="Herr"
                    <?php
                        if(isset($_POST['anrede']) && $_POST['anrede'] == 'Herr') echo 'checked';
                    ?>
                >
                <label class="form-check-label" for="m">Herr</label>
                </div>
                <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="anrede" id="d" value="Divers"
                    <?php
                        if(isset($_POST['anrede']) && $_POST['anrede'] == 'Divers') echo 'checked';
                    ?>
                >
                <label class="form-check-label" for="d">Divers</label>
            </div>
            <div class="invalid-feedback"><?php print_errmsg($val,'anrede');?></div>
        </div>
        <div class="form-group">
            <label for="vorname">Vorname*</label>
            <input type="text" name="vorname" id="vorname" class="form-control
                <?php html_isInvalid_check($val,'vorname');?>" value="<?php echo $_POST['vorname'] ?? '' ?>">
            <div class="invalid-feedback"><?php print_errmsg($val,'vorname');?></div>
        </div>
        <div class="form-group">
            <label for="Nachname">Nachname*</label>
            <input type="text" name="nachname" id="nachname" class="form-control 
                <?php html_isInvalid_check($val,'nachname');?>" value="<?php echo $_POST['nachname'] ?? '' ?>">
            <div class="invalid-feedback"><?php print_errmsg($val,'nachname');?></div>
        </div>
        <div class="form-group">
            <label for="email">E-Mail*</label>
            <input type="text" name="email" id="email" class="form-control 
                <?php html_isInvalid_check($val,'email');?> " value="<?php echo $_POST['email'] ?? '' ?>">
            <div class="invalid-feedback"><?php print_errmsg($val,'email');?></div>
        </div>
        <div class="form-group">
            <div class="form-check is-invalid">
                <input class="form-check-input" type="checkbox" name="datenschutz" id="ds" value="Datenschutz gelesen">
                <label class="form-check-label is-invalid" for="ds">Ich habe die <a href="#">Datenschutzerklärung</a> gelesen und bin damit einverstanden, dass die von mir eingegebenen personenbezogenen Daten gespeichert werden. Die Anmeldung kann ich jederzeit widerrufen.</label>
                <div class="invalid-feedback"><?php print_errmsg($val,'datenschutz');?></div>
            </div>
        </div>
        <input type="hidden" name="_token" value="<?php echo $sess->setCsrf();?>">
       <button type="submit" class="btn btn-primary">Anmelden</button>
    </form>
  </div>
</body>
</html>