<?php
/**
 * Índice dos Models
 * Sistema de Análise Contratual Automatizada
 * 
 * @package App\Models
 * @author Sistema Ross
 * @version 1.0.0
 */

// Carregar todos os models
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Contract.php';
require_once __DIR__ . '/AnalysisDataPoint.php';
require_once __DIR__ . '/ContractView.php';
require_once __DIR__ . '/Report.php';

/**
 * Classe para facilitar o uso dos models
 */
class Models
{
    /**
     * Instâncias dos models (singleton)
     */
    private static $instances = [];

    /**
     * Obter instância do model User
     * 
     * @return User
     */
    public static function user()
    {
        if (!isset(self::$instances['user'])) {
            self::$instances['user'] = new User();
        }
        return self::$instances['user'];
    }

    /**
     * Obter instância do model Contract
     * 
     * @return Contract
     */
    public static function contract()
    {
        if (!isset(self::$instances['contract'])) {
            self::$instances['contract'] = new Contract();
        }
        return self::$instances['contract'];
    }

    /**
     * Obter instância do model AnalysisDataPoint
     * 
     * @return AnalysisDataPoint
     */
    public static function analysisDataPoint()
    {
        if (!isset(self::$instances['analysisDataPoint'])) {
            self::$instances['analysisDataPoint'] = new AnalysisDataPoint();
        }
        return self::$instances['analysisDataPoint'];
    }

    /**
     * Obter instância do model ContractView
     * 
     * @return ContractView
     */
    public static function contractView()
    {
        if (!isset(self::$instances['contractView'])) {
            self::$instances['contractView'] = new ContractView();
        }
        return self::$instances['contractView'];
    }

    /**
     * Obter instância do model Report
     * 
     * @return Report
     */
    public static function report()
    {
        if (!isset(self::$instances['report'])) {
            self::$instances['report'] = new Report();
        }
        return self::$instances['report'];
    }

    /**
     * Limpar todas as instâncias
     */
    public static function clearInstances()
    {
        self::$instances = [];
    }
}
