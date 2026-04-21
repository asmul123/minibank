<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Backup extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->db_config = $this->db;
    }

    public function backup_database()
    {
        try {
            $db_name = $this->db->database;
            $backup_dir = FCPATH . 'assets/backup/';
            
            if (!is_dir($backup_dir)) {
                mkdir($backup_dir, 0777, true);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $filename = 'db_bms_backup_' . $timestamp . '.sql';
            $file_path = $backup_dir . $filename;

            // Get all tables
            $tables = array();
            $tables_query = $this->db->query("SHOW TABLES");
            
            foreach ($tables_query->result_array() as $table) {
                $tables[] = $table['Tables_in_' . $db_name];
            }

            if (empty($tables)) {
                return array('status' => false, 'message' => 'Tidak ada tabel yang ditemukan');
            }

            $output = "-- Database Backup\n";
            $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $output .= "-- Database: " . $db_name . "\n";
            $output .= "-- --------------------------------------------------------\n\n";
            $output .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $output .= "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n\n";

            // Backup each table
            foreach ($tables as $table) {
                $output .= "-- --------------------------------------------------------\n";
                $output .= "-- Table structure for table `" . $table . "`\n";
                $output .= "-- --------------------------------------------------------\n\n";

                // Get CREATE TABLE statement
                $create_table = $this->db->query("SHOW CREATE TABLE `" . $table . "`")->row();
                $output .= $create_table->{'Create Table'} . ";\n\n";

                // Get table data
                $data_query = $this->db->query("SELECT * FROM `" . $table . "`");
                
                if ($data_query->num_rows() > 0) {
                    $output .= "-- --------------------------------------------------------\n";
                    $output .= "-- Dumping data for table `" . $table . "`\n";
                    $output .= "-- --------------------------------------------------------\n\n";

                    foreach ($data_query->result_array() as $row) {
                        $output .= "INSERT INTO `" . $table . "` (";
                        
                        $columns = array_keys($row);
                        $output .= "`" . implode("`, `", $columns) . "`";
                        $output .= ") VALUES (";
                        
                        $values = array();
                        foreach ($row as $value) {
                            if ($value === null) {
                                $values[] = "NULL";
                            } else {
                                $values[] = "'" . addslashes($value) . "'";
                            }
                        }
                        $output .= implode(", ", $values);
                        $output .= ");\n";
                    }
                    $output .= "\n";
                }
            }

            $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

            // Write to file
            if (file_put_contents($file_path, $output)) {
                return array('status' => true, 'filename' => $filename, 'path' => $file_path);
            } else {
                return array('status' => false, 'message' => 'Gagal menulis file backup');
            }

        } catch (Exception $e) {
            return array('status' => false, 'message' => $e->getMessage());
        }
    }

    public function restore_database($file_path)
    {
        try {
            if (!file_exists($file_path)) {
                return array('status' => false, 'message' => 'File backup tidak ditemukan');
            }

            $sql_content = file_get_contents($file_path);
            
            if (empty($sql_content)) {
                return array('status' => false, 'message' => 'File backup kosong');
            }

            // Split SQL statements
            $sql_statements = array_filter(array_map('trim', preg_split('/;(?=(?:[^\']*\'[^\']*\')*[^\']*$)/', $sql_content)));

            foreach ($sql_statements as $statement) {
                if (!empty($statement)) {
                    // Skip comments
                    if (substr(trim($statement), 0, 2) !== '--') {
                        try {
                            $this->db->query($statement);
                        } catch (Exception $e) {
                            // Log error tapi lanjutkan
                            log_message('error', 'Restore error: ' . $e->getMessage());
                        }
                    }
                }
            }

            return array('status' => true, 'message' => 'Database berhasil di-restore');

        } catch (Exception $e) {
            return array('status' => false, 'message' => $e->getMessage());
        }
    }

    public function get_database_size()
    {
        $db_name = $this->db->database;
        $query = $this->db->query("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.TABLES WHERE table_schema = '" . $db_name . "'");
        
        if ($query->num_rows() > 0) {
            return $query->row()->size_mb;
        }
        return 0;
    }
}
?>
