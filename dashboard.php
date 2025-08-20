<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener datos del usuario de la sesión
$nombre_completo = $_SESSION['nombre_completo'] ?? 'Usuario';
$email = $_SESSION['email'] ?? 'No disponible';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bananox</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #e0b400, #f4d03f);
            min-height: 100vh;
            padding: 20px;
        }
        
        .header {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .welcome-text {
            color: #e0b400;
            font-size: 1.5rem;
        }
        
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
            display: inline-block;
        }
        
        .logout-btn:hover {
            background-color: #c82333;
        }
        
        .dashboard-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .user-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #e0b400;
            text-align: left;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="welcome-text">¡Bienvenido a Bananox!</h1>
        <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
    </div>
    
    <div class="dashboard-content">
        <div class="success-message">
            ✅ <strong>¡Inicio de sesión exitoso!</strong>
        </div>
        
        <h2>Panel de Usuario</h2>
        
        <div class="user-info">
            <h3>Información de tu cuenta:</h3>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre_completo); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Fecha de ingreso:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
            <p><strong>ID de usuario:</strong> <?php echo htmlspecialchars($_SESSION['usuario_id'] ?? 'N/A'); ?></p>
        </div>
        
        <p>Has iniciado sesión correctamente en Bananox.</p>
        <p>Desde aquí puedes acceder a todas las funcionalidades de la plataforma.</p>
        
        <div style="margin-top: 30px;">
            <a href="index.html" style="background-color: #e0b400; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
                Realizar Compra
            </a>
        </div>
    </div>
</body>
</html>