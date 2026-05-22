<div align="center">

# Gestión de FFE en centros educativos

**Trabajo de Fin de Grado · Desarrollo de Aplicaciones Web**

IES Ciudad Escolar · Curso académico 2025–2026

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL%2FMariaDB-10.4-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-4.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)

</div>

---

## Autores

| Nombre | Rol principal |
|---|---|
| Sebastián García Cifuentes | Programación backend — Módulo de Convenios, integraciones y pruebas |
| Alain Vázquez Medina | Programación backend — Módulo de Alumnos, filtros, modales y coordinación técnica |
| Abraham Josué Velásquez Granados | Diseño UI, base de datos, diagramas y documentación |

**Profesora tutora:** María Victoria González López

---

## Descripción del proyecto

El sistema **Gestión de FFE** es una aplicación web desarrollada como Trabajo de Fin de Grado del ciclo formativo de Desarrollo de Aplicaciones Web. Su objetivo es digitalizar y centralizar la gestión de las prácticas en empresa (FFE — Formación en Fase de Empresa) de los alumnos de Formación Profesional, sustituyendo el uso de hojas de cálculo y documentos dispersos por una plataforma única adaptada al flujo de trabajo real de los tutores de FP.

La aplicación está orientada a dos tipos de usuario:

- **Tutor**: gestiona los convenios con empresas, los alumnos de su ciclo, el plan formativo y la documentación de seguimiento.
- **Administrador**: supervisa tutores, valida convenios pendientes y tiene visión global del estado de todas las prácticas del centro.

El flujo de trabajo se organiza en **cuatro pasos**: Convenios → Alumnos → Plan Formativo → Seguimiento.

---

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.2 — patrón MVC implementado manualmente, sin framework |
| Base de datos | MySQL / MariaDB 10.4 — acceso mediante PDO con sentencias preparadas |
| Frontend interno | Bootstrap + JavaScript ES6 vanilla + CSS propio |
| Frontend externo | Tailwind CSS v4 (CDN) |
| Exportación | PHPSpreadsheet (Excel) · PHPWord (Word) · ZipStream |
| Servidor local | XAMPP (Apache 2.4 + PHP 8.2 + MariaDB 10.4) |
| Dependencias | Composer |
| Control de versiones | Git + GitHub |

---

## Arquitectura

El proyecto implementa el patrón **MVC de forma manual**, sin uso de ningún framework de backend. Esta decisión fue deliberada: construir el MVC desde cero permite demostrar dominio de la arquitectura más allá del uso de herramientas que lo implementan automáticamente.

```
Petición HTTP
      │
      ▼
  index.php  ←── Front Controller único
      │
      ├── Sin sesión  →  Vista/Login.php
      │
      ├── rol = tutor  →  Controlador_Tutores  →  Modelo  →  Vista/Steps/
      │
      └── rol = admin  →  Controlador_Admin   →  Modelo  →  Vista/Admin/
```

**Patrones de diseño aplicados:**

- **Singleton** — `Core/Conexion.php`: una única instancia PDO por petición compartida por todos los modelos.
- **Front Controller** — `index.php`: punto de entrada único que lee `$_SESSION['rol']` y `$_POST['accion']` para despachar al controlador correcto.
- **Template Method (implícito)** — Todas las vistas de los cuatro pasos siguen la misma estructura de inclusión desde `index_vista.php`.

---

## Estructura de carpetas

