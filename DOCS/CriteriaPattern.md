# Dominando el patrón Criteria

El patrón Criteria te permite construir consultas complejas y desacopladas en tu capa de dominio sin filtrar detalles de infraestructura (como SQL o Eloquent).

## Componentes principales

1.  **FilterField**: El nombre del campo por el que quieres filtrar.
2.  **FilterOperator**: La operación (EQUAL, GT, CONTAINS, IN, etc.).
3.  **FilterValue**: El valor con el que comparar.
4.  **Filters**: Una colección de objetos `Filter`.
5.  **Order**: Configuración de ordenación (`OrderBy` y `OrderType`).
6.  **Criteria**: El objeto final que contiene `Filters`, `Order`, `Offset` y `Limit`.

## Ejemplo de uso

### En la capa de aplicación

```php
$filters = new Filters([
    Filter::fromValues('name', '=', 'John'),
    Filter::fromValues('age', '>', '18')
]);
$order = Order::fromValues('name', 'asc');
$criteria = new Criteria($filters, $order, 0, 20);

$users = $this->repository->matching($criteria);
```

### En la capa de infraestructura (Eloquent)

El `EloquentCriteriaConverter` transforma automáticamente el `Criteria` de dominio en llamadas al query builder de Eloquent.

```php
public function matching(Criteria $criteria): array
{
    $eloquentCriteria = EloquentCriteriaConverter::convert($criteria);
    
    return $this->matching($eloquentCriteria)->get()->toArray();
}
```

## Operadores soportados

- `EQUAL` (=)
- `NOT_EQUAL` (!=)
- `GT` (>)
- `LT` (<)
- `GTE` (>=)
- `LTE` (<=)
- `IN`
- `NOT_IN`
- `CONTAINS` (LIKE %valor%)
- `NOT_CONTAINS`
- `STARTS_WITH`
- `ENDS_WITH`
- `BETWEEN`
- `IS_NULL`
- `IS_NOT_NULL`
