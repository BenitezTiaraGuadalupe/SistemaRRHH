# Guía para presentación — TalentLink

> **Propósito de este documento:** Contiene toda la información necesaria para que una herramienta de IA genere una presentación académica/profesional sobre el sistema **TalentLink**. Incluye contexto del proyecto, dominio de negocio, actores, flujos, funcionalidades, diseño de interfaz, tecnologías y estado actual de desarrollo.

---

## Instrucciones sugeridas para la IA generadora

Al crear la presentación, tener en cuenta:

- **Audiencia:** Docente y compañeros de cursada (proyecto académico de 2.º año, Análisis de Sistemas).
- **Tono:** Profesional, claro y didáctico. Evitar jerga excesiva.
- **Nombre del sistema:** **TalentLink** (plataforma de gestión de RRHH).
- **Tipo de sistema:** Aplicación web de gestión (no embebido, no móvil nativo).
- **Enfoque:** Explicar el problema real, la solución propuesta, cómo funciona el dominio y qué hace cada módulo.
- **Paleta visual del prototipo:** Azules, grises neutros y acentos claros. Interfaz moderna, limpia y responsiva.
- **Incluir:** Portada, problema, objetivo, cliente/organización, actores, flujo del negocio, módulos, pantallas, roles, resultados esperados y estado del desarrollo.

---

## 1. Contexto académico

| Dato | Detalle |
|------|---------|
| Institución | Escuela Normal Superior N.º 10 — Anexo Comercial San Antonio |
| Materia | Lenguaje Generador de Informes (LGI) |
| Carrera | Análisis de Sistemas de Computación — 2.º año |
| Año lectivo | 2026 |
| Docente | Fausto Fabián Garcete |
| Integrantes | Appes Agustina, Benitez Tiara Guadalupe |
| Tipo de proyecto | Sistema de software de gestión |

---

## 2. ¿Qué es TalentLink?

**TalentLink** es una plataforma web orientada a la **gestión integral de procesos de reclutamiento y selección de personal**. Está pensada para ser utilizada por una **consultora de Recursos Humanos** que trabaja como intermediaria entre:

- **Empresas cliente** que necesitan cubrir vacantes laborales.
- **Candidatos** que buscan oportunidades de empleo.

La plataforma centraliza en un solo lugar lo que hoy suele hacerse de forma dispersa: correos electrónicos, mensajes de WhatsApp, planillas de Excel y carpetas con CVs. El objetivo es **organizar, agilizar y automatizar** el ciclo completo desde que una empresa pide personal hasta que un candidato avanza (o no) en el proceso de selección.

**Eslogan conceptual:** *"Conectamos talento con oportunidades."*

---

## 3. Problemática que resuelve

### Situación actual (sin sistema)

En muchas consultoras de RRHH, el proceso de vinculación entre candidatos y empresas se gestiona de manera **manual** y a través de **múltiples canales externos**:

- Las empresas envían pedidos de personal por email o mensajería.
- Los reclutadores publican ofertas en distintos medios sin un registro unificado.
- Los CVs llegan por correo y se archivan en carpetas o hojas de cálculo.
- El seguimiento de cada candidato (en qué etapa está: entrevista, evaluación, descartado, contratado) depende de la memoria o notas personales del reclutador.

### Consecuencias

- **Desorganización** y pérdida de información.
- **Demoras** en la gestión de vacantes y respuesta a candidatos.
- **Carga operativa elevada** para el personal de RRHH.
- **Dificultad** para saber en tiempo real cuántas búsquedas, ofertas y postulaciones hay activas.
- **Comunicación fragmentada** entre empresa, consultora y candidato.

### Necesidad

Implementar una **solución tecnológica** que centralice y automatice estos procesos, mejorando la organización, la eficiencia y la rapidez en la gestión de búsquedas laborales.

---

## 4. Objetivo del proyecto

Desarrollar un **sistema web** que permita:

1. **Automatizar y centralizar** la vinculación entre candidatos y empresas cliente.
2. **Gestionar solicitudes de búsqueda** de personal pedidas por las empresas.
3. **Publicar ofertas laborales** derivadas de esas búsquedas.
4. **Recibir y administrar postulaciones** de candidatos.
5. **Hacer seguimiento por etapas** del proceso de selección.
6. **Administrar perfiles** de candidatos, empresas y personal de RRHH.
7. **Visualizar métricas** en un panel de resumen para facilitar decisiones.

---

## 5. La organización del dominio (cliente del sistema)

