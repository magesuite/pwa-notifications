# Queue setup

This module depends on queue consumers being run constantly.

Queue name depends on environment configuration:
- `magesuite.consumer.db` if RabbitMQ is not configured in `app/etc/env.php`
- `magesuite.consumer.amqp` if RabbitMQ is configured in `app/etc/env.php`

Example of queue startup:

`magesuite.consumer.db`:
```bash
bin/magento queue:consumers:start magesuite.consumer.db
```
`magesuite.consumer.amqp`:
```bash
bin/magento queue:consumers:start magesuite.consumer.amqp
```
