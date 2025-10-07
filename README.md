# 💾 Backupator
Backupator es una herramienta en PHP para la automatización de copias de seguridad de bases de datos MySQL/MariaDB. Permite exportar procedimientos almacenados, funciones, estructuras de tablas y datos en bloques, organizar los backups en carpetas estructuradas, comprimirlos en ZIP y limpiar directorios antiguos.

# 📂 Estructura del repositorio

```bash
backupator/
├── config.php               # Configuración de conexión a la base de datos
├── list_tables.php          # Lista todas las tablas de la BD en JSON
├── count_rows.php           # Devuelve el número de filas de una tabla
├── backup_procedures.php    # Exporta procedimientos almacenados a .sql
├── backup_functions.php     # Exporta funciones definidas a .sql
├── backup_structure.php     # Exporta la estructura de tablas a .sql
├── backup_data_10k.php      # Exporta datos de una tabla en bloques de 10.000 filas
├── backup_data_1k.php       # Exporta datos de una tabla en bloques de 1.000 filas
├── zip_backup.php           # Comprime en ZIP un backup de tabla/carpeta
├── zip_full.php             # Genera un ZIP completo (BACKUP + DDL) para descarga
├── clean_backups.php        # Limpia carpetas BACKUP, DDL y ZIP
├── DDL/                     # Carpeta de salida (procedures/, functions/, structure/)
├── BACKUP YYYY-MM-DD/       # Carpeta de backups diarios (datos exportados)
├── ZIP/                     # Carpeta para backups comprimidos
├── temp/                    # Carpeta temporal para generar los .zip
└── README.md                # Documentación
```

# 🧩 Funcionalidades principales
🔑 Conexión a ```MySQL/MariaDB``` mediante mysqli.

📦 Exportación de procedimientos almacenados (**```SHOW CREATE PROCEDURE```**).

🧮 Exportación de funciones (**```SHOW CREATE FUNCTION```**).

🗂️ Exportación de estructuras de tablas (**```SHOW CREATE TABLE```**).

📊 Exportación de datos en bloques de 1.000 o 10.000 filas, comprimidos en ZIP.

📁 Organización automática en carpetas DDL/ y BACKUP YYYY-MM-DD/.

🗜️ Compresión en ZIP de backups individuales o completos (```zip_backup.php```, ```zip_full.php```).

🗑️ Limpieza de backups antiguos con ```clean_backups.php```.

🕒 Automatización programable mediante cron (```Linux```) o Tareas Programadas (```Windows```).

🌐 Interfaz web interactiva:

- Botones para lanzar backups y resetear.

- Gráficos en tiempo real con Chart.js (donuts y líneas).

- Cálculo aproximado del tiempo restante.

- Barras de progreso por tabla.

- Panel superior con métricas globales (tablas, filas, efectividad, tiempo).

- Menú lateral configurable (conexión, idioma, gráficos, log).

- Descarga automática del ZIP final al completar el proceso.

# ⚙️ Metodología del código
**Lenguaje: PHP.**

Scripts principales:

- list_tables.php: devuelve todas las tablas en JSON.

- count_rows.php: devuelve el número de registros de una tabla.

- backup_procedures.php: exporta procedimientos almacenados.

- backup_functions.php: exporta funciones.

- backup_structure.php: exporta la estructura de una tabla.

- backup_data_10k.php / backup_data_1k.php: exportan datos en bloques paginados.

- zip_backup.php: comprime en ZIP una tabla/carpeta.

- zip_full.php: genera un ZIP completo con todo el backup + DDL.

- clean_backups.php: elimina directorios antiguos.

# 🚀 Instalación y uso
Clonar el repositorio:

```bash
git clone https://github.com/Francisco-Sole/backupator.git
cd backupator
```

Configurar la conexión en config.php:

```php
<?php
$host_C = "localhost";
$usuario_C = "usuario";
$pasword_C = "password";
$nombreDeBaseDeDatos_C = "midatabase";
?>
```
## Modo CLI: ejecutar scripts PHP manualmente o con cron.

Exportar procedimientos:

```bash
php backup_procedures.php
```
Exportar funciones:

```bash
php backup_functions.php
```
Exportar estructura de una tabla:

```bash
php backup_structure.php tabla=usuarios
```
Exportar datos en bloques:

```bash
php backup_data_10k.php tabla=usuarios count=50000 current=0
```
Generar ZIP completo:
```bash
php zip_full.php
```
Limpiar directorios:
```bash
php clean_backups.php
```
##Modo Web: abrir la interfaz en el navegador:

```bach
http://localhost/backupator/interfaz/index.html
```
Desde ahí podrás:

- Ver todas las tablas y su número de registros.

- Lanzar backups de procedimientos, funciones, estructuras y datos.

- Seguir el progreso en gráficos y barras de estado.

- Descargar el ZIP final con todo el backup.
# 🖱️ Usabilidad
Puede ejecutarse manualmente o programarse con cron en Linux.

Ejemplo de cron para ejecutar cada día a las 2:00 AM:

```bash
0 2 * * * /usr/bin/php /ruta/backupator/backup_procedures.php
```
Ejemplo de cron para exportar datos en bloques cada día a las 3:00 AM:

```bash
0 3 * * * /usr/bin/php /ruta/backupator/backup_data_10k.php tabla=usuarios count=50000 current=0
```
Ejemplo de cron para generar ZIP completo cada día a las 4:00 AM:

```bash
0 4 * * * /usr/bin/php /ruta/backupator/zip_full.php
```
Ejemplo de cron para limpiar directorios cada mes:

```bash
0 0 1 * * /usr/bin/php /ruta/backupator/clean_backups.php
```

# 📸 Interfaz Web (Magic Backup)
La interfaz web ofrece un dashboard visual con:

Panel superior de métricas globales.

Barras de progreso por tabla.

Gráficos dinámicos (donuts y líneas) con Chart.js.

Botones de acción (BACKUP!, RESET).

Menú lateral configurable.
# 👨‍💻 Autor
Francisco Solé 

📍 Barcelona, España 

🎯 Documentación técnica, optimización SQL y visualización de datos
