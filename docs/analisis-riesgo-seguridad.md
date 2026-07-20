# Análisis de riesgo y medidas de seguridad — Sistema CapitalHumano

**Sistema evaluado:** CapitalHumano (gestión de talento humano/RRHH) — PHP 8 puro (MVC casero, PSR-4), PostgreSQL vía PDO, Apache 2.
**Metodología de análisis de riesgo:** OWASP Risk Rating Methodology.
**Fecha:** 2026-07-07.

## 1. Introducción y alcance

Este informe documenta (a) las medidas de seguridad implementadas sobre el sistema CapitalHumano y (b) el análisis de riesgo de las vulnerabilidades encontradas, calificadas con la **metodología de calificación de riesgo de OWASP** (OWASP Risk Rating Methodology), junto con el tratamiento aplicado a cada riesgo.

A diferencia de un ejercicio teórico, todos los hallazgos de este informe provienen de una revisión real del código fuente de CapitalHumano (rutas, controladores, modelos, vistas y configuración del servidor), por lo que las medidas y el análisis de riesgo reflejan el estado real del sistema antes y después de la intervención.

## 2. Medidas de seguridad implementadas

Se implementaron **8 medidas** (supera el mínimo de 7 solicitado), cada una dirigida a una vulnerabilidad real encontrada en el sistema:

| # | Medida | Archivo(s) principal(es) | OWASP Top 10 2021/2025 relacionado |
|---|--------|---------------------------|-------------------------------|
| 1 | Validación segura de subida de archivos: whitelist de extensión, verificación de tipo MIME real (magic bytes con `finfo`), límite de tamaño, nombre de archivo aleatorio; bloqueo de ejecución de PHP en `public/uploads/` | `app/Helpers/UploadHelper.php`, `public/uploads/.htaccess`, `ColaboradorController.php` | A05:2025 Injection / A06:2025 Insecure Design |
| 2 | Protección CSRF (token sincronizador por sesión) en todos los formularios que modifican estado, con validación centralizada en el router | `app/Helpers/CsrfHelper.php`, `public/index.php`, vistas con `<form method="POST">` | A01:2025 Broken Access Control |
| 3 | Corrección de control de acceso: `/usuarios/update` ahora exige rol administrador (antes solo exigía sesión); endpoints `/api/*` exigen sesión activa **o** API Key válida; eliminación de checks de sesión duplicados y dispersos por el router | `public/index.php`, `app/Helpers/AuthHelper.php`, `app/Controllers/Api/ApiController.php` | A01:2025 Broken Access Control |
| 4 | Endurecimiento de sesión: cookie `HttpOnly` + `SameSite=Strict` + `Secure` condicional a HTTPS; regeneración del ID de sesión al autenticar (previene fijación de sesión); limpieza explícita de la cookie en logout | `public/index.php`, `app/Controllers/UsuarioController.php` | A07:2021 Identification and Authentication Failures |
| 5 | Eliminación de fuga de información en errores: los mensajes técnicos de excepciones PDO ya no se muestran al usuario, se registran en el log del servidor (`error_log`) | `app/Config/Database.php`, `app/Models/Colaborador.php` | A09:2025 Security Logging and Monitoring Failures |
| 6 | Cabeceras de seguridad HTTP: `Content-Security-Policy`, `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` en toda respuesta; se eliminaron los `<script>` inline restantes para poder aplicar una CSP estricta sin `unsafe-inline` en scripts | `public/index.php`, `public/js/*.js` | A02:2025 Security Misconfiguration |
| 7 | Gestión de secretos: la API Key de Contraloría se movió del código fuente a variables de entorno (`.env`), comparación en tiempo constante con `hash_equals()`, se eliminó la clave que se imprimía en el JavaScript del cliente, se corrigieron permisos de archivo de `.env` | `app/Controllers/Api/ApiController.php`, `.env`, `views/dashboard.php` | A04:2025 Cryptographic Failures |
| 8 | Protección contra fuerza bruta en el login: bloqueo temporal (5 minutos) de la cuenta tras 5 intentos fallidos consecutivos, con reseteo del contador al autenticar correctamente | `app/Models/Usuario.php`, `app/Controllers/UsuarioController.php`, migración en `database.sql` | A07:2021 Identification and Authentication Failures |

