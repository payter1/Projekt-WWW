<?php

function saveFile($fileInputName, $allowedExtentions, $maxFileSizeMb, $saveFolder) {
    // fileInputName - name pola <input type="file"> przesyłającego pliki
    // allowedExtentions - Dozwolone rozszerzenia plików wypisane po przecinku np.: 'png, jpg, gif'
    // maxFileSizeMb - maksymalny rozmiar pliku w Mb
    // saveFolder - Folder w którym ma się zapisać plik

    // Zwraca array:
    //              [1,"ścieżki do pliku oddzielone ';'", ilość dodanych plików] - w przypadku dodania pliku
    //              [0,"błędy"] - w przypadku wystąpienia błędów

    if (!isset($_FILES[$fileInputName])) {
        return array(0, array("Nie przesłano żadnego pliku."));
    }

    $counter = 0;
    $errors = array();
    $sciezkaArray = array(); 

    $names = (array)$_FILES[$fileInputName]['name'];
    $sizes = (array)$_FILES[$fileInputName]['size'];
    $tmps  = (array)$_FILES[$fileInputName]['tmp_name'];
    $errs  = (array)$_FILES[$fileInputName]['error'];

    $exExt = explode(',', $allowedExtentions);
    $extensions = array_map('trim', $exExt); 
    
    $maxBytes = $maxFileSizeMb * 1024 * 1024; 

    $saveFolder = rtrim($saveFolder, '/');
    if (!file_exists($saveFolder)) {
        mkdir($saveFolder, 0777, true);
    }

    $filesToMove = array();

    foreach ($names as $key => $name) {
        if (empty($name) || $errs[$key] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $file_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($file_ext, $extensions)) {
            $errors[] = "Rozszerzenie pliku '$name' jest niedozwolone.";
        }

        if ($sizes[$key] > $maxBytes) {
            $errors[] = "Plik '$name' przekracza dozwolony rozmiar $maxFileSizeMb MB.";
        }

        $uniqueName = uniqid('img_') . '.' . $file_ext;
        $filesToMove[] = array(
            'tmp_name' => $tmps[$key],
            'target_path' => $saveFolder . '/' . $uniqueName
        );
    }

    if (empty($filesToMove) && empty($errors)) {
        return array(0, array("Nie wybrano żadnego pliku do wgrania."));
    }

    if (empty($errors)) {
        foreach ($filesToMove as $file) {
            if (move_uploaded_file($file['tmp_name'], $file['target_path'])) {
                $sciezkaArray[] = $file['target_path'];
                $counter++;
            } else {
                return array(0, array("Błąd podczas zapisu pliku na serwerze."));
            }
        }
        
        $sciezki_string = implode(';', $sciezkaArray);
        return array(1, $sciezki_string, $counter);
        
    } else {
        return array(0, $errors);
    }
}

//OBSŁUGA WYSYŁKI PLIKÓW
    function file_check_err($inputName)
    {
    if ($_FILES[$inputName]['error'] > 0)
    {
        switch ($_FILES[$inputName]['error'])
        {
        // jest większy niż domyślny maksymalny rozmiar,
        // podany w pliku konfiguracyjnym
        case 1: {$problem = 'Rozmiar pliku jest zbyt duży.'; break;} 

        // jest większy niż wartość pola formularza 
        // MAX_FILE_SIZE
        case 2: {$problem = 'Rozmiar pliku jest zbyt duży.'; break;}

        // plik nie został wysłany w całości
        case 3: {$problem = 'Plik wysłany tylko częściowo.'; break;}

        // plik nie został wysłany
        case 4: {$problem = 'Nie wysłano żadnego pliku.'; break;}

        // pozostałe błędy
        default: {$problem = 'Wystąpił błąd podczas wysyłania.'; break;}
        }
        return "PROBLEM: ".$problem;
    }
    return true;
    }

    function file_check_type($inputName)
    {
        $fileType = $_FILES[$inputName]['type'];
        if ($fileType != 'image/jpeg' && $fileType != 'image/jpg' && $fileType != 'image/png' && $fileType != 'image/gif'){
            return "niewłaściwe rozszerzenie pliku";
        }
        return true;
    }

    function file_save($inputName, $folder, $nazwa)
    {
    $fileType = explode('/', $_FILES[$inputName]['type']);
    $fType = $fileType[1];
    $lokalizacja = $folder.'/'.$nazwa.'.'.$fType;
    if(is_uploaded_file($_FILES[$inputName]['tmp_name']))
    {
        if(!move_uploaded_file($_FILES[$inputName]['tmp_name'], $lokalizacja))
        {
            return 'problem: Nie udało się skopiować pliku do katalogu.';
        }
        
    }
    else
    {
        return 'NULL';
    }
    return $lokalizacja;

    }
// / OBSŁUGA WYSYŁKI PLIKÓW

function plCharset($string) {

    $string = strtolower($string);
    $polskie = array(',', ' - ',' ','ę', 'Ę', 'ó', 'Ó', 'Ą', 'ą', 'Ś', 's', 'ł', 'Ł', 'ż', 'Ż', 'Ź', 'ź', 'ć', 'Ć', 'ń', 'Ń','-',"'","/","?", '"', ":", 'ś', '!','.', '&', '&', '#', ';', '[',']','domena.pl', '(', ')', '`', '%', '”', '„', '…');
    $miedzyn = array('-','-','-','e', 'e', 'o', 'o', 'a', 'a', 's', 's', 'l', 'l', 'z', 'z', 'z', 'z', 'c', 'c', 'n', 'n','-',"","","","","",'s','','', '', '', '', '', '', '', '', '', '', '', '', '');
    $string = str_replace($polskie, $miedzyn, $string);
    
    // usuń wszytko co jest niedozwolonym znakiem
    $string = preg_replace('/[^0-9a-z\-]+/', '', $string);
    
    // zredukuj liczbę myślników do jednego obok siebie
    $string = preg_replace('/[\-]+/', '-', $string);
    
    // usuwamy możliwe myślniki na początku i końcu
    $string = trim($string, '-');
    
    $string = stripslashes($string);
    
    // na wszelki wypadek
    $string = urlencode($string);
    
    return $string;
    }

function GetAge($bday){
$dob = new DateTime($bday);
$now = new DateTime();
$difference = $now->diff($dob);
$age = $difference->y;

return $age;
}
function GetBdayRange($age) {
    $now = new DateTime();
    
    $maxBday = clone $now;
    $maxBday->modify("-$age years");
    
    $minBday = clone $now;
    $minBday->modify("-" . ($age + 1) . " years +1 day");
    
    return [
        'min' => $minBday->format('Y-m-d'),
        'max' => $maxBday->format('Y-m-d')
    ];
}

function FirstFreeID($dbName, $tableName){
    $sql = "SELECT `AUTO_INCREMENT` 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = '$dbName' 
            AND TABLE_NAME = '$tableName'";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $firstFree = (int)$row['AUTO_INCREMENT'];
    } else {
        // Jeśli tabela nie istnieje lub jest błąd, domyślnie 1
        $firstFree = 1; 
        return $firstFree;
}
}
?>