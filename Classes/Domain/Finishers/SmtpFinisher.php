<?php
declare(strict_types=1);

namespace PeerNissen\SmtpFinisher\Domain\Finishers;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;


final class SmtpFinisher extends AbstractFinisher
{
	protected PHPMailer $phpMailerLib;

    protected $shortFinisherIdentifier = 'smtp';

    /**
     * @throws Exception
     */
    public function executeInternal() : void
    {
        $mailer = $this->phpMailerLib = new PHPMailer();

				$mailer->isSMTP();
				$mailer->SMTPAuth =true;
				$mailer->SMTPSecure = $this->phpMailerLib::ENCRYPTION_STARTTLS;
				
				$mailer->Port = (int)$this->options["smtpPort"];
				$mailer->Host = $this->options["smtpServer"];

				$mailer->Username = $this->options["username"];
				$mailer->Password = $this->options["password"];

				$mailer->setFrom($this->options["senderAddress"]) ;
				$mailer->addAddress($this->parseText($this->options["recipent"]));

				$mailer->isHTML();
				$mailer->Subject = $this->parseText($this->options["subject"]);
				$mailer->Body = $this->parseText($this->options["htmlBody"]);
				$mailer->AltBody = $this->parseText($this->options["altBody"]);

				$mailer->Send(); 
		}

		private function parseText(string $s) : string
		{
			  $result = $s;
        $formValues = $this->finisherContext->getFormValues();
        $keys = array_keys($formValues);
        foreach($keys as $key)
        {
          $result = str_replace("{".$key."}", $formValues[$key], $result);
        }
        return $result;
		}
}