### ¿Quién usa TalentLink?

Una **consultora de Recursos Humanos** llamada **TalentLink** que ofrece servicios de reclutamiento y selección a múltiples empresas.

### ¿Cómo opera hoy conceptualmente?

1. Una **empresa cliente** comunica que necesita personal (ej.: "buscamos 2 desarrolladores Full Stack").
2. Eso se convierte en una **solicitud de búsqueda** con requisitos: puesto, vacantes, experiencia, modalidad (presencial/remoto/híbrido), ubicación y habilidades.
3. El **personal de RRHH** de la consultora revisa la solicitud y, si corresponde, la transforma en una **oferta laboral** publicada.
4. Los **candidatos** registrados ven las ofertas y **se postulan**.
5. El personal de RRHH **avanza cada postulación por etapas**: recepción de CV → entrevista → evaluación → contratación o descarte.
6. La empresa cliente puede **consultar el estado** de sus búsquedas y procesos.

### Documento manual que el sistema reemplaza

Hoy una solicitud puede llegar como un email informal o un formulario en papel. El sistema digitaliza algo equivalente a esto:

```
SOLICITUD DE BÚSQUEDA DE PERSONAL
Empresa: Aurora Tech
Puesto: Desarrollador Full Stack
Vacantes: 2
Experiencia: 2 años
Modalidad: Híbrida
Ubicación: Posadas, Misiones
Habilidades: PHP, MySQL, JavaScript
Estado: Pendiente de revisión
```

---

## 6. Actores del sistema

Son los tipos de usuarios que interactúan con la plataforma:

### Personal de RRHH (rol: admin)

- Es el usuario principal de gestión.
- Registra y administra solicitudes de búsqueda.
- Publica ofertas laborales.
- Gestiona candidatos y postulaciones.
- Avanza las postulaciones por etapas del proceso de selección.
- Administra usuarios del sistema.
- Consulta estadísticas y el dashboard.

**Perfil en el sistema:** tabla `personal_rrhh` vinculada a un `usuario` con rol admin.

### Empresa cliente (rol: empresa)

- Representa a una organización que contrata los servicios de la consultora.
- Puede solicitar búsquedas de personal.
- Consulta el estado de sus solicitudes, ofertas y postulaciones vinculadas.
- Mantiene los datos de su empresa actualizados.

**Perfil en el sistema:** tabla `empresas` vinculada a un `usuario` con rol empresa.

**Ejemplos de empresas demo:** Aurora Tech, Mate y Code, Pampa Foods, Andes Cargo, Río Plata SA.

### Candidato (rol: candidato)

- Persona en búsqueda de empleo.
- Mantiene su perfil personal y profesional.
- Consulta ofertas laborales disponibles.
- Se postula a las vacantes de su interés.
- Puede cargar habilidades y CV (funcionalidad prevista).

**Perfil en el sistema:** tabla `candidatos` vinculada a un `usuario` con rol candidato.

**Ejemplos de candidatos demo:** Luna Pérez, Tomás Rojas, María García, Juan Sosa, Sofía Díaz.

---

## 7. Flujo principal del negocio (circuito)

Este es el recorrido central que la presentación debe explicar de forma clara:

```
EMPRESA CLIENTE
      │
      │  Solicita personal (puesto, requisitos, vacantes)
      ▼
SOLICITUD DE BÚSQUEDA
      │
      │  El personal de RRHH la revisa y registra en el sistema
      ▼
BÚSQUEDA REGISTRADA + DETALLE
      │  (descripción, modalidad, ubicación, habilidades requeridas)
      ▼
OFERTA LABORAL PUBLICADA
      │
      │  Visible para candidatos en la plataforma
      ▼
CANDIDATO SE POSTULA
      │
      ▼
POSTULACIÓN CREADA
      │
      │  Seguimiento por etapas del proceso de selección
      ▼
RESULTADO: Contratado / Descartado / En proceso
```

### Conceptos clave del dominio

| Concepto | Qué es |
|----------|--------|
| **Solicitud / Búsqueda** | Pedido de una empresa para encontrar personal. Contiene el nombre del puesto, la empresa y un estado. |
| **Detalle de búsqueda** | Información ampliada: descripción, cantidad de vacantes, años de experiencia, modalidad, ubicación geográfica. |
| **Habilidades requeridas** | Competencias técnicas o blandas asociadas a una búsqueda (ej.: PHP, trabajo en equipo). |
| **Oferta laboral** | Publicación concreta de una vacante, vinculada a una búsqueda y gestionada por un referente de RRHH. |
| **Postulación** | Registro de que un candidato aplicó a una oferta. |
| **Etapa** | Estado del candidato dentro del proceso (recepción de CV, entrevista, evaluación, contratado, descartado). |

