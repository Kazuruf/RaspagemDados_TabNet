<?php
/**
 * 
 * Class para recuperar os dados da tabela e gravar
 */

class DAO {
	
	public function __construct() {
		
	}
	
	/**
	 * Metodo para pegar os dados do banco da primeira url
	 * @param string $filtro
	 * @return multitype:Ambigous <>
	 */
	public function getUrlPrimaria($filtro = null) {
		
		$sql = "SELECT * FROM url_primaria urp where 1=1";
		
		if(!empty($filtro["urp_id"])) {
			$sql .= " AND urp.urp_id = '" . $filtro["urp_id"] ."'"; 
		}
		
		if(!empty($filtro["urp_path"])) {
			$sql .= " AND urp.urp_path = '" . $filtro["urp_path"] ."'";
		}
		
		if(!empty($filtro["urp_nome"])) {
			$sql .= " AND urp.urp_nome = '" . $filtro["urp_nome"] ."'";
		}
		
		$result = mysql_query($sql);
		$arrVal = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {			
			$arrVal[ $row["urp_id"]] = $row["urp_path"];
		}
		
		return $arrVal;
	}
	
	/**
	 * Metodo para pegar as urls radios
	 * @param string $filtros
	 */
	public function getUrlSecundaria($filtro=null) {
		$sql = "SELECT * FROM url_secundaria urs where 1=1";
		
		if(!empty($filtro["urs_id"])) {
			$sql .= " AND urs.urs_id = '" . $filtro["urs_id"] ."' ";
		}
		
		if(!empty($filtro["urs_urp_id"])) {
			$sql .= " AND urs.urs_urp_id = '" . $filtro["urs_urp_id"] ."' ";
		}
		
		if(!empty($filtro["urs_path"])) {
			$sql .= " AND urs.urs_path = '" . $filtro["urs_path"] ."' ";
		}
		
		if(!empty($filtro["urs_nome"])) {
			$sql .= " AND urs.urs_nome = '" . $filtro["urs_nome"] ."' ";
		}
		
		$result = mysql_query($sql);
		$arrVal = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			$arrVal[$row["urs_id"]] = $row["urs_path"];
		
		return $arrVal;
	}
	
	/**
	 * Metodo para gravar no banco de dados
	 * @param unknown $dados
	 */
	public function saveUrlSecundaria($dados) {
		
		$sql = "insert into url_secundaria (urs_urp_id, 
											urs_path) 
									values ('".$dados["urs_urp_id"]."',
											'".$dados["urs_path"]."');";
		
		$result = mysql_query($sql);
		
