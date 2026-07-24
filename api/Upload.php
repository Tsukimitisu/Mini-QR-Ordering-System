<?php
/**
 * Upload - File upload handler
 * Manages file uploads with validation and storage
 */

require_once __DIR__ . '/Logger.php';

class Upload
{
    private const UPLOAD_DIR = __DIR__ . '/../uploads';
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xlsx'];
    private const MAX_FILE_SIZE = 10485760; // 10MB
    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
    ];

    private $file;
    private $error = '';

    public function __construct(array $file)
    {
        $this->file = $file;
    }

    /**
     * Validate uploaded file
     */
    public function validate(int $maxSize = self::MAX_FILE_SIZE): bool
    {
        // Check for upload errors
        if ($this->file['error'] !== UPLOAD_ERR_OK) {
            $this->error = $this->getUploadErrorMessage($this->file['error']);
            return false;
        }

        // Check file size
        if ($this->file['size'] > $maxSize) {
            $this->error = 'File size exceeds maximum allowed size of ' . ($maxSize / 1024 / 1024) . 'MB';
            return false;
        }

        // Check file type
        $mimeType = mime_content_type($this->file['tmp_name']);
        if (!isset(self::ALLOWED_MIMES[$mimeType])) {
            $this->error = 'File type is not allowed. Allowed types: ' . implode(', ', self::ALLOWED_EXTENSIONS);
            return false;
        }

        // Check extension
        $extension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($extension), self::ALLOWED_EXTENSIONS)) {
            $this->error = 'File extension is not allowed';
            return false;
        }

        return true;
    }

    /**
     * Store uploaded file
     */
    public function store(string $directory = '', string $filename = null): ?string
    {
        if (!$this->validate()) {
            return null;
        }

        self::init();

        $uploadDir = self::UPLOAD_DIR;

        if ($directory) {
            $uploadDir = $uploadDir . '/' . trim($directory, '/');
        }

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate filename if not provided
        if ($filename === null) {
            $extension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('upload_') . '.' . strtolower($extension);
        }

        $filepath = $uploadDir . '/' . $filename;
        $relativePath = str_replace(self::UPLOAD_DIR, '', $filepath);

        // Move file
        if (!move_uploaded_file($this->file['tmp_name'], $filepath)) {
            $this->error = 'Failed to move uploaded file';
            log_error('File upload failed', ['filename' => $filename, 'error' => $this->error]);
            return null;
        }

        chmod($filepath, 0644);

        log_info('File uploaded successfully', [
            'filename' => $filename,
            'size' => filesize($filepath),
            'path' => $relativePath,
        ]);

        return $relativePath;
    }

    /**
     * Get last error
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Get file name
     */
    public function getFileName(): string
    {
        return $this->file['name'];
    }

    /**
     * Get file size
     */
    public function getFileSize(): int
    {
        return $this->file['size'];
    }

    /**
     * Get file MIME type
     */
    public function getMimeType(): string
    {
        return mime_content_type($this->file['tmp_name']);
    }

    /**
     * Delete an uploaded file
     */
    public static function delete(string $filepath): bool
    {
        $fullPath = self::UPLOAD_DIR . $filepath;

        if (!file_exists($fullPath)) {
            return false;
        }

        $result = unlink($fullPath);

        if ($result) {
            log_info('File deleted', ['path' => $filepath]);
        }

        return $result;
    }

    /**
     * Initialize upload directory
     */
    private static function init(): void
    {
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        // Create .htaccess to prevent script execution
        $htaccess = self::UPLOAD_DIR . '/.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "deny from all\n");
        }
    }

    /**
     * Get upload error message
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        $messages = [
            UPLOAD_ERR_OK => 'No error',
            UPLOAD_ERR_INI_SIZE => 'File exceeds server maximum upload size',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form maximum upload size',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary directory',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Extension stopped file upload',
        ];

        return $messages[$errorCode] ?? 'Unknown upload error';
    }
}
?>