---

## 8. Módulos y funcionalidades del sistema

A continuación, cada módulo con su propósito, qué permite hacer y su estado de desarrollo.

---

### 8.1. Módulo de Autenticación (Login)

**Propósito:** Controlar el acceso seguro a la plataforma.

**Funcionalidades:**
- Inicio de sesión con correo electrónico y contraseña.
- Validación de credenciales contra la base de datos.
- Carga de rol y permisos en la sesión del usuario.
- Cierre de sesión (logout).
- Redirección automática al login si el usuario no está autenticado.
- Mensajes de error claros (credenciales incorrectas, campos vacíos).

**Pantalla:** Vista de login dividida en dos columnas: lado izquierdo con branding ("Conectamos talento con oportunidades") y logo; lado derecho con formulario de acceso. Incluye opción de mostrar/ocultar contraseña.

**Estado:** ✅ Implementado

---

### 8.2. Módulo Dashboard (Panel de resumen)

**Propósito:** Ofrecer una vista general del estado del sistema para facilitar la toma de decisiones.

**Funcionalidades:**
- Muestra métricas en tiempo real desde la base de datos:
  - **Candidatos:** cantidad de personas registradas.
  - **Solicitudes:** cantidad de búsquedas pedidas por empresas.
  - **Ofertas activas:** vacantes publicadas y vigentes.
  - **Empresas:** organizaciones cliente registradas.
- Saludo personalizado con el nombre del usuario logueado.
- Tarjetas visuales con número, etiqueta y descripción de cada métrica.

**Pantalla:** Vista principal tras el login. Sidebar de navegación a la izquierda, barra superior con título y datos del usuario, área central con las 4 tarjetas de métricas.

**Estado:** ✅ Implementado

---

### 8.3. Módulo de Solicitudes de Personal

**Propósito:** Registrar y consultar las búsquedas de personal solicitadas por empresas cliente.

**Funcionalidades planificadas:**
- Listar todas las solicitudes con filtros y búsqueda.
- Ver detalle de cada solicitud.
- Editar y cambiar estado de una solicitud.
- Eliminar solicitudes (con permisos).

**Funcionalidades implementadas:**
- **Alta de solicitud:** formulario completo que registra:
  - Nombre del puesto.
  - Empresa cliente (selección de catálogo).
  - Estado de la búsqueda.
  - Descripción del puesto (hasta 500 caracteres).
  - Cantidad de vacantes.
  - Años de experiencia requeridos.
  - Modalidad de trabajo (presencial, remoto, híbrida).
  - Ubicación: país, provincia y ciudad (selectores en cascada).
  - Habilidades requeridas: selección del catálogo existente o creación de nuevas al momento.
- Validación de campos obligatorios con mensajes de error por campo.
- Persistencia en base de datos dentro de una transacción (búsqueda + detalle + habilidades).
- Mensaje de éxito tras el registro.

**Pantalla de alta:** Formulario estructurado por secciones (datos del puesto, detalle, ubicación, habilidades). Diseño alineado al resto del sistema.

**Pantalla de listado:** Existe la vista pero aún **no consulta datos reales** de la base de datos (muestra mensaje de listado vacío).

**Estado:** ⚠️ Parcial (alta implementada; listado y edición pendientes)

---

### 8.4. Módulo de Ofertas Laborales

**Propósito:** Publicar y gestionar las vacantes derivadas de las búsquedas registradas.

**Funcionalidades planificadas:**
- Listar ofertas con filtros (estado, empresa, puesto, fecha).
- Publicar una nueva oferta a partir de una búsqueda existente.
- Asignar un referente de RRHH responsable de la oferta.
- Cambiar estado de la oferta (activa, pausada, cerrada).
- Ver detalle de la oferta con datos de la búsqueda asociada.
- Vista pública para candidatos: explorar ofertas disponibles y ver requisitos.

**Regla de negocio importante:** Solo las ofertas en estado **activo** pueden recibir postulaciones.

**Pantalla prevista:** Listado tipo tabla o tarjetas con nombre del puesto, empresa, estado, fecha y acciones. Filtros en la parte superior.

**Estado:** ❌ Pendiente (existe en menú y modelo de datos, sin pantalla funcional)

---

### 8.5. Módulo de Candidatos

