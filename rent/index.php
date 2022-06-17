<?php
  // Validação de rota
  if($_SERVER['REQUEST_METHOD']!="POST"){
    http_response_code(400);
    die(json_encode(array("message"=>"Nao encontrada")));
  }

  // Configurando tipo da resposta
  header("Content-type: application/json; charset=utf-8");
  
  // Validação dos campos do body
  $json = file_get_contents('php://input');
  $data = json_decode($json,true);
  if(!isset($data["name"])){
    http_response_code(422);
    die(json_encode(array("message"=>"Nome é obrigatório.")));
  }
  if(!isset($data["started_at"])){
    http_response_code(422);
    die(json_encode(array("message"=>"Data de início é obrigatório.")));
  }
  if(!isset($data["finished_at"])){
    http_response_code(422);
    die(json_encode(array("message"=>"Data de termino é obrigatório.")));
  }
  if(!isset($data["id_car"])){
    http_response_code(422);
    die(json_encode(array("message"=>"Carro é obrigatório.")));
  }
 
  // Tratamento dos campos
  $name = $data["name"];
  $started_at = date("Y-m-d\TH:i:s",strtotime($data["started_at"]));
  $finished_at = date("Y-m-d\TH:i:s",strtotime($data["finished_at"]));
  $id_car = $data["id_car"];

  // Conexão com o banco
  $host='localhost';
  $user = 'edgarsilva';
  $password = 'G3C9M2P8c7tL5';
  $database = 'locadora';
  $connection = mysqli_connect($host,$user,$password,$database) or die('Não foi possível se conectar com o banco de dados:'.mysqli_connect_error());
  
  // Validação da disponibilidade do carro
  $isCarAvailableQuery = "SELECT status from cars WHERE id=$id_car";
  $isCarAvailableResult = mysqli_query($connection, $isCarAvailableQuery);
  $carStatus = "";
  if (mysqli_num_rows($isCarAvailableResult) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($isCarAvailableResult)) {
      $carStatus = $row['status'];
    }
  } else {
    http_response_code(404);
    die(json_encode(array("message"=>"Carro não encontrado.")));
  }
  if($carStatus!=="available"){
    http_response_code(422);
    die(json_encode(array("message"=>"Carro não está disponível.")));
  }

  // Inserção dos dados
  $insertNewSaleQuery = "INSERT INTO sales (name,started_at, finished_at, id_car) values('$name','$started_at','$finished_at', $id_car)";
  $insertNewSaleResult = mysqli_query($connection,$insertNewSaleQuery);

  // Tratamento de erro
  if(!$insertNewSaleResult){
    http_response_code(422);
    die(json_encode(array("message"=>"ocorreu um erro desconhecido")));
  }

  // Mudança no status do carro
  $changeStatusQuery = "UPDATE cars SET status='rented' WHERE id=$id_car";
  $changeStatusResult =mysqli_query($connection,$changeStatusQuery);

  // Tratamento de erro
  if(!$insertNewSaleResult){
    http_response_code(422);
    die(json_encode(array("message"=>"ocorreu um erro desconhecido")));
  }

  // Encerramento da conexão.
  $connection->close();

  // Resposta ao cliente
  ob_clean();
  http_response_code(201);
  die(json_encode(array("message"=>"Carro alugado com sucesso.")));
?>
