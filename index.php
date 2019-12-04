<?
ini_set("display_errors", "Off");
ini_set("log_errors", "Off");
header("Content-type: text/html; charset=ISO-8859-1");

// INICIA A SESSÃO
if(!isset($_SESSION))
	session_start();

if($_SERVER['HTTPS'] != "on")
		die(header("Location: https://".($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])));

if(!preg_match("/([a-z]*)\.([a-z]*)\.com*/", $_SERVER['HTTP_HOST'])){
	if(!preg_match("/www/", $_SERVER['HTTP_HOST'])){
		header("HTTP/1.1 301 Moved Permanently");
		die(header("Location: https://www.".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
	}
}

$id_loja = "clubvest";
$pagina = "https://sistema.wbuy.com.br".$_SERVER['REQUEST_URI'];
$painel = "https://sistema.wbuy.com.br"."/painel";

preg_match("/^\/painel$|^\/painel\/$/", $_SERVER['REQUEST_URI']) ? header("Location:{$painel}") : "";
preg_match("/^\/blog$/", $_SERVER['REQUEST_URI']) ? header("Location: ./blog/") : "";

$strCookie = session_name()."=".$_COOKIE[session_name()].";document_root=".$_SERVER['DOCUMENT_ROOT'].";path=/;lojaID=".$id_loja.";ip=".$_SERVER['REMOTE_ADDR'].";base=".$_SERVER['HTTP_HOST'].";referer=".$_SERVER['HTTP_REFERER'].";sessid=".session_id();

$post_array = array();

foreach($_FILES as $param => $file){
	$cfile = curl_file_create($file['tmp_name'], $file['type'], $file['name']);
	$post_array[$param] = $cfile;
}

foreach($_POST as $param => $file){
	if(is_array($file))
		$file = json_encode($file);
	$post_array[$param] = @$file;
}

if(preg_match("/\.js/", $_SERVER['REQUEST_URI'])){
	header("Content-type: text/javascript");
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data, Accept-Encoding: gzip"));
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_URL, $pagina);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);
curl_setopt($ch, CURLOPT_COOKIE, $strCookie);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_VERSION_IPV6);

$retorno = curl_exec($ch);

if(empty($retorno))
	echo "Erro ao acessar cURL: ".curl_error($ch);

curl_close($ch);

die($retorno);
