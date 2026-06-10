<?php

function saveFile ($fileInputName, $allowedExtentions, $maxFileSizeMb, $saveFolder){
    // fileInputName - name pola <input type="file"> przesyłającego pliki
    // allowedExtentions - Dozwolone rozszerzenia plików wypisane po przecinku np.: 'png, jpg, gif'
    // maxFileSizeMb - maksymalny rozmiar pliku w Mb
    // saveFolder - Folder w którym ma się zapisać plik

    // Zwraca array:
    //              [1,"ścieżki do pliku oddzielone ';'", ilość dodanych plików] - w przypadku dodania pliku
    //              [0,"błędy"] - w przypadku wystąpienia błędów

    $counter = 0;
    $sciezka = '';

    $exExt = explode(',',$allowedExtentions);
    $extArray = array();
    foreach($exExt as $ext){
       $extArray[] = "$ext";
    }
    $errors= array();
    if(is_uploaded_file($_FILES['$fileInputName']['tmp_name'])){
        $errors[]="Nie przesłano żadnego pliku";
    }
    $file_name = $_FILES[$fileInputName]['name'];
    $file_size =$_FILES[$fileInputName]['size'];
    $file_tmp =$_FILES[$fileInputName]['tmp_name']; 
    $file_type=$_FILES[$fileInputName]['type'];
    $extensions= $extArray; 
    foreach($file_name as $key => $value){ 
     $tmp = explode('.',$_FILES[$fileInputName]['name'][$key]);
     $file_ext = strtolower(end($tmp));
     if(in_array($file_ext,$extensions)=== false){
         $errors[]="Rozszerzenie niedozwolone.";
     } 
     $maxFileSize2B = $maxFileSizeMb * 1000000; //przeliczanie mb na bajty
     if($file_size[$key] > $maxFileSize2B){
         $errors[]="Plik nie może być większy niż $maxFileSizeMb MB.";
     } 
    }  
    if(empty($errors)==true){        
     foreach($file_name as $key => $value){
       $data = date('Y-m-d H:i:s');
       $saveFolder=trim($saveFolder,'/');
       if(!file_exists($saveFolder)){
          mkdir($saveFolder, 0777);
       }
        $saveFolder = $saveFolder.'/';
        $sciezka .= $saveFolder.$file_name[$key].';';
        $sciezka2 = substr($sciezka, 0, -1);
        move_uploaded_file($file_tmp[$key],$sciezka2);
        $counter++;
     } 
     $sciezka = substr($sciezka, 0, -1);
     return array(1, $sciezka, $counter);
    }else{
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
?>