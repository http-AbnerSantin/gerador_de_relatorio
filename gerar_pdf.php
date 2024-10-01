<?php

include('config.php');
date_default_timezone_set('America/Sao_Paulo');

$dataInicial = $_POST["dataInicial"];
$dataFinal = $_POST["dataFinal"];

//WHERE `data` BETWEEN '$dataInicial' AND '$dataFinal'
$sql = "SELECT * FROM relatorio WHERE `data` BETWEEN '$dataInicial' AND '$dataFinal'";
$res = $conn->query($sql);

//horario da emissão do relatório
$hora = date('H:i:s');


//condição caso o banco vanh vazio
if($res->num_rows > 0) {

  $html = "<table style='margin: 0 auto;'>";
  $html .= "<style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .header { font-size: 14px; font-weight: bold; border-bottom: 1px solid #000; }
        .footer { font-size: 10px; margin-top: 40px; border-top: 1px solid #000; padding-top: 10px; }
        .content { margin-top: 20px; }
        .content h2 { font-size: 16px; text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 2px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>";
  $html .= "<p>Vendas por Funcionario no periodo de $dataInicial ate $dataFinal</p>";
  $html .= "<p>Hora... $hora</p>";


  //vendedor
  $sqlVendedor = "SELECT vendedor FROM relatorio GROUP BY vendedor";

  $sqlVendedorRes = $conn->query($sqlVendedor);
  $linha;
  while($linha = $sqlVendedorRes->fetch_array()) {
    $vendedor = $linha['vendedor'];
    $html .= '<p>Vendedor: '.$linha['vendedor']. '</p>';
    $html .= "<tr>";
    $html .= "<th> DATA </th>";
    $html .= "<th> PEDIDO </th>";
    $html .= "<th> VALOR </th>";
    $html .= "</tr>";

    $sqlVendas = "SELECT * FROM relatorio WHERE vendedor = '$vendedor'";

    $retorno = $conn->query($sqlVendas);

    while($row2 = $retorno->fetch_array()) {
      $html .= "<tr>";
      $html .= "<td>".$row2['data']."</td>";
      $html .= "<td>".$row2['pedido']."</td>";
      $html .= "<td>".$row2['valor']."</td>";
      $html .= "</tr>";
    }
  }

  $html .= "</table>";

} else {
  $html .= "Nenhum dado encontrado";
}
// echo $html;
use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream();