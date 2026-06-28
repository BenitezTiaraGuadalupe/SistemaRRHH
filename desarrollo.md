# TalentLink — Seguimiento de desarrollo

Lista de trabajo para ir completando el sistema de a poco.  
Marcar con `[x]` lo terminado y dejar `[ ]` lo pendiente.

## Reglas de este proyecto

- Código **básico**: controladores + vistas + SQL directo.
- **Sin** carpetas nuevas, helpers, librerías ni archivos de configuración extra.
- Estructura fija: `controllers/`, `views/`, `database/`, `index.php`.
- Rutas: `index.php?accion=...`
- Permisos: `AuthController::requerirPermiso()` en controladores.
- Mensajes entre pantallas: `$_SESSION` (`mensaje_exito`, `errores`, `old`).

---

## Estado general del flujo de negocio

| Paso | Descripción | Estado |
|------|-------------|--------|
| 1 | Empresa solicita personal | Parcial (demo + alta manual) |
| 2 | RRHH registra / revisa solicitud | Parcial (listado admin con datos demo) |
| 3 | RRHH publica oferta laboral | Parcial (desde búsqueda, solo admin gestiona) |
| 4 | Candidato se postula | Pendiente |
| 5 | RRHH avanza etapas del proceso | Pendiente |
| 6 | Empresa consulta avance | Pendiente |

---

## Ya implementado

- [x] Login, logout, sesión y permisos (`authController`)
- [x] Dashboard con métricas
- [x] Solicitudes: alta + listado (admin)
- [x] Solicitudes: alta restringida a su empresa (rol empresa)
- [x] Candidatos: listado (admin)
- [x] Usuarios: listado, alta, roles y permisos (admin)
- [x] Base de datos y migraciones
- [x] Seeds: roles, permisos, georef, entidades demo

---

## Pendiente — por prioridad

### P1 · Datos base (prerrequisito)

Sin catálogos cargados, varios formularios no funcionan en una instalación nueva.

- [x] Ampliar `seed.php`:
  - [x] `estado_busqueda` (ej. Pendiente, En proceso, Cerrada)
  - [x] `estado_ofertas` (ej. Activa, Pausada, Cerrada)
  - [x] `modalidades` (Presencial, Remoto, Híbrida)
  - [x] `etapas` (Recepción CV, Entrevista, Evaluación, Contratado, Descartado)
- [x] Guardar en `database/seed_catalogos.php` (invocado desde `seed.php`)
- [x] Datos demo: 1–2 búsquedas de ejemplo para probar ofertas

**Archivos previstos:** solo `database/`

---

### P2 · Ofertas laborales

Conecta una búsqueda con una vacante visible para candidatos.

- [x] `controllers/ofertasController.php`
- [x] Rutas en `index.php`
- [x] `views/ofertas/index.php` — listado
- [x] `views/ofertas/create.php` — publicar desde búsqueda existente
- [x] `views/ofertas/edit.php` — cambiar estado
- [x] Acciones guardar (POST store / update)
- [x] Enlace del menú lateral
- [x] Permisos: `ofertas.ver` (lectura), `ofertas.crear` + rol admin (gestión)

**Regla:** una oferta por búsqueda; solo RRHH publica y edita estado.

---

### P3 · Postulaciones

Núcleo del circuito: candidato aplica y RRHH hace seguimiento.

- [ ] `controllers/postulacionesController.php`
- [ ] Rutas en `index.php`
- [ ] `views/postulaciones/index.php` — listado según rol
- [ ] Postularse desde oferta activa (candidato)
- [ ] Validar no duplicar (mismo candidato + misma oferta)
- [ ] Asignar primera etapa al crear postulación
- [ ] Avanzar etapa (admin)
- [ ] Enlace del menú lateral
- [ ] Permisos: `postulaciones.ver`, `postulaciones.crear`, `postulaciones.gestionar`

---

### P4 · Cierre por actor

Para demo con los tres roles sin depender solo del admin.

- [ ] Empresa: **Mis solicitudes** (listado filtrado por su empresa)
- [ ] Empresa: ver ofertas/postulaciones de sus búsquedas (vista simple)
- [ ] Candidato: ver ofertas activas y botón postular (si no está en P3)
- [ ] Admin: cambiar estado de solicitud (pendiente → en proceso, etc.)

---

### P5 · Perfiles (básico)

- [ ] Candidato: editar perfil propio (`candidatos.editar`)
- [ ] Admin: detalle de candidato (datos + postulaciones)
- [ ] Empresa: editar datos de su empresa (`empresas.editar`)

---

### P6 · Solicitudes — refinamiento

- [ ] Ver detalle de una solicitud
- [ ] Editar solicitud
- [ ] Eliminar solicitud (si hace falta para la entrega)

---

### P7 · Opcional (después del flujo completo)

- [ ] Listado / gestión de empresas (admin)
- [ ] Reportes / estadísticas avanzadas
- [ ] AJAX demo (ej. actualizar métricas del dashboard)
- [ ] Carga de CV

---

## Módulos del menú — estado

| Ítem menú | Acción | Estado |
|-----------|--------|--------|
| Dashboard | `dashboard` | Hecho |
| Solicitudes | `solicitudes` | Hecho (listado admin) |
| Nueva solicitud | `create` | Hecho |
| Ofertas laborales | `ofertas` | Hecho |
| Candidatos | `candidatos` | Hecho (solo listado) |
| Postulaciones | — | Pendiente |
| Empresas | — | Pendiente |
| Estadísticas | — | Pendiente |
| Usuarios | `usuarios` | Hecho |

---

## Cómo usar este archivo

1. Elegir un bloque (ej. **P1** o **P2**).
2. Decir en el chat: *"avancemos con P2"* o el ítem concreto.
3. Al terminar un módulo, marcar `[x]` aquí y actualizar la tabla de flujo si corresponde.

---

*Última revisión: junio 2026*
