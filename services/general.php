<?php

error_reporting(0);

class general
{

    public function validarUsuario(string $usuarioEmail, string $clave)
    {
        if (!$usuarioEmail || !$clave) {
            throw new Exception("Invalid Data"); // si los datos no son válidos, se lanza una excepción
        }
        if ( !valString($usuarioEmail, 50) || !valString($clave, 50)) {
            throw new Exception("Datos de string invalidos ");
        }
             
        $R = dbExecSP("dbo.spG_validarUsuario", [
            "usuarioEmail" => $usuarioEmail,
            "clave" => md5($clave)
        ]);

       
        if (!$R) {
            throw new Exception("Error al validar usuario"); // si el SP no devuelve nada, se lanza una excepción generica
            $_SESSION['logged'] = FALSE; // Si la validación falla, marcar al usuario como no logueado
        } else {
            // Si la validación es exitosa, se inicia la sesión y se guardan los datos del usuario}
            session_regenerate_id(true); // Regenerar ID de sesión para prevenir ataques de fijación de sesión
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT']; // Guardar el user agent en la sesión 
            $_SESSION['usuarioEmail'] = $usuarioEmail; // Guardar el nombre de usuario en la sesión
            $_SESSION['permisos'] = ['TODOS']; // Ejemplo de permisos asignados al usuario
            $_SESSION['logged'] = TRUE; // Marcar al usuario como logueado
        }
        
        return $R;
    }

    
    public function usuarioClave(int $idUsuario, string $clave) 
    {
    
        if (!$idUsuario || !$clave) {
            throw new Exception("Invalid Data"); // si los datos no son válidos, se lanza una excepción
        }
        if ( !valString($clave, 50)) {
            throw new Exception("Datos de string invalidos ");
        }
        $R = dbExecSP("dbo.spP_usuarioClave", [
            "idUsuario" => $idUsuario,
            "clave" => md5($clave)
       ]);

        if (!$R) {
           
            $R = [
                "mensaje" => "Clave cambiada correctamente."
            ];
        }

        return $R;
    }
    
    public function logout()
    {
        // Limpiar la sesión actual
        $_SESSION = [];
        // Destruir la sesión para cerrar sesión
        session_destroy();
        
        // Aquí se podría agregar la lógica de cierre de sesión, como invalidar un token o limpiar la sesión
        return [
            "mensaje" => "Logout ok"
        ];
    }  

    //----------------------------------------------------------------------------------------------------

    private function ejecutarCurl(array $params, string $method) {
        $url = 'https://wse.cooperativalaplata.com.ar/sistemas/';
        $curl = curl_init();
        $fields = array(
            'service' => 'comercios',
            'method' => $method,
            'params' => $params              
        );
    
        $access_token = getToken();
        $json_string = json_encode($fields);
        curl_setopt($curl, CURLOPT_URL, $url);     
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token]);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
        $R = curl_exec($curl);
        if(curl_errno($curl))
        {
            //echo 'Error Curl : ' . curl_error($curl);
            throw new Exception("Error al enviar el mensaje con CURL". " - " . curl_error($curl));
        }
        curl_close($curl);
        if (!$DATA = @json_decode($R, TRUE)) {
            throw new Exception("Imposible obtener datos de respuesta");
        }
        
        return $DATA;
    }
   
   
   

}

?>