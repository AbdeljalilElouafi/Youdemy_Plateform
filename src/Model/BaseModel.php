<?php
namespace App\Model;

use Config\Database;
use PDO;
use PDOException;

abstract class BaseModel {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function insertRecord($table, $data) {
        try {
            $columns = implode(',', array_keys($data));
            $values = implode(',', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO $table ($columns) VALUES ($values)";
            $stmt = $this->db->prepare($sql);
            $params = array_values($data);
            $stmt->execute($params);
            return $this->db->lastInsertId();
        } catch(PDOException $e) {
            error_log("Error in insertRecord: " . $e->getMessage());
            return false;
        }
    }

    protected function updateRecord($table, $data, $id) {
        try {
            $args = array();
            foreach ($data as $key => $value) {
                $args[] = "$key = ?";
            }
            $sql = "UPDATE $table SET " . implode(',', $args) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $params = array_values($data);
            $params[] = $id;
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch(PDOException $e) {
            error_log("Error in updateRecord: " . $e->getMessage());
            return false;
        }
    }

    protected function deleteRecord($table, $id) {
        try {
            $sql = "DELETE FROM $table WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch(PDOException $e) {
            error_log("Error in deleteRecord: " . $e->getMessage());
            return false;
        }
    }

    protected function fetchRecords($table, $conditions = [], $orderBy = '', $limit = null) {
        try {
            $sql = "SELECT * FROM $table";
            
            if (!empty($conditions)) {
                $where = [];
                foreach ($conditions as $key => $value) {
                    $where[] = "$key = ?";
                }
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            if ($orderBy) {
                $sql .= " ORDER BY $orderBy";
            }
            
            if ($limit) {
                $sql .= " LIMIT $limit";
            }

            $stmt = $this->db->prepare($sql);
            
            if (!empty($conditions)) {
                $stmt->execute(array_values($conditions));
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in fetchRecords: " . $e->getMessage());
            return false;
        }
    }
}

?>