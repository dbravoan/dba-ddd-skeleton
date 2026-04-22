# Procesamiento de eventos asíncronos

El DBA DDD Skeleton soporta tanto la publicación de eventos síncrona como asíncrona utilizando la infraestructura de Laravel.

## LaravelEventBus (síncrono)

Por defecto, el `LaravelEventBus` ejecuta los suscriptores inmediatamente en la misma petición/proceso.

## LaravelQueueEventBus (asíncrono)

Para procesar eventos en segundo plano, utiliza `LaravelQueueEventBus`. Esta implementación serializa el `DomainEvent` y despacha un `ProcessDomainEventJob` a la cola de Laravel.

### Configuración

1.  Asegúrate de que tu `DomainEvent` implemente `fromPrimitives` y `toPrimitives` correctamente (el generador de módulos hace esto por ti).
2.  Vincula `EventBus` a `LaravelQueueEventBus` en tu `ServiceProvider`.

```php
$this->app->bind(EventBus::class, LaravelQueueEventBus::class);
```

3.  Ejecuta tu worker de colas: `php artisan queue:work`.

## Fiabilidad

El procesamiento asíncrono es crucial para:
- Enviar correos electrónicos o notificaciones.
- Actualizar modelos de lectura (CQRS).
- Comunicación con microservicios externos.
- Tareas computacionales pesadas que no deben bloquear la respuesta al usuario.
