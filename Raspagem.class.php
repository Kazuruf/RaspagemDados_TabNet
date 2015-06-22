<?php
/**
 * Classe para realizar a raspagem dos dados  
 * 
 * @author Willians Paulo Pedroso
 * <willianspedroso@gmail.com>
 * @since meados de março 2015
 *
 */

class Raspagem {
	
	//atributos
	private $ch = "";
	private $mapa;
	
	/**
	 * Metodo construtor que starta ao se instanciar
	 */
	public function __construct() {
		
		$this->dao = new DAO();
		
	}
	
	/**
	 * Inicio do processo
	 */
	public function startUrls(){
		
		echo "Iniciando a raspagem...\n";
		
		echo "Setando as urls\n";
		
		//pega as urls
		#$filtro["urp_id"] = 1;
		$arrUrl = $this->dao->getUrlPrimaria($filtro);
		
		echo "Iniciando Curl\n";
		// Inicia o cURL
		$this->ch = curl_init();
				
		//varre as maquinas
		foreach($arrUrl as $urp_id => $url){
			
			echo "URL: $url\n";
			
			//pega os valores por variavel de cada maquina
			$dados = $this->getUrls($url); 
			
			echo "Trabalhando a string\n";
			//metodo para separar os headers, bodys do html
			$str = $this->trabalhaString($dados,"name=\"saude\"","name=\"saude\"");
			
			echo "Pegando as primeiras URLs\n";
			//metodo para pegar as urls secundarias
			$arrUrlsRadio = $this->separaValue($str,"value=","onClick");
			
			echo "Montando as urls dos filtros\n";
			//monta as urls de filtros
			$this->montaUrls_getUrls($url, $arrUrlsRadio, $urp_id);
			
			print "####################foi####################\n";
			
			//file_put_contents("html.txt", $mapa, FILE_APPEND);
				
		}//fim foreach urls
		
		//fecha a conexao
		$this->closeConn();
		
	} // fim metodo start
	
	private function trabalhaString($dados, $inicio, $ultima, $qtd = 150) {
		//pega a posicao onde esta
		//$var = strpos($dados, "Nivel0");
		$varInicio 	= strpos($dados, $inicio);
		$varUltima 	= strrpos($dados, $ultima);
			
		//total de caracteres para pegar a url
		$varFim = ($varUltima - $varInicio) + $qtd;
			
		//pega da posicao para frente
		$str = substr($dados, $varInicio, $varFim);
		
		return $str;
	}
	
	private function trabalhaString2($dados, $inicio, $ultima, $qtd = 150) {
		//pega a posicao onde esta
		//$var = strpos($dados, "Nivel0");
		$varInicio 	= strpos($dados, $inicio);
		$varUltima 	= strpos($dados, $ultima);
			
		//total de caracteres para pegar a url
		$varFim = ($varUltima - $varInicio) + $qtd;
			
		//pega da posicao para frente
		$str = substr($dados, $varInicio, $varFim);
	
		return $str;
	}
	
	
	/**
	 * Verificar se existe conexao
	 */
	private function pingMaquina($ip) {
		//executa o ping
		$ping = exec("ping -n 1 -w 1 " . $ip);
		
		//verifica se o ping foi executado corretamente
		if("    M" == substr($ping,0,5)) {
			return true;
		} else {
			return false;
		}//fim verificacao
		
	}
	
	
	/**
	 * Busca o valor da variavel nas maquinas
	 */
	private function getUrls($url) {
		
		//$variaveis = "Linha=Regi%E3o&Coluna=--N%E3o-Ativa--&Incremento=Nascim_p%2Fresid.m%E3e&Arquivos=nvuf12.dbf&SRegi%E3o=TODAS_AS_CATEGORIAS__&pesqmes2=Digite+o+texto+e+ache+f%E1cil&SUnidade_da_Federa%E7%E3o=TODAS_AS_CATEGORIAS__&SLocal_ocorr%EAncia=TODAS_AS_CATEGORIAS__&pesqmes4=Digite+o+texto+e+ache+f%E1cil&SIdade_da_m%E3e=TODAS_AS_CATEGORIAS__&pesqmes5=Digite+o+texto+e+ache+f%E1cil&SInstru%E7%E3o_da_m%E3e=TODAS_AS_CATEGORIAS__&SEstado_civil_m%E3e=TODAS_AS_CATEGORIAS__&SDura%E7%E3o_gesta%E7%E3o=TODAS_AS_CATEGORIAS__&STipo_de_gravidez=TODAS_AS_CATEGORIAS__&STipo_de_parto=TODAS_AS_CATEGORIAS__&SConsult_pr%E9-natal=TODAS_AS_CATEGORIAS__&SSexo=TODAS_AS_CATEGORIAS__&SCor%2Fra%E7a=TODAS_AS_CATEGORIAS__&SApgar_1%BA_minuto=TODAS_AS_CATEGORIAS__&SApgar_5%BA_minuto=TODAS_AS_CATEGORIAS__&SPeso_ao_nascer=TODAS_AS_CATEGORIAS__&SAnomalia_cong%EAnita=TODAS_AS_CATEGORIAS__&pesqmes17=Digite+o+texto+e+ache+f%E1cil&STipo_anomal_cong%EAn=TODAS_AS_CATEGORIAS__&formato=prn&mostre=Mostra";
		
		//se consecta na maquina
		curl_setopt($this->ch, CURLOPT_URL, $url);
	
		// Define o tipo de transferência (Padrão: 1)
		curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, 1);
		