		return self::getUrlSecundaria($dados);
		
	}
	
	/**
	 * Metodo para pegar as urls do select onde está os estados com os filtros
	 * @param string $filtros
	 */
	public function getUrlTerciaria($filtro=null) {
		
		$sql = "SELECT * FROM url_terciaria urt where 1=1 ";
	
		if(!empty($filtro["urt_id"])) {
			$sql .= " AND urt.urt_id = '" . $filtro["urt_id"] ."' ";
		}
	
		if(!empty($filtro["urt_urs_id"])) {
			$sql .= " AND urt.urt_urs_id = '" . $filtro["urt_urs_id"] ."' ";
		}
	
		if(!empty($filtro["urt_path"])) {
			$sql .= " AND urt.urt_path = '" . $filtro["urt_path"] ."' ";
		}
	
		if(!empty($filtro["urt_nome"])) {
			$sql .= " AND urt.urt_nome = '" . $filtro["urt_nome"] ."' ";
		}
		
		if(!empty($filtro["urt_id_in"])) {
			$sql .= " AND urt.urt_id IN (" . $filtro["urt_id_in"] .") ";
		}
		
		if(!empty($filtro["urt_urs_id_in"])) {
			$sql .= " AND urt.urt_urs_id IN (" . $filtro["urt_urs_id_in"] .") ";
		}
		
		#print $sql."<br>";
	
		$result = mysql_query($sql);
		$arrVal = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			$arrVal[$row["urt_id"]] = $row["urt_path"];
	
		return $arrVal;
	}
	
	/**
	 * Metodo para gravar no banco de dados
	 * @param unknown $dados
	 */
	public function saveUrlTerciaria($dados) {
	
		$sql = "insert into url_terciaria (urt_urs_id,
											urt_path)
									values ('".$dados["urt_urs_id"]."',
											'".$dados["urt_path"]."');";
	
		$result = mysql_query($sql);
	
		return self::getUrlTerciaria($dados);
	
	}
	
	
	/**
	 * Metodo para pegar os tipos de filtros
	 * @param string $filtros
	 */
	public function getFiltroTipo($filtro=null) {
		
		$sql = "SELECT * FROM filtro_tipo fit where 1=1 ";
	
		if(!empty($filtro["fit_id"])) {
			$sql .= " AND fit.fit_id = '" . $filtro["fit_id"] ."' ";
		}
	
		if(!empty($filtro["fit_urt_id"])) {
			$sql .= " AND fit.fit_urt_id = '" . $filtro["fit_urt_id"] ."' ";
		}
	
		if(!empty($filtro["fit_label"])) {
			$sql .= " AND fit.fit_label = '" . $filtro["fit_label"] ."' ";
		}
	
		if(!empty($filtro["fit_form_name"])) {
			$sql .= " AND fit.fit_form_name = '" . $filtro["fit_form_name"] ."' ";
		}
		
	
		$result = mysql_query($sql);
		$arrVal = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$arrVal = $row;
		}
		
		return $arrVal;
	}
	
	/**
	 * Metodo para gravar no banco de dados
	 * @param unknown $dados
	 */
	public function saveFiltroTipo($dados) {
	
		$sql = "insert into filtro_tipo (fit_urt_id,
										fit_label,
										fit_form_name)
									values ('".$dados["fit_urt_id"]."',
											'".$dados["fit_label"]."',
											'".$dados["fit_form_name"]."');";
		#print $sql."\n\n";
		$result = mysql_query($sql);
	
		return self::getFiltroTipo($dados);
	
	}
	
	
	/**
	 * Metodo para pegar os valores dos forms
	 * @param string $filtros
	 */
	public function getFiltroValue($filtro=null) {
	
		$sql = "SELECT fiv_id FROM filtro_value fiv where 1=1 ";
	
		if(!empty($filtro["fiv_id"])) {
			$sql .= " AND fiv.fiv_id = '" . $filtro["fiv_id"] ."' ";
		}
	
		if(!empty($filtro["fiv_fit_id"])) {
			$sql .= " AND fiv.fiv_fit_id = '" . $filtro["fiv_fit_id"] ."' ";
		}
	
		if(!empty($filtro["fiv_value"])) {
			$sql .= " AND fiv.fiv_value = \"" . $filtro["fiv_value"] ."\" ";
		}
	
		if(!empty($filtro["fiv_valor"])) {
			$sql .= " AND fiv.fiv_valor = \"" . $filtro["fiv_valor"] ."\" ";
		}
		$sql .= ";";
		
		#print $sql."\n";
		#file_put_contents("html.txt", $sql."\n",FILE_APPEND);
		
		$result = mysql_query($sql);
		$arrVal = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$arrVal = $row;
		}
	
		return $arrVal;
	}
	
	/**
	 * Metodo para gravar no banco de dados
	 * @param unknown $dados
	 */
	public function saveFiltroValue($dados) {
	
		$sql = "insert into filtro_value (fiv_fit_id,
										fiv_value,
										fiv_valor)
									values ('".$dados["fiv_fit_id"]."',
											\"".$dados["fiv_value"]."\",
											\"".$dados["fiv_valor"]."\");";
		#print $sql."\n\n";
		$result = mysql_query($sql);
	
		return self::getFiltroValue($dados);
	
	}
	
	/**
	 * Metodo para gravar no banco de dados
	 * @param unknown $dados
	 */
	public function saveFiltroValueMassivo($dados) {
		$dados = substr($dados, 0,-1);
		
		$sql = "insert into filtro_value (fiv_fit_id,
										fiv_value,
										fiv_valor)";
		
		
		$sql .=" values " . $dados;
		#print $sql."\n\n";
		$result = mysql_query($sql);
	
		//return self::getFiltroValue($dados);
	
	}
	
	/**
	 * Metodo para pegar os where dos filtros
	 * 
	 * @param string $filtros
	 */
	public function getFiltros($filtro = null) {
		
		$sql = "SELECT fv.fiv_id, 
						REPLACE(ft.fit_form_name, '\"', '') AS fit_form_name,  
						REPLACE(fv.fiv_value, ' SELECTED', '') AS fiv_value
				FROM filtro_tipo ft
					INNER JOIN filtro_value fv ON ft.fit_id = fv.fiv_fit_id 
				WHERE 1=1 ";
		
		//$sql .= " AND ft.fit_form_name <> '\"Arquivos\"' ";
		
		if(!empty($filtro["fit_urt_id"])) {
			$sql .= " AND ft.fit_urt_id = '" . $filtro["fit_urt_id"] ."' ";
		}
		
		$sql .= "GROUP BY fv.fiv_value ORDER BY fv.fiv_id";
		
		#print $sql."<br>";exit;
		
		$result = mysql_query($sql);
		$arrVal = array();
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$arrVal[] = $row;
		}
		
		return $arrVal;
		
	}
	
	
	/**
	 * Metodo para pegar os dados inseridos na resultado_header
	 * @param string $filtros
	 */
	public function getResultadoHeader($filtro=null) {
		
		$sql = "SELECT * FROM resultado_header reh where 1=1";
	
		if(!empty($filtro["reh_id"])) {
			$sql .= " AND reh.reh_id = '" . $filtro["reh_id"] ."' ";
		}
		
		if(!empty($filtro["reh_titulo"])) {
			$sql .= " AND reh.reh_titulo = '" . $filtro["reh_titulo"] ."' ";
		}
		
		if(!empty($filtro["reh_sub_titulo"])) {
			$sql .= " AND reh.reh_sub_titulo = '" . $filtro["reh_sub_titulo"] ."' ";
		}
	
		if(!empty($filtro["reh_periodo"])) {
			$sql .= " AND reh.reh_periodo = '" . $filtro["reh_periodo"] ."' ";
		}
		
		if(!empty($filtro["reh_tipo"])) {
			$sql .= " AND reh.reh_tipo = '" . $filtro["reh_tipo"] ."' ";
		}
		
		$result = mysql_query($sql);
		$row = mysql_fetch_row($result);
		
		return $row[0];
		
	}
	
	
	/**
	 * Metodo para gravar no banco de dados
	 * @param unknown $dados
	 */
	public function saveResultadoHeader($dados) {
	
		$sql = "insert into resultado_header (	reh_titulo,
												reh_sub_titulo,
												reh_periodo,
												reh_tipo)
									values ('".$dados["reh_titulo"]."',
											'".$dados["reh_sub_titulo"]."',
											'".$dados["reh_periodo"]."',
											'".$dados["reh_tipo"]."');";
				
		$result = mysql_query($sql);	
		return mysql_insert_id();
	
	}
	
	
	/**
	 * Metodo para pegar os dados inseridos na resultado_header
	 * @param string $filtros
	 */
	public function getResultadoColuna($filtro=null) {
	
		$sql = "SELECT * FROM resultado_coluna rec where 1=1";
	
		if(!empty($filtro["rec_id"])) {
			$sql .= " AND rec.rec_id = '" . $filtro["rec_id"] ."' ";
		}
		
		if(!empty($filtro["rec_reh_id"])) {
			$sql .= " AND rec.rec_reh_id = '" . $filtro["rec_reh_id"] ."' ";
		}
		
		if(!empty($filtro["rec_descricao"])) {
			$sql .= " AND rec.rec_descricao = '" . $filtro["rec_descricao"] ."' ";
		}
		#print $sql;
		$result = mysql_query($sql);
		$row = mysql_fetch_row($result);
	
		return $row[0];
	
	}
	
	
	/**
	 * Metodo para gravar no banco de dados
	 * @param unknown $dados
	 */
	public function saveResultadoColuna($dados) {
		
		$sql = "insert into resultado_coluna (	rec_reh_id,
												rec_descricao)
									values ('".$dados["rec_reh_id"]."',
											'".$dados["rec_descricao"]."');";
		#print $sql;
		$result = mysql_query($sql);
		return mysql_insert_id();
	
	}
	
	
	/**
	 * Metodo para pegar os dados inseridos na resultado
	 * @param string $filtros
	 */
	public function getResultado($filtro=null) {
	
		$sql = "SELECT * FROM resultado res where 1=1";
	
		if(!empty($filtro["res_id"])) {
			$sql .= " AND res.res_id = '" . $filtro["res_id"] ."' ";
		}
	
		if(!empty($filtro["res_rec_id"])) {
			$sql .= " AND res.res_rec_id = '" . $filtro["res_rec_id"] ."' ";
		}
	
		if(!empty($filtro["res_descricao"])) {
			$sql .= " AND res.res_descricao = '" . $filtro["res_descricao"] ."' ";
		}
		
		if(!empty($filtro["res_valor"])) {
			$sql .= " AND res.res_valor = '" . $filtro["res_valor"] ."' ";
		}
		
		#print $sql;
		$result = mysql_query($sql);
		$row = mysql_fetch_row($result);
	
		return $row[0];
	
	}


	/**
	* Metodo para gravar no banco de dados
	 * @param unknown $dados
	 */
	 public function saveResultado($dados) {

		$sql = "insert into resultado (	res_rec_id,
										res_descricao,
										res_valor)
								values ('".$dados["res_rec_id"]."',
										'".$dados["res_descricao"]."',
										'".$dados["res_valor"]."');";
		
		#print $sql;
		$result = mysql_query($sql);
		return mysql_insert_id();

	}
	
	
}
