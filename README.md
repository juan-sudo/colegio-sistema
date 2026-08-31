# Sistema Escolar — Laravel + XAMPP

Sistema de gestión escolar con:
- **Asistencia** por QR (cámara), código de barras (lector USB) y biométrico (huella, vía API/webhook)
- **Alertas automáticas por WhatsApp** a los padres cuando el alumno no llega
- **Notas**: ingreso manual por el profesor + **carga masiva por Excel**
- **Portal de padres**: ver notas y asistencia de sus hijos
- **Portal de estudiante**: ver y entregar tareas por curso
- **Panel de administrador**: gestión de alumnos, profesores, padres, cursos

---

## ⚠️ Importante sobre este entregable

Este paquete contiene el **código fuente completo** de la aplicación (migraciones, modelos,
controladores, rutas, vistas, servicios). **No incluye el framework Laravel en sí** (carpeta
`vendor/`, `artisan`, `bootstrap/`, etc.) porque eso se genera con Composer en tu propia máquina.
Sigue los pasos de abajo — toma unos 10 minutos.

---

## 1. Requisitos previos

1. **XAMPP** con PHP 8.2 o superior y MySQL — https://www.apachefriends.org
2. **Composer** — https://getcomposer.org/download/
3. (Opcional pero recomendado) **Node.js** si luego quieres compilar assets con Vite

Verifica en consola:
```bash
php -v
composer -V
```

## 2. Crear el proyecto base de Laravel

Abre una terminal dentro de la carpeta `htdocs` de XAMPP (ej. `C:\xampp\htdocs` en Windows,
o `/Applications/XAMPP/htdocs` en Mac) y ejecuta:

```bash
composer create-project laravel/laravel colegio-sistema
cd colegio-sistema
```

## 3. Copiar los archivos de este paquete

Copia (sobrescribiendo cuando se te pida) las carpetas de este ZIP dentro del proyecto que
acabas de crear, respetando la misma ruta:

```
database/migrations/*.php   → colegio-sistema/database/migrations/
database/seeders/*.php      → colegio-sistema/database/seeders/
app/Models/*.php            → colegio-sistema/app/Models/
app/Http/Controllers/*      → colegio-sistema/app/Http/Controllers/
app/Http/Middleware/*       → colegio-sistema/app/Http/Middleware/
app/Imports/*                → colegio-sistema/app/Imports/
app/Exports/*                → colegio-sistema/app/Exports/
app/Services/*               → colegio-sistema/app/Services/
app/Console/Commands/*       → colegio-sistema/app/Console/Commands/
routes/web.php               → colegio-sistema/routes/web.php  (reemplaza el archivo existente)
resources/views/*            → colegio-sistema/resources/views/ (reemplaza welcome.blade.php si aplica)
```

> Los archivos `0001_01_01_000000_create_users_table.php` de este ZIP **reemplazan** al que
> Laravel crea por defecto (agrega el campo `role`).

## 4. Instalar paquetes adicionales por Composer

```bash
composer require maatwebsite/excel      # Carga masiva de notas por Excel
composer require laravel/sanctum        # API para dispositivo biométrico
```

## 5. Registrar el middleware de roles

En Laravel 11, abre `bootstrap/app.php` y dentro de `->withMiddleware(function (Middleware $middleware) {...})`
agrega:

```php
$middleware->alias([
    'role' => \App\Http\Middleware\RoleMiddleware::class,
]);
```

## 6. Configurar la base de datos

Crea la base de datos en phpMyAdmin (XAMPP) llamada, por ejemplo, `colegio_sistema`.

En el archivo `.env` de tu proyecto:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=colegio_sistema
DB_USERNAME=root
DB_PASSWORD=
```

## 7. Configurar WhatsApp

Copia el contenido de `config/services_whatsapp_snippet.php` (de este ZIP) dentro del array
de `config/services.php` de tu proyecto. Luego agrega en `.env` las credenciales del proveedor
que elijas (recomendado para empezar: **Ultramsg**, es el más simple y económico):

```env
WHATSAPP_PROVIDER=ultramsg
ULTRAMSG_INSTANCE_ID=tu_instance_id
ULTRAMSG_TOKEN=tu_token
```

Alternativas: Twilio (`TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_WHATSAPP_FROM`) o Meta Cloud API
(`META_WHATSAPP_PHONE_ID`, `META_WHATSAPP_TOKEN`).

## 8. Migrar y poblar con datos de prueba

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

Esto crea 4 usuarios de prueba (contraseña `password` para todos):
| Rol | Correo |
|---|---|
| Admin | admin@colegio.test |
| Profesor | profesor@colegio.test |
| Padre | padre@colegio.test |
| Estudiante | estudiante@colegio.test |

## 9. Levantar el servidor

```bash
php artisan serve
```

Abre `http://localhost:8000/login`

## 10. Activar las alertas automáticas de inasistencia (cron)

Agrega el contenido de `routes/console_schedule_snippet.php` (de este ZIP) dentro de tu
`routes/console.php`. Luego, en el Programador de tareas de Windows (o cron en Linux/Mac),
agrega una tarea que ejecute cada minuto:

```bash
php /ruta/a/tu/proyecto/artisan schedule:run
```

Esto revisará todos los días a las 9:00am si hay alumnos marcados como "falta" sin notificar,
y enviará el WhatsApp automáticamente. También puedes disparar el envío manualmente desde el
botón **"Marcar faltas del día y notificar por WhatsApp"** en la pantalla de asistencia del profesor.

---

## Sobre el lector biométrico de huella

Un lector de huella físico (ZKTeco, Suprema, DigitalPersona, etc.) **no se conecta directamente
a una web** — tiene su propio SDK. Dos caminos típicos:

1. **Dispositivos con salida TCP/IP y SDK propio (recomendado, ej. ZKTeco):** su software puede
   configurarse para hacer un POST a un endpoint cuando detecta una huella. Ya dejé preparado
   `POST /api/biometric/marcar` (protegido con Sanctum) que recibe el `biometric_id` capturado y
   registra asistencia igual que el QR o código de barras — solo debes mapear el ID de huella del
   dispositivo al campo `biometric_id` de cada alumno (se hace una sola vez al momento de enrolarlos).
2. **Dispositivos "standalone":** guardan la marcación en su propia memoria/software (ej. ZKTime) y
   exportan un archivo (Excel/TXT) al final del día — en ese caso se puede construir un pequeño
   importador similar al de notas para subir ese archivo.

Cuéntame qué marca/modelo de lector planeas usar y te ayudo a preparar la integración exacta.

---

## Próximos módulos sugeridos (no incluidos aún, puedo generarlos)

- CRUD completo del panel de Administrador (alumnos, profesores, padres, cursos) con vistas
- Generación de carnet del estudiante en PDF con su QR y código de barras impresos
- Reportes de asistencia y boletas de notas en PDF
- Registro/edición de usuarios desde el panel admin con carga masiva de alumnos por Excel
- Notificaciones también por correo, además de WhatsApp
- Dashboard con gráficos (asistencia mensual, promedio de notas por curso)

Dime cuál priorizamos y seguimos construyendo.
