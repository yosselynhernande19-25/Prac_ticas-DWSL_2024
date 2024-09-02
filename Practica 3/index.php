<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Practica 3</title>

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card mt-5">
                    <div class="card-header">
                        Promedios
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <form action="index.php" method="get">
                                    <div class="row">
                                        <div class="col-7 mb-3">
                                            <label class="form-label" for="numero_estudiantes">Numero de
                                                Estudiantes</label>

                                            <div class="input-group">
                                                <input type="number" name="numero_estudiantes" min="1" step="1"
                                                    class="form-control" placeholder="Ingresa el numero de estudiantes"
                                                    value="<?= (isset($_GET["numero_estudiantes"]) ? $_GET["numero_estudiantes"] : 1) ?>">

                                                <button type="submit" class="btn btn-primary">Generar Computo</button>

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row">

                            <?php
                            if (isset($_GET["numero_estudiantes"])) {
                                ?>
                                <form action="index.php" method="post">
                                    <div class="col-12">
                                        <table class="table table-bordered table-stripped">
                                            <thead>
                                                <tr>
                                                    <th>N#</th>
                                                    <th class="col-8">Nombre</th>
                                                    <th>Laboratorio</th>
                                                    <th>Parcial</th>

                                                </tr>
                                            </thead>

                                            <tbody>

                                                <?php
                                                $rows = $_GET["numero_estudiantes"];
                                                $row = 1;

                                                while ($row <= $rows) {

                                                    ?>
                                                    <tr>
                                                        <td><?= $row ?></td>
                                                        <td>
                                                            <input type="text" name="estudiante[]" id="" class="form-control"
                                                                placeholder="Ingresa el nombre del estudiante" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="laboratorio[]" id="" class="form-control"
                                                                placeholder="0.00" min="0" max="10" step="0.01" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="parcial[]" id="" class="form-control"
                                                                placeholder="0.00" min="0" max="10" step="0.01" required>
                                                        </td>

                                                    </tr>

                                                    <?php
                                                    $row++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-2">
                                        <button class="btn btn-success" type="submit">Enviar Notas</button>
                                    </div>
                                </form>


                                <?php
                            }
                            ?>

                        </div>



                        <div class="row">

                            <?php
                            if (isset($_POST["estudiante"])) {
                                ?>
                                <form action="index.php" method="post">
                                    <div class="col-12">
                                        <table class="table table-bordered table-stripped">
                                            <thead>
                                                <tr>
                                                    <th>N#</th>
                                                    <th class="col-8">Nombre</th>
                                                    <th>Laboratorio</th>
                                                    <th>Parcial</th>
                                                    <th>Promedio</th>

                                                </tr>
                                            </thead>

                                            <tbody>

                                                <?php
                                                $rows = count($_POST["estudiante"]);
                                                $row = 0;

                                                while ($row < $rows) {

                                                    $nombre = $_POST["estudiante"][$row];
                                                    $laboratorio = $_POST["laboratorio"][$row] ?? 0;
                                                    $parcial = $_POST["parcial"][$row] ?? 0;
                                                    $promedio = ($laboratorio * 0.6) + ($parcial * 0.4);


                                                    ?>
                                                    <tr>
                                                        <td><?= ($row + 1) ?></td>
                                                        <td>
                                                            <?= $nombre ?>
                                                        </td>
                                                        <td>
                                                            <?= round($laboratorio,2) ?>
                                                        </td>
                                                        <td>
                                                            <?= round($parcial,2) ?>
                                                        </td>

                                                        <td>
                                                            <?= round($promedio,2)?>
                                                        </td>

                                                    </tr>

                                                    <?php
                                                    $row++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>


                                <?php
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>