**Controles preexistentes que se preservaron** (ya cumplían buenas prácticas y no se modificaron): hashing de contraseñas con `password_hash`/`password_verify` (bcrypt), uso consistente de sentencias preparadas PDO con parámetros vinculados (sin inyección SQL en ningún modelo), separación del código de aplicación fuera del webroot público (`public/` como único `DocumentRoot`), exclusión correcta de `.env` del control de versiones.

## 3. Análisis de riesgo — Metodología OWASP Risk Rating

Se identificaron **7 riesgos** (supera el mínimo de 5 solicitado). Para cada uno se calculan los factores de **Probabilidad** (Likelihood) — agente de amenaza + facilidad de explotación — y de **Impacto** (Impact) — técnico + de negocio —, cada factor puntuado de 0 a 9 según las tablas estándar de OWASP. El promedio de cada grupo de 4 factores determina si Probabilidad e Impacto son BAJO (&lt;3), MEDIO (3-6) o ALTO (&gt;6). La combinación de ambos en la matriz de OWASP produce la severidad global.

### Matriz de severidad OWASP (Probabilidad × Impacto)

| Probabilidad \ Impacto | BAJO | MEDIO | ALTO |
|---|---|---|---|
| **ALTO** | Medio | Alto | **Crítico** |
| **MEDIO** | Bajo | Medio | Alto |
| **BAJO** | Nota | Bajo | Medio |

---

### R1 — Subida de archivos sin restricción → Ejecución remota de código (RCE)

*Antes de la corrección:* `ColaboradorController::store()`/`update()` solo verificaban `UPLOAD_ERR_OK`; no había whitelist de extensión ni verificación de tipo MIME real; los archivos se guardaban dentro del webroot (`public/uploads/`) conservando la extensión original, sin ninguna restricción que impidiera la ejecución de PHP en esa carpeta. Un atacante con una cuenta de administrador (o que engañara a un administrador mediante CSRF, dado que tampoco existía protección CSRF) podía subir un archivo `shell.php` disfrazado de foto o PDF y ejecutarlo visitando su URL directamente.

| Factor de Probabilidad | Puntaje | Factor de Impacto | Puntaje |
|---|---|---|---|
| Nivel de habilidad | 5 | Pérdida de confidencialidad | 9 |
| Motivo | 9 | Pérdida de integridad | 9 |
| Oportunidad (combinado con ausencia de CSRF) | 7 | Pérdida de disponibilidad | 7 |
| Tamaño (usuarios autenticados) | 6 | Pérdida de trazabilidad | 9 |
| Facilidad de descubrimiento | 7 | Daño financiero | 7 |
| Facilidad de explotación | 9 | Daño reputacional | 8 |
| Conocimiento público | 9 | Incumplimiento normativo | 7 |
| Detección de intrusiones | 9 | Violación de privacidad | 7 |
| **Probabilidad promedio** | **7.6 — ALTO** | **Impacto promedio** | **7.9 — ALTO** |

**Severidad global: CRÍTICO**

---

### R2 — Ausencia de protección CSRF

*Antes de la corrección:* ningún formulario POST (login, registro, crear/editar colaborador, cargos, actualizar usuario, generar resuelto de vacaciones) incluía token CSRF. Un atacante podía alojar una página con un formulario auto-enviado que, al ser visitado por un administrador con sesión activa, ejecutara cualquier acción de escritura en su nombre.

