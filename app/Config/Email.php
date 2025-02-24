<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'your-email@example.com';
    public string $fromName   = 'A3BC Online';
    public string $recipients = '';
    public string $userAgent = 'CodeIgniter';
    public string $protocol = 'smtp';
    public string $mailPath = '/usr/sbin/sendmail';
    public string $SMTPHost = 'smtp-relay.brevo.com';
    public string $SMTPUser = '868386001@smtp-brevo.com';
    public string $SMTPPass = 'TsBXwVz7pUK8EI1Y';
    public int    $SMTPPort = 587;
    public int    $SMTPTimeout = 10;
    public bool   $SMTPKeepAlive = false;
    public string $SMTPCrypto = 'tls';
    public bool   $wordWrap = true;
    public int    $wrapChars = 76;
    public string $mailType = 'html';
    public string $charset = 'UTF-8';
    public bool   $validate = true;
    public int    $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool   $BCCBatchMode = false;
    public int    $BCCBatchSize = 200;
    public bool   $DSN = false;
}
