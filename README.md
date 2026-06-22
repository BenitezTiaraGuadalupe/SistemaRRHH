Pasos para levantar el proyecto en local

1- Crear archivo config.php

2- Copiar contenido de config.example.php y pegarlo en 'config.php'

3- Configurar 'config.php' con datos de conexión a la base de datos local

4- Crear la base de datos con el mismo nombre que `DB_NAME` (ej: `talent_link`)

5- Ejecutar migraciones (crea tablas + tabla técnica `migrations`)

    php database/migrate.php

6- Ejecutar seeders en orden (roles → georef → entidades demo)

    php database/seed.php
Notas:
- Si querés correr solo roles: `php database/seed_roles.php`
- Si querés correr solo georef (provincias + ciudades): `php database/seed_georef.php`
- Si corrés `seed_entidades_demo.php` directo, requiere ciudades cargadas previamente.