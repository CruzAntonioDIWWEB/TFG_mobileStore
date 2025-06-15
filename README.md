# 📱 Mobile Store

**Tienda online de dispositivos móviles y accesorios** -- TFG Desarrollo de Aplicaciones Web 

## 🎯 **Descripción del Proyecto**

Mi proyecto es una aplicación web completa que simula una tienda online especializada en la venta de dispositivos móviles y accesorios. Se ha implementado un sistema e-commerce funcional con gestión de usuarios, catálogo de productos, carrito de compras y panel administrativo.

## Enlace a la tienda
https://293d-2a02-3100-ac00-9f00-fd98-7abe-b7c4-caa.ngrok-free.app/dashboard/TFG/Public/index.php?controller=home&action=index

## ✨ **Características Principales**

### 🛒 **E-commerce Completo**
- Catálogo de productos con filtros avanzados
- Carrito de compras persistente
- Proceso de checkout con integración PayPal
- Gestión de pedidos y estados

### 👥 **Sistema de Usuarios**
- Registro y autenticación segura
- Perfiles de usuario con historial de compras
- Sistema de roles (Cliente/Administrador)
- Panel administrativo completo

### 📱 **Diseño Responsive**
- Mobile-first design
- Interfaz adaptativa para todos los dispositivos
- Navegación optimizada para pantallas táctiles
- Menú hamburguesa en móviles

### 🔧 **Características Técnicas**
- Arquitectura MVC personalizada
- URLs limpias con routing avanzado
- Validación client-side y server-side
- Medidas de seguridad implementadas
- Accesibilidad web (WCAG)

## 🛠 **Tecnologías Utilizadas**

### **Backend**
- **PHP 8.0+** - Lógica de servidor y MVC
- **MySQL 8.0+** - Base de datos relacional
- **PDO** - Conexión segura a base de datos
- **Composer** - Gestión de dependencias

### **Frontend**
- **HTML5** - Estructura semántica
- **SCSS/Sass** - Estilos organizados y modulares
- **JavaScript ES6+** - Interactividad y validación
- **Font Awesome** - Iconografía
- **Google Fonts** - Tipografías (Roboto, Open Sans, Lato)

### **Integraciones**
- **PayPal SDK** - Procesamiento de pagos
- **phpdotenv** - Gestión de variables de entorno

## 📁 **Estructura del Proyecto**

```
TFG/
├── 📁 assets/                 # Recursos estáticos
├── 📁 config/                 # Configuración del sistema
├── 📁 Controllers/            # Controladores MVC
├── 📁 Core/                   # Sistema de routing
├── 📁 Database/               # Scripts SQL
├── 📁 Models/                 # Modelos de datos
├── 📁 Public/                 # Punto de entrada
├── 📁 Views/                  # Templates y vistas
└── 📁 vendor/                 # Dependencias Composer
```

## 🚀 **Instalación y Configuración**

### **Requisitos del Sistema**
- PHP 8.0 o superior
- MySQL 8.0 o superior
- Apache/Nginx con mod_rewrite
- Composer

### **Pasos de Instalación**

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/mobile-store.git
   cd mobile-store
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar base de datos**
   ```bash
   # Crear base de datos
   mysql -u root -p < Database/SQL.sql
   ```

4. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   # Editar .env con tus credenciales de base de datos
   ```

5. **Configurar servidor web**
   - Apuntar el DocumentRoot a la carpeta `Public/`
   - Asegurar que mod_rewrite esté habilitado

## 🎮 **Uso de la Aplicación**

### **Para Usuarios**
- **Navegación:** Explora catálogos de móviles y accesorios
- **Filtros:** Busca por marca, tipo de accesorio, precio
- **Compra:** Añade productos al carrito y finaliza compra
- **Cuenta:** Gestiona perfil e historial de pedidos

### **Para Administradores**
- **Productos:** CRUD completo de catálogo
- **Categorías:** Gestión de clasificaciones
- **Usuarios:** Administración de cuentas
- **Pedidos:** Seguimiento y actualización de estados

## 🎨 **Diseño y UX**

### **Paleta de Colores**
- **Primario:** Rojo Corporativo (#E60000)
- **Secundario:** Gris Grafito (#333333)
- **Neutros:** Blanco (#FFFFFF), Gris Claro (#F2F2F2)

### **Tipografías**
- **Principal:** Roboto (legibilidad en pantalla)
- **Secundaria:** Open Sans (calidez y accesibilidad)
- **Botones:** Lato (modernidad y dinamismo)

## 🔐 **Seguridad Implementada**

- **Hash de contraseñas** con password_hash()
- **Sanitización de entrada** con htmlspecialchars()
- **Consultas preparadas** para prevenir SQL injection
- **Validación dual** (cliente y servidor)
- **Control de sesiones** seguro
- **Protección CSRF** en formularios

## 📱 **Responsive Design**

### **Breakpoints**
- **Mobile:** ≤ 480px
- **Tablet:** ≤ 768px  
- **Desktop:** ≤ 992px
- **Large Desktop:** ≥ 1200px

### **Características Móviles**
- Imágenes de productos optimizadas
- Touch targets de 44px mínimo
- Menú hamburguesa animado
- Navegación por gestos

## 🧪 **Testing y Validación**

- ✅ Validación de formularios en tiempo real
- ✅ Casos de prueba de funcionalidad del carrito
- ✅ Testing de seguridad (XSS, SQL injection)
- ✅ Pruebas de accesibilidad (ARIA, navegación por teclado)
- ✅ Compatibilidad cross-browser
- ✅ Testing responsive en múltiples dispositivos

## 📊 **Métricas del Proyecto**

- **~15,000 líneas de código**
- **40+ archivos PHP**
- **20+ componentes SCSS**
- **10+ módulos JavaScript**
- **30+ vistas/templates**
- **7 modelos de base de datos**

## 🎓 **Contexto Académico**

Este proyecto fue desarrollado como **Trabajo de Fin de Grado** para el **Ciclo Formativo de Grado Superior en Desarrollo de Aplicaciones Web** en el **IES Francisco Ayala (Granada)**.

### **Objetivos Pedagógicos Cumplidos**
- ✅ Aplicación de patrones de diseño MVC
- ✅ Desarrollo full-stack con tecnologías modernas
- ✅ Implementación de medidas de seguridad web
- ✅ Diseño responsive y accesible
- ✅ Gestión de base de datos relacionales
- ✅ Integración con APIs externas (PayPal)

## 📄 **Documentación**

El proyecto incluye documentación técnica completa:
- **Análisis y diseño** del sistema
- **Diagramas E/R** y de clases
- **Manual de estilos** con mockups
- **Casos de uso** y testing
- **Estructura detallada** de archivos

## 🤝 **Contribución**

Este es un proyecto académico, pero las sugerencias y feedback son bienvenidos:

1. Fork el proyecto
2. Crea una rama feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 **Licencia**

Este proyecto es de uso educativo y académico. Consulta el archivo `LICENSE` para más detalles.

## 👨‍💻 **Autor**

**Antonio Cruz García**
- 📧 Email: acg.purullena@gmail.com
- 🎓 TFG Desarrollo de Aplicaciones Web
- 🏫 IES Francisco Ayala - Granada

---

⭐ **Si este proyecto te resulta útil, no olvides darle una estrella!**
