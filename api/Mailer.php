<?php
/**
 * Mailer - Email sending utility
 * Handles email composition and delivery
 */

require_once __DIR__ . '/Logger.php';

class Mailer
{
    private $to = [];
    private $cc = [];
    private $bcc = [];
    private $subject = '';
    private $body = '';
    private $htmlBody = '';
    private $headers = [];
    private $attachments = [];

    /**
     * Set recipient email
     */
    public function to(string $email, string $name = ''): self
    {
        $this->to[] = [
            'email' => $email,
            'name' => $name,
        ];
        return $this;
    }

    /**
     * Set CC recipient
     */
    public function cc(string $email, string $name = ''): self
    {
        $this->cc[] = [
            'email' => $email,
            'name' => $name,
        ];
        return $this;
    }

    /**
     * Set BCC recipient
     */
    public function bcc(string $email, string $name = ''): self
    {
        $this->bcc[] = [
            'email' => $email,
            'name' => $name,
        ];
        return $this;
    }

    /**
     * Set email subject
     */
    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set plain text body
     */
    public function body(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Set HTML body
     */
    public function htmlBody(string $body): self
    {
        $this->htmlBody = $body;
        return $this;
    }

    /**
     * Add attachment
     */
    public function attach(string $filepath, string $filename = ''): self
    {
        if (!file_exists($filepath)) {
            log_warning('Attachment file not found', ['path' => $filepath]);
            return $this;
        }

        $this->attachments[] = [
            'path' => $filepath,
            'name' => $filename ?: basename($filepath),
        ];

        return $this;
    }

    /**
     * Add custom header
     */
    public function addHeader(string $header, string $value): self
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * Send email
     */
    public function send(): bool
    {
        if (empty($this->to)) {
            $this->logError('No recipient specified');
            return false;
        }

        if (empty($this->subject)) {
            $this->logError('No subject specified');
            return false;
        }

        if (empty($this->body) && empty($this->htmlBody)) {
            $this->logError('No body content specified');
            return false;
        }

        // Build headers
        $headers = $this->buildHeaders();

        // Build email content
        $emailBody = $this->htmlBody ?: $this->body;

        // Get recipient list
        $toAddresses = implode(',', array_map(fn($r) => $this->formatAddress($r), $this->to));

        // Send email
        $result = mail($toAddresses, $this->subject, $emailBody, $headers);

        if ($result) {
            log_info('Email sent successfully', [
                'to' => $toAddresses,
                'subject' => $this->subject,
            ]);
        } else {
            $this->logError('Failed to send email');
        }

        return $result;
    }

    /**
     * Format recipient address
     */
    private function formatAddress(array $recipient): string
    {
        if (empty($recipient['name'])) {
            return $recipient['email'];
        }

        return $recipient['name'] . ' <' . $recipient['email'] . '>';
    }

    /**
     * Build email headers
     */
    private function buildHeaders(): string
    {
        $headers = [];

        // Set default headers
        $headers[] = 'From: ' . $this->formatAddress([
            'email' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@gourmetexpress.com',
            'name' => APP_NAME,
        ]);

        $headers[] = 'Reply-To: ' . $this->formatAddress([
            'email' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@gourmetexpress.com',
        ]);

        // Add CC recipients
        if (!empty($this->cc)) {
            $ccAddresses = implode(',', array_map(fn($r) => $this->formatAddress($r), $this->cc));
            $headers[] = 'Cc: ' . $ccAddresses;
        }

        // Add BCC recipients
        if (!empty($this->bcc)) {
            $bccAddresses = implode(',', array_map(fn($r) => $this->formatAddress($r), $this->bcc));
            $headers[] = 'Bcc: ' . $bccAddresses;
        }

        // Content type
        $headers[] = 'Content-Type: ' . ($this->htmlBody ? 'text/html' : 'text/plain') . '; charset=UTF-8';

        // Add custom headers
        foreach ($this->headers as $header => $value) {
            $headers[] = $header . ': ' . $value;
        }

        return implode("\r\n", $headers);
    }

    /**
     * Log error
     */
    private function logError(string $message): void
    {
        log_error('Mail error: ' . $message, [
            'subject' => $this->subject,
            'to' => array_map(fn($r) => $r['email'], $this->to),
        ]);
    }

    /**
     * Create simple text email
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Send simple email
     */
    public static function sendSimple(string $to, string $subject, string $body): bool
    {
        return self::create()
            ->to($to)
            ->subject($subject)
            ->body($body)
            ->send();
    }
}
?>
