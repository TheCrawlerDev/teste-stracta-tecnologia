<?php
header ('Content-type: text/html; charset=iso-8859-1');
$html = crawlerPage('http://www.guiatrabalhista.com.br/guia/salario_minimo.htm');
$tabela = pesquisar($html,'<table border="1" width="1050" cellspacing="1" cellpadding="4" height="624" style="border-collapse: collapse; margin: 0px; padding: 0px;">','</table>',false);
$vetor = htmlTable2PHP($tabela);
array_pop($vetor); // removendo o ultimo elemento do array, pois este vem vazio
$vetor_organizado = orgarnizarArray($vetor);
print_r($vetor_organizado);
function htmlTable2PHP($tabela){
	$colunas = explode('</tr>',$tabela);
	$retorno = array();
	foreach($colunas as $coluna){
		$linhas = explode('</td>',$coluna);
		$retorno[] = array_filter(
						array_map(function($v){
					    	return trim(strip_tags($v));
						}, $linhas));
	}
	return $retorno;
}
function orgarnizarArray($array){
	$keys = $array[0];
	array_shift($array);
	$retorno = array(); 
	foreach($array as $values){
		for($i=1;$i<=count($keys);$i++){
			$key = $keys[$i];
			$coluna[$key] = $values[$i];
		}
		$retorno[] = $coluna;
	}
	return $retorno;
}
function crawlerPage($url,$useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36',$timeout = 12000){
		// $useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
		// $timeout = 12000;
		$dir = dirname(__FILE__);
		$cookie_file = $dir . '/cookies/' . md5($_SERVER['REMOTE_ADDR']) . '.txt';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_ENCODING, "" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_AUTOREFERER, true );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout );
		// curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		// $retorno = curl_exec($ch);
		// $this->returnPage = $retorno;
		return curl_exec($ch);
}
function pesquisar($string, $after, $before,$striptags=true){
	$subresult = '';
	if(strpos($string,$after) !== false) {
		$subresult = substr($string,strpos($string,$after)+strlen($after));
		$subresult = strchr($subresult,$before,true);		
	}
	$subresult = str_replace('&nbsp;','',$subresult);
	return $striptags===true ? strip_tags(trim($subresult)) : trim($subresult);
}
?>