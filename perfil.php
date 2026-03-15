<?php
session_start();
require 'database.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje_exito = '';
$mensaje_error = '';

// Función para validar email
function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Cargar datos usuario actual
$stmt = $pdo->prepare("SELECT id, nombre, email, telefono, direccion, fecha_nacimiento, genero, foto_perfil, rol FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit;
}

// Procesar formulario de actualización datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar datos personales
    if (isset($_POST['actualizar_datos'])) {
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion']);
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?: null;
        $genero = $_POST['genero'];

        if (!$nombre || !$email) {
            $mensaje_error = "El nombre y correo son obligatorios.";
        } elseif (!validar_email($email)) {
            $mensaje_error = "Correo electrónico inválido.";
        } else {
            // Si cambia el email, podría enviar confirmación (simulado)
            $cambio_email = $email !== $usuario['email'];

            $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, telefono=?, direccion=?, fecha_nacimiento=?, genero=? WHERE id=?");
            $stmt->execute([$nombre, $email, $telefono, $direccion, $fecha_nacimiento, $genero, $usuario_id]);

            $mensaje_exito = "Datos actualizados correctamente.";
            if ($cambio_email) {
                $mensaje_exito .= " Se ha enviado un correo de confirmación a $email.";
                // Aquí se implementaría el envío real de correo
            }
            // Refrescar datos
            header("Location: perfil.php?success=1");
            exit;
        }
    }

    // Cambiar contraseña
    if (isset($_POST['cambiar_password'])) {
        $password_actual = $_POST['password_actual'] ?? '';
        $password_nuevo = $_POST['password_nuevo'] ?? '';
        $password_confirmar = $_POST['password_confirmar'] ?? '';

        if (!$password_actual || !$password_nuevo || !$password_confirmar) {
            $mensaje_error = "Todos los campos de contraseña son obligatorios.";
        } elseif ($password_nuevo !== $password_confirmar) {
            $mensaje_error = "La nueva contraseña y la confirmación no coinciden.";
        } elseif (strlen($password_nuevo) < 6) {
            $mensaje_error = "La contraseña debe tener al menos 6 caracteres.";
        } else {
            // Verificar contraseña actual
            $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->execute([$usuario_id]);
            $hash = $stmt->fetchColumn();

            if (!password_verify($password_actual, $hash)) {
                $mensaje_error = "La contraseña actual es incorrecta.";
            } else {
                // Actualizar contraseña
                $hash_nuevo = password_hash($password_nuevo, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                $stmt->execute([$hash_nuevo, $usuario_id]);

                $mensaje_exito = "Contraseña actualizada correctamente. Por seguridad, por favor vuelva a iniciar sesión.";
                header("Refresh:3; url=logout.php");
            }
        }
    }

    // Subir foto de perfil
    if (isset($_POST['subir_foto'])) {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $ext_permitidas = ['jpg','jpeg','png','gif'];
            $archivo = $_FILES['foto'];
            $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $ext_permitidas)) {
                $mensaje_error = "Formato de imagen no permitido. Use JPG, PNG o GIF.";
            } elseif ($archivo['size'] > 2 * 1024 * 1024) {
                $mensaje_error = "Archivo demasiado grande. Máximo 2MB.";
            } else {
                $nuevo_nombre = "uploads/perfil_{$usuario_id}." . $ext;
                if (!is_dir('uploads')) mkdir('uploads');
                move_uploaded_file($archivo['tmp_name'], $nuevo_nombre);

                $stmt = $pdo->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
                $stmt->execute([$nuevo_nombre, $usuario_id]);

                $mensaje_exito = "Foto de perfil actualizada.";
                header("Location: perfil.php?success=1");
                exit;
            }
        } else {
            $mensaje_error = "Error al subir la imagen.";
        }
    }
}

