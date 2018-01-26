<?php

class Conn {

    private static $Host = "localhost";
    private static $User = "root";
    private static $Pass = "";
    private static $Dbsa = "empresa";
    // @Var PDO
    private static $Connect = null;

    // Conecta com o banco de dados com o pattern singleton.
    // Retorna um objeto PDO.

    private static function Conectar() {
        try {
            if (self::$Connect == null):
                $dsn = 'mysql:host=' . self::$Host . ';dbname=' . self::$Dbsa;
                $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'];
                self::$Connect = new PDO($dsn, self::$User, self::$Pass, $options);
            endif;
        } catch (Exception $e) {
            Self::PHPErro($e->getCode(), $e->getMessage(), $e->getFile(), $e->getline());
            die;
        }
        self::$Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return self::$Connect;
    }

    // Retorna um objeto PDO singleton pattern.

    public static function getConn() {
        return self::Conectar();
    }
    public static function Disconect(){
        self::$Connect = null;
    }
    private static function PHPErro($code,$msg,$file,$line) {
        echo "<div id='avisoError'>";
        echo "Código do erro: ".$code."<br>";
        echo "Erro: ".$msg."<br>";
        echo "Arquivo: ".$file."<br>";
        echo "Linha: ".$line."<br>";
        echo "</div>";
    }

}
?>

