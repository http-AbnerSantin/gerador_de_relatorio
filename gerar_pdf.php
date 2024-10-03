<?php

include('config.php');
date_default_timezone_set('America/Sao_Paulo');

//pegando as datas
$dataInicial = $_POST["dataInicial"];
$dataFinal = $_POST["dataFinal"];

//Data no formato DD-MM-YYYY
$dataInicialConverter = $_POST['dataInicial'];
$dataFinalConverter = $_POST['dataFinal'];

$data_inicial_formatada = DateTime::createFromFormat('Y-m-d', $dataInicialConverter)->format('d/m/Y');
$data_final_formatada = DateTime::createFromFormat('Y-m-d', $dataFinalConverter)->format('d/m/Y');

//selecionando o relatorio de acordo com as datas definidas
$sql = "SELECT * FROM relatorio2 WHERE `data` BETWEEN '$dataInicial' AND '$dataFinal'";
$res = $conn->query($sql);

//horario da emissão do relatório
$hora = date('H:i:s');
$empresa = "CANINANA & RIBEIRO LTDA";
$dataDeEmissao = date('d/m/Y');
$dataEmissaoParaONomeDoArquivo= date('d-m-Y');
$html="";

//condição caso o banco venha vazio
if($res->num_rows > 0) {

  $html = "<style>
  body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
  table { width: 100%;  margin-bottom: 20px; }
  th, td { padding: 3px;  text-align: left; }
  th { background-color: #f2f2f2; font-weight: bold; }
  </style>";
  //cabeçalho
  $html .= "
    <h3>$empresa</h3>
    <p>Vendas por Funcionario no periodo de $data_inicial_formatada ate $data_final_formatada</p>
    <p>Data Emitida $dataDeEmissao</p>
    <p>Hora... $hora</p>
    ";
  
  
  $html .= "<table style='margin: 0 auto;'>";
  //vendedor
  $sqlVendedor = "SELECT FUNCIONARIO FROM relatorio2 GROUP BY FUNCIONARIO";
  
  $sqlVendedorRes = $conn->query($sqlVendedor);

  
  while($linha = $sqlVendedorRes->fetch_array()) {
    $vendedor = $linha['FUNCIONARIO'];
    $html .= "
      <p>Vendedor: $vendedor</p>
      <tr>
        <th> DATA </th>
        <th> PEDIDO </th>
        <th> VALOR </th>
        <th> PERDE/GANHA </th>
      </tr>
    ";

    $sqlVendas = "SELECT * FROM relatorio2 WHERE FUNCIONARIO = '$vendedor' AND `data` BETWEEN '$dataInicial' AND '$dataFinal'";
    $retorno = $conn->query($sqlVendas);

  
    while($row2 = $retorno->fetch_array()) {

      $data_correta = DateTime::createFromFormat('Y-m-d', $row2['DATA'])->format('d/m/Y');

      $html .= "
      <tr>
        <td>".$data_correta."</td>
        <td>".$row2['NUMERO']."</td>
        <td>".$row2['TOTAL']."</td>
        <td>".$row2['PERDE_GANHA']."</td>
      </tr>
      ";

    }
    $sqlTotalPorVendedor = "SELECT SUM(TOTAL), SUM(PERDE_GANHA) FROM relatorio2 WHERE FUNCIONARIO = '$vendedor' AND `data` BETWEEN '$dataInicial' AND '$dataFinal'";
    $sqlTotalPorVendedorRes = $conn->query($sqlTotalPorVendedor);
    
    $resposta = $sqlTotalPorVendedorRes->fetch_array();

    $html .= "
    <tr>
      <td colspan='2' > <span style='font-weight: bold;'>TOTAL DE VENDAS: </span> R$ ".$resposta[0]."</td>
      <td colspan='2' > <span style='font-weight: bold;'>PERDE/GANHA: </span> R$ ".$resposta[1]."</td>
    </tr>
    ";
  }

  //VALOR TOTAL DE VENDAS DO RELATORIO
  $sqlSomaTotal = "SELECT SUM(TOTAL), SUM(PERDE_GANHA) FROM relatorio2 WHERE `DATA` BETWEEN '$dataInicial' AND '$dataFinal'";
  $sqlSomaTotalRes = $conn->query($sqlSomaTotal);

  $row3 = $sqlSomaTotalRes->fetch_array();
    $html .= "<tr>
    <th colspan='2'> TOTAL GERAL </th>
    <th colspan='2'> PERDE/GANHA </th>
    </tr>
    <td colspan='2'>R$".$row3[0]."</td>
    <td colspan='2'>R$".$row3[1]."</td>
    </tr>
    ";
  
  $html .= "</table>";

} else {
  $html .= "Nenhum dado encontrado";
}


use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream('relatorio'.$dataEmissaoParaONomeDoArquivo.'.pdf', array("Attachment" => false));