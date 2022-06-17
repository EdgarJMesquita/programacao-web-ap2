<?php
  // Configurando tipo da resposta
  header("Content-type: application/json; charset=utf-8");
  
  // Validação de rota
  if($_SERVER['REQUEST_METHOD']!="PUT"){
    http_response_code(400);
    die(json_encode(array("message"=>"Rota não encontrada")));
  }
  
  // Validação dos campos do body
  $json = file_get_contents('php://input');
  $data = json_decode($json,true);
  
  if(!isset($data["id_car"])){
    http_response_code(422);
    die(json_encode(array("message"=>"Carro é obrigatório.")));
  }
 
  // Tratamento dos campos
  $id_car = $data["id_car"];

  // Conexão com o banco
  $host='localhost';
  $user = 'edgarsilva';
  $password = 'G3C9M2P8c7tL5';
  $database = 'locadora';
  $connection = mysqli_connect($host,$user,$password,$database) or die('Não foi possível se conectar com o banco de dados:'.mysqli_connect_error());

  // Mudança no status do carro
  $changeStatusToAvailableQuery = "UPDATE cars SET status='available' WHERE id=$id_car";
  $result = mysqli_query($connection,$changeStatusToAvailableQuery);

  // Encerramento da conexão.
  $connection->close();

  if($result){
    http_response_code(201);
    die(json_encode(array("message"=>"Atualizado com sucesso!")));
  }

  // Resposta ao cliente
  ob_clean();
  http_response_code(404);
  die(json_encode(array("message"=>"Registro não encontrado")));
?>