<?php

namespace NogaMailer;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Mailer implements MailerTransportInterface
{
  public $config;
  public $log;
  public $LoggerSet;

  public function __construct(array $arr)
  {
    $this->config = $arr;
    $this->LoggerSet = false;
  }
  public function setLogs(string $path, string $username): void
  {
    $this->log = new Logger($username);
    $stream = new StreamHandler($path);
    $stream->setFormatter(new JsonFormatter());
    $this->log->pushHandler($stream);
    $this->LoggerSet = true;
  }
  public function renderMail(string $body, string $temp, array $params): string
  {
    extract($params);
    ob_start();
    include_once($body);
    $body = ob_get_clean();
    ob_clean();
    ob_start();
    include_once($temp);
    $output = ob_get_clean();
    return $output;
  }
  public function sendMail(string $template, string $mail, string $reciver, string $title, array $params): bool
  {
    extract($this->config);
    extract($params);
    $result = false;
    $template = $path . $template . '.php';
    $body = $this->renderMail($template, $default, $params);
    $transport = (new Swift_SmtpTransport($smtp, $port, $encryption))
      ->setUsername($email)
      ->setPassword($password);
    $mailer = new Swift_Mailer($transport);
    $message = (new Swift_Message($title))
      ->setContentType("text/html")
      ->setFrom([$email => $username])
      ->setTo([$recipientMail => "$recipient"])
      ->setBody("$body", 'text/html');
    try {
      (bool)$result = $mailer->send($message);
    } catch (\Exception $exception) {
      if ($this->LoggerSet) {
        $this->log->error('Error', [$exception]);
      }
    }
    return $result;
  }
}
