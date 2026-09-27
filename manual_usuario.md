# 📖 Manual de Usuario — Biotel Suites

## 🔐 Acceso al Sistema

### 1. Iniciar Sesión
1. Abrir `login.html` en el navegador
2. Ingresar correo y contraseña
3. Click en "Ingresar al sistema"
4. Será redirigido al dashboard

### 2. Registrarse
1. Abrir `registro.html`
2. Completar formulario (nombre, cédula, correo, contraseña)
3. Aceptar términos y condiciones
4. Click en "Crear mi cuenta"

## 📊 Dashboard

- **Sidebar izquierdo:** navegación entre módulos
- **4 KPIs:** ocupación, check-ins, check-outs, ingresos
- **Tabla de reservaciones:** vista rápida del día

## 🧾 Gestión de Reservaciones (CRUD)

### Crear Reservación
1. Click en "+ Nueva Reservación"
2. Completar el modal
3. Click en "Guardar"

### Editar Reservación
1. Click en ✎ (lápiz) en la fila
2. Modificar campos
3. Guardar

### Eliminar Reservación
1. Click en 🗑 (papelera)
2. Confirmar en el modal

### Buscar
- Escribir en la barra → filtra en tiempo real
- Ignora mayúsculas y tildes

### Filtrar
- Por estado (Confirmada/Pendiente/Cancelada)
- Por habitación (Suite/Ejecutiva/Estándar)

## 📱 Uso en Móvil

- Presionar ☰ (hamburguesa) para abrir el menú
- La tabla tiene scroll horizontal
- Los modales ocupan 95% del ancho

## 👥 Roles de Usuario

| Rol | Permisos |
|-----|----------|
| Recepcionista | Check-in/out, reservaciones |
| Supervisor | + Reportes |
| Gerente | + Administración |
| Cliente | Solo reservas propias |