<?php  
require_once("OlxRestApi.php");
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
class OlxApi extends OlxRestApi {  
    private $_method;  
    private $_argumentos;  
    public function __construct() {  
        parent::__construct();    
    }
    private function generateToken(){
        if(isset($this->PetitionData['admin_nick']) && isset($this->PetitionData['admin_pass'])){
            $this->PetitionData['admin_nick'];
            $this->PetitionData['admin_pass'];      
        }

        $key = 'OlxTokenKey';
        $time = time();
        $settings = parse_ini_file("settings.ini.php");
        if($settings['USER_LOGIN'] == 'parameters'){
            if($this->PetitionData['admin_nick'] == 'Aram' && $this->PetitionData['admin_pass'] == 'OlxPass'){
                $token = array(
                'init_time' => $time,
                'expirate_token' => (int) $time + (60*60*24*365), // 1 año para pruebas
                'data' => [ 
                    'id' => $admin_id,
                    'name' =>  $this->PetitionData['admin_nick']
                ]
                );
                $jwt = JWT::encode($token, $key);

                $data = JWT::decode($jwt, $key, array('HS256'));
                $response['respuesta'] = 'TOKEN GENERADO';  
                $response['token'] = $jwt;  
                $this->loadResponse($this->encodingData($response), 200);                          
            }
            else{
                $this->loadResponse($this->encodingData($this->messaggeResponse(14)), 401);
            }
        }
        elseif($settings['USER_LOGIN'] == 'database'){
            $login = $this->login($this->PetitionData['admin_nick'],$this->PetitionData['admin_pass']);
            if($login == true){
                $token = array(
                    'init_time' => $time,
                    'expirate_token' => $time + (60*60*24), // 1 hora
                    'data' => [ 
                    'id' => $admin_id,
                    'name' =>  $this->PetitionData['admin_nick']
                ]
                );
                $jwt = JWT::encode($token, $key);

                $data = JWT::decode($jwt, $key, array('HS256'));
                $response['respuesta'] = 'TOKEN GENERADO';  
                $response['token'] = $jwt;  
                $this->loadResponse($this->encodingData($response), 200);                          
            }
            else{
                $this->loadResponse($this->encodingData($this->messaggeResponse(14)), 401);
            }
        }
        $this->loadResponse($this->encodingData($this->messaggeResponse(14)), 401);
    }
    private function login($admin_nick,$admin_pass) {   
        $sql = "SELECT 
                admin_id
              FROM 
                admins
              WHERE admin_nick = '$admin_nick' AND 
                    admin_pass = md5('$admin_pass')";
        $user = $this->query($sql);;
        if (count($user) > 0) {  
        return true;  
        }
        else{
        return false;
        }     

    }
    private function verifyToken(){
        if(isset($this->PetitionData['token'])){
            try {
            
                $key = 'OlxTokenKey';
                $data = JWT::decode($this->PetitionData['token'], $key, array('HS256')); 
                $data = json_decode(json_encode($data),true);
                $time = time();
                if($time <= $data['expirate_token']){
                    $autentication = true;
                }
                else{
                    $autentication = false;
                }              
                return $autentication;
            }     
            catch (Exception $e) {
              echo $e;
                return false;
            }
        }

    }  
    private function addPicture($user_id){
        if (isset($this->PetitionData['path'])) {
            $picture = $this->PetitionData['path'];
        } 
        //echo $user_id;
        $ch = curl_init();
        $path = $picture;
        $data = array('file' => '@'.$path.';type=image/jpg');
        curl_setopt($ch, CURLOPT_URL, 'https://api.olx.com/v1.0/users/images');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // requerido a partir de PHP 5.6.0
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt(CURLOPT_HTTPHEADER,'Content-type: application/json');
        $response = curl_exec($ch); 
        //decoddifico el json y lo convierto a array
        $arrData = json_decode($response,true);
        //var_dump($arrData);
        $picture_url = $arrData['url'];
        if (!empty($picture_url) and $user_id > 0) {  
            $sql = "UPDATE users SET picture = :p WHERE user_id = :user_id";
            $update = $this->query($sql,array("p"=>"$picture_url","user_id"=>$user_id));
            if ($update == 1) {  
                $this->loadResponse($this->encodingData($this->messaggeResponse(11)), 200); 
            } 
            else {  
                $this->loadResponse($this->encodingData($this->messaggeResponse(12)), 304);  
            } 
        }            
          $this->loadResponse($this->encodingData($this->messaggeResponse(13)), 400);              
    }
private function editUser($user_id) {  
    if ($_SERVER['REQUEST_METHOD'] != "PUT" && $_SERVER['REQUEST_METHOD'] != "POST") {  
        $this->loadResponse($this->encodingData($this->messaggeResponse(1)), 405);  
    }
    if(empty($this->PetitionData['name'])){
        $this->loadResponse($this->encodingData($this->messaggeResponse(9)), 400);     
    }            
    if (isset($this->PetitionData['name'])) {  
        $name = $this->PetitionData['name'];
        $address = $this->PetitionData['address'];
        $picture = $this->PetitionData['picture'];  
        $user_id = (int) $user_id;  
    if (!empty($name) and $user_id > 0) {  
        $sql = "UPDATE users SET name = :n, address = :a, picture = :p WHERE user_id = :user_id";
        $update = $this->query($sql,array("n"=>"$name","a"=>"$address","p"=>"$picture","user_id"=>$user_id));
        //echo $update;            
        if ($update == 1) {    
            $this->loadResponse($this->encodingData($this->messaggeResponse(15)), 200);  
        } 
        else {  
            $this->loadResponse($this->encodingData($this->messaggeResponse(5)), 400);  
        }  
    }  
}  
 $this->loadResponse($this->encodingData($this->messaggeResponse(5)), 400);  
} 
    private function deleteUser($user_id) {  
        if ($_SERVER['REQUEST_METHOD'] != "DELETE" && $_SERVER['REQUEST_METHOD'] != "POST") {  
            $this->loadResponse($this->encodingData($this->messaggeResponse(1)), 405);  
        }  
        $user_id = (int) $user_id;  
        if ($user_id >= 0) {  
            $sql = "DELETE FROM users WHERE user_id = :user_id";
            $delete = $this->query($sql,array("user_id"=>$user_id)); ;  
            if ($delete == 1) {   
                $this->loadResponse($this->encodingData($this->messaggeResponse(10)), 200);  
            } else {  
                $this->loadResponse($this->encodingData($this->messaggeResponse(4)), 400);  
            }  
        }  
        $this->loadResponse($this->encodingData($this->messaggeResponse(4)), 400);  
    }           
    private function messaggeResponse($id) {  
        $errores = array(  
            array('state_response' => "ERROR", "msg" => "PETICION NO ENCONTRADA"),  
            array('state_response' => "ERROR", "msg" => "PETICIÓN NO ACEPTADA"),  
            array('state_response' => "OK",    "msg" => "SIN RESULTADOS"),  
            array('state_response' => "ERROR", "msg" => "NICK O PASSWORD INCORRECTOS"),  
            array('state_response' => "ERROR", "msg" => "NO SE BORRO EL USUARIO"),  
            array('state_response' => "ERROR", "msg" => "USUARIO NO ACTUALIZADO"),  
            array('state_response' => "ERROR", "msg" => "ERROR BUSCANDO USUARIO"),  
            array('state_response' => "ERROR", "msg" => "ERROR CREANDO USUARIO"),  
            array('state_response' => "ERROR", "msg" => "EL USUARIO YA EXISTE"),
            array('state_response' => "ERROR", "msg" => "EL NOMBRE NO PUEDE SER NULO"),
            array('state_response' => "OK",    "msg" => "USUARIO BORRADO"),
            array('state_response' => "OK",    "msg" => "IMAGEN SUBIDA"),
            array('state_response' => "ERROR", "msg" => "LA IMAGEN SE SUBIO PERO OCURRIO UN ERROR AL ASIGNARSELA AL USUARIO."),
            array('state_response' => "ERROR", "msg" => "OCURRIO UN ERROR AL SUBIR LA IMAGEN"),
            array('state_response' => "ERROR", "msg" => "USUARIO NO AUTENTICADO"),
            array('state_response' => "OK",    "msg" => "USUARIO ACTUALIZADO")   
        );  
        return $errores[$id];  
    }  
    public function processPetition() {
        if(($_SERVER['REQUEST_METHOD'] == 'PUT' OR $_SERVER['REQUEST_METHOD'] == 'DELETE') && isset($this->PetitionData['token'])){
        //esto es por que en PUT o DELETE no vienen por REQUEST los parametros sino que ya los tengo cargados en PETITION data gracias al metodo processData
        $_REQUEST['token'] = $this->PetitionData['token'];
        }
        if (!isset($_REQUEST['token']) && $_REQUEST['url'] != "generateToken") {
            $this->loadResponse($this->encodingData($this->messaggeResponse(14)), 401);
        } 
        if (isset($_REQUEST['url'])) { 
            $autentication = $this->verifyToken($_REQUEST['token']);
        if ($autentication == false  && $_REQUEST['url'] != "generateToken") {
            $this->loadResponse($this->encodingData($this->messaggeResponse(14)), 401);
        }
        $url = explode('/', trim($_REQUEST['url'])); 
        $url = array_filter($url);
        $this->_method = strtolower(array_shift($url));  
        $this->_argumentos = $url;  
        $func = $this->_method;  
        if ((int) method_exists($this, $func) > 0) {  
            if (count($this->_argumentos) > 0) {  
               call_user_func_array(array($this, $this->_method), $this->_argumentos);  
             } else {  
               call_user_func(array($this, $this->_method));  
             }  
        }  
        else  
            $this->loadResponse($this->encodingData($this->messaggeResponse(0)), 404);  
        }  
        $this->loadResponse($this->encodingData($this->messaggeResponse(0)), 404);  
    }    
    private function encodingData($data) {  
        return json_encode($data);  
    }  

