<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
  </style>
  <title>Gerar Relatorio</title>
</head>
<body>
  <form action="gerar_pdf.php" method="POST">
    <label for="dataInicial">Data Inicial</label>
    <input type="date" name="dataInicial" id="">
    <label for="dataFinal">Data Final</label>
    <input type="date" name="dataFinal" id="">
    <input type="submit" value="Gerar Relatório">
  </form>
</body>
</html>