| Factor de Probabilidad | Puntaje | Factor de Impacto | Puntaje |
|---|---|---|---|
| Nivel de habilidad | 3 | Pérdida de confidencialidad | 3 |
| Motivo | 6 | Pérdida de integridad | 8 |
| Oportunidad | 9 | Pérdida de disponibilidad | 3 |
| Tamaño (usuarios de internet) | 9 | Pérdida de trazabilidad | 7 |
| Facilidad de descubrimiento | 7 | Daño financiero | 5 |
| Facilidad de explotación | 7 | Daño reputacional | 5 |
| Conocimiento público | 9 | Incumplimiento normativo | 5 |
| Detección de intrusiones | 9 | Violación de privacidad | 5 |
| **Probabilidad promedio** | **7.4 — ALTO** | **Impacto promedio** | **5.1 — MEDIO** |

**Severidad global: ALTO**

---

### R3 — Control de acceso roto (`/usuarios/update` sin verificación de rol; `/api/*` sin sesión)

*Antes de la corrección:* cualquier usuario autenticado (incluido el rol "Consulta", de solo lectura) podía enviar un POST a `/usuarios/update` y cambiar el rol o estado de cualquier usuario del sistema, incluso convertirse a sí mismo en administrador. Los endpoints `/api/*` no exigían sesión, solo una API Key hardcodeada en el código fuente que además se imprimía en el JavaScript del dashboard, visible para cualquier usuario autenticado desde "Ver código fuente".

| Factor de Probabilidad | Puntaje | Factor de Impacto | Puntaje |
|---|---|---|---|
| Nivel de habilidad | 3 | Pérdida de confidencialidad | 7 |
| Motivo | 6 | Pérdida de integridad | 7 |
| Oportunidad | 6 | Pérdida de disponibilidad | 2 |
| Tamaño (usuarios autenticados) | 6 | Pérdida de trazabilidad | 7 |
| Facilidad de descubrimiento | 7 | Daño financiero | 5 |
| Facilidad de explotación | 7 | Daño reputacional | 6 |
| Conocimiento público | 6 | Incumplimiento normativo | 5 |
| Detección de intrusiones | 9 | Violación de privacidad | 5 |
| **Probabilidad promedio** | **6.25 — ALTO** | **Impacto promedio** | **5.5 — MEDIO** |

**Severidad global: ALTO**

---

### R4 — Divulgación de información mediante mensajes de error

*Antes de la corrección:* `Database.php` y `Colaborador::actualizar()` usaban `die($e->getMessage())` o mensajes de depuración (`"Depuración: ..."`), exponiendo directamente al navegador detalles internos (mensajes de PDO, nombres de tablas/columnas, IDs) ante cualquier fallo de conexión o de actualización.

| Factor de Probabilidad | Puntaje | Factor de Impacto | Puntaje |
|---|---|---|---|
| Nivel de habilidad | 1 | Pérdida de confidencialidad | 4 |
| Motivo | 4 | Pérdida de integridad | 1 |
| Oportunidad | 7 | Pérdida de disponibilidad | 1 |
| Tamaño (usuarios anónimos) | 9 | Pérdida de trazabilidad | 1 |
| Facilidad de descubrimiento | 7 | Daño financiero | 1 |
| Facilidad de explotación | 7 | Daño reputacional | 3 |
| Conocimiento público | 6 | Incumplimiento normativo | 2 |
| Detección de intrusiones | 9 | Violación de privacidad | 1 |
| **Probabilidad promedio** | **6.25 — ALTO** | **Impacto promedio** | **1.75 — BAJO** |

**Severidad global: MEDIO**

---

### R5 — Gestión de sesión débil (fijación de sesión, cookie sin `HttpOnly`/`Secure`/`SameSite`)

*Antes de la corrección:* `session_start()` se invocaba sin ninguna configuración de cookie; no se regeneraba el ID de sesión al autenticar (fijación de sesión); el logout no limpiaba explícitamente la cookie.

