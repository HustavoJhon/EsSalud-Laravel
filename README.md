# EsSalud - Plataforma de Atención al Asegurado

Sistema web de gestión de trámites y atención al asegurado para EsSalud. Permite crear, dar seguimiento y gestionar trámites, interactuar con un chatbot inteligente (RAG), subir documentos, ver noticias y consultar preguntas frecuentes.

## Stack Tecnológico

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.3 + Laravel 11 |
| Frontend | Blade + Livewire 3 + Alpine.js + Tailwind CSS |
| Base de datos | MySQL 8 |
| Cache / Queue | Redis 7 |
| Búsqueda vectorial | Qdrant (RAG) |
| Almacenamiento | MinIO (S3-compatible) |
| OCR | Tesseract (español) |
| IA | OpenAI (embeddings + chat completions) |
| Infraestructura | Docker Compose (8 servicios) |

## Roles y Permisos

| Rol | Descripción | Permisos principales |
|---|---|---|
| **ASEG** | Asegurado | Crear trámites, subsanar, chatear, ver FAQ/noticias, ver su perfil |
| **OPER** | Operador | Todo lo de ASEG + aprobar/rechazar trámites, solicitar subsanaciones |
| **SUPV** | Supervisor | Todo lo de OPER + asignar trámites, dashboard KPIs, reportes, auditoría |
| **GESDOC** | Gestor Documental | Gestionar FAQ y noticias, subir/validar documentos oficiales, administrar fuentes RAG |
| **SADM** | Super Admin | Acceso total al sistema, gestión de usuarios y roles |

## Módulos

### Auth
- Login, registro, recuperación de contraseña
- Autenticación con Laravel Sanctum (web + API)
- Bloqueo de cuenta tras 5 intentos fallidos (30 min)

### Trámites
- 6 tipos de trámite (afiliación cónyuge/hijo, maternidad, lactancia, sepelio, enfermedad)
- 7 estados: Borrador → Pendiente → En Revisión → Aprobado/Rechazado/Subsanación → Cancelado
- Subsanaciones: máximo 3 intentos, 15 días de plazo
- Timeline de historial, comentarios, asignación a operadores

### Chatbot
- FAQ por palabras clave: 8 preguntas precargadas en 5 categorías
- RAG con Qdrant: búsqueda en documentos oficiales indexados
- OpenAI: genera respuestas con citas a documentos fuente
- Escalación a operador cuando la confianza es baja

### Documentos
- Subida de PDF, JPG, PNG (máx 10 MB)
- OCR con Tesseract en español
- Indexación en Qdrant vía jobs asíncronos
- Validación por GESDOC, versionado

### Noticias y FAQ
- CRUD de noticias con categorías
- FAQ organizada por categorías con acordeón
- Búsqueda full-text

### Admin
- Dashboard KPIs: trámites pendientes, tasa de aprobación, tiempo promedio
- Reportes exportables (PDF, Excel)
- Auditoría de acciones del sistema

## Requisitos

- Docker y Docker Compose
- (Opcional) OpenAI API Key para el chatbot RAG

## Instalación

```bash
# Clonar
git clone git@github.com:HustavoJhon/EsSalud-Laravel.git
cd EsSalud-Laravel

# Configurar
cp .env.example .env
# Editar .env si necesitas configurar OPENAI_API_KEY

# Construir y arrancar
docker compose up -d --build

# Ejecutar migraciones y seeders
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force

# (Opcional) Publicar migraciones de Sanctum y Spatie
docker compose exec -u root app php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --tag="sanctum-migrations" --force
docker compose exec app php artisan migrate --force
```

Abrir `http://localhost:8082`

## Credenciales de prueba

| Rol | Email | Contraseña |
|---|---|---|
| SADM | admin@essalud.pe | Admin123! |
| ASEG | aseg@essalud.pe | Aseg123! |
| OPER | oper@essalud.pe | Oper123! |

## Servicios Docker

| Servicio | Puerto | Descripción |
|---|---|---|
| nginx | `8082` | Servidor web |
| app | — | PHP-FPM 8.3 |
| mysql | `3306` | Base de datos MySQL 8 |
| redis | `6379` | Cache + Queue |
| qdrant | `6333` | Base de datos vectorial |
| minio | `9002` / `9003` | Almacenamiento S3 |
| queue-worker | — | Laravel Queue worker |
| scheduler | — | Tareas programadas (auto-cancelar trámites) |

## Comandos útiles

```bash
# Auto-cancelar trámites vencidos
docker compose exec app php artisan procedures:auto-cancel

# Limpiar cachés
docker compose exec app php artisan optimize:clear

# Ver rutas
docker compose exec app php artisan route:list
```

## Documentación

La documentación técnica completa se encuentra en `task/essalud/v1.0-laravel/` (27 documentos: arquitectura, especificaciones, diseño, modelo ER, roles, historias de usuario, casos de uso, RAG, OCR, Docker, CI/CD, etc.).

## CI/CD

GitHub Actions con 3 workflows:

| Workflow | Disparador |
|---|---|
| `ci.yml` | PR a `main`/`dev`, push a `dev` |
| `cd-staging.yml` | Push a `dev` |
| `cd-prod.yml` | Push a `main` |

## Licencia

Proyecto privado. Uso interno EsSalud.