```
/
├── index.php                        # Front controller y punto de entrada único
├── composer.json
│
├── Core/
│   └── Conexion.php                 # Singleton PDO
│
├── Controlador/
│   ├── Controlador_Admin.php        # Dispatcher del rol administrador
│   ├── Controlador_Tutores.php      # Dispatcher del rol tutor
│   ├── Controlador_Alumnos.php
│   ├── Controlador_Convenios_Tutores.php
│   ├── Controlador_Convenios_Validos.php
│   ├── Controlador_Convenios_Pendientes.php
│   ├── Controlador_Plan_Formativo.php
│   ├── Controlador_Seguimiento.php
│   ├── Controlador_Docentes.php
│   ├── Controlador_Listado_Alumnos.php
│   ├── Controlador_Usuarios.php     # Autenticación
│   └── Logout.php
│
├── Modelo/
│   ├── Alumnos.php
│   ├── Convenios.php
│   ├── Convenios_Nuevos.php
│   ├── Tutores.php
│   ├── Usuarios.php
│   ├── Modulos.php
│   ├── Resultados_Aprendizaje.php
│   ├── Exportar.php
│   ├── Importar.php
│   └── GestorDocumentacion.php
│
├── Vista/
│   ├── Login.php
│   ├── index_vista.php              # Panel principal del tutor
│   ├── Vista_Admin.php
│   ├── Admin/
│   │   ├── Dashboard_Admin.php
│   │   ├── Components/
│   │   └── Sections/
│   ├── Tutores/
│   │   ├── Dashboard_Tutores.php
│   │   ├── Components/
│   │   └── Steps/                   # Convenios · Alumnos · Plan_Formativo · Seguimiento
│   └── Shared/
│       └── Modal_Paginacion.php
│
├── Convenios/                       # Módulo público (sin sesión requerida)
│   ├── Registro.php
│   ├── Procesar.php
│   └── Controlador_Registro.php
│
├── Helpers/
│   ├── Paginador.php
│   ├── Importar_Alumnos.php
│   ├── Importar_Convenios.php
│   ├── Exportar_PF.php
│   ├── Exportar_PF_Todo.php
│   ├── Exportar_Alumnos_Word.php
│   ├── Seguimiento_Listar.php
│   ├── Seguimiento_Subir.php
│   ├── Seguimiento_Eliminar.php
│   └── Seguimiento_Descargar.php
│
├── Seguridad/
│   └── Control_Accesos.php
│
├── Errores/
│   ├── 403.php · 404.php · 500.php
│   └── AlertaSistema.php
│
├── Public/
│   ├── css/dark-mode.css
│   ├── js/
│   │   ├── dark-mode.js
│   │   ├── script_tabs.js           # Navegación entre los 4 pasos
│   │   └── script_password.js       # Toggle visibilidad contraseña
│   └── ajax/Autocompletar.php
│
├── Documentacion/                   # PDFs subidos (fichas, valoraciones)
│   └── {ciclo}/{tipo}/{archivo}.pdf
│
└── Recursos/
    ├── citye.sql                    # Esquema completo + datos de prueba
    ├── Importar/                    # Plantillas Excel para importación
    └── Exportar/                    # Plantillas Word/Excel para exportación
```

---

## Flujo de trabajo del tutor (4 pasos)

```
Panel Tutor
  │
  ├── Paso 1 — Convenios
  │     ├── Buscar empresa en el catálogo general
  │     ├── Añadir convenio a su listado personal
  │     ├── Registrar nuevo convenio (empresa nueva)
  │     └── Revisar convenios pendientes de validación
  │
  ├── Paso 2 — Alumnos
  │     ├── CRUD de alumnos del ciclo
  │     ├── Importar desde plantilla Excel
  │     ├── Estados automáticos: Sin asignar / En proceso / Completado
  │     ├── Marcar como Enviado (requiere estado Completado)
  │     ├── Firmar alumno (requiere estado Enviado)
  │     └── Exportar listado a Word
  │
  ├── Paso 3 — Plan Formativo
  │     ├── Solo accesible para alumnos Firmados
  │     ├── Editar Resultados de Aprendizaje por módulo
  │     ├── Exportar PF individual (Excel)
  │     ├── Exportar todos los PF del ciclo (ZIP)
  │     └── Retroceder alumno al paso 2 (elimina Firmado y Enviado)
  │
  └── Paso 4 — Seguimiento
        ├── Listar documentos por alumno (detección automática)
        ├── Subir documentos PDF
        └── Descargar / Eliminar documentos
```

---

## Roles y permisos

