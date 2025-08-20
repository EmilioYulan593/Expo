<?php
// Configuración de la base de datos
$servername = "localhost:3307";
$username = "root";
$password = ""; // Por defecto XAMPP no tiene contraseña
$dbname = "bananox";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar charset
$conn->set_charset("utf8");

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_completo = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar_password'];
    
    // Validaciones básicas
    $errores = [];
    
    if (empty($nombre_completo)) {
        $errores[] = "El nombre completo es requerido";
    }
    
    if (empty($email)) {
        $errores[] = "El email es requerido";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido";
    }
    
    if (empty($password)) {
        $errores[] = "La contraseña es requerida";
    } elseif (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres";
    }
    
    if ($password !== $confirmar_password) {
        $errores[] = "Las contraseñas no coinciden";
    }
    
    // Verificar si el email ya existe
    if (empty($errores)) {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errores[] = "Este email ya está registrado";
        }
        $stmt->close();
    }
    
    // Si no hay errores, registrar el usuario
    if (empty($errores)) {
        // Encriptar la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Preparar y ejecutar la consulta
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre_completo, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre_completo, $email, $password_hash);
        
        if ($stmt->execute()) {
            $mensaje_exito = "¡Registro exitoso! Tu cuenta ha sido creada.";
            $registro_exitoso = true;
        } else {
            $errores[] = "Error al registrar el usuario: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Bananox</title>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body, html {
            height: 100%;
            overflow-x: hidden;
        }
        
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: -1;
            filter: brightness(0.4) blur(4px);
            object-fit: cover;
        }
        
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 2;
            background-color: #e0b400;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            text-decoration: none;
            display: inline-block;
        }
        
        .back-button:hover {
            background-color: #c89f00;
            transform: translateY(-2px);
        }
        
        .form-container {
            position: relative;
            z-index: 1;
            max-width: 400px;
            margin: 100px auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            animation: fadeIn 1s ease-in-out;
        }
        
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #e0b400;
        }
        
        .form-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #e0b400;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-container button {
            width: 100%;
            background-color: #e0b400;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .form-container button:hover {
            background-color: #c89f00;
        }
        
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            text-align: center;
        }
        
        .login-link {
            text-align: center;
            margin-top: 15px;
            color: #666;
        }
        
        .login-link a {
            color: #e0b400;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>
    <!-- Botón Regresar -->
    <a href="index.html" class="back-button">← Regresar</a>
    
    <!-- Fondo de video -->
    <video autoplay muted loop class="video-background">
        <source src="136467-764399899_medium.mp4" type="video/webm">
        Tu navegador no soporta el video de fondo.
    </video>
    
    <!-- Formulario -->
    <div class="form-container" data-aos="zoom-in">
        <h2>Registrarse en Bananox</h2>
        
        <?php if (isset($registro_exitoso) && $registro_exitoso): ?>
            <div class="mensaje-exito">
                <?php echo $mensaje_exito; ?>
                <div class="login-link" style="margin-top: 10px;">
                    <a href="login.php">Iniciar sesión ahora</a>
                </div>
            </div>
        <?php else: ?>
            <?php if (isset($errores) && !empty($errores)): ?>
                <div class="mensaje-error">
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="text" name="nombre_completo" placeholder="Nombre completo" required 
                       value="<?php echo isset($_POST['nombre_completo']) ? htmlspecialchars($_POST['nombre_completo']) : ''; ?>">
                <input type="email" name="email" placeholder="Correo electrónico" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <input type="password" name="password" placeholder="Contraseña" required>
                <input type="password" name="confirmar_password" placeholder="Confirmar contraseña" required>
                <button type="submit">Crear cuenta</button>
            </form>
            
            <div class="login-link">
                ¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>