| Factor de Probabilidad | Puntaje | Factor de Impacto | Puntaje |
|---|---|---|---|
| Nivel de habilidad | 6 | Pérdida de confidencialidad | 7 |
| Motivo | 6 | Pérdida de integridad | 7 |
| Oportunidad | 4 | Pérdida de disponibilidad | 2 |
| Tamaño (usuarios autenticados) | 6 | Pérdida de trazabilidad | 7 |
| Facilidad de descubrimiento | 4 | Daño financiero | 5 |
| Facilidad de explotación | 4 | Daño reputacional | 5 |
| Conocimiento público | 6 | Incumplimiento normativo | 5 |
| Detección de intrusiones | 9 | Violación de privacidad | 5 |
| **Probabilidad promedio** | **5.6 — MEDIO** | **Impacto promedio** | **5.4 — MEDIO** |

**Severidad global: MEDIO**

---

### R6 — Ausencia de cabeceras de seguridad HTTP y de HTTPS

*Antes de la corrección:* el sitio solo escuchaba en el puerto 80 (HTTP), sin `headers_module` ni `ssl_module` habilitados en Apache; no existía ninguna cabecera `Content-Security-Policy`, `X-Frame-Options`, `X-Content-Type-Options` ni `Strict-Transport-Security`.

| Factor de Probabilidad | Puntaje | Factor de Impacto | Puntaje |
|---|---|---|---|
| Nivel de habilidad | 6 | Pérdida de confidencialidad | 7 |
| Motivo | 4 | Pérdida de integridad | 5 |
| Oportunidad | 4 | Pérdida de disponibilidad | 1 |
| Tamaño (usuarios autenticados) | 6 | Pérdida de trazabilidad | 5 |
| Facilidad de descubrimiento | 9 | Daño financiero | 3 |
| Facilidad de explotación | 5 | Daño reputacional | 5 |
| Conocimiento público | 6 | Incumplimiento normativo | 5 |
| Detección de intrusiones | 9 | Violación de privacidad | 5 |
| **Probabilidad promedio** | **6.1 — ALTO** | **Impacto promedio** | **4.5 — MEDIO** |

**Severidad global: ALTO**

---

### R7 — Secretos débiles o expuestos (API Key hardcodeada + contraseña de BD trivial)

*Antes de la corrección:* `ApiController::API_KEY_CONTRALORIA` era una constante fija en el código fuente (`'CONT-123-XYZ'`), comparada sin tiempo constante, y se imprimía en el JavaScript del dashboard visible para cualquier usuario autenticado. Adicionalmente, la contraseña de la base de datos en `.env` era trivialmente débil (`capitalista`).

| Factor de Probabilidad | Puntaje | Factor de Impacto | Puntaje |
|---|---|---|---|
| Nivel de habilidad | 3 | Pérdida de confidencialidad | 7 |
| Motivo | 6 | Pérdida de integridad | 2 |
| Oportunidad | 7 | Pérdida de disponibilidad | 1 |
| Tamaño (usuarios autenticados) | 6 | Pérdida de trazabilidad | 7 |
| Facilidad de descubrimiento | 7 | Daño financiero | 3 |
| Facilidad de explotación | 7 | Daño reputacional | 5 |
| Conocimiento público | 6 | Incumplimiento normativo | 5 |
| Detección de intrusiones | 9 | Violación de privacidad | 5 |
| **Probabilidad promedio** | **6.4 — ALTO** | **Impacto promedio** | **4.4 — MEDIO** |

**Severidad global: ALTO**

### Resumen de severidad

| Riesgo | Probabilidad | Impacto | Severidad |
|---|---|---|---|
| R1 — Subida de archivos sin restricción (RCE) | Alto | Alto | **Crítico** |
| R2 — Ausencia de CSRF | Alto | Medio | Alto |
| R3 — Control de acceso roto | Alto | Medio | Alto |
| R6 — Sin cabeceras de seguridad / sin HTTPS | Alto | Medio | Alto |
| R7 — Secretos débiles/expuestos | Alto | Medio | Alto |
| R4 — Divulgación de información en errores | Alto | Bajo | Medio |
| R5 — Gestión de sesión débil | Medio | Medio | Medio |

