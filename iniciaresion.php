<?php
session_start();

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
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
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
    
    // Si no hay errores, verificar credenciales
    if (empty($errores)) {
        $stmt = $conn->prepare("SELECT id, nombre_completo, password FROM usuarios WHERE email = ?");
        if (!$stmt) {
            $errores[] = "Error en la consulta: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $usuario = $result->fetch_assoc();
                
                // Verificar la contraseña
                if (password_verify($password, $usuario['password'])) {
                    // Login exitoso - crear sesión
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
                    $_SESSION['email'] = $email;
                    
                    $mensaje_exito = "¡Bienvenido de vuelta, " . htmlspecialchars($usuario['nombre_completo']) . "! Has iniciado sesión correctamente.";
                    $login_exitoso = true;
                } else {
                    $errores[] = "Email o contraseña incorrectos";
                }
            } else {
                $errores[] = "Email o contraseña incorrectos";
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
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      border: 1px solid #c3e6cb;
      text-align: center;
    }

    .dashboard-link {
      text-align: center;
      margin-top: 15px;
    }

    .dashboard-link a {
      background-color: #28a745;
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 5px;
      display: inline-block;
      transition: background-color 0.3s ease;
    }

    .dashboard-link a:hover {
      background-color: #218838;
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
      background: #ddd;
      z-index: 1;
    }

    .divider span {
      background: rgba(255, 255, 255, 0.9);
      padding: 0 15px;
      position: relative;
      z-index: 2;
    }

    .google-button {
      width: 100%;
      background-color: #fff;
      color: #333;
      padding: 12px;
      border: 2px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-top: 10px;
    }

    .google-button:hover {
      background-color: #f8f9fa;
      border-color: #dadce0;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .google-icon {
      width: 20px;
      height: 20px;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }

    /* Efecto de confeti */
    .confetti {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1000;
    }

    .confetti-piece {
      position: absolute;
      width: 10px;
      height: 10px;
      background: #e0b400;
      animation: confetti-fall 3s linear infinite;
    }

    @keyframes confetti-fall {
      0% {
        transform: translateY(-100vh) rotate(0deg);
        opacity: 1;
      }
      100% {
        transform: translateY(100vh) rotate(720deg);
        opacity: 0;
      }
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
    
    <?php if (isset($login_exitoso) && $login_exitoso): ?>
      <div class="mensaje-exito">
        🎉 <?php echo $mensaje_exito; ?>
        <div class="dashboard-link" style="margin-top: 15px;">
          <a href="dashboard.php">Ir al Panel de Usuario</a>
        </div>
      </div>
      
      <!-- Efecto confeti -->
      <div class="confetti" id="confetti"></div>
      
      <script>
        // Crear efecto de confeti
        function createConfetti() {
          const confetti = document.getElementById('confetti');
          for (let i = 0; i < 50; i++) {
            const piece = document.createElement('div');
            piece.className = 'confetti-piece';
            piece.style.left = Math.random() * 100 + '%';
            piece.style.animationDelay = Math.random() * 3 + 's';
            piece.style.backgroundColor = ['#e0b400', '#f4d03f', '#28a745', '#dc3545', '#007bff'][Math.floor(Math.random() * 5)];
            confetti.appendChild(piece);
          }
          
          setTimeout(() => {
            confetti.remove();
          }, 4000);
        }
        
        createConfetti();
        
        // Auto-redirect después de 5 segundos
        setTimeout(() => {
          window.location.href = 'dashboard.php';
        }, 5000);
      </script>
      
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
        <input type="email" name="email" placeholder="Correo electrónico" required
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Iniciar Sesión</button>
      </form>
      
      <div class="divider">
        <span>o</span>
      </div>
      
      <button class="google-button" onclick="signInWithGoogle()">
        <svg class="google-icon" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Continuar con Google
      </button>
      
      <div class="register-link">
        ¿No tienes cuenta? <a href="registrarse.html">Regístrate aquí</a>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init();
    
    function signInWithGoogle() {
      alert('Función de Google Login pendiente de implementar');
    }
  </script>
</body>
</html>