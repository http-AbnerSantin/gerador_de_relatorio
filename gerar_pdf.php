<?php

include('config.php');
date_default_timezone_set('America/Sao_Paulo');

$dataInicial = $_POST["dataInicial"];
$dataFinal = $_POST["dataFinal"];

//WHERE `data` BETWEEN '$dataInicial' AND '$dataFinal'
$sql = "SELECT * FROM relatorio2 WHERE `data` BETWEEN '$dataInicial' AND '$dataFinal'";
// $sql = "SELECT * FROM relatorio2 WHERE `data` BETWEEN '2020-09-30' AND '2020-10-01'";

$res = $conn->query($sql);

//horario da emissão do relatório
$hora = date('H:i:s');
$empresa = "CANINANA & RIBEIRO LTDA";

$html="";

//condição caso o banco vanh vazio
if($res->num_rows > 0) {

  $html = "<style>
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
  //cabeçalho
  $html .= "<h3>$empresa</h3>";
  $html .= "<p>Vendas por Funcionario no periodo de $dataInicial ate $dataFinal</p>";
  $html .= "<p>Hora... $hora</p>";
  
  
  $html .= "<table style='margin: 0 auto;'>";
  //vendedor
  $sqlVendedor = "SELECT FUNCIONARIO FROM relatorio2 GROUP BY FUNCIONARIO";
  
  $sqlVendedorRes = $conn->query($sqlVendedor);
  
  while($linha = $sqlVendedorRes->fetch_array()) {
    $vendedor = $linha['FUNCIONARIO'];
    $html .= '<p>Vendedor: '.$vendedor. '</p>';
    $html .= "<tr>";
    $html .= "<th> DATA </th>";
    $html .= "<th> PEDIDO </th>";
    $html .= "<th> VALOR </th>";
    $html .= "<th> PERDE/GANHA </th>";
    $html .= "</tr>";

    $sqlVendas = "SELECT * FROM relatorio2 WHERE FUNCIONARIO = '$vendedor' AND `data` BETWEEN '$dataInicial' AND '$dataFinal'";
    // $sqlVendas = "SELECT * FROM relatorio2 WHERE FUNCIONARIO = '$vendedor' AND `data` BETWEEN '2020-09-30' AND '2020-10-01'";
    $retorno = $conn->query($sqlVendas);

  
    while($row2 = $retorno->fetch_array()) {
      $html .= "<tr>";
      $html .= "<td>".$row2['DATA']."</td>";
      $html .= "<td>".$row2['NUMERO']."</td>";
      $html .= "<td>".$row2['TOTAL']."</td>";
      $html .= "<td>".$row2['PERDE_GANHA']."</td>";
      $html .= "</tr>";
    }
  }
  $sqlSomaTotal = "SELECT SUM(TOTAL) FROM relatorio2 WHERE `DATA` BETWEEN '$dataInicial' AND '$dataFinal'";
  // $sqlSomaTotal = "SELECT SUM(TOTAL) FROM relatorio2 WHERE `DATA` BETWEEN '2020-09-30' AND '2020-10-01'";
  $sqlSomaTotalRes = $conn->query($sqlSomaTotal);

  $row3 = $sqlSomaTotalRes->fetch_array();
    $html .= "<tr>";
    $html .= "<th> Total Geral </th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .="<td>R$".$row3[0]."</td>";
    $html .="</tr>";
  
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

$dompdf->stream('relatorio.pdf', array("Attachment" => false));