// Recargar usuario actualizado para mostrar en el formulario
$stmt = $pdo->prepare("SELECT id, nombre, email, telefono, direccion, fecha_nacimiento, genero, foto_perfil, rol FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener últimos accesos (últimos 5)
$stmt = $pdo->prepare("SELECT fecha_acceso FROM accesos_usuario WHERE usuario_id = ? ORDER BY fecha_acceso DESC LIMIT 5");
$stmt->execute([$usuario_id]);
$ultimos_accesos = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Obtener libros prestados activos (ajustado para debug)
$stmt = $pdo->prepare("
    SELECT libros.titulo, prestamos.fecha_prestamo, prestamos.fecha_devolucion, prestamos.estado
    FROM prestamos 
    JOIN libros ON prestamos.libro_id = libros.id
    WHERE prestamos.usuario_id = ?
");
$stmt->execute([$usuario_id]);
$libros_prestados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// DEBUG: Mostrar cuántos libros se encontraron
// Puedes comentar esta línea una vez verificado
// var_dump($libros_prestados);

if (count($libros_prestados) === 0) {
    $mensaje_error .= " No tienes libros prestados actualmente o el estado no coincide.";
}


// Preferencias de usuario - para demo, guardamos modo oscuro en localStorage solo

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Perfil de Usuario - Biblioteca Super Max</title>

<!-- Fuentes -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #eee;
    margin: 0; padding: 0;
    min-height: 100vh;
  }
  .container {
    max-width: 900px;
    margin: 2rem auto;
    background: rgba(255 255 255 / 0.1);
    padding: 2rem 3rem 3rem;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.25);
  }
  h1 {
    text-align: center;
    margin-bottom: 1rem;
  }
  .alert {
    padding: 1rem 1.5rem;
    border-radius: 10px;
    margin-bottom: 1rem;
    font-weight: 600;
  }
  .alert-success {
    background: #22c55e;
    color: white;
  }
  .alert-error {
    background: #ef4444;
    color: white;
  }
  form {
    margin-bottom: 2rem;
  }
  label {
    display: block;
    margin-bottom: .4rem;
    font-weight: 600;
  }
  input[type="text"], input[type="email"], input[type="password"], input[type="date"], select {
    width: 100%;
    padding: 0.5rem 0.75rem;
    margin-bottom: 1rem;
    border-radius: 8px;
    border: none;
    font-size: 1rem;
  }
  input[type="file"] {
    margin-bottom: 1rem;
  }
  button {
    background: #4f46e5;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 0.7rem 1.3rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  button:hover {
    background: #6366f1;
  }
  .foto-perfil {
    display: flex;
    justify-content: center;
    margin-bottom: 2rem;
  }
  .foto-perfil img {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fbbf24;
    box-shadow: 0 5px 15px rgba(251,191,36,0.6);
  }
  .seccion {
    margin-bottom: 2rem;
    background: rgba(0,0,0,0.15);
    padding: 1rem 1.5rem;
    border-radius: 12px;
  }
  .seccion h2 {
    margin-top: 0;
    margin-bottom: 1rem;
    border-bottom: 2px solid #fbbf24;
    padding-bottom: 0.5rem;
    color: #fbbf24;
  }
  .ultimos-accesos, .libros-prestados {
    list-style: none;
    padding-left: 0;
  }
  .ultimos-accesos li, .libros-prestados li {
    background: rgba(251,191,36,0.2);
    margin-bottom: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    color: #333;
  }
  .toggle-theme {
    cursor: pointer;
    padding: 0.3rem 0.6rem;
    background: #fbbf24;
    color: #333;
    border-radius: 8px;
    font-weight: 700;
    border: none;
    margin-left: 0.5rem;
  }
  .footer {
    text-align: center;
    margin-top: 3rem;
    color: #fbbf24;
  }
  .cerrar-sesion {
    float: right;
    background: #ef4444;
  }
</style>

</head>
<body>

<div class="container">

<h1>Perfil de Usuario</h1>

<?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success">¡Datos actualizados con éxito!</div>
<?php endif; ?>

<?php if ($mensaje_exito): ?>
  <div class="alert alert-success"><?=htmlspecialchars($mensaje_exito)?></div>
<?php endif; ?>
<?php if ($mensaje_error): ?>
  <div class="alert alert-error"><?=htmlspecialchars($mensaje_error)?></div>
<?php endif; ?>

<div class="foto-perfil">
  <img src="<?= $usuario['foto_perfil'] ? htmlspecialchars($usuario['foto_perfil']) : 'https://i.pravatar.cc/130?u=' . $usuario['id'] ?>" alt="Foto de perfil" />
</div>

<!-- Formulario foto -->
<form method="POST" enctype="multipart/form-data">
  <label for="foto">Cambiar foto de perfil:</label>
  <input type="file" name="foto" accept="image/*" />
  <button type="submit" name="subir_foto">Subir</button>
</form>

