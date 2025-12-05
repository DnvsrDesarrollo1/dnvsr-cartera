# Optimización de PlanController - pdfMoraProyecto

## Fecha: 2025-12-04

## Problema Identificado
La función `pdfMoraProyecto` en `PlanController.php` presentaba serios problemas de rendimiento debido a consultas N+1 dentro de un bucle que iteraba sobre todos los beneficiarios de un proyecto.

### Cuellos de Botella Detectados:

1.  **Consultas Redundantes de Beneficiarios**: Se realizaban dos consultas separadas para obtener los mismos beneficiarios (una para contar estados y otra para el detalle).
2.  **N+1 en Planes/Reajustes**: Dentro del bucle `foreach`, se llamaba a `getCurrentPlan('VENCIDO', '=')`, lo que ejecutaba consultas a las tablas `plans` y `readjustments` por cada beneficiario.
3.  **N+1 en Vouchers**: Se ejecutaba una consulta a la tabla `vouchers` por cada beneficiario para obtener la fecha del último pago.

## Soluciones Implementadas

### 1. Unificación de Consultas y Eager Loading

Se reemplazaron las múltiples consultas por una única consulta optimizada:

```php
$beneficiarios = Beneficiary::query()
    ->where('proyecto', $proyecto)
    ->select('beneficiaries.*')
    ->addSelect([
        'ultimo_pago_fecha' => \App\Models\Voucher::select('fecha_pago')
            ->whereColumn('numprestamo', 'beneficiaries.idepro')
            ->orderBy('fecha_pago', 'desc')
            ->limit(1)
    ])
    ->with([
        'plans' => function ($query) {
            $query->where('estado', 'VENCIDO')->orderBy('fecha_ppg', 'asc');
        },
        'readjustments' => function ($query) {
            $query->where('estado', 'VENCIDO')->orderBy('fecha_ppg', 'asc');
        }
    ])
    ->orderBy('nombre')
    ->get();
```

**Mejoras:**
-   **Subquery para Último Pago**: En lugar de cargar todos los vouchers o hacer una consulta por beneficiario, se usa una subquery (`addSelect`) para obtener solo la fecha del último pago directamente en la consulta principal.
-   **Eager Loading Filtrado**: Se cargan anticipadamente (`with`) solo los planes y reajustes que están 'VENCIDO', evitando cargar datos innecesarios y eliminando el problema N+1.
-   **Consulta Única**: Se reutiliza la colección `$beneficiarios` tanto para el conteo de estados como para la generación del reporte detallado.

### 2. Procesamiento en Memoria

Dentro del bucle, se utiliza la información ya cargada en las relaciones en lugar de realizar nuevas consultas:

```php
// Antes (N+1 queries)
$vencidoPlan = $beneficiario->getCurrentPlan('VENCIDO', '=')->first();

// Ahora (En memoria)
$planesVencidos = $beneficiario->plans;
if ($planesVencidos->isEmpty()) {
    $planesVencidos = $beneficiario->readjustments;
}
$vencidoPlan = $planesVencidos->first();
```

## Impacto en el Rendimiento

-   **Reducción de Consultas**: De `2 + (3 * N)` consultas (donde N es el número de beneficiarios) a **1 sola consulta SQL** compleja pero eficiente.
-   **Menor Uso de Memoria**: Al usar subqueries para la fecha de pago y filtrar las relaciones eager loaded, se evita hidratar miles de modelos innecesarios.
-   **Tiempo de Respuesta**: Debería ser drásticamente menor, especialmente para proyectos con muchos beneficiarios.

## Recomendaciones

-   Verificar que los índices en las columnas `idepro`, `proyecto`, `estado` y `fecha_pago` existan en la base de datos para maximizar la velocidad de la consulta optimizada.
