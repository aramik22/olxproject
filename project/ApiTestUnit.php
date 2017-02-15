<?php 
Class ApiTestUnit extends PHPUnit_Framework_TestCase{
	private $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpbml0X3RpbWUiOjE0ODcwOTMzNzcsImV4cGlyYXRlX3Rva2VuIjoxNDg3MTc5Nzc3LCJkYXRhIjp7ImlkIjpudWxsLCJuYW1lIjoiQXJhbSJ9fQ.V2CddP7l8Cz-jiIq8tSymaZQyC_9VD3y4OwvEE77Bhw";
	private $address = "Test Unitario - Address";
	private $name = "Test Unitario - Name";
	private $picture = "unittest.png";
	private $user_id;
	private $path = "img/img.jpg";
	public function testGetUser(){
		$data = array('token' => "$this->token");
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, 'http://localhost/project/getUsersByname/aram');
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // requerido a partir de PHP 5.6.0
	    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
	    //curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    //curl_setopt(CURLOPT_HTTPHEADER,'Content-type: application/json');
	    $response = curl_exec($ch);
	    $arrData = json_decode($response,true);
	    //var_dump($arrData);
	    $this->assertEquals('Aram',$arrData['users'][0]['name']);
	}
	public function testGetUserById(){
		$data = array('token' => "$this->token");
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, 'http://localhost/project/getUserById/1');
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // requerido a partir de PHP 5.6.0
	    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
	    //curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    //curl_setopt(CURLOPT_HTTPHEADER,'Content-type: application/json');
	    $response = curl_exec($ch);
	    $arrData = json_decode($response,true);
	    $this->assertEquals('Aram',$arrData['users'][0]['name']);
	}	
	public function testAddUser(){
		$data = array('token' => "$this->token",'name'=>"$this->name",'address'=>"$this->address",'picture'=>"$this->picture");
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, 'http://localhost/project/addUser');
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // requerido a partir de PHP 5.6.0
	    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    $response = curl_exec($ch);
	    $arrData = json_decode($response,true);
	    $this->user_id = $arrData['user']['user_id'];
	    $this->assertObjectHasAttribute('user_id',$this);
	}
	public function testGetAllUsers(){
		$ch = curl_init();
		$data = array('token' => "$this->token");
	    curl_setopt($ch, CURLOPT_URL, 'http://localhost/project/allUsers');
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // requerido a partir de PHP 5.6.0
	    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    $response = curl_exec($ch);
	    $arrData = json_decode($response,true);
	    $usersCant = count($arrData['users']);
	    //echo $usersCant;
	    if($usersCant){
	    	$hasResult = 1;
	    }
	    $this->assertEquals(1,$hasResult);
	}
	public function testEditUser(){
		$data = array('token' => "$this->token",'name'=>"$this->name",'address'=>"$this->address",'picture'=>"$this->picture");
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, 'http://localhost/project/addUser');
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
	    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    $response = curl_exec($ch);
	    $arrData = json_decode($response,true);
	    $this->user_id = $arrData['user']['user_id'];		
		$data = array('token' => "$this->token",'name'=>"Test Update Name",'address'=>"$this->address",'picture'=>"$this->picture");
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "http://localhost/project/editUser/$this->user_id");
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
	    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    $response = curl_exec($ch);
	    $arrData = json_decode($response,true);
	    $status = $arrData['state_response'];	    
	    $this->assertEquals('OK',$status);
	}
	public function testDeleteUser(){
		$data = array('token' => "$this->token",'name'=>"$this->name",'address'=>"$this->address",'picture'=>"$this->picture");
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, 'http://localhost/project/addUser');
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
	    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    $response = curl_exec($ch);
	    $arrData = json_decode($response,true);
	    $this->user_id = $arrData['user']['user_id'];
		$data = array('token' => "$this->token");
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "http://localhost/project/deleteUser/$this->user_id");
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
	    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    $response = curl_exec($ch);
	    $arrData = json_decode($response,true);
	    $status = $arrData['state_response'];	    
	    $this->assertEquals('OK',$status);
	}
	public function testAddPicture(){
		$data = array('token' => "$this->token",'path'=>"$this->path");
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, 'http://localhost/project/addPicture/1');
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // requerido a partir de PHP 5.6.0
	    curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    $response = curl_exec($ch);
	    $arrData = json_decode($response,true);
	    $status = $arrData['state_response'];	    
	    $this->assertEquals('OK',$status);
	}						
}
?>
