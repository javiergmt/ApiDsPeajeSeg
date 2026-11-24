<?php

error_reporting(0);

class peajes
{

    public function peajeABM(string $accion, int $idPeaje, string $nombrePeaje, int $idCodPostal,
    int $tipoPeaje, float $latitud, float $longitud, int $rumbo) 
    {
    
        if (!$accion || !$nombrePeaje || !$idCodPostal || !$tipoPeaje || !$latitud || !$longitud || !$rumbo) {
            throw new Exception("Datos invalidos"); // si los datos no son válidos, se lanza una excepción
        }
        if (!valString($accion, 50) || !valString($nombrePeaje, 100)) {
            throw new Exception("Datos de string invalidos ");
        }
        $R = dbExecSP("dbo.spP_peajeABM", [
            "accion" => $accion,
            "idPeaje" => $idPeaje,
            "nombrePeaje" => $nombrePeaje,
            "idCodPostal" => $idCodPostal,
            "tipoPeaje" => $tipoPeaje,
            "latitud" => $latitud,
            "longitud" => $longitud,
            "rumbo" => $rumbo
       ]);

        if (!$R) {
           
            $R = [
                "mensaje" => "Peaje procesado correctamente."
            ];
        }

        return $R;
    }

    public function pasadaABM(string $accion,int $idPasada, int $idUsuario, int $idPeaje,
    float $precio,string $fecha, string $hora, string $patente, int $idCategoria) 
    {
    
        if (!$accion || !$idPasada || !$idUsuario || !$idPeaje || !$precio || !$fecha || !$hora || !$patente || !$idCategoria) {
            throw new Exception("Datos invalidos"); // si los datos no son válidos, se lanza una excepción
        }
        if (!valString($accion, 50) || !valString($fecha, 10) || !valString($hora, 8) || !valString($patente, 10)) {
            throw new Exception("Datos de string invalidos ");
        }

        $fecha = date('Y-m-d', strtotime($fecha));

        $R = dbExecSP("dbo.spP_pasadaABM", [
            "accion" => $accion,
            "idPasada" => $idPasada,
            "idUsuario" => $idUsuario,
            "idPeaje" => $idPeaje,
            "precio" => $precio,
            "fecha" => $fecha,
            "hora" => $hora,
            "patente" => $patente,
            "idCategoria" => $idCategoria
       ]);
       
        if (!$R Or $R['error']) {
           
            $R = [
                "mensaje" => "Pasada procesada correctamente."
            ];
        }

        return $R;
    }

    public function peajeBuscar(float $latitud, float $longitud, int $rumbo) 
    {
    
        if (!$latitud || !$longitud || !$rumbo) {
            throw new Exception("Datos invalidos"); // si los datos no son válidos, se lanza una excepción
        }
       
        $R = dbExecSP("dbo.spG_peajeBuscar", [
            "latitud" => $latitud,
            "longitud" => $longitud,
            "rumbo" => $rumbo
       ],TRUE);
         if (!$R) {
           
            $R = [
                "mensaje" => "Peaje No encontrado"
            ];
        }

        return $R;
    }

    function enviarMail( string $clienteEmail,string $clienteEmail2,string $clienteEmail3,string $clienteEmail4,string $clienteEmail5, string $asunto, string $cuerpo)
    {
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        // admin@restosoft.com.ar
        // Rest0.s0ft

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'mail.cooperativalaplata.com.ar'; // servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'comercios'; // usuario SMTP
            $mail->Password = 'Sebronad@3190'; // contraseña SMTP
            //$mail->SMTPSecure = "ssl";
            $mail->Port = 587; // O el puerto de servidor SMTP

            // Destinatarios
            $mail->setFrom('comercios@cooperativalaplata.com.ar', 'Cooperativa La Plata');
            $mail->addAddress($clienteEmail);
            if ($clienteEmail2 <> '') {
                $mail->addAddress($clienteEmail2);
            }
            if ($clienteEmail3 <> '') {
                $mail->addAddress($clienteEmail3);
            }
            if ($clienteEmail4 <> '') {
                $mail->addAddress($clienteEmail4);
            }
            if ($clienteEmail5 <> '') {
                $mail->addAddress($clienteEmail5);
            }
            //$mail->AddAttachment($pdf); 
            //$mail->addCC('otro_remitente@example.com');

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $cuerpo;

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->send();
            //echo 'El mensaje se ha enviado';
            return true;
        } catch (Exception $e) {
            //echo "El mensaje no pudo ser enviado. Error: {$mail->ErrorInfo}";
            return $e->getMessage();
        }
    }

}
