<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo 'Starting...<br>';



// // установка соединения
// $conn_id = ftp_connect($ftp_server);

// // проверка имени пользователя и пароля
// $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// // получить содержимое текущей директории
// $contents = ftp_nlist($conn_id, ".");

// // вывод $contents
// var_dump($contents);


$ftp_server = "178.250.159.1";
$conn_id = ftp_connect($ftp_server);
$ftp_user_name = "empire_ftp_new";
$ftp_user_pass = "1qaz2wsX";
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// $contents = ftp_nlist($conn_id, '/www/empirehall.com.ua/image/');
// $ttt = ftp_rawlist($conn_id, '/www/empirehall.com.ua/image/');

// Get lists
$nlist = ftp_nlist($conn_id, '/www/empirehall.com.ua/image/');
$rawlist = ftp_rawlist($conn_id, '/www/empirehall.com.ua/image/');

// Сортируем для правильного порядка (что бы совпадал с rawlist)
sort($nlist);

// echo "<pre>";
// var_dump($nlist);
// var_dump($rawlist);
// // echo "</pre>";


// $ftp_folders = array();
// $ftp_main_images = array();

// for ($i = 0; $i < count($nlist) - 1; $i++) 
// { 
//     if($rawlist[$i][0] == 'd')
//     {   
//         // Игнорим путь с Кэшем картинок
//         if($nlist[$i] == "/www/empirehall.com.ua/image/cache"){
//         } else if($nlist[$i] == "/www/empirehall.com.ua/image/jetcache"){
//         } else {
//             // Добавляем в аррей с папками
//             $ftp_folders[] = $nlist[$i];  
//         }

//     } else {
//         // Сохраняем в аррей картинок
//         $ftp_main_images[] = $nlist[$i];
//     }
// }

// // Сабкатегории
// // Перебираем подкатегории

// foreach($ftp_folders as $ftp_folder){

//     $sub_n_list = ftp_nlist($conn_id, $ftp_folder);
//     $sub_r_list = ftp_rawlist($conn_id, $ftp_folder);
//     sort($sub_n_list);

//     echo "<pre>";
//     var_dump($ftp_folder);
//     var_dump($sub_n_list);
//     var_dump($sub_r_list);
//     echo "</pre>";

//     for($a = 0; $a < count($sub_n_list) - 1; $a++){
//         // echo "<pre>";
//         // var_dump($sub_n_list[$a]);
//         // var_dump($sub_r_list[$a]);
//         // var_dump($sub_r_list[$a][0]);
//         // echo "</pre>";
//         if($sub_r_list[$a][0] == 'd'){
//             echo "<pre>";
//             var_dump($sub_n_list[$a]);
//             var_dump($sub_r_list[$a]);
//             var_dump($sub_r_list[$a][0]);
//             echo "</pre>";
//             $ftp_folders[] = $sub_n_list[$a];
//         } else {

//             $ftp_main_images[] = $sub_n_list[$a];
//         }
//     } 
//     // Чистим переменные 
//     unset($sub_n_list, $sub_r_list, $a);
// }


// echo "FOLDERS ". count($ftp_folders) ."<pre>";
// var_dump($ftp_folders);
// // var_dump($rawlist);
// echo "</pre>";

// echo "IMAGES ". count($ftp_main_images) ."<pre>";
// var_dump($ftp_main_images);
// // var_dump($rawlist);
// echo "</pre>";










    function getAllDirs($directory, $directory_seperator) {
        $dirs = array_map(function ($item) use ($directory_seperator) {
            return $item . $directory_seperator;
        }, array_filter(glob($directory . '*'), 'is_dir'));
        foreach ($dirs AS $dir) {
            if (strpos($dir, 'image') === 0) {
                // echo "<pre>";
                // var_dump($dir);
                // echo "</pre>";
                // It starts with 'image'
                if(strpos($dir, 'image/cache/') === 0){
                    //var_dump("tes");
                } else {
                    $dirs = array_merge($dirs, getAllDirs($dir, $directory_seperator));
                }
                
            }
        }
        return $dirs;
    }
    
    function getAllImgs($directory) {
        $resizedFilePath = array();
        foreach ($directory AS $dir) {
            foreach (glob($dir . '*.{jpg,JPG,jpeg,JPEG,png,PNG}', GLOB_BRACE) as $filename) {
                array_push($resizedFilePath, $filename);
            }
        }
        return $resizedFilePath;
    }


