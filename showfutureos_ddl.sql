-- =====================================================================
-- ShowFutureOS — Script DDL (Data Definition Language)
-- Base de datos: showfutureos
-- Motor: MySQL 8.x / MariaDB 10.x
-- Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- Generado: 2026-10-04
-- =====================================================================

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS `showfutureos`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `showfutureos`;

-- =====================================================================
-- 1. TABLA: users
-- Descripción: Usuarios del sistema (Admin, Account Manager, Editor,
--              Copywriter, Comercial, Ads Manager, etc.)
-- =====================================================================
CREATE TABLE `users` (
    `id`                BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(255)        NOT NULL                         COMMENT 'Nombre completo del usuario',
    `email`             VARCHAR(255)        NOT NULL                         COMMENT 'Correo electrónico (login)',
    `email_verified_at` TIMESTAMP           NULL DEFAULT NULL                COMMENT 'Fecha de verificación de email',
    `password`          VARCHAR(255)        NOT NULL                         COMMENT 'Contraseña hasheada (bcrypt)',
    `avatar`            VARCHAR(255)        NULL DEFAULT NULL                COMMENT 'Ruta de la foto de perfil',
    `phone`             VARCHAR(50)         NULL DEFAULT NULL                COMMENT 'Teléfono / WhatsApp del usuario',
    `is_active`         TINYINT(1)          NOT NULL DEFAULT 1              COMMENT 'Estado: 1=Activo, 0=Inactivo',
    `remember_token`    VARCHAR(100)        NULL DEFAULT NULL                COMMENT 'Token de sesión persistente',
    `created_at`        TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP           NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Usuarios del equipo ShowFuture (empleados internos)';

-- =====================================================================
-- 2. TABLA: roles
-- Descripción: Catálogo de roles del sistema.
--   Roles por defecto: Admin, Account Manager, Editor, Copywriter,
--                      Ads Manager, Comercial, Scrum Master
-- =====================================================================
CREATE TABLE `roles` (
    `id`            BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(100)        NOT NULL                             COMMENT 'Nombre legible del rol',
    `slug`          VARCHAR(100)        NOT NULL                             COMMENT 'Slug único (ej: account_manager)',
    `description`   TEXT                NULL DEFAULT NULL                    COMMENT 'Descripción de responsabilidades',
    `created_at`    TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP           NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `roles_name_unique` (`name`),
    UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo de roles del sistema ShowFutureOS';

-- =====================================================================
-- 3. TABLA: role_user  (Tabla pivote N:M)
-- Descripción: Asignación de roles a usuarios.
--   Un usuario puede tener múltiples roles.
--   Un rol puede ser asignado a múltiples usuarios.
-- =====================================================================
CREATE TABLE `role_user` (
    `id`            BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `user_id`       BIGINT UNSIGNED     NOT NULL                             COMMENT 'FK → users.id',
    `role_id`       BIGINT UNSIGNED     NOT NULL                             COMMENT 'FK → roles.id',
    `created_at`    TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP           NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `role_user_user_id_role_id_unique` (`user_id`, `role_id`),

    CONSTRAINT `role_user_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE,

    CONSTRAINT `role_user_role_id_foreign`
        FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Relación muchos a muchos: usuarios ↔ roles';

-- =====================================================================
-- 4. TABLA: clients
-- Descripción: Clientes (marcas/empresas) gestionados por la agencia.
--   Cada cliente tiene un Account Manager asignado y un nivel de servicio.
-- =====================================================================
CREATE TABLE `clients` (
    `id`                    BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`                  VARCHAR(255)        NOT NULL                     COMMENT 'Nombre de la empresa/marca',
    `status`                VARCHAR(255)        NOT NULL DEFAULT 'onboarding' COMMENT 'Estado: onboarding, active, paused, churned',
    `level`                 VARCHAR(255)        NOT NULL DEFAULT 'starter'   COMMENT 'Nivel: starter, growth, scale',
    `ads_budget`            DECIMAL(12,2)       NULL DEFAULT NULL            COMMENT 'Presupuesto mensual de publicidad',
    `account_manager_id`    BIGINT UNSIGNED     NULL DEFAULT NULL            COMMENT 'FK → users.id (AM asignado)',
    `start_date`            DATE                NULL DEFAULT NULL            COMMENT 'Fecha de inicio de contrato',
    `renewal_date`          DATE                NULL DEFAULT NULL            COMMENT 'Fecha de renovación del contrato',
    `has_sla`               TINYINT(1)          NOT NULL DEFAULT 0           COMMENT 'Tiene SLA garantizado: 1=Sí, 0=No',
    `main_platform`         VARCHAR(100)        NULL DEFAULT NULL            COMMENT 'Plataforma principal (Meta, Google, TikTok)',
    `main_objective`        VARCHAR(255)        NULL DEFAULT NULL            COMMENT 'Objetivo principal del cliente',
    `logo`                  VARCHAR(255)        NULL DEFAULT NULL            COMMENT 'Ruta del logotipo del cliente',
    `notes`                 TEXT                NULL DEFAULT NULL            COMMENT 'Notas internas del cliente',
    `created_at`            TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`            TIMESTAMP           NULL DEFAULT NULL,
    `deleted_at`            TIMESTAMP           NULL DEFAULT NULL            COMMENT 'Soft delete (eliminación lógica)',

    PRIMARY KEY (`id`),

    KEY `clients_account_manager_id_index` (`account_manager_id`),

    CONSTRAINT `clients_account_manager_id_foreign`
        FOREIGN KEY (`account_manager_id`) REFERENCES `users` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Clientes gestionados por la agencia ShowFuture';

-- =====================================================================
-- 5. TABLA: client_contacts
-- Descripción: Contactos clave del cliente (dueño, marketing, finanzas).
--   Cada contacto pertenece a un solo cliente.
-- =====================================================================
CREATE TABLE `client_contacts` (
    `id`            BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `client_id`     BIGINT UNSIGNED     NOT NULL                             COMMENT 'FK → clients.id',
    `name`          VARCHAR(255)        NOT NULL                             COMMENT 'Nombre del contacto',
    `role`          VARCHAR(100)        NULL DEFAULT NULL                    COMMENT 'Cargo/rol dentro de la empresa',
    `phone`         VARCHAR(50)         NULL DEFAULT NULL                    COMMENT 'Teléfono / WhatsApp',
    `email`         VARCHAR(255)        NULL DEFAULT NULL                    COMMENT 'Correo electrónico',
    `is_primary`    TINYINT(1)          NOT NULL DEFAULT 0                   COMMENT 'Contacto principal: 1=Sí, 0=No',
    `notes`         TEXT                NULL DEFAULT NULL                    COMMENT 'Notas adicionales del contacto',
    `created_at`    TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP           NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `client_contacts_client_id_index` (`client_id`),

    CONSTRAINT `client_contacts_client_id_foreign`
        FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Contactos clave de cada cliente';

-- =====================================================================
-- 6. TABLA: client_accesses
-- Descripción: Bóveda de credenciales y accesos del cliente.
--   Almacena URLs, usuarios y contraseñas de plataformas (Meta, BM, etc.)
-- =====================================================================
CREATE TABLE `client_accesses` (
    `id`            BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `client_id`     BIGINT UNSIGNED     NOT NULL                             COMMENT 'FK → clients.id',
    `platform`      VARCHAR(100)        NOT NULL                             COMMENT 'Plataforma (Meta Ads, Google Ads, BM, Shopify, Web)',
    `url`           VARCHAR(255)        NULL DEFAULT NULL                    COMMENT 'URL de acceso',
    `username`      VARCHAR(255)        NULL DEFAULT NULL                    COMMENT 'Usuario / email de acceso',
    `password`      VARCHAR(255)        NULL DEFAULT NULL                    COMMENT 'Contraseña (almacenada cifrada)',
    `credentials`   TEXT                NULL DEFAULT NULL                    COMMENT 'Credenciales adicionales (JSON/texto)',
    `access_type`   VARCHAR(50)         NULL DEFAULT NULL                    COMMENT 'Tipo de acceso (admin, viewer, editor)',
    `status`        VARCHAR(255)        NOT NULL DEFAULT 'active'            COMMENT 'Estado del acceso: active, revoked, expired',
    `notes`         TEXT                NULL DEFAULT NULL                    COMMENT 'Notas sobre el acceso',
    `created_at`    TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP           NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `client_accesses_client_id_index` (`client_id`),

    CONSTRAINT `client_accesses_client_id_foreign`
        FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Bóveda de accesos y credenciales de los clientes';

-- =====================================================================
-- 7. TABLA: onboardings
-- Descripción: Proceso de onboarding de cada cliente (relación 1:1).
--   Registra progreso, fechas, reglas de comunicación y canal.
-- =====================================================================
CREATE TABLE `onboardings` (
    `id`                        BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `client_id`                 BIGINT UNSIGNED     NOT NULL                 COMMENT 'FK → clients.id (relación 1:1)',
    `status`                    VARCHAR(255)        NOT NULL DEFAULT 'pending' COMMENT 'Estado: pending, in_progress, completed',
    `communication_rules_sent`  TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Reglas de comunicación enviadas',
    `calendar_shared`           TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Calendario compartido con el cliente',
    `started_at`                DATE                NULL DEFAULT NULL        COMMENT 'Fecha de inicio del onboarding',
    `target_completion_date`    DATE                NULL DEFAULT NULL        COMMENT 'Fecha objetivo de finalización',
    `progress_percentage`       INT                 NOT NULL DEFAULT 0       COMMENT 'Porcentaje de avance (0-100)',
    `completed_at`              DATE                NULL DEFAULT NULL        COMMENT 'Fecha real de finalización',
    `notes`                     TEXT                NULL DEFAULT NULL        COMMENT 'Notas del proceso de onboarding',
    `communication_channel`     TEXT                NULL DEFAULT NULL        COMMENT 'Canal de comunicación preferido',
    `created_at`                TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`                TIMESTAMP           NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `onboardings_client_id_unique` (`client_id`),

    CONSTRAINT `onboardings_client_id_foreign`
        FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Proceso de onboarding por cliente (1:1)';

-- =====================================================================
-- 8. TABLA: onboarding_checklist_items
-- Descripción: Ítems de la lista de verificación del onboarding.
--   Estructura de 7 días con tareas específicas por día.
-- =====================================================================
CREATE TABLE `onboarding_checklist_items` (
    `id`                BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `onboarding_id`     BIGINT UNSIGNED     NOT NULL                         COMMENT 'FK → onboardings.id',
    `day_number`        INT                 NOT NULL DEFAULT 1               COMMENT 'Día del onboarding (1-7)',
    `title`             VARCHAR(255)        NOT NULL                         COMMENT 'Título de la tarea',
    `description`       TEXT                NULL DEFAULT NULL                COMMENT 'Descripción detallada de la tarea',
    `is_completed`      TINYINT(1)          NOT NULL DEFAULT 0              COMMENT 'Completado: 1=Sí, 0=No',
    `completed_at`      TIMESTAMP           NULL DEFAULT NULL               COMMENT 'Fecha y hora de completado',
    `completed_by`      BIGINT UNSIGNED     NULL DEFAULT NULL               COMMENT 'FK → users.id (quién completó)',
    `order`             INT                 NOT NULL DEFAULT 0              COMMENT 'Orden dentro del día',
    `sort_order`        INT                 NOT NULL DEFAULT 0              COMMENT 'Orden global de visualización',
    `created_at`        TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP           NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `onboarding_checklist_items_onboarding_id_index` (`onboarding_id`),
    KEY `onboarding_checklist_items_completed_by_index` (`completed_by`),

    CONSTRAINT `onboarding_checklist_items_onboarding_id_foreign`
        FOREIGN KEY (`onboarding_id`) REFERENCES `onboardings` (`id`)
        ON DELETE CASCADE,

    CONSTRAINT `onboarding_checklist_items_completed_by_foreign`
        FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Ítems del checklist de onboarding (plan de 7 días)';

-- =====================================================================
-- 9. TABLA: tickets
-- Descripción: Tickets de trabajo (tareas de producción).
--   Tipos: Creativo, Ads, Copy, Comercial.
--   Estados: Backlog → In Progress → QA → Delivered.
-- =====================================================================
CREATE TABLE `tickets` (
    `id`                        BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `ticket_number`             VARCHAR(20)         NOT NULL                 COMMENT 'Número auto-generado (SF-0001)',
    `client_id`                 BIGINT UNSIGNED     NOT NULL                 COMMENT 'FK → clients.id',
    `type`                      VARCHAR(255)        NOT NULL                 COMMENT 'Tipo: creative, ads, copy, commercial',
    `title`                     VARCHAR(255)        NOT NULL                 COMMENT 'Título descriptivo de la tarea',
    `description`               TEXT                NULL DEFAULT NULL        COMMENT 'Descripción detallada / brief',
    `format`                    VARCHAR(100)        NULL DEFAULT NULL        COMMENT 'Formato del entregable (1080x1080, 9:16, etc.)',
    `priority`                  VARCHAR(255)        NOT NULL DEFAULT 'medium' COMMENT 'Prioridad: high, medium, low',
    `status`                    VARCHAR(255)        NOT NULL DEFAULT 'backlog' COMMENT 'Estado Kanban: backlog, in_progress, qa, delivered',
    `assigned_to`               BIGINT UNSIGNED     NULL DEFAULT NULL        COMMENT 'FK → users.id (responsable asignado)',
    `created_by`                BIGINT UNSIGNED     NOT NULL                 COMMENT 'FK → users.id (creador del ticket)',
    `deadline`                  DATE                NULL DEFAULT NULL        COMMENT 'Fecha límite de entrega',
    `requires_client_approval`  TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Requiere aprobación del cliente',
    `client_approved`           TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Cliente aprobó el entregable',
    `client_approved_at`        TIMESTAMP           NULL DEFAULT NULL        COMMENT 'Fecha de aprobación del cliente',
    `delivered_at`              TIMESTAMP           NULL DEFAULT NULL        COMMENT 'Fecha de entrega efectiva',
    `kanban_order`              INT                 NOT NULL DEFAULT 0       COMMENT 'Orden de visualización en Kanban',
    `created_at`                TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`                TIMESTAMP           NULL DEFAULT NULL,
    `deleted_at`                TIMESTAMP           NULL DEFAULT NULL        COMMENT 'Soft delete (eliminación lógica)',

    PRIMARY KEY (`id`),
    UNIQUE KEY `tickets_ticket_number_unique` (`ticket_number`),

    KEY `tickets_client_id_index` (`client_id`),
    KEY `tickets_assigned_to_index` (`assigned_to`),
    KEY `tickets_created_by_index` (`created_by`),

    CONSTRAINT `tickets_client_id_foreign`
        FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
        ON DELETE CASCADE,

    CONSTRAINT `tickets_assigned_to_foreign`
        FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`)
        ON DELETE SET NULL,

    CONSTRAINT `tickets_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Tickets de trabajo del equipo (tareas de producción)';

-- =====================================================================
-- 10. TABLA: ticket_comments
-- Descripción: Comentarios y discusión dentro de cada ticket.
--   Cada comentario pertenece a un ticket y a un usuario.
-- =====================================================================
CREATE TABLE `ticket_comments` (
    `id`            BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `ticket_id`     BIGINT UNSIGNED     NOT NULL                             COMMENT 'FK → tickets.id',
    `user_id`       BIGINT UNSIGNED     NOT NULL                             COMMENT 'FK → users.id (autor del comentario)',
    `body`          TEXT                NOT NULL                             COMMENT 'Contenido del comentario',
    `created_at`    TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP           NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `ticket_comments_ticket_id_index` (`ticket_id`),
    KEY `ticket_comments_user_id_index` (`user_id`),

    CONSTRAINT `ticket_comments_ticket_id_foreign`
        FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
        ON DELETE CASCADE,

    CONSTRAINT `ticket_comments_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Comentarios de discusión dentro de cada ticket';

-- =====================================================================
-- 11. TABLA: ticket_attachments
-- Descripción: Archivos adjuntos asociados a cada ticket.
--   Almacena nombre, ruta, tipo MIME y tamaño del archivo.
-- =====================================================================
CREATE TABLE `ticket_attachments` (
    `id`            BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `ticket_id`     BIGINT UNSIGNED     NOT NULL                             COMMENT 'FK → tickets.id',
    `uploaded_by`   BIGINT UNSIGNED     NOT NULL                             COMMENT 'FK → users.id (quién subió el archivo)',
    `file_name`     VARCHAR(255)        NOT NULL                             COMMENT 'Nombre original del archivo',
    `file_path`     VARCHAR(500)        NOT NULL                             COMMENT 'Ruta de almacenamiento en disco',
    `file_type`     VARCHAR(50)         NULL DEFAULT NULL                    COMMENT 'Tipo MIME (image/png, video/mp4, etc.)',
    `file_size`     BIGINT              NULL DEFAULT NULL                    COMMENT 'Tamaño del archivo en bytes',
    `created_at`    TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP           NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `ticket_attachments_ticket_id_index` (`ticket_id`),
    KEY `ticket_attachments_uploaded_by_index` (`uploaded_by`),

    CONSTRAINT `ticket_attachments_ticket_id_foreign`
        FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
        ON DELETE CASCADE,

    CONSTRAINT `ticket_attachments_uploaded_by_foreign`
        FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Archivos adjuntos de los tickets (creativos, assets)';

-- =====================================================================
-- 12. TABLA: ticket_history
-- Descripción: Historial de cambios de estado de los tickets.
--   Registra cada transición con el campo, valor anterior y nuevo valor.
-- =====================================================================
CREATE TABLE `ticket_history` (
    `id`            BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `ticket_id`     BIGINT UNSIGNED     NOT NULL                             COMMENT 'FK → tickets.id',
    `user_id`       BIGINT UNSIGNED     NOT NULL                             COMMENT 'FK → users.id (quién realizó el cambio)',
    `field`         VARCHAR(100)        NOT NULL                             COMMENT 'Campo modificado (status, assigned_to, etc.)',
    `old_value`     VARCHAR(255)        NULL DEFAULT NULL                    COMMENT 'Valor anterior',
    `new_value`     VARCHAR(255)        NULL DEFAULT NULL                    COMMENT 'Valor nuevo',
    `created_at`    TIMESTAMP           NULL DEFAULT NULL                    COMMENT 'Fecha del cambio',

    PRIMARY KEY (`id`),

    KEY `ticket_history_ticket_id_index` (`ticket_id`),
    KEY `ticket_history_user_id_index` (`user_id`),

    CONSTRAINT `ticket_history_ticket_id_foreign`
        FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
        ON DELETE CASCADE,

    CONSTRAINT `ticket_history_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Historial de cambios de estado y campos de tickets';

-- =====================================================================
-- 13. TABLA: qa_reviews
-- Descripción: Revisiones de Control de Calidad (QA) por ticket.
--   Evaluación en 3 bloques: Estratégico, Tono & Marca, y Técnico.
--   Un ticket puede tener múltiples revisiones (por devoluciones).
-- =====================================================================
CREATE TABLE `qa_reviews` (
    `id`                        BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `ticket_id`                 BIGINT UNSIGNED     NOT NULL                 COMMENT 'FK → tickets.id',
    `reviewed_by`               BIGINT UNSIGNED     NOT NULL                 COMMENT 'FK → users.id (Account Manager que revisa)',

    -- Resultado general
    `is_approved`               TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Aprobado: 1=Sí, 0=No (Devuelto)',

    -- Bloque 1: QA Estratégico
    `strategic_message_aligned` TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Mensaje alineado con objetivo del cliente',
    `strategic_cta_clear`       TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'CTA claro y accionable',
    `strategic_funnel_correct`  TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Etapa del funnel correcta (TOFU/MOFU/BOFU)',

    -- Bloque 2: QA Tono & Marca
    `brand_tone_correct`        TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Tono de voz acorde a la marca',
    `brand_no_risky_claims`     TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Sin afirmaciones riesgosas o prohibidas',
    `brand_policy_aligned`      TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Cumple políticas de la plataforma',

    -- Bloque 3: QA Técnico
    `tech_format_correct`       TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Formato y resolución correctos',
    `tech_subtitles_legible`    TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Subtítulos legibles (si aplica)',
    `tech_duration_adequate`    TINYINT(1)          NOT NULL DEFAULT 0       COMMENT 'Duración del video adecuada (si aplica)',

    -- Motivo de devolución
    `rejection_reason`          TEXT                NULL DEFAULT NULL         COMMENT 'Motivo detallado del rechazo QA',
    `notes`                     TEXT                NULL DEFAULT NULL         COMMENT 'Notas adicionales del revisor',
    `reviewed_at`               TIMESTAMP           NULL DEFAULT NULL        COMMENT 'Fecha y hora de la revisión',

    `created_at`                TIMESTAMP           NULL DEFAULT NULL,
    `updated_at`                TIMESTAMP           NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `qa_reviews_ticket_id_index` (`ticket_id`),
    KEY `qa_reviews_reviewed_by_index` (`reviewed_by`),

    CONSTRAINT `qa_reviews_ticket_id_foreign`
        FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
        ON DELETE CASCADE,

    CONSTRAINT `qa_reviews_reviewed_by_foreign`
        FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Revisiones QA de tickets (Estratégico, Marca, Técnico)';

-- =====================================================================
-- RESUMEN DEL MODELO RELACIONAL
-- =====================================================================
--
--  users ──────────┬──── 1:N ────→ role_user ←──── N:1 ──── roles
--                  │
--                  ├──── 1:N ────→ clients          (como Account Manager)
--                  ├──── 1:N ────→ tickets           (como assigned_to / created_by)
--                  ├──── 1:N ────→ ticket_comments   (como autor)
--                  ├──── 1:N ────→ ticket_attachments (como uploaded_by)
--                  ├──── 1:N ────→ ticket_history     (como user_id)
--                  ├──── 1:N ────→ qa_reviews         (como reviewed_by)
--                  └──── 1:N ────→ onboarding_checklist_items (como completed_by)
--
--  clients ────────┬──── 1:1 ────→ onboardings
--                  ├──── 1:N ────→ client_contacts
--                  ├──── 1:N ────→ client_accesses
--                  └──── 1:N ────→ tickets
--
--  onboardings ────└──── 1:N ────→ onboarding_checklist_items
--
--  tickets ────────┬──── 1:N ────→ ticket_comments
--                  ├──── 1:N ────→ ticket_attachments
--                  ├──── 1:N ────→ ticket_history
--                  └──── 1:N ────→ qa_reviews
--
-- =====================================================================
