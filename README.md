# Gestión de Equipos en el FOG

Una plataforma web desarrollada en Laravel 12 y Blade, orientada a la **gestión de equipos informáticos** en el IES Martínez Montañés mediante la integración con la API de FOG Project y una base de datos NoSQL.

## Objetivo

Facilitar el seguimiento del estado, ubicación y disponibilidad de los equipos informáticos en un centro educativo. Incluye funcionalidades para administración, préstamos, ubicación, y estadísticas de los dispositivos.

---

## Stack Tecnológico

- **Framework**: Laravel 12
- **Frontend**: Blade + Tailwind CSS
- **UI Kit**: [Flowbite Design System](https://flowbite.com/design-system/) para componentes + Flux para estructura y layout.
- **Autenticación**: Laravel Breeze + Livewire
- **Base de datos**: MongoDB (NoSQL)
- **Integraciones**: FOG Project API

---

## Diseño (Figma)

El diseño está realizado con **Flowbite Design System**, con estilo adaptado para entornos educativos:  
[Ver diseño en Figma](https://www.figma.com/design/fuh7QpJ1Vj1HFaXcG0GgsU/FCT-Proyecto---Pablo-Ram%C3%ADrez-Oria?node-id=1103-1766&t=FrrVM63McBlIXTqs-1)

**Notas sobre el diseño**:
- El diseño es solo una **representación visual**, la información y estructura de algunas tablas pueden cambiar durante el desarrollo.
- Se han incluido comentarios en el archivo Figma para aclarar decisiones de diseño y flujos.
- Solo se puede acceder al diseño con la **cuenta a la que se ha enviado este repositorio**.

---

## Roles de Usuario

- `admin`: acceso completo, administración de usuarios, ubicaciones, equipos y préstamos.
- `usuario`: acceso limitado (solo lectura).

## Configuración de la API de FOG

Para configurar la API de FOG, añade las siguientes entradas a tu archivo `.env`:

```env
FOG_SERVER_URL=http://tu-servidor-fog.com
FOG_API_TOKEN=tu_api_token
FOG_USER_TOKEN=tu_user_token
