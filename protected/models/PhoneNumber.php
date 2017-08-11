<?php
/**
 * Modelo para a tabela "PhoneNumber".
 * MagnusSolution.com <info@magnussolution.com> 
 * 28/10/2012
 */

class PhoneNumber extends Model
{
	protected $_module = 'phonenumber';
	var $hours;
	/**
	 * Retorna a classe estatica da model.
	 * @return CcPrefix classe estatica da model.
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
		return 'pkg_phonenumber';
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
			array('number', 'required'),
			array('id_phonebook, id_category, edad, id_user,  status, cita_concreta', 'numerical', 'integerOnly'=>true),
			array('city, dni', 'length', 'max'=>30),
			array('name,beneficio_number,quantidade_transacoes,inicio_beneficio,beneficio_valor,
					banco,agencia,conta,endereco_complementar,telefone_fixo1,
					telefone_fixo2,telefone_fixo3,telefone_celular1,telefone_celular2,telefone_celular3,
					telefone_fixo_comercial1,telefone_fixo_comercial2,telefone_fixo_comercial3,
					parente1,fone_parente1,parente2,fone_parente2,parente3,fone_parente3,
					vizinho1,telefone_vizinho1,vizinho2,telefone_vizinho2,
					vizinho3,telefone_vizinho3', 'length', 'max'=>60),
			array('number, state, country, mobile, number_home, number_office, zip_code', 'length', 'max'=>30),
			array('profesion, datebackcall, email,email2,email3,address, creationdate', 'length', 'max'=>50),
			array('sexo, sessiontime', 'length', 'max'=>10),
			array('info, company, birth_date, type_user, mobile_2, option_1, option_2, option_3, option_4, option_5', 'length', 'max'=>100)
		);
	} 
	  
	/**
	 * @return array regras de relacionamento.
	 */
	public function relations()
	{
		return array(
			'idPhonebook' => array(self::BELONGS_TO, 'PhoneBook', 'id_phonebook'),
			'idCategory' => array(self::BELONGS_TO, 'Category', 'id_category'),
			'idUser' => array(self::BELONGS_TO, 'User', 'id_user')
		);
	}
}