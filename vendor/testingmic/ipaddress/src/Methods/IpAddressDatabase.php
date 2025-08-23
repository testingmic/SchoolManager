<?php

namespace Testingmic\Methods;

class IpAddressDatabase
{
    const DS = DIRECTORY_SEPARATOR;
    /**
     * Database instance
     *
     * @var \PDO
     */
    private $oPDOInstance;

    /**
     * Table name
     *
     * @var string
     */
    private $tableName = 'ipCountryRange';

    /**
     * PDO transaction Counter
    *
    * @var integer
    */
    private $transactionCounter = 0;

    /**
     * Class Constructor
     *
     * @param string $database
     */
    public function __construct($database = null)
    {
        try {
            if(!empty($this->oPDOInstance)) return $this->oPDOInstance;
            $aOptions = [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES   => false,
                ];
            $this->oPDOInstance = new \PDO($this->genDsn($database), null, null, $aOptions);
            $this->initialize();
        } catch (\Throwable $th) {
            trigger_error($th->getMessage(), E_USER_ERROR);
        }
        return $this->oPDOInstance;
    }

    /**
     * Create Database tables structure.
     *
     * @return GeoipDatabase
     */
    private function initialize()
    {
        $aCommands = [
            "CREATE TABLE IF NOT EXISTS `{$this->tableName}`(
                `start` BIGINT UNSIGNED ,
                `end` BIGINT UNSIGNED ,
                `country_code` VARCHAR(2),
                `country_name` VARCHAR(100),
                `continent_code` VARCHAR(2),
                `city` VARCHAR(300),
                `latitude` FLOAT,
                `longitude` FLOAT
            )", "CREATE UNIQUE INDEX IF NOT EXISTS `idx_{$this->tableName}` ON `{$this->tableName}`(`start`, `end`)"
        ];
        foreach ($aCommands as $command)
        {
            $this->oPDOInstance->query($command);
        }
        return $this;
    }

    /**
     * Generate PDO SQLite3 DSN
     *
     * @param string|null $database
     * @return string
     */
    private function genDsn($database = null)
    {
        try {
            $destination = rtrim(dirname(__DIR__), self::DS);
            $info = new \SplFileInfo($database);
            $dbName = $info->getFilename();
            $dbSuffix ='.sqlite';
            if (substr_compare(strtolower($dbName), $dbSuffix, -strlen($dbSuffix)) !== 0) { $dbName .= $dbSuffix ; }
        } catch (\Throwable $th) {
            trigger_error($th->getMessage(), E_USER_ERROR);
        }
        $destination .= self::DS.'data';
        if (!is_dir($destination)) { mkdir($destination, '0755', true); }
        return 'sqlite:'.realpath($destination).self::DS.$dbName;
    }

    /**
     * Get the table list in the database
     *
     * @return array
     */
    public function showTables()
    {
        try
        {
            $command = 'SELECT `name` FROM `sqlite_master` WHERE `type` = \'table\' ORDER BY name';
            $statement = $this->oPDOInstance->query($command);
            $tables = [];
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $tables[] = $row['name'];
            }
            return $tables;
        } catch (\PDOException $th) {
            trigger_error($th->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * Retrieve Column(s) value from given table
     *
     * @param string $sTable
     * @param array $columns
     * @return array
     */
    public function fetchAll(string $sTable, array $columns = [])
    {
        !empty($columns) || $columns = '*';
        if (is_array($columns)) { $columns = implode('`, `', $columns); }
        try
        {
            $sCommand = 'SELECT `%s` from  `%s`';
            $statement = $this->oPDOInstance->prepare(sprintf($sCommand, $columns, $sTable));
            $statement->execute();
            return $statement->fetchAll();
        } catch (\PDOException $th) {
            trigger_error($th->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * Return Country code from given IP address (converted to integer)
     *
     * @param integer $start
     * @param integer $ipVersion  (ip version)
     * @return mixed
     */
    public function fetch(int $start)
    {
        try
        {
            $sCommand  = 'SELECT `start`, `end`, `country_code`, `country_name`, `continent_code`, `city`, `latitude`, `longitude` ';
            $sCommand .= "FROM `{$this->tableName}` ";
            $sCommand .= "WHERE `start` <= :start ";
            $sCommand .= 'ORDER BY start DESC LIMIT 1';
            $statement = $this->oPDOInstance->prepare(sprintf($sCommand));
            $statement->execute([':start' => $start ]);
            $row = $statement->fetch(\PDO::FETCH_ASSOC);
            if (is_bool($row) && $row === false)
            {
                $row = [];
                $row['end'] = 0;
            }
            if ($row['end'] < $start || empty($row['country_code'])) { 
                $row['country_code'] = 'ZZ'; 
            }
            return $row;
        } catch (\PDOException $th) {
            trigger_error($th->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * Update existing records in the database
     *
     * @param int $start
     * @param int $end
     * @param string $country_name
     * @param string $city
     * @param float $latitude
     * @param float $longitude
     * @param string $continent
     * @return bool
     */
    public function updateExistingRecords($start, $end, $country_code, $country_name, $city, $latitude, $longitude, $continent)
    {
        
        try
        {
            $sQuery = 'UPDATE `%s` SET `country_name` = :country_name, `city` = :city, `latitude` = :latitude, `longitude` = :longitude, `continent_code` = :continent 
                WHERE `start` = :start AND `end` = :end AND country_code = :country_code';
            $command = sprintf($sQuery, $this->tableName);
            $statement = $this->oPDOInstance->prepare($command);
            $statement->execute([
                ':country_name' => $country_name,
                ':city' => $city,
                ':latitude' => $latitude,
                ':longitude' => $longitude,
                ':continent' => $continent,
                ':start'   => $start,
                ':end'     => $end,
                ':country_code' => $country_code
            ]);
        } catch (\PDOException $th) {
            trigger_error('Statement failed: ' . $th->getMessage(), E_USER_ERROR);
        }

        
    }

    /**
     * Empty a given list of database tables
     *
     * @param array $tablesList
     * @return void
     */
    public function flush(array $tablesList = [])
    {
        !empty($tablesList) || $tablesList = $this->showTables();
        is_array($tablesList) || $tablesList = [$tablesList];
        try
        {
            if (!empty($tablesList)):
                $sCommand = 'DELETE FROM `%s`';
                foreach ($tablesList as $sTable) {
                    if($sTable === $this->tableName) { continue; }
                    $this->oPDOInstance->query(sprintf($sCommand, $sTable));
                }
                $this->oPDOInstance->query('VACUUM');
            endif;
        } catch (\PDOException $th) {
            trigger_error('Statement failed: '.$th->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * Insert data into database
     *
     * @param integer $start
     * @param integer $end
     * @param integer $ipVersion
     * @param string $country
     * @param string $country_name
     * @param string $continent_code
     * @param string $city
     * @param float $latitude
     * @param float $longitude
     * @return void
     */
    public function insert(int $start, int $end, string $table, string $country, string $country_name, string $continent_code, string $city, float $latitude, float $longitude)
    {
        try
        {
            $sQuery = 'INSERT INTO `%s` (`start`, `end`, `country_code`, `country_name`, `continent_code`, `city`, `latitude`, `longitude`) 
                values (:start, :end, :country_code, :country_name, :continent_code, :city, :latitude, :longitude)';
            $command = sprintf($sQuery, $table);
            $statement = $this->oPDOInstance->prepare($command);
            $statement->execute([
                ':start'   => $start,
                ':end'     => $end,
                ':country_code' => $country,
                ':country_name' => $country_name,
                ':continent_code' => $continent_code,
                ':city' => $city,
                ':latitude' => $latitude,
                ':longitude' => $longitude
            ]);
        } catch (\PDOException $th) {
            trigger_error('Statement failed: ' . $th->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * Begin PDO transaction, turning off autocommit
     *
     * @return bool
     */
    public function beginTransaction()
    {
        if (!$this->transactionCounter++) {return $this->oPDOInstance->beginTransaction();}
        return $this->transactionCounter >= 0;
    }

    /**
     * Commit PDO transaction changes
     *
     * @return bool
     */
    public function commit()
    {
        if (!--$this->transactionCounter) {return $this->oPDOInstance->commit(); }
        return $this->transactionCounter >= 0;
    }
    
    /**
     * Rollback PDO transaction, Recognize mistake and roll back changes
     *
     * @return bool
     */
    public function rollback()
    {
        if ($this->transactionCounter >= 0) {
            $this->transactionCounter = 0;
            return $this->oPDOInstance->rollback();
        }
        $this->transactionCounter = 0;
        return false;
    }
}