## 4. Tratamiento del riesgo

Para cada riesgo se indica la decisión de tratamiento (**Mitigar**, **Aceptar**, **Transferir** o **Evitar**), la acción tomada y el riesgo residual esperado.

| Riesgo | Decisión | Acción tomada | Riesgo residual |
|---|---|---|---|
| R1 — Subida de archivos (RCE) | **Mitigar** | Medida 1: whitelist de extensión + verificación de tipo MIME real + nombre de archivo aleatorio + bloqueo de ejecución PHP en `public/uploads/` vía `.htaccess` | Bajo. Recomendación futura: servir los archivos subidos desde fuera del webroot mediante un script controlador con `Content-Disposition: attachment`, en vez de exponerlos como archivos estáticos directos. |
| R2 — Ausencia de CSRF | **Mitigar** | Medida 2: token CSRF por sesión, validado de forma centralizada para toda solicitud POST | Bajo. |
| R3 — Control de acceso roto | **Mitigar** | Medida 3: `/usuarios/update` ahora exige rol administrador; `/api/*` exige sesión o API Key | Bajo, aunque se recomienda migrar el enrutador de un `switch` con checks manuales a un middleware centralizado, dado que esta clase de regresión (olvidar un check de permiso) ya había ocurrido antes en `/register` (commits `ada8157`/`f11e760`). |
| R4 — Divulgación de información en errores | **Mitigar** | Medida 5: los errores técnicos se registran con `error_log()`, el usuario solo ve un mensaje genérico | Bajo. |
| R5 — Gestión de sesión débil | **Mitigar** (parcial) | Medida 4: cookie `HttpOnly`/`SameSite=Strict`, `Secure` condicional a HTTPS, regeneración de ID en login, limpieza en logout | Medio-Bajo. El sitio aún opera solo sobre HTTP en este entorno, por lo que el flag `Secure` permanece inactivo y la cookie viaja sin cifrar; el tratamiento completo depende de R6 (HTTPS). |
| R6 — Sin cabeceras de seguridad / sin HTTPS | **Mitigar** (parcial) + **Aceptar temporalmente** el residual de HTTPS | Medida 6: CSP, `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` añadidas a toda respuesta | Medio. Habilitar HTTPS/HSTS requiere emitir un certificado TLS (p. ej. Let's Encrypt) y reconfigurar el vhost de Apache — está fuera del alcance de este cambio de código y se acepta como riesgo residual con la recomendación explícita de resolverlo antes de exponer el sistema en producción. |
| R7 — Secretos débiles/expuestos | **Mitigar** (parcial) + **Aceptar temporalmente** la contraseña de BD | Medida 7: clave API movida a `.env`, comparación con `hash_equals()`, ya no se expone en el cliente | Medio-Bajo. La contraseña de la base de datos (`capitalista`) sigue siendo débil; rotarla requiere un `ALTER USER` coordinado en PostgreSQL fuera de este repositorio, por lo que se documenta como acción pendiente con dueño y plazo definidos por el equipo. |

## 5. Conclusiones

El sistema CapitalHumano, si bien contaba con buenas bases (hashing bcrypt de contraseñas y uso consistente de sentencias preparadas PDO que evitan inyección SQL), presentaba vulnerabilidades reales y de severidad significativa — en particular una ruta de ejecución remota de código a través de la subida de archivos sin restricciones, calificada como **Crítica** bajo la metodología OWASP. Las 8 medidas implementadas mitigan directamente los 7 riesgos identificados, reduciendo la severidad global del sistema. Los riesgos residuales que quedan pendientes (principalmente la migración a HTTPS y la rotación de la contraseña de base de datos) se documentan explícitamente con su tratamiento recomendado, en línea con el principio de que la seguridad es un proceso continuo de identificación, tratamiento y reevaluación de riesgos, no un estado que se alcanza una única vez.