**Propósito:** Gestionar el registro y seguimiento de las personas que buscan empleo.

**Funcionalidades implementadas:**
- **Listado de candidatos** para personal de RRHH:
  - Nombre completo.
  - Correo electrónico.
  - Ciudad y provincia de residencia.
  - Fecha de nacimiento.
  - Ordenado alfabéticamente por apellido.
  - Contador de candidatos y estado vacío si no hay registros.

**Funcionalidades planificadas:**
- Alta de candidato (autoregistro o carga por RRHH).
- Edición de perfil (datos personales, experiencia, formación).
- Carga de CV.
- Asociación de habilidades al perfil.
- Vista detalle para RRHH: historial de postulaciones, CVs cargados, características principales.

**Pantalla de listado:** Tabla dentro de una tarjeta con encabezado, subtítulo y badge con cantidad de candidatos.

**Estado:** ⚠️ Parcial (listado implementado; alta, edición y detalle pendientes)

---

### 8.6. Módulo de Postulaciones

**Propósito:** Registrar y hacer seguimiento de las aplicaciones de candidatos a ofertas laborales.

**Funcionalidades planificadas:**
- Listar postulaciones (vista RRHH: todas; vista candidato: las propias; vista empresa: las de sus ofertas).
- Permitir al candidato postularse a una oferta activa.
- Impedir postulaciones duplicadas (mismo candidato, misma oferta).
- Asignar automáticamente la primera etapa al crear la postulación.
- Avanzar, retroceder o cerrar la etapa de una postulación (solo RRHH).
- Consultar historial de postulaciones desde el perfil del candidato.

**Etapas previstas del proceso:**
1. Recepción de CV
2. Entrevista inicial
3. Evaluación técnica
4. Entrevista final
5. Contratado
6. Descartado

**Estado:** ❌ Pendiente

---

### 8.7. Módulo de Empresas

**Propósito:** Administrar las organizaciones cliente de la consultora.

**Funcionalidades planificadas:**
- Listar empresas registradas.
- Ver detalle de cada empresa (datos, solicitudes asociadas, ofertas).
- Editar datos de la empresa.
- Dar de alta nuevas empresas con su usuario de acceso.

**Estado:** ❌ Pendiente (empresas existen en base de datos demo, sin pantalla de gestión)

---

### 8.8. Módulo de Usuarios (Personal RRHH)

**Propósito:** Administrar las cuentas del personal de la consultora.

**Funcionalidades planificadas:**
- Listar usuarios del sistema con su rol y estado.
- Dar de alta nuevos usuarios de RRHH.
- Desactivar usuarios (sin eliminar historial).
- Reactivar usuarios desactivados.

**Pantalla prevista:** Tabla con nombre, correo, rol y botones de acción (alta / desactivar / reactivar).

**Estado:** ❌ Pendiente

---

### 8.9. Módulo de Reportes / Estadísticas

**Propósito:** Brindar indicadores para análisis y toma de decisiones.

**Funcionalidades planificadas:**
- Gráficos y tablas con volumen de postulaciones por período.
- Ofertas activas vs. cerradas.
- Candidatos por zona geográfica.
- Tiempo promedio de cobertura de vacantes.

**Estado:** ❌ Pendiente (el dashboard cubre métricas básicas; reportes avanzados no están desarrollados)

---

## 9. Roles y permisos

El sistema usa **tres roles** con permisos diferenciados:

### Rol: admin (Personal de RRHH)

Tiene **todos los permisos** del sistema:
- Ver dashboard, solicitudes, candidatos, empresas, ofertas, postulaciones, reportes y usuarios.
- Crear, editar y eliminar solicitudes.
- Crear ofertas y gestionar postulaciones.
- Administrar usuarios.

### Rol: empresa (Empresa cliente)

Permisos limitados a su operación:
- Ver dashboard.
- Ver y crear solicitudes de búsqueda.
- Editar datos de su empresa.
- Ver ofertas y postulaciones vinculadas a sus procesos.

### Rol: candidato

Permisos orientados a su perfil y postulaciones:
- Ver dashboard.
- Editar su propio perfil.
- Ver ofertas laborales disponibles.
- Ver sus postulaciones y crear nuevas.

**Seguridad implementada:**
- Contraseñas encriptadas (nunca en texto plano).
- Sesión segura con regeneración de ID tras login.
- Cada pantalla verifica permisos antes de mostrar contenido.
- Menú lateral dinámico: solo muestra las secciones permitidas para el rol del usuario.

---

