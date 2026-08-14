<?php

namespace App\Modules\AgriVerse\Services;

use Carbon\Carbon;

class BackupService
{
    protected string $backupPath = 'backups';

    protected int $keepDays = 30;

    /**
     * Create full backup (database + files)
     */
    public function createFullBackup(): array
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupName = "full_backup_{$timestamp}";

        $results = [
            'database' => $this->backupDatabase($backupName),
            'files' => $this->backupFiles($backupName),
            'created_at' => now()->toISOString(),
            'backup_name' => $backupName,
        ];

        // Cleanup old backups
        $this->cleanupOldBackups();

        return $results;
    }

    /**
     * Backup database only
     */
    public function backupDatabase(?string $name = null): string
    {
        $name = $name ?? 'db_backup_'.now()->format('Y-m-d_H-i-s');
        $filename = "{$name}.sql";
        $path = "{$this->backupPath}/database/{$filename}";

        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        if ($config['driver'] === 'mysql') {
            $this->backupMysql($config, $path);
        } elseif ($config['driver'] === 'sqlite') {
            $this->backupSqlite($config, $path);
        }

        return $path;
    }

    /**
     * Backup MySQL database
     */
    protected function backupMysql(array $config, string $path): void
    {
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '3306';
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'] ?? '';

        $command = sprintf(
            'mysqldump -h %s -P %s -u %s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg(storage_path("app/{$path}"))
        );

        if (! empty($password)) {
            $command = sprintf(
                'mysqldump -h %s -P %s -u %s -p%s %s > %s 2>&1',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg(storage_path("app/{$path}"))
            );
        }

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException('Database backup failed: '.implode("\n", $output));
        }
    }

    /**
     * Backup SQLite database
     */
    protected function backupSqlite(array $config, string $path): void
    {
        $database = $config['database'];
        $command = sprintf(
            'cp %s %s',
            escapeshellarg($database),
            escapeshellarg(storage_path("app/{$path}"))
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException('SQLite backup failed: '.implode("\n", $output));
        }
    }

    /**
     * Backup files only
     */
    public function backupFiles(?string $name = null): string
    {
        $name = $name ?? 'files_backup_'.now()->format('Y-m-d_H-i-s');
        $filename = "{$name}.tar.gz";
        $path = "{$this->backupPath}/files/{$filename}";

        $sourcePath = storage_path('app/public');
        $destPath = storage_path("app/{$path}");

        // Create backup directory
        $backupDir = dirname($destPath);
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $command = sprintf(
            'tar -czf %s -C %s . 2>&1',
            escapeshellarg($destPath),
            escapeshellarg($sourcePath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException('File backup failed: '.implode("\n", $output));
        }

        return $path;
    }

    /**
     * Restore from database backup
     */
    public function restoreDatabase(string $backupPath): bool
    {
        $fullPath = storage_path("app/{$backupPath}");

        if (! file_exists($fullPath)) {
            throw new \RuntimeException("Backup file not found: {$backupPath}");
        }

        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        if ($config['driver'] === 'mysql') {
            $this->restoreMysql($config, $fullPath);
        } else {
            throw new \RuntimeException("Restore not supported for driver: {$config['driver']}");
        }

        return true;
    }

    /**
     * Restore MySQL database
     */
    protected function restoreMysql(array $config, string $filePath): void
    {
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '3306';
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'] ?? '';

        $command = sprintf(
            'mysql -h %s -P %s -u %s %s < %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        if (! empty($password)) {
            $command = sprintf(
                'mysql -h %s -P %s -u %s -p%s %s < %s 2>&1',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($filePath)
            );
        }

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException('Database restore failed: '.implode("\n", $output));
        }
    }

    /**
     * List all backups
     */
    public function listBackups(): array
    {
        $backups = [
            'database' => [],
            'files' => [],
        ];

        $dbPath = storage_path("app/{$this->backupPath}/database");
        if (is_dir($dbPath)) {
            $files = glob("{$dbPath}/*.sql");
            foreach ($files as $file) {
                $backups['database'][] = [
                    'filename' => basename($file),
                    'size' => filesize($file),
                    'created_at' => Carbon::createFromTimestamp(filemtime($file)),
                ];
            }
        }

        $filePath = storage_path("app/{$this->backupPath}/files");
        if (is_dir($filePath)) {
            $files = glob("{$filePath}/*.tar.gz");
            foreach ($files as $file) {
                $backups['files'][] = [
                    'filename' => basename($file),
                    'size' => filesize($file),
                    'created_at' => Carbon::createFromTimestamp(filemtime($file)),
                ];
            }
        }

        // Sort by date
        foreach ($backups as &$items) {
            usort($items, fn ($a, $b) => $b['created_at'] <=> $a['created_at']);
        }

        return $backups;
    }

    /**
     * Delete a backup
     */
    public function deleteBackup(string $type, string $filename): bool
    {
        $path = storage_path("app/{$this->backupPath}/{$type}/{$filename}");

        if (! file_exists($path)) {
            return false;
        }

        return unlink($path);
    }

    /**
     * Cleanup old backups
     */
    public function cleanupOldBackups(): int
    {
        $deleted = 0;
        $cutoff = Carbon::now()->subDays($this->keepDays);

        $types = ['database', 'files'];

        foreach ($types as $type) {
            $path = storage_path("app/{$this->backupPath}/{$type}");

            if (! is_dir($path)) {
                continue;
            }

            $files = glob("{$path}/*");
            foreach ($files as $file) {
                if (Carbon::createFromTimestamp(filemtime($file))->lt($cutoff)) {
                    unlink($file);
                    $deleted++;
                }
            }
        }

        return $deleted;
    }

    /**
     * Get backup stats
     */
    public function getStats(): array
    {
        $backups = $this->listBackups();

        $dbSize = array_sum(array_column($backups['database'], 'size'));
        $fileSize = array_sum(array_column($backups['files'], 'size'));

        return [
            'total_backups' => count($backups['database']) + count($backups['files']),
            'database_backups' => count($backups['database']),
            'file_backups' => count($backups['files']),
            'total_size' => $this->formatSize($dbSize + $fileSize),
            'database_size' => $this->formatSize($dbSize),
            'files_size' => $this->formatSize($fileSize),
            'last_backup' => collect($backups['database'])
                ->merge($backups['files'])
                ->sortByDesc('created_at')
                ->first()['created_at'] ?? null,
        ];
    }

    /**
     * Format file size
     */
    protected function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        $size = $bytes;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2).' '.$units[$i];
    }
}
