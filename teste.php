<?php 
  include('config.php');

  $sqlVendedor = "SELECT vendedor FROM relatorio GROUP BY vendedor";
  $sqlVendedorRes = $conn->query($sqlVendedor);
  $row;
  while($row = $sqlVendedorRes->fetch_array()) {
    $vendedor = $row['vendedor'];
    $sqlVendas = "SELECT * FROM relatorio WHERE vendedor = '$vendedor'";
    $retorno = $conn->query($sqlVendas);
    echo 'Vendedor: '.$row['vendedor'];
    while($row2 = $retorno->fetch_array()) {
      
      echo "<p>Pedido " .$row2['pedido']."</p>";
    }
  }
?>
