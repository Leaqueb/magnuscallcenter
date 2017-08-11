<?php 
/**
 * 
 */
 class ScheduledNumberDispatcher implements NumberDispatcherInterface
 {

 	public function getPhonenumber($id_user)
	{
		$modelPhoneNumber = NULL;

		if (count(($modelPhoneNumber = $this->getModelPhoneNumber($id_user))) == 0) {
			//verifica se tem numero agendado para qualquer operador			
			$modelPhoneNumber = $this->getModelPhoneNumber('NULL');	
		}

		return $modelPhoneNumber;
	}

	private function getModelPhoneNumber($id_user)
	{
		return PhoneNumber::model()->findAll(array(
				'condition' => $this->phoneNumberCondition($id_user),
				'order' => 'id DESC , RAND( )',
				'limit' => 1
			));
	}

	private function phoneNumberCondition($id_user)
	{
		$timeToCall = date('Y-m-d H:i', mktime(date('H'), date('i') - 10 , date('s'), date('m'), date('d'), date('Y')));

		return "id_category = 2 AND datebackcall BETWEEN '$timeToCall' AND  NOW()
					AND id_user = $id_user";
	}

 } ?>