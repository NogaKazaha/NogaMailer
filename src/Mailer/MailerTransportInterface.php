<?php

namespace NogaMailer;


interface MailerTransportInterface
{
  public function sendMail(string $temp, string $mail, string $reciver, string $title, array $params): bool;
  public function setLogs(string $pathLog, string $username): void;
  public function renderMail(string $body, string $temp, array $params): string;
}
