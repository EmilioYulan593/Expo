<?php
session_start();

// Configuración de la base de datos (igual que en registro)
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

// Variable para debug - quitar después
$debug_info = "";

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $debug_info .= "POST recibido. Email: $email<br>";
    
    // Validaciones básicas
    $errores = [];
    
    if (empty($email)) {
        $errores[] = "El email es requerido";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido";
    }
    
    if (empty($password)) {
        $errores[] = "La contraseña es requerida";
    }
    
    $debug_info .= "Errores de validación: " . count($errores) . "<br>";
    
    // Si no hay errores, verificar credenciales
    if (empty($errores)) {
        $stmt = $conn->prepare("SELECT id, nombre_completo, password FROM usuarios WHERE email = ?");
        if (!$stmt) {
            $errores[] = "Error en la consulta: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $debug_info .= "Usuarios encontrados: " . $result->num_rows . "<br>";
            
            if ($result->num_rows == 1) {
                $usuario = $result->fetch_assoc();
                
                // Verificar la contraseña
                if (password_verify($password, $usuario['password'])) {
                    // Login exitoso - crear sesión
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
                    $_SESSION['email'] = $email;
                    
                    $debug_info .= "Login exitoso, redirigiendo...<br>";
                    
                    // Redirigir a la página principal o dashboard
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $errores[] = "Email o contraseña incorrectos";
                    $debug_info .= "Contraseña incorrecta<br>";
                }
            } else {
                $errores[] = "Email o contraseña incorrectos";
                $debug_info .= "Usuario no encontrado<br>";
            }
            
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Bananox</title>
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
            margin-bottom: 30px;
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
            margin: 10px 0;
        }
        
        .form-container button:hover {
            background-color: #c89f00;
        }
        
        .google-button {
            background-color: #4285f4 !important;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .google-button:hover {
            background-color: #357ae8 !important;
        }
        
        .google-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            background-color: white;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .register-link {
            text-align: center;
            margin-top: 15px;
            color: #666;
        }
        
        .register-link a {
            color: #e0b400;
            text-decoration: none;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
            color: #666;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #ddd;
            z-index: 1;
        }
        
        .divider span {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 0 15px;
            position: relative;
            z-index: 2;
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
        <h2>Iniciar Sesión en Bananox</h2>
        
        <!-- Información de debug - QUITAR DESPUÉS -->
        <?php if (!empty($debug_info)): ?>
            <div style="background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 12px;">
                <strong>DEBUG:</strong><br>
                <?php echo $debug_info; ?>
            </div>
        <?php endif; ?>
        
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
            <input type="email" name="email" placeholder="Correo electrónico" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
        
        <div class="divider">
            <span>o</span>
        </div>
        
        <button class="google-button" onclick="loginWithGoogle()">
            <div class="google-icon">G</div>
            Continuar con Google
        </button>
        
        <div class="register-link">
            ¿No tienes cuenta? <a href="registrarse.php">Regístrate aquí</a>
        </div>
    </div>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        
        function loginWithGoogle() {
            // Aquí puedes implementar la autenticación con Google
            alert('Función de Google Login pendiente de implementar');
        }
    </script>
</body>
</html>