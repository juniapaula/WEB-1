<?php
class Conexao
{
    private static $dsn = 'mysql:host=localhost;port=3306;dbname=atividade11;charset=utf8';
    private static $usuario = 'root';
    private static $senha = 'root';
    private static $conexao = null;

    // Função para conectar ao banco de dados
    private static function conecta()
    {
        if (self::$conexao === null) {
            self::$conexao = new PDO(
                self::$dsn,
                self::$usuario,
                self::$senha
            );
            self::$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    // Retorna a conexão ativa
    public static function getConexao()
    {
        self::conecta();
        return self::$conexao;
    }

    // Prepara um comando SQL
    public static function preparaComando($sql)
    {
        self::conecta();
        return self::$conexao->prepare($sql);
    }
}
?>
