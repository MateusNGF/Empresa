<?php 
/**
* 
* @author Mateus Nicolau
*
**/
class Client {

	private $Nome;
	private $Idade;
	private $CPF;

/*	
	public function __Construct(){
		Client::ERR_EXCEPTION('Nenhum erro encontrado');
	}
*/
	public function ClientNow(){
		try {
			if (empty($this->Nome)){
				throw new Exception("Nenhum cliente estanciado");
			}else{
				return nl2br(" CLIENTE : {$this->Nome}\n IDADE : {$this->Idade}\n CPF : {$this->CPF}");
			}
		} catch (Exception $ERR_CLIENT_EMPTY) {
			Client::ERR_EXCEPTION($ERR_CLIENT_EMPTY->getMessage());
			return false;
		}
	}
	public function setClient($Nome = '', $Idade = '', $CPF = ''){
		try {
			if (empty($Nome)){
				throw new Exception("Nome não passado");
			}elseif(empty($Idade)){
				throw new Exception("Idade não passado");
			}elseif(empty($CPF)){
				throw new Exception("CPF não passado");
			}else{
				if (!preg_match('/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}$/', $CPF)){
					throw new Exception("Formato de cpf invalido");
				}elseif(!Client::ValidaCPF($CPF)){
					throw new Exception("CPF Invalido");
				}else{
					if (!intval($Idade)){
						throw new Exception("Idade precisa ser numerico");
					}elseif(!is_string($Nome)){
						throw new Exception("Nome precisa escrita");
					}else{
						$this->Nome  = $Nome;
						$this->Idade = $Idade;
						$this->CPF   = $CPF;
						return true;
                    }
				}
			}
		} catch (Exception $ERR_SET_CLIENT) {
			Client::ERR_EXCEPTION($ERR_SET_CLIENT->getMessage());
			return false;
		}
	}
	public function ValidaCPF($CPF = ''){
	// 000.000.00-XX
		try {
			if (empty($CPF)){
				throw new Exception("PRECISA INFORMA O CPF");
			}else{
				$CPF = preg_replace('/[^0-9]/', '', $CPF);
				$DIGITO_A = 0; $DIGITO_B = 0;
				for ($i=0, $x=10; $i <= 8 ; $i++, $x--) {
					$DIGITO_A += $CPF[$i] * $x;
				}
				for ($i=0, $x=11; $i <= 9 ; $i++, $x--) {
					if (str_repeat($i, 11) == $CPF){
						throw new Exception("CPF INVALIDO");
					}
					$DIGITO_B += $CPF[$i] * $x;
				}
				$SOMA_A = (($DIGITO_A%11) < 2) ? 0 : 11-($DIGITO_A%11);
				$SOMA_B = (($DIGITO_B%11) < 2) ? 0 : 11-($DIGITO_B%11);
				if ($SOMA_A != $CPF[9]){
					throw new Exception("CDP INVALIDO");
				}else{
					return true;
				}
			}
		} catch (Exception $ERR_CPF) {
			Client::ERR_EXCEPTION($ERR_CPF->getMessage());
			return false;
		}
	}

	// MOSTRAR O ULTIMO ERRO EXECUTADO
	public function ERR_PRINT(){
		try {
			if (empty(EX_ERR)){
				throw new Exception("Nenhum erro encontrado");
			}else{
				return EX_ERR;
			}
		} catch (Exception $ERR_PRINT_EXEC) {
			Client::ERR_EXCEPTION($ERR_PRINT_EXEC->getMessage());
			return false;
		}
	}

	// FUNÇÃO PARA MOSTRAR O STATUS DO ERRO
	private function ERR_EXCEPTION($ERR = ''){
		try {
			if (empty($ERR)){
				throw new Exception("EXEÇÃO DE ERRO NÃO PASSADA");
			}else{
				define("EX_ERR", $ERR);
			}
		} catch (Exception $ERR_EXCEPTION_EXEC) {
			Client::ERR_EXCEPTION($ERR_EXCEPTION_EXEC->getMessage());
			return false;
		}
	}
}

?>