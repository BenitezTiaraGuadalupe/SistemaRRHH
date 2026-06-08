<?php

require_once dirname(__DIR__, 2) . '/lib/paths.php';

$pageTitle = isset($pageTitle) ? $pageTitle : 'Iniciar sesión — TalentLink';
$error = isset($error) ? $error : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(app_module_styles('auth'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>

<div class="lg-shell">

    <aside class="lg-side" aria-label="Branding">
        <div class="lg-logo">
            <img src="<?php echo htmlspecialchars(app_url('public/images/logo.jpg'), ENT_QUOTES, 'UTF-8'); ?>" alt="TalentLink">
        </div>

        <div class="lg-side__pitch">
            <h1>Conectamos talento<br>con <span class="lg-accent">oportunidades</span></h1>
            <p>TalentLink es la plataforma que facilita la conexión entre empresas que buscan talento y candidatos que buscan crecer.</p>

            <div class="lg-side__illu" aria-hidden="true">
                <svg viewBox="0 0 220 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="45" r="22" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"/>
                    <circle cx="40" cy="38" r="7" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"/>
                    <path d="M28 56c2-6 8-9 12-9s10 3 12 9" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-linecap="round"/>
                    <rect x="90" y="28" width="48" height="40" rx="6" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"/>
                    <path d="M104 28v-6h20v6" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"/>
                    <path d="M90 44h48" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"/>
                    <circle cx="172" cy="44" r="14" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"/>
                    <path d="M182 54l8 8" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <div class="lg-side__footer">
            © <?php echo date('Y'); ?> TalentLink. Todos los derechos reservados.
        </div>
    </aside>

    <main class="lg-main">
        <div class="lg-card" role="region" aria-label="Formulario de inicio de sesión">

            <div class="lg-card__head">
                <h2 class="lg-card__title">Iniciar sesión</h2>
                <p class="lg-card__subtitle">Bienvenido de nuevo a TalentLink</p>
            </div>

            <?php if (!empty($error)) : ?>
                <div class="lg-alert" role="alert">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="index.php?accion=login" autocomplete="on">

                <div class="lg-field">
                    <label for="correo">Email</label>
                    <div class="lg-input">
                        <span class="lg-input__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.6"/>
                                <path d="M3 7l9 6 9-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input id="correo" name="correo" type="email" placeholder="Ingresa tu email" required>
                    </div>
                </div>

                <div class="lg-field">
                    <label for="password">Contraseña</label>
                    <div class="lg-input lg-input--password">
                        <span class="lg-input__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="4" y="10" width="16" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/>
                                <path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input id="password" name="password" type="password" placeholder="Ingresa tu contraseña" required>
                        <button type="button" class="lg-input__toggle" id="togglePassword" aria-label="Mostrar u ocultar contraseña">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="1.6"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="lg-row">
                    <label class="lg-check">
                        <input type="checkbox" name="recordarme" value="1" checked>
                        <span>Recordarme</span>
                    </label>
                    <a href="#" class="lg-link">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="lg-btn lg-btn--primary">Iniciar sesión</button>

                <div class="lg-divider"><span>o continúa con</span></div>

                <button type="button" class="lg-btn lg-btn--ghost">
                    <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.6 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.8 1.1 7.9 3l5.7-5.7C34 6 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.2-.1-2.4-.4-3.5z"/>
                        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 16 19 13 24 13c3 0 5.8 1.1 7.9 3l5.7-5.7C34 6 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>
                        <path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.4 35 26.8 36 24 36c-5.3 0-9.7-3.4-11.3-8l-6.6 5.1C9.5 39.5 16.2 44 24 44z"/>
                        <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.2-4.1 5.6l6.2 5.2C41.7 35.6 44 30.2 44 24c0-1.2-.1-2.4-.4-3.5z"/>
                    </svg>
                    <span>Continuar con Google</span>
                </button>

                <p class="lg-foot">
                    ¿No tienes cuenta? <a href="#" class="lg-link">Regístrate aquí</a>
                </p>

            </form>
        </div>
    </main>

</div>

<script>
(function () {
    var btn = document.getElementById('togglePassword');
    var input = document.getElementById('password');
    if (!btn || !input) return;
    btn.addEventListener('click', function () {
        var isPwd = input.type === 'password';
        input.type = isPwd ? 'text' : 'password';
        btn.setAttribute('aria-pressed', isPwd ? 'true' : 'false');
    });
})();
</script>

</body>
</html>
