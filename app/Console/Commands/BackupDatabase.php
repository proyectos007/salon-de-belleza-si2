<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Realiza una copia de seguridad de la base de datos en un archivo .sql';

    public function handle()
    {
        $database = env('DB_DATABASE');
        $backupFileName = $database . '_backup_' . date('Y-m-d_H-i-s') . '.csv';
        $backupPath = 'backups/' . $backupFileName;


        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups', 0755, true);
        }

        
        $dbDriver = DB::getDriverName();

        if ($dbDriver === 'mysql') {
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $database;
        } elseif ($dbDriver === 'pgsql') {
            
            $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
            $tableKey = 'table_name';

        } else {
            $this->error('Unsupported database driver: ' . $dbDriver);
            return;
        }
        

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            $rows = DB::table($tableName)->get();
            Storage::append($backupPath, 'Table: ' . $tableName . "\n");

            foreach ($rows as $row) {
                $line = implode(',', (array) $row);
                Storage::append($backupPath, $line);
            }

            Storage::append($backupPath, "\n\n");
        }

        return "Backup created at: $backupPath";
    }
}