| Acción | Admin | Tutor |
|---|:---:|:---:|
| Gestionar personal docente | ✓ | — |
| Ver todos los convenios del centro | ✓ | — |
| Validar convenios pendientes | ✓ | ✓ |
| Importar convenios desde Excel | ✓ | — |
| Ver alumnos de su ciclo | — | ✓ |
| Alta / edición / baja de alumnos | — | ✓ |
| Importar alumnos desde Excel | — | ✓ |
| Exportar listado de alumnos a Word | — | ✓ |
| Gestionar Plan Formativo | — | ✓ |
| Subir documentos de seguimiento | — | ✓ |
| Firmar alumno | ✓ | ✓ |
| Ver listado global de alumnos | ✓ | — |

---

## Instalación local (XAMPP)

### Requisitos

- PHP 8.0 o superior
- MySQL / MariaDB
- Apache con `mod_rewrite` activo
- Composer

### Pasos

```bash
# 1. Clonar el repositorio en htdocs
git clone https://github.com/AbrahamVelasquez/PROYECTO xampp/htdocs/PROYECTO
cd xampp/htdocs/PROYECTO

# 2. Instalar dependencias PHP
composer install

# 3. Importar la base de datos desde phpMyAdmin o consola
mysql -u root -p < Recursos/citye.sql

# 4. Configurar la conexión (ver más abajo)

# 5. Acceder en el navegador
# http://localhost/PROYECTO/
```

---

## Configuración de la base de datos

Editar `Core/Conexion.php` con los datos del entorno local:

```php
self::$instancia = new PDO(
    "mysql:host=localhost;dbname=citye;charset=utf8",
    "root",   // usuario MySQL
    "",       // contraseña (vacía en XAMPP por defecto)
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
```

---

## Credenciales de demostración

El archivo `Recursos/citye.sql` incluye datos de prueba precargados. Para evaluar la aplicación sin registro previo:

| | |
|---|---|
| **URL local** | `http://localhost/PROYECTO/` |
| **Usuario** | `carlos_tutor` |
| **Contraseña** | `car123` |
| **Rol** | tutor |
| **Usuario** | `admin` |
| **Contraseña** | `admin789` |
| **Rol** | admin |


---

## Seguridad

El sistema aplica las siguientes capas de protección:

1. **Autenticación por sesión**: sin sesión activa, cualquier petición redirige al login.
2. **Control de roles por petición**: el rol se evalúa en `index.php` en cada request, no solo al hacer login.
3. **PDO con sentencias preparadas**: todas las consultas SQL usan parámetros nombrados (`:param`), sin concatenación de strings.
4. **Saneamiento XSS**: todos los datos mostrados en vistas pasan por `htmlspecialchars()`.
5. **Aislamiento por ciclo**: cada tutor solo puede ver y modificar los alumnos de su ciclo (`id_ciclo` en sesión filtra todas las consultas).
6. **`.htaccess` en carpetas sensibles**: bloquea el acceso HTTP directo a `Controlador/`, `Modelo/`, `Core/`, `Helpers/`, `Seguridad/` y `Recursos/`.
7. **Output buffering**: `ob_start()` en `index.php` evita filtrar salida parcial antes de mostrar páginas de error.

---

## Importación y exportación

### Importación masiva

| Tipo | Formato | Helper |
|---|---|---|
| Alumnos | `.xlsx` / `.xls` | `Helpers/Importar_Alumnos.php` |
| Convenios | `.xlsx` / `.xls` | `Helpers/Importar_Convenios.php` |

Las plantillas están en `Recursos/Importar/`. Las columnas deben seguir el orden de la plantilla oficial; si el orden es incorrecto, el sistema lanza un error genérico (limitación conocida — PT-14).

### Exportación

| Tipo | Formato | Helper |
|---|---|---|
| Plan Formativo (alumno) | `.xlsx` | `Helpers/Exportar_PF.php` |
| Planes Formativos (ciclo completo) | `.zip` con un Excel por alumno | `Helpers/Exportar_PF_Todo.php` |
| Listado de alumnos | `.docx` | `Helpers/Exportar_Alumnos_Word.php` |
| Documentos de seguimiento | Descarga directa PDF | `Helpers/Seguimiento_Descargar.php` |

