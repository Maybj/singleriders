<?php

class Usuario

{

  private $id;
  private $email;
  private $password;

  //registro de usuario
  public function registrar($datosuser,$imagenperfil){
      $datosuser['password'] = password_hash($datosuser['password'],PASSWORD_DEFAULT);
      $this->id = $this->obtenerUltimoId();
      $this->id ? $this->id = $this->id + 1 : $this->id = 1;
      $datosuser['id'] = $this->id;
      $ext = strtolower(pathinfo($imagenperfil['imgperfil']['name'], PATHINFO_EXTENSION));
      $hasta = '/images/profileImg/'.'perf'.$this->id.'.'.$ext;
      $datosuser['srcImagenperfil'] = $hasta;
      $userjson = json_encode($datosuser);
      file_put_contents('usuarios.json', $userjson . PHP_EOL, FILE_APPEND);
      $this->subirImgPerfil($imagenperfil,$this->id);

      $_SESSION['id'] = $this->id;

      header('location:home.php');
  }

  //subir imagen

  public function subirImgPerfil($imagen,$id){
    if ($imagen['imgperfil']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($imagen['imgperfil']['name'], PATHINFO_EXTENSION));
        if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg') {
            $hasta = dirname(dirname(__FILE__)) .'/images/profileImg/'.'perf'.$id.'.'.$ext ;
            $desde = $imagen['imgperfil']['tmp_name'];
            //$end = end(explode('/', $hasta));
            if (file_exists($hasta)) {
              //echo "<br>";
              //echo "<p>El archivo ya existe, no será subido</p>";
              move_uploaded_file($desde, $hasta);
            }else {
              move_uploaded_file($desde, $hasta);
            }

        }else {
            var_dump('extension invalida!');
        }
    }else {
        var_dump('error al subir');
    }
  }

  //obtener id del ultimo usuario
  public function obtenerUltimoId(){
    $usuarios = file_get_contents('usuarios.json');
    $arrUsuariosJSON = explode(PHP_EOL,$usuarios);
    $arrUsuarioPHP = [];
    array_pop($arrUsuariosJSON);
    foreach ($arrUsuariosJSON as $key => $usuario) {
        $arrUsuarioPHP[] = json_decode($usuario,true);
    }
    $ultimo = array_pop($arrUsuarioPHP);
    $id = $ultimo['id'];
    return $id;
  }

  //obtener usuarios (para registrar si no existe el mail o para loguear si son correctas las credenciales y el email)
  public function buscarUsuarios(){
    $usuarios = file_get_contents('usuarios.json');
    $arrUsuariosJSON = explode(PHP_EOL,$usuarios);
    $arrUsuarioPHP = [];
    array_pop($arrUsuariosJSON);
    foreach ($arrUsuariosJSON as $key => $usuario) {
        $arrUsuarioPHP[] = json_decode($usuario,true);
    }
    return $arrUsuarioPHP;
  }



  //obtener id de usuario
  public function obtenerId($id){
    $usuarios = $this->buscarUsuarios();
    $usuario = [];
    if (!empty($usuarios)) {
      foreach ($usuarios as $usuario) {
        //var_dump($usuario['id']);
        if ($id == $usuario['id']) {
          return $usuario;
        }
      }
    }
    return false;
  }


  //obtener un usuario
  public function buscarUsuario($email){
    $usuarios = $this->buscarUsuarios();
    $usuario = [];
    if (!empty($usuarios)) {
      foreach ($usuarios as $usuario) {
        if (strtolower($email) == strtolower($usuario['email'])) {
          return $usuario;
        }
      }
    }
    return false;
  }

  //actualizar perfil de usuario
  public function actualizarusuario($imagenperfil,$datosuser,$id){
    // NOTE: obtener los usuarios en usuarios.json, transformandolos en un array
    $usuarios = $this->buscarUsuarios();
    // NOTE: recorrer array de todos los usuarios y sobreescribir el registro que corresponda al usuario a editar
    if (!empty($usuarios)) {
      // NOTE: vacio el archivo usuarios.json para poder insertar las modificaciones sin duplicar al usuario
        file_put_contents('usuarios.json', '');
        foreach ($usuarios as $usuario) {
          // NOTE: si encuentro al usuario guardo las modificaciones
          if (strtolower($id) == strtolower($usuario['id'])) {
            $usuario['nombre'] = $datosuser['nombre'];
            $usuario['apellido'] = $datosuser['apellido'];
            $usuario['email'] = $datosuser['email'];
            if ($usuario['password'] != $datosuser['password']) {
              $usuario['password'] = password_hash($datosuser['password'],PASSWORD_DEFAULT);
            }
            if ($imagenperfil['imgperfil']['error'] === UPLOAD_ERR_OK) {
              $ext = strtolower(pathinfo($imagenperfil['imgperfil']['name'], PATHINFO_EXTENSION));
              $hasta = '/images/profileImg/'.'perf'.$id.'.'.$ext;
              $usuario['srcImagenperfil'] = $hasta;
          }
          $userjson = json_encode($usuario);
          file_put_contents('usuarios.json', $userjson . PHP_EOL, FILE_APPEND);
          $this->subirImgPerfil($imagenperfil,$id);
        }else {
          // NOTE: si no encuentro al usuario guardo a los demas sin cambios
          $userjson = json_encode($usuario);
          file_put_contents('usuarios.json', $userjson . PHP_EOL, FILE_APPEND);
        }
      }
    }
    // NOTE: volver a convertir a json y guardar archivo usuarios.json
    //$userjson = json_encode($usuarios);
    //file_put_contents('usuarios2.json', $userjson . PHP_EOL, FILE_APPEND);
    //subirImgPerfil($imagenperfil,$id);


  }

  //Todos los nombres en un array
  public function traerNombreDeUsuarios(){
    $todosLosUsuarios = $this->buscarUsuarios();
    $ultimoId = $this->obtenerUltimoId();
    $nombres = [];
    $nombres['usuarios'] = array_map(function($item){
      return $item['nombre'];
      }, $todosLosUsuarios);
      //unset($nombres['usuarios'][0]);
      return $nombres;
    }

    // asociar el nombre a una id
    public function nombreAsocId($nombre){
      $todosLosUsuarios = $this->buscarUsuarios();
      $datosDeUsuario = [];
      $idDelUsuario;
      foreach ($todosLosUsuarios as $usuario) {
        if($usuario['nombre'] == $nombre){
          $datosDeUsuario[] = $usuario;
        }
      }
      foreach ($datosDeUsuario as $dato) {
        $idDelUsuario = $dato['id'];
      }

      return $idDelUsuario;
    }


}







 ?>