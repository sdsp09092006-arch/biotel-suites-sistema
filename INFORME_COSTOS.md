# 📊 INFORME DE VALORACIÓN DE COSTOS TI
## Sistema de Gestión Hotelera — Biotel Suites

**Metodología:** Norma Oficial de 7 Pasos del Ing. Eduardo Nieves
**Materia:** ADS-433 — Análisis y Diseño de Sistemas
**Institución:** IUJO Extensión Barquisimeto
**Lapso:** 2-2026
**Fecha:** Octubre 2026

---

## 📋 Datos Base del Proyecto

| Parámetro | Valor |
|-----------|-------|
| **Total de horas registradas en Kanban** | 20.0 h |
| **Integrantes del equipo** | 5 desarrolladores Full-Stack |
| **Base de horas mensuales** | 176 h (22 días × 8 h) |
| **Base de horas anuales** | 2,112 h (176 h × 12 meses) |
| **Moneda** | USD (dólares) |

---

## 🔢 Aplicación de los 7 Pasos

### PASO 1: Insumos Digitales (C_dir)

Servicios cloud y suscripciones necesarias para el desarrollo:

| Insumo | Costo Mensual | Costo Prorrateado (20h) |
|--------|---------------|-------------------------|
| Dominio (.com anual) | $1.25 | $0.15 |
| Hosting desarrollo | $5.00 | $0.60 |
| Servicios OpenData / BD | $3.00 | $0.36 |
| **Total C_dir** | **$9.25** | **$1.11** |

**Fórmula aplicada:**
C_dir = Σ Insumos Reales = $1.11


---

### PASO 2: Costos Operativos Fijos (C_op)

Gastos de infraestructura del taller/escritorio (prorrateado sobre 176h mensuales):

| Concepto | Costo Mensual |
|----------|---------------|
| Internet banda ancha | $40.00 |
| Electricidad | $20.00 |
| Espacio coworking/oficina | $20.00 |
| **Total Gastos Fijos** | **$80.00** |

**Tarifa operativa por hora:**
Tarifa_Op = Gastos_Fijos / 176 h = $80.00 / 176 = $0.4545/h


**Costo total operativo para 20 h:**

C_op = Tarifa_Op × Horas_Tickets = $0.4545 × 20 = $9.09



---

### PASO 3: Mano de Obra Técnica (C_labor)

Salario base del desarrollador Full-Stack junior remoto LATAM:

| Parámetro | Valor |
|-----------|-------|
| Sueldo mensual de referencia | $300.00 USD |
| Base mensual | 176 h |
| **Tarifa MO por hora** | **$1.7045/h** |

**Costo total de mano de obra para 20 h:**

C_labor = Tarifa_MO × Horas_Tickets = $1.7045 × 20 = $34.09


---

### PASO 4: Inversión Anual en Hardware (C_inv)

Amortización de equipos de desarrollo (vida útil 2-3 años):

| Equipo | Costo Total | Vida Útil | Amortización Anual |
|--------|-------------|-----------|--------------------|
| Laptop de desarrollo | $800.00 | 2 años | $400.00 |
| Monitor externo | $200.00 | 3 años | $66.67 |
| Periféricos (mouse, teclado, audífonos) | $150.00 | 3 años | $50.00 |
| Cursos y certificaciones | $50.00 | 1 año | $50.00 |
| **Total inversión anual** | **$1,200.00** | — | **$566.67** |

**Tarifa de inversión por hora:**

Tarifa_Inv = Presupuesto_Año / 2112 h = $566.67 / 2112 = $0.2683/h


**Costo total de inversión para 20 h:**

C_inv = Tarifa_Inv × Horas_Tickets = $0.2683 × 20 = $5.37


---

### PASO 5: Desgaste y Contingencia (C_desgaste)

Factor del 20% sobre el subtotal acumulado (cubre imprevistos, code reviews, refactoring):

**Subtotal acumulado:**
C_dir + C_op + C_labor + C_inv = $1.11 + $9.09 + $34.09 + $5.37 = $49.66


**Desgaste:**

C_desgaste = Subtotal × 0.20 = $49.66 × 0.20 = $9.93


---

### PASO 6: Costo Total Consolidado (CTC)

**Punto de equilibrio técnico:**

CTC = C_dir + C_op + C_labor + C_inv + C_desgaste
CTC = $1.11 + $9.09 + $34.09 + $5.37 + $9.93
CTC = $59.59


**Costo Total Consolidado: $59.59 USD**

---

### PASO 7: Banda Absoluta de Precios

Fijación de los 3 niveles de precio comercial con margen:

| Nivel | Fórmula | Precio |
|-------|---------|--------|
| **Piso Mínimo** | CTC × 1.30 | **$77.47** |
| **Precio Estándar** | CTC × 1.45 | **$86.41** |
| **Techo Empresarial** | CTC × 1.70 | **$101.30** |

**Rango de precio recomendado:** **$77.47 – $101.30 USD**

---

## 📊 Matriz Espejo de Conciliación

Verificación de coherencia entre el tablero Kanban y el informe económico:

| Ticket ID | Descripción | Rama GitFlow / PR | Horas Reales | Costo MO |
|-----------|-------------|-------------------|--------------|----------|
| TASK-101 | Diseño DDL, migraciones y conexión DB | feature/101-ddl-db (PR #01) | 5.0 h | 5.0 × $1.7045 = $8.52 |
| TASK-102 | Controladores y rutas API REST CRUD | feature/102-api-crud (PR #02) | 4.0 h | 4.0 × $1.7045 = $6.82 |
| TASK-103 | Vistas frontend, consumo fetch | feature/103-vistas-ui (PR #03) | 4.0 h | 4.0 × $1.7045 = $6.82 |
| TASK-104 | Validaciones y manejo de errores | feature/104-validaciones (PR #04) | 3.0 h | 3.0 × $1.7045 = $5.11 |
| TASK-105 | Pruebas de integración y despliegue local | feature/105-pruebas (PR #05) | 4.0 h | 4.0 × $1.7045 = $6.82 |
| **TOTAL** | **5 tickets** | **5 PRs mergeados** | **20.0 h** | **$34.09** |

✅ **CONCILIACIÓN PERFECTA:**
- Total horas Kanban = 20.0 h
- Total horas Informe = 20.0 h
- Total MO = $34.09

---

## 💰 Resumen Ejecutivo

| Concepto | Monto USD |
|----------|-----------|
| Insumos Digitales (C_dir) | $1.11 |
| Costos Operativos (C_op) | $9.09 |
| Mano de Obra (C_labor) | $34.09 |
| Inversión Hardware (C_inv) | $5.37 |
| Desgaste 20% (C_desgaste) | $9.93 |
| **COSTO TOTAL CONSOLIDADO (CTC)** | **$59.59** |
| Piso Mínimo (+30%) | $77.47 |
| Precio Estándar (+45%) | $86.41 |
| Techo Empresarial (+70%) | $101.30 |

---

## 🎯 Conclusión

El módulo CRUD de Reservaciones para Biotel Suites tiene un **Costo Total Consolidado de manufactura de $59.59 USD**, basado en 20 horas reales registradas en el tablero Kanban.

El precio comercial recomendado oscila entre **$77.47 y $101.30 USD**, dependiendo del margen de ganancia aplicado.

---

**Elaborado por:** Rafael Zubillaga, Santiago Salazar, Angelo Navarro, César Araujo, Andrés Jatar
**Facilitador:** Prof. Eduardo Nieves
**Fecha:** Octubre 2026