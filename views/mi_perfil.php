<?php
// espera que exista $user proveniente del controlador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Aruma Spa</title>
    <link href="../public/estilos/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/estilos/style.css">
</head>
<body>
<div class="container py-5">
    <h2>Mi Perfil</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <?php
                    $avatar = $user['avatar'] ?? '/public/img/profiles/default.png';
                    ?>
                    <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="img-fluid rounded-circle mb-3" style="width:150px;height:150px;object-fit:cover;">
                    <h5 class="card-title"><?= htmlspecialchars($user['nombre'] ?? '') ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <form action="mi-perfil/update" method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($user['nombre'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($user['telefono'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($user['fecha_nacimiento'] ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($user['direccion'] ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Foto de Perfil</label>
                        <input type="file" name="avatar" class="form-control">
                    </div>

                    <div class="col-12">
                        <hr>
                        <h5>Cambiar Contraseña</h5>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Contraseña Actual</label>
                        <input type="password" name="current_password" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" name="new_password" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Confirmar Contraseña</label>
                        <input type="password" name="confirm_password" class="form-control">
                    </div>

                    <div class="col-12 text-end">
                        <a href="/cliente" class="btn btn-outline-secondary me-2">Volver</a>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
