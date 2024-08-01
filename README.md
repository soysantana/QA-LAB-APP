## Documentación para Mantenimiento del Código del Laboratorio de Mecánica de Suelos

---

### Estructura del Código

El sistema de software está desarrollado utilizando las siguientes tecnologías y estructuras:

- **Lenguaje de Programación:** PHP 8.3.9
- **Framework:** Bootstrap 5.3.3
- **Biblioteca:** Apexcharts, Boxicons, Echarts, Remix Icon, TinyMCE, FPDF, FPDI
- **Base de Datos:** MySQL 8.0.38
- **Frontend:** HTML, CSS, JavaScript, Sass.

La estructura del proyecto sigue las siguientes divisiones principales:

1. **Directorios Principales:**
   - `/lab-app/`: Directorio principal del proyecto.
   - `/assets/`: Directorio que contiene recuersos estaticos como imagenes, hoja de estilo y vendor.
   - `/components/`: Directorio que contiene partes reutilizables para la aplicacion.
   - `/config/`: Directorio que contiene la conexion a la base de datos.
   - `/database/`: Directorio que contiene la logica para cada ensayo de guardar, actualizar y eliminar de la base de datos.
   - `/js/`: Directorio que contiene los calculos para cada ensayo.
   - `/libs/`: Directorio que contiene las bibliotecas o librerias como FPDF y FPDI para generar reportes en PDF.
   - `/pages/`: Directorio que contiene cada uno de los ensayos requeridos por el laboratorio.
   - `/pdf/`: Directorio que contiene los PDF para cada ensayo.
   - `/php/`: Directorio que contiene ajax para hacer busqueda en la base de datos como muestra, humedad, gravedad espesifica.
   - `/reviews/`: Directorio que contiene cada uno de los ensayos para su revision del supervisior.
   - `/user/`: Directorio que contiene la logica para los usuarios como autenticacion, salida.

2. **Componentes Principales:**
   
---

### Funcionalidades Implementadas

El sistema de software actualmente soporta las siguientes funcionalidades principales:
  
1. **Gestión de Ensayos:**
   - Registro de los ensayos.
   - Analiis de los ensayos.
   - Revision de los ensayos.
   - Almacenamientos de los ensayos.
   - Capacidad para el seguimiento de muestra.
   - Capacidad para ingresar datos de muestra.
   - Lista de los ensayos pendientes.
   - Capacidad para la Planificacion Semanal.
   - Proceso de muestreo.

2. **Reportes y Gráficos:**
   - Generación automática de reportes en formato PDF para cada tipo de ensayo.
   - Representación gráfica de resultados utilizando bibliotecas JavaScript como Echarts.
   - Generacion automatica de los ensayos pendientes separada por tipo de ensayos.
   - Generacion automaticade de sumarios en formato XLSX.

3. **Administración de Usuarios:**
   - Autenticación de usuarios con diferentes roles (Supervisor, Control Documnetos, Tecnico).
   - Control de acceso basado en roles para gestionar permisos de usuario.
   - Creacion de nuevos usuarios.
  
4. **Otros:**
   - Notificaciones para saber el estado del ensayo como repetido o revisado.
   - Visualizacion del proceso de muestreo.
   - Visualizacion del metodo del proctor.
   - Perfil de usuario.
   - Acceso limitado por roles de usuario.
   - Cambio de contraseña.

---

### Mantenimiento y Actualización

Para mantener el sistema de software actualizado y optimizado, se deben seguir las siguientes prácticas:

1. **Actualización de Dependencias:**
   - Programa revisiones periódicas de las dependencias del proyecto, incluyendo frameworks, bibliotecas y paquetes de terceros.
   - Identifica versiones obsoletas o vulnerabilidades conocidas que puedan afectar la seguridad o el rendimiento del sistema.
  
2. **Gestión de Versiones con Git:**
   - Utiliza Git para gestionar las dependencias del proyecto.
   - Crea ramas específicas para las actualizaciones de dependencias y realiza fusiones (merges) solo después de confirmar que las pruebas han sido exitosas.

3. **Documentación Continua:**
   - Mantener actualizada la documentación técnica del código, incluyendo descripciones detalladas de funciones, clases y métodos.
   - Utilizar comentarios claros en el código para facilitar la comprensión y el mantenimiento futuro.

---

 
