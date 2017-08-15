<?php
/**
 * Modelo para a tabela "Campaign".
 * MagnusSolution.com <info@magnussolution.com>
 * 28/10/2012
 */

class Campaign extends Model
{
    protected $_module = 'campaign';

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
        return 'pkg_campaign';
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
            array('name', 'required'),
            array('monday, tuesday, wednesday, thursday, friday, saturday, sunday, allow_email,
                    allow_email2,allow_email3,allow_city,allow_address,allow_state,allow_country,
                    allow_dni,allow_mobile, allow_number_home,allow_number_office,allow_zip_code,
                    allow_company,allow_birth_date,allow_type_user,allow_sexo,allow_edad,allow_profesion,
                    allow_id_phonebook, allow_name, allow_sessiontime, allow_beneficio_number,
                    allow_quantidade_transacoes,allow_inicio_beneficio,allow_beneficio_valor,
                    allow_banco,allow_agencia,allow_conta,allow_endereco_complementar,allow_telefone_fixo1,
                    allow_telefone_fixo2,allow_telefone_fixo3,allow_telefone_celular1,
                    allow_telefone_celular2,allow_telefone_celular3, allow_telefone_fixo_comercial1,
                    allow_telefone_fixo_comercial2,allow_telefone_fixo_comercial3, allow_parente1,
                    allow_fone_parente1,allow_parente2,allow_fone_parente2,allow_parente3,allow_fone_parente3,
                    allow_vizinho1,allow_telefone_vizinho1,allow_vizinho2,allow_telefone_vizinho2,
                    allow_vizinho3,allow_telefone_vizinho3, timeout, retry, wrapuptime, weight,
                    periodic-announce-frequency, announce-frequency, call_limit, call_next_try, predictive', 'numerical', 'integerOnly' => true),
            array('name, description', 'length', 'max' => 100),
            array('status', 'length', 'max' => 1),
            array('startingdate, expirationdate, allow_option_1_type, allow_option_2_type, allow_option_3_type,
                    allow_option_4_type, allow_option_5_type', 'length', 'max' => 50),
            array('daily_start_time, daily_stop_time, daily_morning_start_time, daily_morning_stop_time,
                    daily_afternoon_start_time, daily_afternoon_stop_time, announce-position', 'length', 'max' => 8),
            array('allow_option_1, allow_option_2, allow_option_3, allow_option_4,
                    allow_option_5', 'length', 'max' => 100),
            array('name', 'unique', 'caseSensitive' => 'false'),
            array('musiconhold, strategy', 'length', 'max' => 128),
            array('ringinuse, eventmemberstatus, autopause, setqueuevar, setqueueentryvar,
                    setinterfacevar', 'length', 'max' => 3),
            array('periodic-announce, leavewhenempty, joinempty, announce-holdtime', 'length', 'max' => 128),

        );
    }

    public function beforeSave()
    {
        $this->name        = preg_replace("/ /", "-", $this->name);
        $this->setqueuevar = $this->setqueueentryvar = $this->setinterfacevar = 'yes';
        for ($i = 1; $i <= 5; $i++) {

            if (strlen($this->{"allow_option_" . $i}) == 0) {
                $this->{"allow_option_" . $i . "_type"} = '';
            } elseif (strlen($this->{"allow_option_" . $i}) > 0 && strlen($this->{"allow_option_" . $i . "_type"}) == 0) {
                $this->{"allow_option_" . $i . "_type"} = 'textfield';
            } else {
                $this->{"allow_option_" . $i . "_type"} = $this->{"allow_option_" . $i . "_type"};
            }

        }

        return parent::beforeSave();
    }

    public function campaignPredictive($id_campaign)
    {
        $id_campaign = "SELECT id_phonebook FROM pkg_campaign_phonebook WHERE id_campaign = " . $id_campaign;

        $sql = "SELECT id,
                '$campaign->name' name,
                (SELECT avg(sessiontime)  FROM `pkg_cdr` WHERE `id_campaign` = $campaign->id) answered_call_ratio,
                (SELECT count(*)  FROM `pkg_preditive_gen` WHERE `id_phonebook` IN ($id_campaign)) total_calls,
                (SELECT count(*)  FROM `pkg_preditive_gen` WHERE `id_phonebook` IN ($id_campaign) AND ringing_time > 1) answered,
                count(*) error,
                (SELECT AVG( ringing_time ) FROM  `pkg_preditive_gen` WHERE `id_phonebook` IN ($id_campaign) AND ringing_time >0) ring_delay  ,
                ((SELECT count(*)  FROM `pkg_preditive_gen` WHERE `id_phonebook` IN ($id_campaign) AND ringing_time > 1) * 100) / (SELECT count(*)  FROM `pkg_preditive_gen` WHERE `id_phonebook` IN ($id_campaign)) AS asr

                FROM `pkg_preditive_gen` WHERE `id_phonebook` IN ($id_campaign) AND ringing_time  = 0";
        $result = Yii::app()->db->createCommand($sql)->queryAll();
    }
}
