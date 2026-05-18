<?php

$pageTitle = isset($pageTitle) ? $pageTitle : 'Iniciar sesión — TalentLink';
$error = isset($error) ? $error : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        :root{
            --lg-brand-900: #0b1b3a;
            --lg-brand-800: #122452;
            --lg-brand-700: #1f3c88;
            --lg-brand-600: #2a4aa3;
            --lg-accent: #3b82f6;
            --lg-ink-900: #0f172a;
            --lg-ink-700: #334155;
            --lg-muted-600: #64748b;
            --lg-border-200: #e5e7eb;
            --lg-surface-0: #f4f6f9;
        }

        *{ box-sizing: border-box; }

        html, body{ height: 100%; }

        body{
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            color: var(--lg-ink-900);
            background: var(--lg-surface-0);
        }

        .lg-shell{
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* === Panel izquierdo (branding) === */
        .lg-side{
            position: relative;
            color: #fff;
            padding: 48px 56px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background:
                radial-gradient(900px 500px at 90% 10%, rgba(59, 130, 246, 0.18), transparent 55%),
                radial-gradient(700px 400px at 10% 90%, rgba(31, 60, 136, 0.35), transparent 60%),
                linear-gradient(160deg, var(--lg-brand-900), var(--lg-brand-800) 60%, #0e2150);
            overflow: hidden;
        }

        .lg-side::before,
        .lg-side::after{
            content: "";
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .lg-side::before{
            width: 220px;
            height: 220px;
            top: -60px;
            right: -60px;
            background: radial-gradient(circle, rgba(255,255,255,0.08), transparent 70%);
        }
        .lg-side::after{
            width: 320px;
            height: 320px;
            bottom: -80px;
            left: -100px;
            background: radial-gradient(circle, rgba(59,130,246,0.18), transparent 70%);
        }

        .lg-side > *{ position: relative; z-index: 1; }

        .lg-logo{
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .lg-logo img{
            height: 64px;
            width: auto;
            display: block;
        }

        .lg-side__pitch{
            max-width: 360px;
        }
        .lg-side__pitch h1{
            font-size: 30px;
            line-height: 1.2;
            margin: 0 0 14px;
            font-weight: 800;
            letter-spacing: -0.4px;
        }
        .lg-side__pitch h1 .lg-accent{
            color: var(--lg-accent);
        }
        .lg-side__pitch p{
            margin: 0;
            color: rgba(255,255,255,0.78);
            font-size: 14px;
            line-height: 1.55;
        }

        .lg-side__illu{
            opacity: 0.55;
            margin-top: 24px;
            font-size: 11px;
        }
        .lg-side__illu svg{
            width: 220px;
            height: auto;
            display: block;
        }

        .lg-side__footer{
            font-size: 12px;
            color: rgba(255,255,255,0.55);
        }

        /* === Panel derecho (formulario) === */
        .lg-main{
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
        }

        .lg-card{
            width: 100%;
            max-width: 420px;
            background: #fff;
            border: 1px solid rgba(229,231,235,0.9);
            border-radius: 14px;
            padding: 32px;
            box-shadow: 0 14px 40px rgba(2, 6, 23, 0.08);
        }

        .lg-card__head{
            text-align: center;
            margin-bottom: 24px;
        }
        .lg-card__title{
            margin: 0 0 6px;
            font-size: 22px;
            font-weight: 800;
            color: var(--lg-ink-900);
        }
        .lg-card__subtitle{
            margin: 0;
            color: var(--lg-muted-600);
            font-size: 13px;
        }

        .lg-alert{
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .lg-field{
            margin-bottom: 14px;
        }
        .lg-field label{
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 700;
            color: var(--lg-ink-700);
        }

        .lg-input{
            position: relative;
            display: flex;
            align-items: center;
        }
        .lg-input input{
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1px solid rgba(203, 213, 225, 0.95);
            border-radius: 10px;
            font-size: 14px;
            background: #fff;
            color: var(--lg-ink-900);
            transition: border-color .15s ease, box-shadow .15s ease;
            font-family: inherit;
        }
        .lg-input input:focus{
            outline: none;
            border-color: rgba(31, 60, 136, 0.65);
            box-shadow: 0 0 0 4px rgba(31,60,136,0.12);
        }
        .lg-input input::placeholder{
            color: rgba(100,116,139,.85);
        }
        .lg-input__icon{
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--lg-muted-600);
            display: inline-flex;
            pointer-events: none;
        }
        .lg-input__icon svg{ width: 18px; height: 18px; display: block; }
        .lg-input--password input{ padding-right: 42px; }
        .lg-input__toggle{
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: 0;
            padding: 6px;
            cursor: pointer;
            color: var(--lg-muted-600);
            border-radius: 8px;
            display: inline-flex;
        }
        .lg-input__toggle:hover{ color: var(--lg-ink-700); background: rgba(2,6,23,0.04); }
        .lg-input__toggle svg{ width: 18px; height: 18px; display: block; }

        .lg-row{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 6px 0 18px;
            font-size: 13px;
        }
        .lg-check{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--lg-ink-700);
            cursor: pointer;
            user-select: none;
        }
        .lg-check input{
            accent-color: var(--lg-brand-600);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
        .lg-link{
            color: var(--lg-brand-600);
            text-decoration: none;
            font-weight: 600;
        }
        .lg-link:hover{ text-decoration: underline; }

        .lg-btn{
            width: 100%;
            border: 0;
            padding: 12px 16px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 14px;
            letter-spacing: .2px;
            cursor: pointer;
            transition: transform .12s ease, box-shadow .12s ease, filter .12s ease, border-color .12s ease, background .12s ease;
            font-family: inherit;
        }
        .lg-btn--primary{
            background: linear-gradient(135deg, var(--lg-brand-700), var(--lg-brand-600));
            color: #fff;
            box-shadow: 0 14px 32px rgba(31,60,136,0.22);
        }
        .lg-btn--primary:hover{
            transform: translateY(-1px);
            filter: brightness(0.98);
            box-shadow: 0 18px 42px rgba(31,60,136,0.28);
        }
        .lg-btn--ghost{
            background: #fff;
            color: var(--lg-ink-700);
            border: 1px solid rgba(203, 213, 225, 0.95);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .lg-btn--ghost:hover{
            border-color: rgba(31,60,136,0.25);
            box-shadow: 0 10px 26px rgba(2, 6, 23, 0.08);
            transform: translateY(-1px);
        }
        .lg-btn--ghost svg{ width: 18px; height: 18px; display: block; }

        .lg-divider{
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--lg-muted-600);
            font-size: 12px;
            margin: 16px 0;
        }
        .lg-divider::before,
        .lg-divider::after{
            content: "";
            flex: 1;
            height: 1px;
            background: var(--lg-border-200);
        }

        .lg-foot{
            margin-top: 18px;
            text-align: center;
            font-size: 13px;
            color: var(--lg-muted-600);
        }

        @media (max-width: 900px){
            .lg-shell{ grid-template-columns: 1fr; }
            .lg-side{ display: none; }
            .lg-main{ padding: 24px 16px; }
            .lg-card{ padding: 24px; }
        }
    </style>
</head>
<body>

<div class="lg-shell">

    <aside class="lg-side" aria-label="Branding">
        <div class="lg-logo">
            <img src="public/images/logo.jpg" alt="TalentLink">
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
