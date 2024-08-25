<?php
    if (!isset($_POST["from"]) || !isset($_POST["cantidad"])) {
        header('Location: index.php');
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row mt-4">
            <div class="col-6 m-auto">
                <div class="alert alert-success">
                    <h6 class="alert-heading">Resultados</h6>

                    <?php
                    $from = $_POST["from"] ?? 'km';
                    $cantidad = $_POST["cantidad"] ?? 0;
                    $to = $_POST["to"] ?? 'm';

                    $unidades = [
                        'km'   => 1000,
                        'hm'   => 100,
                        'dam'  => 10,
                        'm'    => 1,
                        'dm'   => 0.1,
                        'cm'   => 0.01,
                        'mm'   => 0.001, 
                    ];

                    $helper = [
                        'km'   => "Kilómetros",
                        'hm'   => "Hectómetro",
                        'dam'  => "Decámetro",
                        'm'    => "Metro",
                        'dm'   => "Decímetro",
                        'cm'   => "Centímetro",
                        'mm'   => "Milímetro", 
                    ];

                    $metros = $cantidad * $unidades[$from];
                    $conversion = $metros / $unidades[$to];
                    $unidad_helper = $helper[$to];

                    echo "
                        <p>
                            El resultado de convertir {$cantidad} {$from} a {$unidad_helper} es {$conversion} {$to}
                        </p>
                    ";
                ?>

                    <a class="alert-link" href="index.php">Regresar</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