$directory = file_get_contents('ftp://empire_ftp_new:1qaz2wsX@178.250.159.1/www/empirehall.com.ua/image/');
$directory_seperator = "/";
$allimages = getAllImgs(getAllDirs($directory, $directory_seperator));

// $extensions_array = array('jpg','png','jpeg');
// $files = scandir($directory);


echo "<pre>";
var_dump("Working...");
//var_dump($allimages);
echo "</pre>";

    // PDO
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=emp_images;charset=utf8mb4","empireha_dev","873^cuHf)X");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");


    // Заносим пути картинок в базу
    $total_image_count = 0;
    $tt_images = array();
    foreach($allimages as $i_image){
        $total_image_count++;
        //createNewImageLog($pdo, $i_image, $total_image_count);
        createImageDiff($pdo, $i_image, $total_image_count);
        //$tt_images[] = $i_image;
        array_push($tt_images, $i_image);
    }
    // echo "<pre>";
    // var_dump($tt_images);
    // echo "</pre>";
    echo " Total Image Count: " . $total_image_count . " <br>";
    // Заносим кол-во картинок в базу
    createTotalImageCount($pdo, $total_image_count);


    function createImageDiff($pdo, $image_path, $log_id){
        // Дата
        $date = date('Y-m-d H:i:s');
        // Фикс енкодинга
        $image_path_fix = mb_convert_encoding($image_path, 'UTF-8', 'Windows-1252');
        // Проверка на дубли
        $dup_check = checkDupImage($pdo, $image_path_fix);
        //var_dump($dup_check);
        // Если картинки нет - добавить
        if(!isset($dup_check[0]['image_path'])){
            $image_log = $pdo->prepare("INSERT INTO `image_diff` ( `image_path`, `date` ) VALUES ( :image_path, :date)");
            //$image_log->bindValue(':log_id', $log_id);
            $image_log->bindValue(':image_path', $image_path_fix);
            $image_log->bindValue(':date', $date);

            $image_log->execute();
            var_dump("DupAdded");
        } 
    }

    
    function createNewImageLog($pdo, $image_path, $log_id)
    {
        // Дата
        $date = date('Y-m-d H:i:s');
        // Фикс енкодинга
        $image_path_fix = mb_convert_encoding($image_path, 'UTF-8', 'Windows-1252');
        // Проверка на дубли
        $dup_check = checkDupImage($pdo, $image_path_fix);
        //var_dump($dup_check);
        // Если картинки нет - добавить
        if(!isset($dup_check[0]['image_path'])){
            $image_log = $pdo->prepare("INSERT INTO `image_log` ( `image_path`, `date` ) VALUES ( :image_path, :date)");
            //$image_log->bindValue(':log_id', $log_id);
            $image_log->bindValue(':image_path', $image_path_fix);
            $image_log->bindValue(':date', $date);

            $image_log->execute();
        } else {
            var_dump("DupImg");

        }

    }

    function checkDupImage($pdo, $image_path){

        $total_image_count = $pdo->prepare("SELECT * FROM image_log WHERE image_path=:image_path LIMIT 1 "); 
        $total_image_count->bindValue(':image_path', $image_path);
        $total_image_count->execute();
        $total_image = $total_image_count->fetchAll(PDO::FETCH_ASSOC);

        return $total_image;
    }

    function getTotalImageCount($pdo){

        $total_image_count = $pdo->prepare("SELECT * FROM image_total LIMIT 1 "); 
        $total_image_count->execute();
        $total_image = $total_image_count->fetchAll(PDO::FETCH_ASSOC);

        return $total_image;
    }

    function createTotalImageCount($pdo, $image_count){
        // Дата
        $date = date('Y-m-d H:i:s');

        $image_log = $pdo->prepare("INSERT INTO `image_total` ( `image_count`, `date` ) VALUES (:image_count, :date)");
        $image_log->bindValue(':image_count', $image_count);
        $image_log->bindValue(':date', $date);

        $image_log->execute();
    }