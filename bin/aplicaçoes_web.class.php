<?php 
/**
* 
* @author Mateus Nicolau
* FUNCTIONS 
*
* getConexao() -
* registerClient() -
* ClientNow() -
* setClient() -
* ValidaCPF() -
* ValidarEmail() -
* ERR_PRINT() - 
* ERR_EXCEPTION() -
*
**/
class Client {

    private $Id;
    private $Nome;
    private $Email;
	private $Senha;
	private $CPF;

	private $EX_ERR = 'Nenhum erro encontrado';

     /** 
     *  ======================
     *   TRATAMENTO DE USUARIO 
     *  =======================
     */ 

    // CONEXÃO COM BANCO DE DADOS
    private function getConexao(){
        if (include("Conexao.class.php")){
            return true;
        }else{
            return false;
        }
    }

    // ATRIBUINDO OS DADOS DO NOVO CLIENTE
	public function setClient($Nome, $Email, $Senha, $CPF){
		try {
			if (empty($Nome)){
				throw new Exception("Nome não passado");
			}elseif(empty($Senha)){
				throw new Exception("Senha não passado");
			}elseif(empty($Email)){
                throw new Exception("Email não passado");
            }elseif(empty($CPF)){
				throw new Exception("CPF não passado");
			}else{
				if (!preg_match('/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}$/', $CPF)){
					throw new Exception("Formato de cpf invalido");
				}elseif(!Client::ValidaCPF($CPF)){
					throw new Exception("CPF Invalido");
				}else{
					if(!is_string($Nome)){
						throw new Exception("Nome precisa ser escrita");
					}elseif(strlen($Nome) >= 100){
                        throw new Exception("Nome atingiu tamanho maximo");
                    }else{
                        if (!Client::ValidarEmail($Email)){
                            throw new Exception("Formato de E-mail invalido");
                        }
						$this->Nome  = htmlspecialchars($Nome);
                        $this->Senha = htmlspecialchars($Senha);
						$this->CPF   = $CPF;
                        $this->Email = $Email;
                        return true;
					}
				}
			}
		}catch(Exception $ERR_SET_CLIENT) {
			Client::ERR_EXCEPTION($ERR_SET_CLIENT->getMessage());
			return false;
		}
	}

    // REGISTRANDO USUARIO EM BANCO DE DADOS
    public function registerClient(){
        try{
            if (!Client::getConexao()){
                throw new Exception("'Erro ao conectar a BD");
            }elseif(empty($this->Nome) || empty($this->Email) || empty($this->CPF) || empty($this->Senha) ){
                throw new Exception("Cliente não verificado ou não passado");
            }else{
                $PDO = new Conn();
                if (!Client::AnaliseClient($this->Email)){
                    throw new Exception("Email já cadastrado");
                }
                    $PREPARANDO_SQL = "INSERT INTO `clientes`(`Client_ID`, `Client_Name`, `Client_Email`, `Client_cpf`, `Client_Senha`) VALUES ('',?,?,?,?)";
                    $PREPARANDO_DADOS = $PDO->getConn()->prepare($PREPARANDO_SQL);
                    
                    // TRATANDO PARA ENVIAR PARA O BD
                    $NOVO_CPF = str_replace('.', '', $this->CPF);
                    $NOVO_CPF = str_replace('-', '', $NOVO_CPF);
                    $SENHACRIPTO =  md5($this->Senha);

                    // PASSANDO OS VALORES
                    $PREPARANDO_DADOS->bindParam(1  , $this->Nome        , PDO::PARAM_STR);
                    $PREPARANDO_DADOS->bindParam(2  , $this->Email       , PDO::PARAM_STR);
                    $PREPARANDO_DADOS->bindParam(3  , $NOVO_CPF          , PDO::PARAM_STR);
                    $PREPARANDO_DADOS->bindParam(4  , $SENHACRIPTO       , PDO::PARAM_STR);

                    if ($PREPARANDO_DADOS->execute()){
                        $this->Id = $PDO->getConn()->lastInsertId();
                        $PDO->Disconect();
                        return true;
                    }else{
                        throw new Exception("Erro ao registrar usuario");
                        return false;
                    }
            }
        }catch(Execption $ERR_REGISTER){
            Client::ERR_EXCEPTION($ERR_REGISTER->getMessage());
            $PDO->Disconect();
            return false;
        }
    }
    
    // EXIBE O CLIENT NA ESTANCIA CRIADA
    public function ClientNow(){
        try {
            if (empty($this->Nome)){
                throw new Exception("Nenhum cliente estanciado");
            }else{
                return nl2br(" CLIENTE : {$this->Nome} | {$this->Id} \n CPF : {$this->CPF}");
            }
        }catch(Exception $ERR_CLIENT_EMPTY) {
            Client::ERR_EXCEPTION($ERR_CLIENT_EMPTY->getMessage());
            return false;
        }
    }
    
    // ANALISAR EMAIL SE JÁ FOI CADASTRADO
    private function AnaliseClient($ANS_EMAIL){
        try{
            if (empty($ANS_EMAIL)){
                throw new Exception("Email para analise, não passado");
            }elseif(!Client::getConexao()){
                throw new Exception("Erro ao conectar a BD");
            }else{
                $PDO = new Conn;
                $PREPARANDO_SQL_ANS = "SELECT `Client_Email` FROM `clientes` WHERE `Client_Email` = ?";
                $PREPARANDO_DADOS_ANS = $PDO->getConn()->prepare($PREPARANDO_SQL_ANS);
                $PREPARANDO_DADOS_ANS->bindParam(1, $ANS_EMAIL);
                if ($PREPARANDO_DADOS_ANS->execute()){
                    if($PREPARANDO_SQL_ANS->rowCount() <= 0){
                        $PDO->Disconect();
                        return true;
                    }else{
                        throw new Exception("Email já cadastrado");
                    }
                }else{
                    throw new Exception("Verificação de dados iguais falhou");
                }
            }
        }catch(Exeption $ERR_ANALISE){
            Client::ERR_EXCEPTION($ERR_ANALISE->getMessage());
            $PDO->Disconect();
            return false;
        }
    }


    /**
     *  ======================
     *   TRATAMENTO DE DADOS
     *  =======================
     */

    // VALIDAÇÃO DO CPF
	private function ValidaCPF($CPF = ''){
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
		}catch(Exception $ERR_CPF) {
			Client::ERR_EXCEPTION($ERR_CPF->getMessage());
			return false;
		}
    }
    
    // VALIDAÇÃO DE EMAIL
    private function ValidarEmail($email){
            if(!preg_match('/^[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/i', $email)){
                return false;
            }else{
                return true;
            }
    }



    /** 
     *  ======================
     *   TRATAMENTO DE ERROS 
     *  =======================
     */ 

	// MOSTRAR O ULTIMO ERRO EXECUTADO
	public function ERR_PRINT(){
		try {
			if (empty($this->EX_ERR)){
				throw new Exception("Nenhum erro encontrado");
			}else{
				return $this->EX_ERR;	
			}
		}catch(Exception $ERR_PRINT_EXEC) {
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
				$this->EX_ERR = $ERR;
			}
		}catch(Exception $ERR_EXCEPTION_EXEC) {
			Client::ERR_EXCEPTION($ERR_EXCEPTION_EXEC->getMessage());
			return false;
		}
	}
}

?>