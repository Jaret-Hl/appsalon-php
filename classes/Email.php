<?php

namespace Classes;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Email{
    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }
    public function enviarConfirmacion(){
        // try {
            // crear el objeto de email
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Port = $_ENV['EMAIL_PORT'];
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];

            $mail->setFrom('cuentas@appsalon.com');
            $mail->addAddress('jaretzlopez@outlook.com', 'AppSalon.com');
            $mail->Subject = 'Confirma tu cuenta';

            // Set HTML

            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre ." </strong> Has creado tu cuenta en App
            Salon, solo debes confirmala presionando el siguiente enlace.</p>";
            $contenido .="<p>Presiona aqui: <a href='" . $_ENV['APP_URL'] . "/confirmar__cuenta?token="
            .$this->token."' >Confirmar cuenta</a> </p>";
            $contenido .= "<p>Si tu no solicitaste esta cuenta, puede ignorar el mensaje.</p>";
            $contenido .= "</html>";

            $mail->Body = $contenido;

            $mail->send();
            // echo 'Message has been sent';
        // } catch (Exception $e) {
        //     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        // }
    }

    public function enviarInstrucciones(){
        $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Port = $_ENV['EMAIL_PORT'];
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];

            $mail->setFrom('cuentas@appsalon.com');
            $mail->addAddress('jaretzlopez@outlook.com', 'AppSalon.com');
            $mail->Subject = 'Reestable tu password';

            // Set HTML

            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre ." </strong>Has solicitado reestablecer tu 
            password,sigue los pasos para hacerlo.</p>";
            $contenido .="<p>Presiona aqui: <a href='" . $_ENV['APP_URL'] . "/recuperar?token="
            .$this->token."' >Reestablecer password</a> </p>";
            $contenido .= "<p>Si tu no solicitaste esta cuenta, puede ignorar el mensaje.</p>";
            $contenido .= "</html>";

            $mail->Body = $contenido;

            $mail->send();
    }
}

?>