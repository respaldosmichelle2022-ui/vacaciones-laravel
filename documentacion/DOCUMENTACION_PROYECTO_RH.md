# Sistema RH - Bitácora Técnica

## Tecnología

* Laravel
* Blade
* MySQL
* VS Code

---

# Módulo Empleados

## Tabla

empleados

### Campos principales

* id
* numero_empleado
* nombre
* apellido_paterno
* apellido_materno
* sitio
* sucursal
* puesto
* fecha_ingreso
* activo

### Características

* Alta de empleados
* Edición
* Eliminación
* Búsqueda por nombre, apellidos y número de empleado
* Orden numérico por numero_empleado usando:

```php
orderByRaw('CAST(numero_empleado AS UNSIGNED)')
```

---

# Módulo Saldos de Vacaciones

## Tabla

saldo_vacacions

### Campos

* id
* empleado_id
* periodo
* dias_corresponden
* dias_restantes

### Relación

Empleado.php

```php
public function saldosVacaciones()
{
    return $this->hasMany(SaldoVacacion::class);
}
```

### Validaciones

No permite crear más de un saldo para:

* mismo empleado
* mismo periodo

Validación implementada:

```php
$existe = SaldoVacacion::where('empleado_id', $request->empleado_id)
    ->where('periodo', $request->periodo)
    ->exists();
```

### Búsqueda

Filtra por:

* número empleado
* nombre
* apellido paterno
* apellido materno

---

# Módulo Movimientos de Vacaciones

## Tabla

movimiento_vacacions

### Campos

* id
* empleado_id
* periodo
* fecha_inicio
* fecha_fin
* dias

### Relación

Empleado.php

```php
public function movimientosVacaciones()
{
    return $this->hasMany(MovimientoVacacion::class);
}
```

---

# Lógica de Descuento

Al guardar:

```php
$dias = $inicio->diffInDays($fin) + 1;
```

Después:

```php
$saldo->dias_restantes -= $dias;
```

---

# Validaciones Implementadas

## 1. Saldo existente

Debe existir saldo para:

* empleado
* periodo

```php
if(!$saldo)
```

---

## 2. Días suficientes

```php
if($saldo->dias_restantes < $dias)
```

Mensaje:

"No tiene suficientes días"

---

## 3. Movimiento duplicado

No permite registrar exactamente:

* mismo empleado
* mismo periodo
* misma fecha inicio
* misma fecha fin

Mensaje:

"Ya existe un movimiento con esas mismas fechas"

---

## 4. Vacaciones traslapadas

Bloquea rangos que se crucen.

Ejemplo:

01/06/2026 - 05/06/2026

contra

03/06/2026 - 07/06/2026

Mensaje:

"Las fechas se traslapan con otras vacaciones"

---

## 5. Fecha fin menor que fecha inicio

```php
if($fin < $inicio)
```

Mensaje:

"La fecha fin no puede ser menor que la fecha inicio"

---

## 6. Cruce de años

```php
if($inicio->year != $fin->year)
```

Mensaje:

"Las vacaciones deben pertenecer al mismo año"

---

# Mejora Implementada

## Periodos dinámicos por empleado

Al seleccionar empleado:

Solo aparecen los periodos que realmente tiene en saldo_vacacions.

Ejemplos:

Jorge:

* 2026

Marco:

* 2025
* 2026

Cinthia:

* 2024

Implementado con:

* data-periodos
* JavaScript
* evento change

---

# Estado Actual

Módulo Vacaciones funcional y estable.

Validaciones probadas y funcionando.

---

# Próximas Mejoras

## 1. Mostrar días disponibles

Al seleccionar:

* empleado
* periodo

Mostrar:

"Días disponibles: X"

---

## 2. Mostrar días a descontar

Al seleccionar:

* fecha inicio
* fecha fin

Mostrar:

"Días a descontar: X"

antes de guardar.

---

# Observaciones

El sistema utiliza layout común de RH.

Se mantiene ordenamiento numérico de empleados.

Las búsquedas funcionan por número y nombre.

El módulo de vacaciones se considera funcionalmente terminado y en fase de mejoras visuales.

"Continuemos el Sistema RH desde la sección Próximas Mejoras."