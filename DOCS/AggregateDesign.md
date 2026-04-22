# Mejores prácticas en el diseño de agregados

Un agregado es un conjunto de objetos de dominio que pueden ser tratados como una sola unidad. Tiene una entidad raíz, llamada raíz del agregado (Aggregate Root).

## Principios clave

1.  **Inmutabilidad por defecto**: Todos los Value Objects y objetos Command/Query deben ser `readonly`.
2.  **Encapsulación**: Los cambios de estado solo deben ocurrir a través de métodos en la raíz del agregado. Evita los setters públicos.
3.  **Límite de consistencia**: Los agregados deben hacer cumplir las invariantes de negocio dentro de su límite.
4.  **Referencia por identidad**: Los agregados deben referenciar a otros agregados solo por su ID, nunca por referencia de objeto.

## Ejemplo: Agregado de usuario

```php
final class User extends AggregateRoot
{
    public function __construct(
        private readonly UserId $id,
        private UserEmail $email,
        private string $name
    ) {}

    public static function create(UserId $id, UserEmail $email, string $name): self
    {
        $user = new self($id, $email, $name);
        $user->record(new UserCreatedDomainEvent($id->value(), $email->value(), $name));
        return $user;
    }

    public function rename(string $newName): void
    {
        // Invariante: el nombre no puede estar vacío
        if (empty($newName)) {
             throw new InvalidArgumentException("El nombre no puede estar vacío");
        }
        $this->name = $newName;
    }
}
```

## Eventos de dominio

Utiliza siempre el método `record()` para rastrear cambios de estado significativos. Estos eventos serán despachados por el repositorio después de una transacción exitosa.

```php
$user->record(new UserRenamedDomainEvent($user->id()->value(), $newName));
```
