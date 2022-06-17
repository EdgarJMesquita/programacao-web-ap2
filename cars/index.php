<?php
  $host='localhost';
  $user = 'edgarsilva';
  $password = 'G3C9M2P8c7tL5';
  $database = 'locadora';

  // Iniciar conexão
  $connection = mysqli_connect($host,$user,$password,$database) or die('Não foi possível se conectar com o banco de dados:'.mysqli_connect_error());
  
  // Tratamento dos filtro de status
  $filter = "";
  if(isset($_REQUEST["status"])){
    $status = $_REQUEST["status"];
    $filter = " WHERE status='$status'";
  }

  // Requisição ao banco
  $query = "SELECT * FROM cars".$filter;
  $result = mysqli_query($connection,$query);
  
  // Tratamento dos dados do banco
  $data = array();
  while($reg=mysqli_fetch_assoc($result)){
    $data[] = $reg;
  }
  
  // Encerrar conexão
  $connection->close();

  // Resposta ao cliente
  ob_clean();
  header("Content-type: application/json; charset=utf-8");
  http_response_code(200);
  die(json_encode($data));
?>