    private function allUsers() { 
        if ($_SERVER['REQUEST_METHOD'] != "GET" AND $_SERVER['REQUEST_METHOD'] != "POST") {  
            $this->loadResponse($this->encodingData($this->messaggeResponse(1)), 405);  
        }  
        $sql = "SELECT user_id, name, address FROM users";
        $Users = $this->query($sql);
        $UsersCant = $this->query($sql, null, PDO::FETCH_NUM);
        if ($UsersCant > 0) {  
            $respuesta['respuesta'] = 'correcta';  
            $respuesta['users'] = $Users;  
            $this->loadResponse($this->encodingData($respuesta), 200);  
        }  
        $this->loadResponse($this->messaggeResponse(2), 204);  
    }     

    private function getUserById($user_id) { 
        if ($_SERVER['REQUEST_METHOD'] != "GET" AND $_SERVER['REQUEST_METHOD'] != "POST") {  
            $this->loadResponse($this->encodingData($this->messaggeResponse(1)), 405);  
        }
        $user_id = (int) $user_id;  
        $sql = "SELECT 
                user_id, 
                name, 
                address,
                picture 
              FROM 
                users
              WHERE user_id = $user_id;";
        $Users = $this->query($sql);
        if (count($Users) > 0) {  
            $respuesta['respuesta'] = 'OK';  
            $respuesta['users'] = $Users;  
            $this->loadResponse($this->encodingData($respuesta), 200);  
        }  
        $this->loadResponse($this->encodingData($this->messaggeResponse(2)), 404);  
    }
    private function getUsersByName($name) { 
        if ($_SERVER['REQUEST_METHOD'] != "GET" AND $_SERVER['REQUEST_METHOD'] != "POST") {    
            $this->loadResponse($this->encodingData($this->messaggeResponse(1)), 405);  
        }  
        $sql = "SELECT 
                user_id, 
                name, 
                address,
                picture 
              FROM 
                users
              WHERE 
                LOWER(name) LIKE '%$name%' OR 
                UPPER(name) LIKE '%$name%' 
              LIMIT 100;";
        $Users = $this->query($sql);
        if (count($Users) > 0) {  
            $respuesta['state_response'] = 'OK';  
            $respuesta['users'] = $Users;  
            $this->loadResponse($this->encodingData($respuesta), 200);  
        }  
        $this->loadResponse($this->encodingData($this->messaggeResponse(2)), 404);  
    }   
    private function addUser() { 
        if ($_SERVER['REQUEST_METHOD'] != "POST") {  
            $this->loadResponse($this->encodingData($this->messaggeResponse(1)), 405);  
        }
        if(empty($this->PetitionData['name'])){
            $this->loadResponse($this->encodingData($this->messaggeResponse(9)), 400);     
        }  
        if (isset($this->PetitionData['name'], $this->PetitionData['address'], $this->PetitionData['picture'])) {  
            $name = $this->PetitionData['name'];  
            $picture = $this->PetitionData['picture'];  
            $address = $this->PetitionData['address'];  
            $sql = "INSERT INTO users (name,address,picture) VALUES (:name, :address, :picture)";
            $insert = $this->query($sql,array("name"=>"$name","address"=>"$address","picture"=>"$picture"));
            if ($insert == 1) {  
                $user_id = $this->lastInsertId();  
                $response['respuesta'] = 'correcto';  
                $response['msg'] = 'EL USUARIO SE CREO CORRECTAMENTE';  
                $response['user']['user_id'] = $user_id;  
                $response['user']['name'] = $name;  
                $response['user']['address'] = $address;
                $response['user']['user_url'] = "http://localhost/project/getUser/".$user_id;  
                $this->loadResponse($this->encodingData($response), 201);  
            }  
            else
            {  
                $this->loadResponse($this->encodingData($this->messaggeResponse(7)), 400);  
            }

        } 
        else 
        {  
            $this->loadResponse($this->encodingData($this->messaggeResponse(7)), 400);  
        }  
    }      
   
}  
  