## 10. Diseño de interfaz (prototipo visual)

### Identidad visual

- **Nombre comercial:** TalentLink
- **Paleta:** Tonos de azul, grises neutros y acentos visuales claros.
- **Estilo:** Moderno, limpio, profesional. Pensado para uso laboral diario.
- **Responsive:** Adaptable a distintos tamaños de pantalla.

### Pantallas diseñadas / implementadas

| Pantalla | Descripción visual |
|----------|-------------------|
| **Login** | Dos columnas: branding a la izquierda con logo y frase "Conectamos talento con oportunidades"; formulario a la derecha con campos email/contraseña, botón de acceso y opciones secundarias. |
| **Dashboard** | Sidebar fija con logo y menú por secciones (Principal / Reportes). Área central con 4 tarjetas de métricas grandes con número, título y descripción. Navbar superior con título de página y avatar del usuario. |
| **Solicitudes (alta)** | Formulario extenso por secciones con selects, campos de texto, selectores geográficos en cascada y gestión de habilidades. |
| **Solicitudes (listado)** | Vista simple con tabla (pendiente de datos reales). |
| **Candidatos (listado)** | Tarjeta con encabezado, badge de cantidad y tabla con columnas: nombre, email, ciudad, provincia, fecha de nacimiento. |
| **Alta de candidato** | Prototipo previsto: formulario con datos personales, experiencia, formación y carga de CV, con información del puesto al que aplica. |
| **Perfil de candidato (detalle RRHH)** | Prototipo previsto: datos personales, habilidades, historial de postulaciones y CVs cargados. |
| **Usuarios RRHH** | Prototipo previsto: tabla con roles y acciones de alta/desactivar/reactivar. |

### Navegación

Menú lateral con íconos y etiquetas:
- Dashboard
- Solicitudes
- Ofertas laborales
- Candidatos
- Postulaciones
- Empresas
- Estadísticas (reportes)
- Usuarios

Los ítems sin funcionalidad desarrollada aparecen en el menú solo si el rol tiene permiso, pero aún no tienen ruta activa.

---

## 11. Modelo de datos (resumen para la presentación)

El sistema persiste la información en una base de datos MySQL llamada `talent_link`. Las entidades principales son:

**Usuarios y seguridad:**
- `usuarios`, `roles`, `permisos`, `permisos_por_roles`

**Actores del dominio:**
- `empresas`, `candidatos`, `personal_rrhh`

**Proceso de reclutamiento:**
- `busquedas` (solicitudes), `detalle_busquedas`, `estado_busqueda`
- `ofertas`, `estado_ofertas`
- `postulaciones`, `postulaciones_por_candidatos`, `etapas`

**Catálogos:**
- `habilidades`, `habilidades_por_busqueda`, `habilidades_candidatos`
- `modalidades` (presencial, remoto, híbrida)
- `paises`, `provincias`, `ciudades` (georreferencia Argentina vía API Georef)

**Relación simplificada:**
```
Empresa → Búsqueda → Detalle + Habilidades
                ↓
              Oferta → Postulación → Etapa
                              ↓
                          Candidato
```

---

## 12. Tecnologías

### Propuesta académica original

| Capa | Tecnología |
|------|------------|
| Framework | Laravel 12 |
| Backend | PHP 8.2 |
| Frontend | HTML5, CSS3, JavaScript |
| UI | Bootstrap 5 |
| Base de datos | MySQL |

### Implementación actual del prototipo

| Capa | Tecnología |
|------|------------|
| Backend | PHP 8.x (sin framework, estructura MVC manual) |
| Base de datos | MySQL (`talent_link`) |
| Acceso a datos | PDO |
| Frontend | HTML5, CSS3, JavaScript vanilla |
| Estilos | CSS propio por módulo (no Bootstrap en el código actual) |
| Autenticación | Sesiones PHP con control de permisos |
| Migraciones | Scripts SQL + ejecutor CLI |
| Datos geográficos | API Georef Argentina |

> **Nota para la presentación:** La propuesta académica contempla Laravel 12; el prototipo funcional actual está desarrollado en PHP plano con arquitectura tipo MVC. Esto puede presentarse como una decisión de prototipado rápido con migración planificada a Laravel.

---

## 13. Resultados esperados

Al completar el sistema, se espera lograr:

