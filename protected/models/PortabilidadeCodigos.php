<?php
/**
 * Modelo para a tabela "Campaign".
 * MagnusSolution.com <info@magnussolution.com> 
 * 28/10/2012
 */

class PortabilidadeCodigos extends Model
{
	protected $_module = 'portabilidadecodigos';
	/**
	 * Retorna a classe estatica da model.
	 * @return Campaign classe estatica da model.
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return nome da tabela.
	 */
	public function tableName()
	{
		return 'pkg_codigos';
	}

	/**
	 * @return nome da(s) chave(s) primaria(s).
	 */
	public function primaryKey()
	{
		return 'id';
	}
	/**
	 * @return array validacao dos campos da model.
	 */
	public function rules()
	{
		return array(
			array('prefix,favorito', 'numerical', 'integerOnly'=>true),
			array('company', 'length', 'max'=>100),
			array('id', 'safe')
		);
	}

	public function importCSV($data)
	{
		chmod($data['filename'] , 0777);
		$sql ="LOAD DATA INFILE '".$data['filename']."'".
			  " INTO TABLE ".$this->tableName().
			  " FIELDS TERMINATED BY '". $data['boundaries']['delimiter']."'".
			  " LINES TERMINATED BY '\n'".
			  " IGNORE 1 LINES  (".implode(",",array_map(
				  									function($v){
				  										return '@'.$v;
				  									},
				  									array_keys($data['columns'])
			  									)
			  								).") SET ".
			 						 implode(" ", array_map(
			 						 				function($v) use($data){
												  		return $data['columns'][$v]." = @".$v.", ";
												  	}, 
												  	array_keys($data["columns"])
												  )
			 						 )." ".
			 						 implode(" ", array_map(
			 						 				function($v){
												  		return $v['key']." = ".$v['value'];
												  	}, 
												  	$data["additionalParams"]
												  )
			 						 );

		//echo substr(trim($sql),0,-1);
		//exit();
		Yii::app()->db->createCommand(substr(trim($sql),0,-1))->execute();
	}

	public function beforeSave()
	{		
		return parent::beforeSave();		
		
	}

	public function afterSave()
	{
		return parent::beforeSave();
	}
	
	
}