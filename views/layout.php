<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') : 'SGRRHH'; ?></title>
    <link rel="stylesheet" href="<?php echo isset($stylesheet) ? htmlspecialchars($stylesheet, ENT_QUOTES, 'UTF-8') : 'views/layoutStyles.css'; ?>">
</head>
<body>
    <div class="app">
        <aside class="sidebar" aria-label="Menú lateral">
            <a class="brand" href="#">TalentLink</a>
            <div class="section">PRINCIPAL</div>
            <nav class="menu" aria-label="Principal">
                <a class="active" href="#">Dashboard</a>
                <a href="#">Ofertas laborales</a>
                <a href="#">Candidatos</a>
                <a href="#">Postulaciones</a>
                <a href="#">Empresas</a>
            </nav>
            <div class="section">REPORTES</div>
            <nav class="menu" aria-label="Reportes">
                <a href="#">Estadísticas</a>
            </nav>
        </aside>

        <div class="content">
            <nav class="navbar" aria-label="Barra superior">
                <div class="navbar-inner">
                    <div class="nav">
                        <a class="active" href="#">Dashboard</a>
                    </div>
                    <div class="session">
                        <span class="user">Camila</span>
                        <button class="logout" type="button">Salir</button>
                    </div>
                </div>
            </nav>
            <main class="main">
                <?php echo $content; ?>
            </main>
        </div>
    </div>
</body>
</html>