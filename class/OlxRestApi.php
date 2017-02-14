 <?php 
 /**
 * Clase para una Api Rest para implementar el project para OLX
 *
 * @version     1.0
 * @author      Arám Kechichian
 */

// ------------------------------------------------------------------------
require_once("MysqlPdo.php");  
class OlxRestApi extends MysqlPdo{  
    public $type = "application/json";  
    public $PetitionData = array();  
    private $EstateCode = 200;  
    public function __construct() {  
    $this->processData();  
  }
private function getCodEstado() {  
    $arrStates = array(  
         200 => 'OK',  
         201 => 'CREATED',  
         202 => 'ACCEPTED',  
         204 => 'NO CONTENT',  
         301 => 'MOVED PERMANENTLY',  
         302 => 'FOUND',  
         303 => 'SEE OTHER',  
         304 => 'NOT MODIFIED',  
         400 => 'BAD REQUEST',  
         401 => 'UNAUTHORIZED',  
         403 => 'FORBIDDEN',  
         404 => 'NOT FOUND',  
         405 => 'METHOD NOT ALLOWED',  
         500 => 'INTERNAL SERVER ERROR');  
    $response = ($arrStates[$this->EstateCode]) ? $arrStates[$this->EstateCode] : $arrStates[500];  
    return $response;  
} 
// Este método recibirá los datos de respuesta en formato json y el estado Http, luego llama a SetHead para setear dicho estado
// El estado por defecto es 200    
    public function loadResponse($data, $state) {
        $this->EstateCode = ($state) ? $state : 200;
        header("HTTP/1.1 " . $this->EstateCode . " " . $this->getCodEstado());  
        header("Content-Type:" . $this->type . ';charset=utf-8'); 
        echo $data;  
        exit;  
    }  

    // Método para saneamiento de datos debido a magic_quotes_gpc
    private function cleanData($data) {  
        $entrada = array();  
        if (is_array($data)) {  
            foreach ($data as $key => $value) {  
            $entrada[$key] = $this->cleanData($value);  
            }  
        } 
        else {  
            if (get_magic_quotes_gpc()) {  
                $data = trim(stripslashes($data));  
            }  
            $data = strip_tags($data);  
            $data = htmlentities($data);  
            $entrada = trim($data);  
        }  
        return $entrada;  
    }  
    private function processData() { 
        $method = $_SERVER['REQUEST_METHOD'];  
        switch ($method) {  
        case "GET":  
            $this->PetitionData = $this->cleanData($_GET);  
           break;  
        case "POST":  
           $this->PetitionData = $this->cleanData($_POST);  
           break;  
        case "DELETE"://ejecuto el proximo case  
        case "PUT": 
           //para interpretar put o delete uso lo siguiente 
           parse_str(file_get_contents("php://input"), $this->PetitionData);  
           $this->PetitionData = $this->cleanData($this->PetitionData); 
           break;  
        default:  
           $this->response('', 404);  
           break;  
        }  
    }  
 
}  
 ?> 