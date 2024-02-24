<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{
    public static function login(Router $router){
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                $usuario  = Usuario::where('email', $auth->email);

                if ($usuario) {
                    // verificar el password
                    if($usuario->comprobarPasswordAndVerificar($auth->password)){
                        // autenticacion de usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . 
                        $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        if($usuario->admin === '1'){
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
                // debuguear($usuario);
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }
    
    public static function logout(){
        session_start();
        $_SESSION = [];
        header('Location: /');
    }
    
    public static function olvide(Router $router){
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario && $usuario->confirmado === "1") {
                    // generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    // enviar un email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    // alerta de exito
                    Usuario::setAlerta('exito', 'Revisa tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no se encuentra confirmado');
                    
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide__password', [
            'alertas' => $alertas
        ]);

    }
    
    public static function recuperar(Router $router){
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);
        // buscar usuario por su token
        $usuario = Usuario::where('token',$token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no valido');
            $error = true;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // LEER NUEVO PASSWORD
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if (empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /');
                }
                debuguear($usuario);
            }
        }
        // debuguear($usuario);
        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar__password', [
            'alertas' => $alertas,
            'error'=>$error
        ]);
    }

    public static function crear__cuenta(Router $router){
        $usuario = new Usuario($_POST);

        // alerts vacias
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $usuario->sincronizar($_POST);
                $alertas = $usuario->validarNuevaCuenta();

                // revisar que alert se encuentre vacio

                if (empty($alertas)) {
                    // verificar que el usuario no se encuentre registrado
                    $verificar = $usuario->existeUsuario();
                    
                    if ($verificar->num_rows) {
                        $alertas = Usuario::getAlertas();
                    }else {
                        // Hashear password
                        $usuario->hashPassword();
                        
                        // generar token unico
                        $usuario->crearToken();

                        // enviar email
                        $email = new Email($usuario->nombre, $usuario->email, 
                        $usuario->token);

                        $email->enviarConfirmacion();

                        // crear el usuario
                        $resultado = $usuario->guardar();

                        if ($resultado) {
                            header('Location: /mensaje');
                        }
                        // debuguear($usuario);

                    }

                }

        }
        $router->render('auth/crear__cuenta',[
            'usuario'=>$usuario,
            'alertas'=>$alertas,

        ]);

    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router){
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no valido');
        } else {
            // modificar a usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');

        }
        // obtener alertas
        $alertas = Usuario::getAlertas();
        // renderizar la vista
        $router->render('auth/confirmar__cuenta',[
            'alertas'=>$alertas
        ]);

    }
}
?>