---

## Módulo externo de registro de convenios

La carpeta `Convenios/` implementa un formulario público accesible sin sesión, para que las empresas registren sus datos directamente:

```
GET  /Convenios/Registro.php?id_ciclo={id}  →  Formulario público (Tailwind CSS)
POST /Convenios/Procesar.php                →  Valida y guarda el convenio como "pendiente"
```

Los convenios pendientes aparecen en el panel del administrador para su validación. Si se accede sin `id_ciclo`, se bloquea el acceso con un modal de error.

---

## Planificación del proyecto

El proyecto se desarrolló con **metodología iterativa** entre marzo y mayo de 2026, con reuniones de seguimiento semanales los miércoles y cinco entregas parciales:

| Entrega | Fecha | Contenido |
|---|---|---|
| 1ª | 22 mar 2026 | Análisis, diseño BD, login funcional, estructura MVC |
| 2ª | 5 abr 2026 | Módulo de Convenios completo |
| 3ª | 19 abr 2026 | Módulo de Alumnos, filtros, modales |
| 4ª | 3 may 2026 | Correcciones e importación/exportación |
| 5ª | 17 may 2026 | Seguimiento completo, aplicación lista |

El diagrama de Gantt completo está disponible en:
[Google Sheets — Gantt TFG](https://docs.google.com/spreadsheets/d/1e8D3chdY5evDucl5nYbJrk0Ky4ZfbwesVBCNetZkw_s/edit?usp=sharing)

---

## Limitaciones conocidas

Documentadas durante el plan de pruebas:

| ID | Módulo | Descripción |
|---|---|---|
| PT-14 | Alumnos | Importar Excel con columnas en orden incorrecto lanza error genérico |
| PT-15 | Autenticación | No existe recuperación de contraseña — gestión manual por el admin |
| PT-16 | Sesión | La sesión expira sin aviso al usuario tras 60 min de inactividad |
| PT-17 | Concurrencia | Edición simultánea del mismo alumno por dos tutores: prevalece el último guardado |
| PT-18 | Compatibilidad | No funciona correctamente en IE 11 / Edge Legacy (Tailwind v4) |

---

## Mejoras futuras

- Despliegue en el servidor real del IES Ciudad Escolar con acceso HTTPS
- Sistema de notificaciones por correo al cambiar el estado de un alumno
- Ayuda contextual y manual de usuario integrado en la aplicación (H10 Nielsen)
- Recuperación de contraseña olvidada
- Atributos ARIA completos en modales (WCAG 4.1.2)
- Integración con la plataforma Raíces (Comunidad de Madrid)
- Migración de credenciales a variables de entorno (`.env`)

---

## Control de versiones

| Rama | Responsable | Función |
|---|---|---|
| `main` | Abraham Velásquez | Rama principal — solo recibe código validado mediante pull request |
| `develop` | Equipo | Rama de integración |
| `feature_alain` | Alain Vázquez | Rama personal de desarrollo |
| `feature_sebastian` | Sebastián García | Rama personal de desarrollo |
| `feature_abraham` | Abraham Velásquez | Rama personal de desarrollo |

Repositorio: [github.com/AbrahamVelasquez/PROYECTO](https://github.com/AbrahamVelasquez/PROYECTO)

---

## Contexto académico

Este proyecto forma parte del módulo **Proyecto** del ciclo formativo de **Grado Superior en Desarrollo de Aplicaciones Web** (DAW) del **IES Ciudad Escolar**, correspondiente al curso académico 2025–2026.

La memoria completa del TFG (53 páginas) incluye estudio de mercado (PEST, DAFO, CAME), especificación de requisitos funcionales y no funcionales, diagramas E-R, UML de clases y casos de uso, plan de pruebas y validación de usabilidad (Nielsen) y accesibilidad (WCAG 2.1).