		//curl_setopt($this->ch, CURLOPT_POST, 1);
		//curl_setopt($this->ch, CURLOPT_POSTFIELDS,$variaveis);
	
		// Executa a requisição
		$dados = curl_exec ($this->ch);
		
		return $dados;
		
	}
	
	/**
	 * Metodo para pegar somete as urls
	 * 
	 * $dados = parte do html com os dados que precisa ser recuperado
	 */
	public function separaValue($dados, $valExplode,$valFim) {

		//variaveis auxiliares
		$urlSecundaria = array();		
		$varArr = explode($valExplode, $dados);
		
		foreach($varArr as $key => $value) {
			
			if($key != 0) {
				
				$varUltima 	= strrpos($value, $valFim);
				$str = substr($value, 0, $varUltima);
				$strF = str_replace('"', '', $str);
				$urlSecundaria[] = $strF;
			}
		}
		return $urlSecundaria;
	}
	
	
	private function montaUrls_getUrls($urlPrimaria, $arrUrlsRadio, $urp_id) {

		$auxUrl = array();
		$auxUrlSel = array();
		$id_urlSecundariaSet = "";
		foreach($arrUrlsRadio as $val) {
			//url dos radios
			$urlSecundariaSet = $urlPrimaria."&VObj=".$val;
			
			echo "Montando da url: $urlSecundariaSet\n";
			
			#verifica se ja existe no banco de dados, se existir pegar o id
			#caso nao exista gravar e pegar o id
			$filtros["urs_urp_id"] = $urp_id;
			$filtros["urs_path"] = $urlSecundariaSet;
			$auxUrl = $this->dao->getUrlSecundaria($filtros);
			
			if(empty($auxUrl)) {
				$auxUrl = $this->dao->saveUrlSecundaria($filtros);
			}
			
			$id_urlSecundariaSet = key($auxUrl);
			
			//dados com o segundo parametro
			$dados = $this->getUrls($urlSecundariaSet);
			
			//pega as urls onde estão os dados com os filtros
			$str = $this->trabalhaString($dados,"name=\"selecao","</select");
			
			//metodo para pegar as urls secundarias
			$arrUrlsSelects = $this->separaValue($str, "value=", " >");
			
			foreach ($arrUrlsSelects as $key => $urlSel) {
				
				if($key != 0) {
					
					echo "Url: $urlSel\n";
					
					#verifica se ja existe no banco de dados, se existir pegar o id
					#caso nao exista gravar e pegar o id
					$f["urt_urs_id"] = $id_urlSecundariaSet;
					$f["urt_path"] = $urlSel;
					$auxUrlSel = $this->dao->getUrlTerciaria($f);
						
					if(empty($auxUrlSel)) {
						$auxUrl = $this->dao->saveUrlTerciaria($f);
					}
					
					#continue;
					
					//trabalha com os filtros
					//$this->buscaFiltros($urlSel);
					
				}
			}
			
		}		
		
	}
	
	
	public function startFiltros() {
		
		#$filtro["urt_id_in"] = "2";
		#$filtro["urt_id_in"] = "1,2,3,4";
		#$filtro["urt_urs_id"] = 8;
		$filtro["urt_urs_id_in"] = "21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46";
		$arrUrl = $this->dao->getUrlTerciaria($filtro);
		
		#$url = current($arrUrl);
		
		foreach($arrUrl as $key => $url){
		
			echo "URL: " .$key . " -- " . $url."\n";
			
			echo "Inicio: " . date('Y-m-d H:i:s')."\n";
			
			// Inicia o cURL
			$this->ch = curl_init();
			
			$this->buscaFiltros($key,$url);
			
			#file_put_contents("html.txt", "\n\n\n\n\n",FILE_APPEND);
			
			echo "Fim: " . date('Y-m-d H:i:s')."\n";
			//print "####################foi####################\n";
			
		}
		
		$this->closeConn();
		
	}
	
	
	/**
	 * Metodo para pegar os filtros de cada pagina
	 * @param string $url = url de onde ira pegar os filtros das paginas
	 */
	private function buscaFiltros($urt_id, $url) {
		
		echo "Buscando os filtros\n";
		#limpando as variaveis
		$dados = "";
		$strHtml = "";
		$arrDivs = array();
		$strLabel = "";
		$strSELECT = "";
		$strSELECTNAME = "";
		$strOption = "";
		$arrSeparado = "";
		$value = "";
		$valor = "";
		
		//dados com o segundo parametro
		$dados = $this->getUrls($url);
		
		echo "Tratando o formulario\n";
		//pega as urls onde estão os dados com os filtros
		$strHtml = $this->trabalhaString($dados,"<FORM","FORM>");
		
		echo "Separando as divs\n";
		$arrDivs = explode("<div",$strHtml);

		foreach($arrDivs as $divs) {
			//verifica para pegar as divs certas
			if(substr($divs, 1,5) == "class") {
								
				//pega o label
				$strLabel = $this->trabalhaString($divs,"<LABEL","</LABEL>",0);
				$strLabel = $this->trataLabel(trim($strLabel));
				
				//pega os selects
				$strSELECT = $this->trabalhaString($divs,"<SELECT","</SELECT>",0);
				$strSELECTNAME = trim(substr($this->trabalhaString2($strSELECT, "NAME=", "ID",0),5));
				
				#verifica se esta vazio
				if(!empty($strLabel) || !empty($strSELECTNAME) ) {
					
					//pega os values
					$strOption = $this->separaValue($strSELECT, "VALUE=", "\n");
					
					if($strLabel == "Per&iacute;odos Dispon&iacute;veis") {
						continue;
					}
					
					if($strSELECTNAME == '"Arquivos"') {
						$strLabel = "Per&iacute;odos Dispon&iacute;veis";
					}
					
					
					####grava na tabela o label e o name dos selects, pega o id de cada um
					$f["fit_urt_id"] = $urt_id;
					$f["fit_label"] = $strLabel;
					$f["fit_form_name"] = $strSELECTNAME;					
					$auxFiltroTipo = $this->dao->getFiltroTipo($f);
					
					if(empty($auxFiltroTipo)) {
						$auxFiltroTipo = $this->dao->saveFiltroTipo($f);
					}
					
					//file_put_contents("html.txt", $strLabel." --- ".$strSELECTNAME."\n",FILE_APPEND);
					//contador
					$cont = 0;
					$mil = "";
					//varre os values para pegar os valores
					foreach($strOption as $val) {
						#separa os valores para dentro de um array
						$arrSeparado = explode(">",$val);
						#value
						$value = trim($arrSeparado[0]);
						#valor
						$valor = trim($arrSeparado[1]);
						
						
						####grava na tabela o name dos selects e o valor, pega o id de cada um
						#$v["fiv_fit_id"] = $auxFiltroTipo["fit_id"];
						#$v["fiv_value"] = $value;
						#$v["fiv_valor"] = $valor;
						#$auxFiltroValue = $this->dao->getFiltroValue($v);
						
						#if(empty($auxFiltroValue)) {							
							###$auxFiltroValue = $this->dao->saveFiltroValue($v);
							$mil .= "(".$auxFiltroTipo["fit_id"].",\"".$value."\",\"".$valor."\"),"; 
							
							if($cont == 1000) {
								echo "Inserindo 1000 registros\n";
								
								$this->dao->saveFiltroValueMassivo($mil);
								$mil = "";
								$cont=0;
							} 
							
							$cont++;
							
						#}
						
						#file_put_contents("html.txt", "	".$value." --- ".$valor,FILE_APPEND);
						
					}// fim foreach dos options
					
					if($mil != "") {
						//echo "Inserindo o resto dos registros\n";
						
						$this->dao->saveFiltroValueMassivo($mil);
					}
					
				} #fim vazio
				
			}// fim if class
		}//fim foreach das divs
		
		#echo "Aguardando 2 sec\n";
		//segura por 20 segundos
		#sleep(2);
		
		//exit;
	}
	
	/**
	 * Metodo para trabalhar o label dos filtros
	 */
	private function trataLabel($label) {
		//verifica se não está vazio
		if(!empty($label)) {
			
			#primeiro tratamento
			$incio = strpos($label,">");
			$label = trim(substr($label, $incio+1));
			#segundo tratamento
			$inicio2 = strpos($label,"</");
			if($inicio2 > 0 ) {
				$label = trim(substr($label, 0, $inicio2));
			}
			#terceiro tratamento
			$inicio3 = strpos($label,">");
			if($inicio3 > 0 ) {
				$label = trim(substr($label, $inicio3+1));
			}
			
			//print $label . "|||\n\n";;
		}
				
		return $label;
		
	}
	
	/**
	 * Fecha a conexao e mata todos os atributos
	 */
	private function closeConn() {
		// Encerra o cURL
		curl_close ($this->ch);
	}
	
	
	/**
	 * Metodo para iniciar a coletagem dos resultados
	 * 
	 */
	public function startResultados() {
		
		// Inicia o cURL
		$this->ch = curl_init();
		
		//url terciaria
		$filtroT["urt_urs_id"] = 1;
		$filtroT["urt_id"] = 1;
		$arrUrlTer = $this->dao->getUrlTerciaria($filtroT);
		
		//varre as urls
		foreach($arrUrlTer as $ut_id => $ut_path) {
			
			$arrUrl = explode("?", $ut_path);
			$url = "http://tabnet.datasus.gov.br/cgi/tabcgi.exe?".$arrUrl[1];
					
			//pega as variaveis de cada url terciaria
			$filtroFT["fit_urt_id"] = $ut_id;
			$arrFiltrosValue = $this->dao->getFiltros($filtroFT);
			
			#monta o array com os filtros posts para procurar no link
			$arrFiltros = array();
			foreach ($arrFiltrosValue as $filtros) { 				
				//$arrFiltros[$filtros["fit_form_name"]][$filtros["fiv_id"]] = $filtros["fiv_value"];
				
				if($filtros["fit_form_name"] == "Arquivos") {
					$arrArquivos[$filtros["fiv_id"]] = trim($filtros["fiv_value"]);
				}
				
				if($filtros["fit_form_name"] == "Linha") {
					$arrLinha[$filtros["fiv_id"]] = trim($filtros["fiv_value"]);
				}
				
				#if($filtros["fit_form_name"] == "Coluna") {
				#	$arrColuna[$filtros["fiv_id"]] = trim($filtros["fiv_value"]);
				#}
				
				if($filtros["fit_form_name"] == "Incremento") {
					$arrConteudo[$filtros["fiv_id"]] = trim($filtros["fiv_value"]);
				}
				
			}
			
			$count = 1;
			$countLinha = 1;
			#monta os posts
			foreach ($arrArquivos as $arqId => $arqValor) {
								
				if($count == 3)
					exit;
				
				#pega a base que vai pesquisar
				$arquivo = "&Arquivos=".$arqValor;
				
				foreach ($arrLinha as $linhaId => $linhaValor) {
					
					
					$linha = "&Linha=".$linhaValor;
					
					#foreach($arrColuna as $colunaId => $colunaValor) {
						#$coluna = "&Coluna=" . $colunaValor;
						
					$coluna = "&Coluna=--Não-Ativa--";
					
					foreach ($arrConteudo as $conteudoId => $conteudoValor) {
						
						$incremento = "&Incremento=".$conteudoValor;
						
						
						##monta o post
						$variaveis = $variaveis = "formato=prn&mostre=Mostra".$arquivo.$linha.$coluna.$incremento;
						
						#busca os dados na pagina, com o post
						$dados = $this->getResultado($url, $variaveis);
						
						#trabalha os dados para inserir no banco
						$this->trataDados($dados);
						
						#exit;
					}
					
					#}
					print "LINHA: " . $countLinha . " INSERIDA COM SUCESSO!";
					print "<br>";
					$countLinha++;
				}
				
				$count++;
				$countLinha = 1;
					
			}
			##print_r($arrFiltros);
			#exit;
			#$variaveis = "formato=prn&mostre=Mostra&Arquivos=nvuf12.dbf&Linha=Região&Coluna=--Não-Ativa--&Incremento=Nascim_p/resid.mãe&SRegião=1&SLocal_ocorrência=3";

			#print $url . "-" . $variaveis . "<br>";
			
			//$dados = $this->getResultado($url, $variaveis);
			
			//print_r($dados);
			
		}
		
		exit;
		
		$this->closeConn();
		
	}
	
	/**
	 * Busca o valor da variavel nas maquinas
	 */
	private function getResultado($url, $variaveis) {
	
		//$variaveis = "Linha=Regi%E3o&Coluna=--N%E3o-Ativa--&Incremento=Nascim_p%2Fresid.m%E3e&Arquivos=nvuf12.dbf&SRegi%E3o=TODAS_AS_CATEGORIAS__&pesqmes2=Digite+o+texto+e+ache+f%E1cil&SUnidade_da_Federa%E7%E3o=TODAS_AS_CATEGORIAS__&SLocal_ocorr%EAncia=TODAS_AS_CATEGORIAS__&pesqmes4=Digite+o+texto+e+ache+f%E1cil&SIdade_da_m%E3e=TODAS_AS_CATEGORIAS__&pesqmes5=Digite+o+texto+e+ache+f%E1cil&SInstru%E7%E3o_da_m%E3e=TODAS_AS_CATEGORIAS__&SEstado_civil_m%E3e=TODAS_AS_CATEGORIAS__&SDura%E7%E3o_gesta%E7%E3o=TODAS_AS_CATEGORIAS__&STipo_de_gravidez=TODAS_AS_CATEGORIAS__&STipo_de_parto=TODAS_AS_CATEGORIAS__&SConsult_pr%E9-natal=TODAS_AS_CATEGORIAS__&SSexo=TODAS_AS_CATEGORIAS__&SCor%2Fra%E7a=TODAS_AS_CATEGORIAS__&SApgar_1%BA_minuto=TODAS_AS_CATEGORIAS__&SApgar_5%BA_minuto=TODAS_AS_CATEGORIAS__&SPeso_ao_nascer=TODAS_AS_CATEGORIAS__&SAnomalia_cong%EAnita=TODAS_AS_CATEGORIAS__&pesqmes17=Digite+o+texto+e+ache+f%E1cil&STipo_anomal_cong%EAn=TODAS_AS_CATEGORIAS__&formato=prn&mostre=Mostra";
		#print $url."+++++++++++++".$variaveis;
		
		//se consecta na maquina
		curl_setopt($this->ch, CURLOPT_URL, $url);
	
		// Define o tipo de transferência (Padrão: 1)
		curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$variaveis);
	
		// Executa a requisição
		$html = curl_exec ($this->ch);
				
		##pega a posicao inicia e final da parte da string
		$posInicial = strpos($html, "<H1 ");
		$posFinal 	= strpos($html, "</PRE");
		$qtdChar = $posFinal - $posInicial;
		
		#recorta o pedaco que vai querer
		$htmlRecortado = substr($html, $posInicial, $qtdChar);
		#limpa os htmls restantes
		$limpeza = str_replace('<H1 CLASS="Nivel0">', '', $htmlRecortado);
		$limpeza = str_replace('</H1>', '', $limpeza);
		$limpeza = str_replace('<b>', '', $limpeza);
		$limpeza = str_replace('</b>', '', $limpeza);
		$limpeza = str_replace('<B>', '', $limpeza);
		$limpeza = str_replace('</B>', '', $limpeza);
		$limpeza = str_replace('<br>', '', $limpeza);
		$limpeza = str_replace('<BR>', '', $limpeza);
		$dados = str_replace('<PRE>', '', $limpeza);
		#separa em array os dados
		$dados = explode("\r\n",$dados);
						
		#print_r($dados);
		#exit;
		
		return $dados;
	
	}
	
	
	/**
	 * Classe para trabalhar os dados e armazer os resultados 
	 * 
	 */
	private function trataDados($dados) {
		
		#verifica a quantidade de linhas
		$qtdLinhas = (count($dados) - 4);
		
		#header
		$titulo = trim($dados[0]);
		$subtitulo = trim($dados[1]);
		$periodo = explode(":", $dados[2]);
		$periodo = trim($periodo[1]); 
		
		#coluna, onde o indice 0 vai para a tabela resultado_header
		$arrColunas = explode('";"',$dados[3]);
		$tipo = str_replace('"', '', $arrColunas[0]); 
		
		##verifica se ja existe
		$fHeader["reh_titulo"] = $titulo;
		$fHeader["reh_sub_titulo"] = $subtitulo;
		$fHeader["reh_periodo"] = $periodo;
		$fHeader["reh_tipo"] = $tipo;
		$idResultadoHeader = $this->dao->getResultadoHeader($fHeader); 
		
		#caso nao exista insere na tabela
		if (empty($idResultadoHeader)) {
			
			##insere na tabela resultado_header
			##recupera o id para inserir nas colunas
			$idResultadoHeader = $this->dao->saveResultadoHeader($fHeader);
			
		}
		
		##varre as colunas a partir do indice 1
		foreach($arrColunas as $keyColuna => $valueColuna) {
			
			if($keyColuna != 0) {
				##trata o valor
				$descricao = str_replace('"', '', $valueColuna); 
				
				##busca na tabela para ver se já não existe uma coluna com a descricao
				$fColuna["rec_descricao"] = $descricao;
				$fColuna["rec_reh_id"] = $idResultadoHeader;
				$idResultadoColuna = $this->dao->getResultadoColuna($fColuna);
				
				if(empty($idResultadoColuna)) {
					#insere na tabela resultado_coluna
					$idResultadoColuna = $this->dao->saveResultadoColuna($fColuna);
				}
				
				#armazena os ids por indices
				$arrColunasNova[$keyColuna] = $idResultadoColuna;
			}
			
		}
		
		#pega as linhas a partir do 4 indice
		for($i=4;$i<=$qtdLinhas;$i++) {
			#monta o array de linhas e separa para insercao dos dados e linha
			# o indice 0 representa a variavel, e o indice 1 para frenterepresenta o valor
			$arrLinhas = explode('";',$dados[$i]);
			
			##varres os valores
			foreach($arrLinhas as $keyLinha => $valueLinha) {
				
				if($keyLinha == 0) {
					$descricao = str_replace('"', '', $valueLinha);
				} else {
					##explode quando for array o resultado
					$valoresLinha = explode(";",$valueLinha);
					
					#varre os valores finais
					foreach($valoresLinha as $keyResultado => $valueResultado) {
						
						##pega o valor do resultado da página, e verifica se esta com "-"
						$valor = ($valueResultado == "-") ? "0" : $valueResultado;
						
						##soma 1 para poder acompanhar os indices das colunas que utiliza o 0,
						##pois quando utilizamos o explode por padrão começa com 0 os indices do array
						$idColuna = $arrColunasNova[$keyResultado+1];
						
						##verifica se ja existe na tabela
						$fRes["res_rec_id"] = $idColuna;
						$fRes["res_descricao"] = $descricao;
						$fRes["res_valor"] = $valor;
						$idResultado = $this->dao->getResultado($fRes);
						
						if(empty($idResultado)) {
							##monta a inserção e insere na tabela resultado
							$idResultado = $this->dao->saveResultado($fRes);
						}
					} ##fim foreach valoresLinha
				} ## fim if / else
				
			} ##fim foreach
			
		} ## fim for
		
	} ## fim metodo trataDados
	
}// fim class