- **Reducir tiempos** de búsqueda y contratación de personal.
- **Centralizar la información** de candidatos, empresas y procesos en una sola plataforma.
- **Evitar pérdida de datos** que hoy se dispersan en emails y mensajes.
- **Facilitar el seguimiento** de cada postulación por etapas.
- **Mejorar la comunicación** entre empresa, consultora y candidato.
- **Disminuir la carga manual** del personal de RRHH.
- **Apoyar decisiones** con métricas e indicadores actualizados.

---

## 14. Estado actual del desarrollo

### Completado ✅

- Modelo de datos completo (20+ tablas relacionadas).
- Sistema de migraciones y seeders (roles, permisos, georef, datos demo).
- Autenticación con roles y permisos.
- Vista de login con diseño del prototipo.
- Dashboard con métricas reales.
- Alta de solicitudes de búsqueda (formulario + guardado en BD).
- Listado de candidatos con datos reales.
- Layout general (sidebar, navbar, estilos por módulo).
- Carga de provincias y localidades argentinas.

### En progreso / parcial ⚠️

- Listado de solicitudes (vista sin consulta a base de datos).
- Catálogos de estados, etapas y modalidades (tablas creadas, seeders pendientes).

### Pendiente ❌

- Ofertas laborales (publicar, listar, filtrar).
- Postulaciones (crear, listar, gestionar etapas).
- Alta y edición de candidatos con CV.
- Detalle de candidato para RRHH.
- Gestión de empresas.
- Administración de usuarios RRHH.
- Reportes y estadísticas avanzadas.
- Registro de nuevos usuarios y recuperación de contraseña.

---

## 15. Cronograma estimado del proyecto

| Etapa | Duración |
|-------|----------|
| Planificación | 2 semanas |
| Modelado de datos | 1 semana |
| Diseño (módulos, roles, interfaces) | 2 semanas |
| Construcción de base de datos | 1 semana |
| Desarrollo backend | 4 semanas |
| Desarrollo frontend | 5 semanas |
| Pruebas y ajustes | 2 semanas |
| **Total estimado** | **17 semanas** |

---

## 16. Datos de demostración

Para probar el sistema en desarrollo existen usuarios de prueba con contraseña `fantasia123`:

**Personal RRHH:** rrhh.ana.gomez@talentlink.com, rrhh.bruno.lopez@talentlink.com, rrhh.carla.martinez@talentlink.com

**Empresas:** talento@auroratech.com, contacto@mateycode.com, rrhh@pampafoods.com, jobs@andescargo.com, empleos@rioplata.com

**Candidatos:** luna.perez@mail.com, tomas.rojas@mail.com, maria.garcia@mail.com, juan.sosa@mail.com, sofia.diaz@mail.com

---

## 17. Propuesta de estructura para la presentación

Sugerencia de diapositivas que la IA puede generar:

1. **Portada** — TalentLink: Sistema de Gestión de Servicios de RRHH
2. **Integrantes y contexto académico**
3. **¿Qué es TalentLink?** — Definición y propósito
4. **Problemática** — Situación actual sin sistema
5. **Objetivo del proyecto**
6. **La consultora y su circuito de trabajo**
7. **Actores del sistema** — RRHH, Empresa, Candidato
8. **Flujo del negocio** — De solicitud a contratación (diagrama)
9. **Módulos del sistema** — Vista general
10. **Login y seguridad** — Roles y permisos
11. **Dashboard** — Métricas y panel de resumen
12. **Solicitudes de personal** — Alta y gestión
13. **Ofertas laborales** — Publicación de vacantes
14. **Candidatos y postulaciones** — Perfiles y seguimiento por etapas
15. **Diseño de interfaz** — Prototipo visual
16. **Modelo de datos** — Entidades principales
17. **Tecnologías utilizadas**
18. **Estado del desarrollo** — Qué está hecho y qué falta
19. **Resultados esperados**
20. **Cierre / preguntas**

---

## 18. Mensajes clave para destacar en la presentación

- TalentLink **no es solo un portal de empleos**: es una herramienta de **gestión interna** para una consultora de RRHH.
- El valor principal está en **centralizar y dar seguimiento** al proceso completo, no solo en publicar avisos.
- Tres actores con **necesidades distintas** conviven en la misma plataforma con permisos diferenciados.
- El sistema modela un **circuito real de negocio**: solicitud → búsqueda → oferta → postulación → etapas.
- Es un **prototipo en evolución**: la base está sólida (datos, auth, dashboard, alta de solicitudes) y los módulos restantes están diseñados y modelados.

---

*Documento preparado para alimentar herramientas de IA generadora de presentaciones. Proyecto TalentLink — LGI 2026 — Escuela Normal Superior N.º 10.*
