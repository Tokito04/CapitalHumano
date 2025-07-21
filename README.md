# **Proyecto Capital Humano \- Guía de Instalación y Puesta en Marcha**

Este documento proporciona una guía detallada para clonar, configurar y ejecutar el proyecto "Capital Humano" en un entorno de desarrollo local.

## **1\. Prerrequisitos**

Antes de comenzar, asegúrate de tener instalado el siguiente software en tu sistema:

* **Un entorno de servidor local:**  
  * **Opción A:** **XAMPP** ([Descargar aquí](https://www.apachefriends.org/index.html))  
  * **Opción B:** **WampServer** ([Descargar aquí](https://www.wampserver.com/en/))  
* **Composer:** Gestor de dependencias para PHP. ([Descargar aquí](https://getcomposer.org/))  
* **Git:** Sistema de control de versiones. ([Descargar aquí](https://git-scm.com/))  
* Un editor de código de tu preferencia (ej. Visual Studio Code).

## **2\. Pasos de Instalación**

### **Paso 2.1: Clonar el Repositorio**

Abre una terminal (se recomienda **Git Bash** en Windows) y clona el repositorio en la carpeta web de tu servidor.

* **Para XAMPP:** La carpeta es C:/xampp/htdocs/  
* **Para WampServer:** La carpeta es C:/wamp64/www/

\# Navega a la carpeta web de tu servidor (ejemplo para XAMPP)  
cd /c/xampp/htdocs/

\# Clona el repositorio  
git clone \[URL\_DEL\_REPOSITORIO\_GIT\] CapitalHumano

\# Ingresa a la carpeta del proyecto  
cd CapitalHumano

### **Paso 2.2: Instalar Dependencias de PHP**

Usa Composer para instalar todas las librerías necesarias.

composer install

### **Paso 2.3: Configurar el Archivo de Entorno (.env)**

El proyecto utiliza una base de datos compartida. Solo necesitas configurar tus credenciales.

1. En la raíz del proyecto, crea un archivo llamado .env.  
2. Pide al administrador del proyecto que te proporcione las credenciales de la base de datos.  
3. Copia y pega el siguiente contenido en el archivo .env, reemplazando los valores.

\# Credenciales de la Base de Datos (Supabase)  
DB\_HOST="el\_host\_proporcionado"  
DB\_PORT="el\_puerto\_proporcionado"  
DB\_DATABASE="postgres"  
DB\_USERNAME="postgres"  
DB\_PASSWORD="la\_contraseña\_proporcionada"

### **Paso 2.4: Generar Claves de OpenSSL**

Para la firma digital de los cargos, necesitas generar un par de claves locales.

1. Abre **Git Bash** en la raíz de tu proyecto.  
2. Crea la carpeta keys:  
   mkdir keys

3. Ejecuta los siguientes comandos:  
   \# Generar la clave privada  
   openssl genpkey \-algorithm RSA \-out keys/private\_key.pem \-pkeyopt rsa\_keygen\_bits:2048

   \# Extraer la clave pública  
   openssl rsa \-pubout \-in keys/private\_key.pem \-out keys/public\_key.pem

## **3\. Configuración del Servidor Local**

Sigue las instrucciones correspondientes al software que hayas instalado.

### **Opción A: Configuración con XAMPP**

1. Abre el panel de control de XAMPP.  
2. En la fila de **Apache**, haz clic en Config y selecciona PHP (php.ini).  
3. Busca y descomenta (elimina el ; del inicio) las siguientes líneas:  
   extension=pdo\_pgsql  
   extension=openssl

4. Guarda el archivo y reinicia el servicio de Apache.

### **Opción B: Configuración con WampServer**

1. Inicia WampServer. El icono en la bandeja del sistema debería ponerse verde.  
2. Haz clic izquierdo en el icono de WampServer en la bandeja del sistema.  
3. Navega a PHP \> PHP Extensions.  
4. Se desplegará una larga lista de extensiones. Asegúrate de que las siguientes dos tengan una marca de verificación (✅) a su lado. Si no la tienen, haz clic en ellas para activarlas.  
   * pdo\_pgsql  
   * openssl  
5. Después de activar cualquier extensión, WampServer reiniciará automáticamente los servicios.

## **4\. Ejecutar el Proyecto**

1. Asegúrate de que todos los servicios de tu servidor (XAMPP o WampServer) estén corriendo (icono en verde).  
2. Abre tu navegador y ve a la siguiente URL:  
   http://localhost/CapitalHumano/public/

3. Serás redirigido a la página de login.

### **Primeros Pasos**

Para empezar a usar el sistema, un **administrador existente debe proporcionarte tus credenciales de acceso (email y contraseña)**. La página de registro público no está habilitada para nuevos usuarios.

Una vez que tengas tus credenciales, podrás iniciar sesión y empezar a utilizar el sistema.

¡Felicidades\! El proyecto "Capital Humano" ya está funcionando en tu entorno local.
