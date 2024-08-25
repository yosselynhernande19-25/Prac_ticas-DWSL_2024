<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practica 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row mt-4">
            <div class="col-6 m-auto">
            <form action="procesar.php" method="post">
                <div class="card">
                    <div class="card-header">
                        Conversor de medidas
                        </div>
                        <div class="card-body">
                            <div class="row">

                            <div class="col-12 mb-3">
                                    <label for="from">De</label>
                                    <select name="from" id="from" class="form-control">
                                    <option value="km">Kilómetro</option>
                                    <option value="hm">Héctometro</option>
                                    <option value="dam">Decámetro</option>
                                    <option value="m">Metro</option>
                                    <option value="dm">Decímetro</option>
                                    <option value="cm">Centrímetro</option>
                                    <option value="mm">Milímetro</option>
                                    </select>
                                </div> 


                                <div class="col-12 mb-3">
                                    <label for="cantidad">Cantidad</label>
                                    <input type="number" name="cantidad" id="cantidad" min="0" 
                                    class="form-control" placeholder="Ingrese la cantidad a convertir" required>
                                </div> 

                                <div class="col-12 mb-3">
                                    <label for="from">A</label>
                                    <select name="to" id="to" class="form-control">
                                    <option value="km">Kilómetro</option>
                                    <option value="hm">Héctometro</option>
                                    <option value="dam">Decámetro</option>
                                    <option value="m">Metro</option>
                                    <option value="dm">Decímetro</option>
                                    <option value="cm">Centrímetro</option>
                                    <option value="mm">Milímetro</option>
                                    </select>
                                </div> 
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary" type="submit">Procesar</button>
                            <button class="btn btn-secondary" type="reset">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>