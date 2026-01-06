<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ErrorController extends Controller
{
    /**
     * Display error logs page
     */
    public function index()
    {
        $logFiles = $this->getLogFiles();
        $selectedFile = request('file', 'laravel.log');
        $logContent = $this->getLogContent($selectedFile);

        return view('Admin.errors.index', compact('logFiles', 'selectedFile', 'logContent'));
    }

    /**
     * Get all log files
     */
    private function getLogFiles()
    {
        $logPath = storage_path('logs');
        $files = [];

        if (File::exists($logPath)) {
            $logFiles = File::files($logPath);

            foreach ($logFiles as $file) {
                if ($file->getExtension() === 'log') {
                    $files[] = [
                        'name' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                        'path' => $file->getPathname()
                    ];
                }
            }
        }

        // Sort by modified date (newest first)
        usort($files, function ($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        return $files;
    }

    /**
     * Get content of specific log file
     */
    private function getLogContent($filename)
    {
        $logPath = storage_path('logs/' . $filename);

        if (!File::exists($logPath)) {
            return "ملف السجل غير موجود: " . $filename;
        }

        try {
            $content = File::get($logPath);

            // If file is too large, get only last 500KB
            if (strlen($content) > 500000) {
                $content = "... [تم اقتطاع الملف بسبب الحجم الكبير]\n" .
                    Str::substr($content, -500000);
            }

            return $content;
        } catch (\Exception $e) {
            return "خطأ في قراءة الملف: " . $e->getMessage();
        }
    }

    /**
     * Delete specific log file
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'file' => 'required|string'
        ]);

        $filename = $request->file;
        $logPath = storage_path('logs/' . $filename);

        // Prevent deleting important system files
        if (
            !in_array($filename, ['laravel.log', 'php_error.log']) &&
            Str::endsWith($filename, '.log')
        ) {
            File::delete($logPath);
            return back()->with('success', 'تم حذف ملف السجل: ' . $filename);
        }

        // Clear content of main log files instead of deleting
        if (File::exists($logPath)) {
            File::put($logPath, '');
            return back()->with('success', 'تم تفريغ محتويات ملف السجل: ' . $filename);
        }

        return back()->with('error', 'ملف السجل غير موجود');
    }

    /**
     * Clear all log files
     */
    public function clearAll()
    {
        $logPath = storage_path('logs');

        if (File::exists($logPath)) {
            $files = File::files($logPath);

            foreach ($files as $file) {
                if ($file->getExtension() === 'log') {
                    File::put($file->getPathname(), '');
                }
            }

            return back()->with('success', 'تم تفريغ جميع ملفات السجلات');
        }

        return back()->with('error', 'مسار السجلات غير موجود');
    }

    /**
     * Download log file
     */
    public function download($filename)
    {
        $logPath = storage_path('logs/' . $filename);

        if (!File::exists($logPath)) {
            return back()->with('error', 'ملف السجل غير موجود');
        }

        return response()->download($logPath, 'error_log_' . date('Y-m-d') . '.log');
    }

    /**
     * Get PHP errors from php_error.log or php_errors.log
     */
    public function phpErrors()
    {
        $logFiles = $this->getPHPLogFiles();
        $selectedFile = request('file', 'php_errors.log');
        $logContent = $this->getLogContent($selectedFile);

        return view('Admin.errors.php-errors', compact('logFiles', 'selectedFile', 'logContent'));
    }

    /**
     * Get PHP specific log files
     */
    private function getPHPLogFiles()
    {
        $logPaths = [
            storage_path('logs'),
            // Common PHP error log paths
            ini_get('error_log') ? dirname(ini_get('error_log')) : null,
            '/var/log/php',
            '/var/log/apache2',
            '/var/log/nginx',
        ];

        $files = [];

        foreach ($logPaths as $path) {
            if ($path && File::exists($path)) {
                $logFiles = File::files($path);

                foreach ($logFiles as $file) {
                    $filename = $file->getFilename();
                    // Look for PHP error logs
                    if (
                        Str::contains(strtolower($filename), ['php', 'error']) &&
                        $file->getExtension() === 'log'
                    ) {
                        $files[] = [
                            'name' => $filename,
                            'size' => $this->formatBytes($file->getSize()),
                            'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                            'path' => $file->getPathname()
                        ];
                    }
                }
            }
        }

        // Sort by modified date
        usort($files, function ($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        return $files;
    }

    /**
     * Search in logs
     */
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2',
            'file' => 'nullable|string'
        ]);

        $search = $request->search;
        $filename = $request->file ?? 'laravel.log';
        $logContent = $this->getLogContent($filename);

        $lines = explode("\n", $logContent);
        $results = [];

        foreach ($lines as $lineNumber => $line) {
            if (stripos($line, $search) !== false) {
                $results[] = [
                    'line' => $lineNumber + 1,
                    'content' => $this->highlightSearch($line, $search)
                ];
            }
        }

        $logFiles = $this->getLogFiles();

        return view('Admin.errors.search', compact('results', 'search', 'filename', 'logFiles'));
    }

    /**
     * Highlight search term in text
     */
    private function highlightSearch($text, $search)
    {
        return preg_replace(
            "/(" . preg_quote($search, '/') . ")/i",
            '<span class="bg-warning text-dark px-1 rounded">$1</span>',
            htmlspecialchars($text)
        );
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Parse PHP log content into structured array
     */
    private function parsePHPLog($content)
    {
        $lines = explode("\n", $content);
        $errors = [];
        $currentError = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Check for new error entry (PHP format)
            if (preg_match('/^\[(.*?)\] (PHP )?(.*?): (.*)$/', $line, $matches)) {
                if ($currentError) {
                    $errors[] = $currentError;
                }

                $currentError = [
                    'timestamp' => $matches[1] ?? '',
                    'level' => $matches[3] ?? '',
                    'message' => $matches[4] ?? '',
                    'file' => '',
                    'line' => '',
                    'stack' => ''
                ];
            }
            // Check for Laravel format
            elseif (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) (.*?): (.*)$/', $line, $matches)) {
                if ($currentError) {
                    $errors[] = $currentError;
                }

                $currentError = [
                    'timestamp' => $matches[1] ?? '',
                    'level' => $matches[2] ?? '',
                    'message' => $matches[3] ?? '',
                    'file' => '',
                    'line' => '',
                    'stack' => ''
                ];
            }
            // Stack trace or file info
            elseif ($currentError) {
                if (str_contains($line, 'Stack trace:')) {
                    $currentError['stack'] = $line;
                } elseif (preg_match('/ in (.*?) on line (\d+)/', $line, $matches)) {
                    $currentError['file'] = $matches[1] ?? '';
                    $currentError['line'] = $matches[2] ?? '';
                } elseif (str_starts_with($line, '#') && !empty($currentError['stack'])) {
                    $currentError['stack'] .= "\n" . $line;
                }
            }
        }

        if ($currentError) {
            $errors[] = $currentError;
        }

        return $errors;
    }
}