<!-- Formulario datos personales -->
<div class="seccion">
  <h2>Datos personales</h2>
  <form method="POST">
    <input type="hidden" name="actualizar_datos" value="1" />
    <label for="nombre">Nombre completo</label>
    <input type="text" id="nombre" name="nombre" value="<?=htmlspecialchars($usuario['nombre'])?>" required />

    <label for="email">Correo electrónico</label>
    <input type="email" id="email" name="email" value="<?=htmlspecialchars($usuario['email'])?>" required />

    <label for="telefono">Teléfono</label>
    <input type="text" id="telefono" name="telefono" value="<?=htmlspecialchars($usuario['telefono'] ?? '')?>" />

    <label for="direccion">Dirección</label>
    <input type="text" id="direccion" name="direccion" value="<?=htmlspecialchars($usuario['direccion'] ?? '')?>" />

    <label for="fecha_nacimiento">Fecha de nacimiento</label>
    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?=htmlspecialchars($usuario['fecha_nacimiento'] ?? '')?>" />

    <label for="genero">Género</label>
    <select id="genero" name="genero" required>
      <option value="Masculino" <?=($usuario['genero'] == 'Masculino' ? 'selected' : '')?>>Masculino</option>
      <option value="Femenino" <?=($usuario['genero'] == 'Femenino' ? 'selected' : '')?>>Femenino</option>
      <option value="Otro" <?=($usuario['genero'] == 'Otro' ? 'selected' : '')?>>Otro</option>
    </select>

    <button type="submit">Actualizar datos</button>
  </form>
</div>

<!-- Formulario cambio contraseña -->
<div class="seccion">
  <h2>Cambiar contraseña</h2>
  <form method="POST">
    <input type="hidden" name="cambiar_password" value="1" />
    <label for="password_actual">Contraseña actual</label>
    <input type="password" id="password_actual" name="password_actual" required />

    <label for="password_nuevo">Nueva contraseña</label>
    <input type="password" id="password_nuevo" name="password_nuevo" required />

    <label for="password_confirmar">Confirmar nueva contraseña</label>
    <input type="password" id="password_confirmar" name="password_confirmar" required />

    <button type="submit">Cambiar contraseña</button>
  </form>
</div>

<!-- Preferencias de usuario -->
<div class="seccion">
  <h2>Preferencias</h2>
  <label>Modo tema:
    <button id="toggle-theme" class="toggle-theme">Cambiar tema</button>
  </label>
  <p>Notificaciones y idioma aún no implementados.</p>
</div>

<div class="seccion">
  <h2>Último acceso</h2>
  <?php if (!empty($ultimo_acceso) && strtotime($ultimo_acceso) !== false): ?>
    <p><?= date('d/m/Y H:i:s', strtotime($ultimo_acceso)) ?></p>
  <?php else: ?>
    <p>No hay registros de acceso disponibles.</p>
  <?php endif; ?>
</div>




<!-- Rol usuario -->
<div class="seccion">
  <h2>Información adicional</h2>
  <p><strong>Rol:</strong> <?=htmlspecialchars($usuario['rol'])?></p>
</div>

<!-- Libros prestados -->
<div class="seccion">
  <h2>Libros prestados activos</h2>
  <?php if (count($libros_prestados) > 0): ?>
    <ul class="libros-prestados">
      <?php foreach ($libros_prestados as $libro): ?>
        <li>
<strong><?= htmlspecialchars($libro['titulo']) ?></strong>
          - Prestado desde <?= date('d/m/Y', strtotime($libro['fecha_prestamo'])) ?>
          <?php if (!empty($libro['fecha_devolucion'])): ?>
            , devolución prevista <?= date('d/m/Y', strtotime($libro['fecha_devolucion'])) ?>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>No tienes libros prestados actualmente.</p>
  <?php endif; ?>
</div>




<a href="panel.php" style="display: inline-block; padding: 10px 20px; background-color: #4f46e5; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; transition: background-color 0.3s;">
  ← Volver al Panel
</a>


<!-- Cerrar sesión -->
<form method="POST" action="logout.php">
  <button type="submit" class="cerrar-sesion">Cerrar sesión</button>
</form>

</div>


<script>
// Tema oscuro / claro
const botonTema = document.getElementById('toggle-theme');
const modoGuardado = localStorage.getItem('modoTema');

function aplicarTema(modo) {
  if (modo === 'oscuro') {
    document.body.style.background = 'linear-gradient(135deg, #1e293b, #334155)';
    document.body.style.color = '#eee';
  } else {
    document.body.style.background = 'linear-gradient(135deg, #667eea, #764ba2)';
    document.body.style.color = '#eee';
  }
  localStorage.setItem('modoTema', modo);
}

botonTema.addEventListener('click', () => {
  const modoActual = localStorage.getItem('modoTema');
  if (modoActual === 'oscuro') {
    aplicarTema('claro');
  } else {
    aplicarTema('oscuro');
  }
});

// Aplicar tema guardado al cargar
if (modoGuardado) {
  aplicarTema(modoGuardado);
} else {
  aplicarTema('claro');
}
</script>

</body>
</html>
