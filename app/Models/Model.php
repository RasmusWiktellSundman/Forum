<?php
namespace App\Models;

use App\Lib\Database;
use InvalidArgumentException;

abstract class Model {
    /**
     * Variabel med giltiga databas tabeller, ska uppdateras av respektive Model
     *
     * @var array
     */
    protected static array $validDbColumns;

    /**
     * Variabel med giltiga databas tabeller, ska uppdateras av respektive Model
     *
     * @var array
     */
    protected static array $tableName;

    /**
     * Kollar om ett specifikt värde i en specifik kolumn finns
     *
     * @param string $column Kolumn att leta i
     * @param mixed $value Värdet att leta efter
     * @return boolean Om värdet finns i kolumnen
     */
    public static function exsistsColumnValue(string $column, mixed $value): bool
    {
        // En kolumn kan inte bindas i ett prepared statment, validerar därför att kolumnen finns.
        if(!in_array($column, self::$validDbColumns, true)) {
            throw new InvalidArgumentException("Invalid column");
        }

        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM user WHERE $column = ?;");
        $stmt->execute([$value]);
        // Hämta antal rader från count
        $count = $stmt->fetchColumn();
        $stmt->closeCursor();

        return $count > 0;
    }

    public static function getTableName()
    {
        if(!isset(self::$tableName)) {
            return get_called_class();
        }
    }
}