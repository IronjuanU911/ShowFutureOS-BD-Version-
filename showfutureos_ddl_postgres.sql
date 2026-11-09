-- =====================================================================
-- ShowFutureOS — Script DDL para PostgreSQL (Supabase)
-- Base de datos: postgres
-- Motor: PostgreSQL 15+ / 17+ (Supabase)
-- Generado: 2026-10-19
-- =====================================================================

-- 1. TABLA: users (Empleados internos de Show Future)
CREATE TABLE IF NOT EXISTS users (
    id                  BIGSERIAL PRIMARY KEY,
    name                VARCHAR(255) NOT NULL,
    email               VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at   TIMESTAMP WITH TIME ZONE NULL,
    password            VARCHAR(255) NOT NULL,
    avatar              VARCHAR(255) NULL,
    phone               VARCHAR(50) NULL,
    is_active           BOOLEAN NOT NULL DEFAULT TRUE,
    remember_token      VARCHAR(100) NULL,
    created_at          TIMESTAMP WITH TIME ZONE NULL,
    updated_at          TIMESTAMP WITH TIME ZONE NULL
);

-- 2. TABLA: roles (Catálogo de roles: Admin, AM, Editor, Copy, Ads, Comercial, etc.)
CREATE TABLE IF NOT EXISTS roles (
    id                  BIGSERIAL PRIMARY KEY,
    name                VARCHAR(100) NOT NULL UNIQUE,
    slug                VARCHAR(100) NOT NULL UNIQUE,
    description         TEXT NULL,
    created_at          TIMESTAMP WITH TIME ZONE NULL,
    updated_at          TIMESTAMP WITH TIME ZONE NULL
);

-- 3. TABLA: role_user (Pivote muchos a muchos: usuarios <-> roles)
CREATE TABLE IF NOT EXISTS role_user (
    id                  BIGSERIAL PRIMARY KEY,
    user_id             BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role_id             BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    created_at          TIMESTAMP WITH TIME ZONE NULL,
    updated_at          TIMESTAMP WITH TIME ZONE NULL,
    CONSTRAINT role_user_unique UNIQUE (user_id, role_id)
);

-- 4. TABLA: clients (Empresas/Marcas gestionadas por Show Future)
CREATE TABLE IF NOT EXISTS clients (
    id                  BIGSERIAL PRIMARY KEY,
    name                VARCHAR(255) NOT NULL,
    status              VARCHAR(255) NOT NULL DEFAULT 'onboarding',
    level               VARCHAR(255) NOT NULL DEFAULT 'starter',
    ads_budget          NUMERIC(12, 2) NULL,
    account_manager_id  BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    start_date          DATE NULL,
    renewal_date        DATE NULL,
    has_sla             BOOLEAN NOT NULL DEFAULT FALSE,
    main_platform       VARCHAR(100) NULL,
    main_objective      VARCHAR(255) NULL,
    logo                VARCHAR(255) NULL,
    notes               TEXT NULL,
    created_at          TIMESTAMP WITH TIME ZONE NULL,
    updated_at          TIMESTAMP WITH TIME ZONE NULL,
    deleted_at          TIMESTAMP WITH TIME ZONE NULL
);

-- 5. TABLA: client_contacts (Contactos clave del cliente)
CREATE TABLE IF NOT EXISTS client_contacts (
    id                  BIGSERIAL PRIMARY KEY,
    client_id           BIGINT NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
    name                VARCHAR(255) NOT NULL,
    role                VARCHAR(100) NULL,
    phone               VARCHAR(50) NULL,
    email               VARCHAR(255) NULL,
    is_primary          BOOLEAN NOT NULL DEFAULT FALSE,
    notes               TEXT NULL,
    created_at          TIMESTAMP WITH TIME ZONE NULL,
    updated_at          TIMESTAMP WITH TIME ZONE NULL
);

-- 6. TABLA: client_accesses (Bóveda de accesos y credenciales)
CREATE TABLE IF NOT EXISTS client_accesses (
    id                  BIGSERIAL PRIMARY KEY,
    client_id           BIGINT NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
    platform            VARCHAR(100) NOT NULL,
    url                 VARCHAR(255) NULL,
    username            VARCHAR(255) NULL,
    password            VARCHAR(255) NULL,
    credentials         TEXT NULL,
    access_type         VARCHAR(50) NULL,
    status              VARCHAR(255) NOT NULL DEFAULT 'active',
    notes               TEXT NULL,
    created_at          TIMESTAMP WITH TIME ZONE NULL,
    updated_at          TIMESTAMP WITH TIME ZONE NULL
);

-- 7. TABLA: onboardings (Proceso de onboarding 1:1 por cliente)
CREATE TABLE IF NOT EXISTS onboardings (
    id                          BIGSERIAL PRIMARY KEY,
    client_id                   BIGINT NOT NULL UNIQUE REFERENCES clients(id) ON DELETE CASCADE,
    status                      VARCHAR(255) NOT NULL DEFAULT 'pending',
    communication_rules_sent    BOOLEAN NOT NULL DEFAULT FALSE,
    calendar_shared             BOOLEAN NOT NULL DEFAULT FALSE,
    started_at                  DATE NULL,
    target_completion_date      DATE NULL,
    progress_percentage         INTEGER NOT NULL DEFAULT 0,
    completed_at                DATE NULL,
    notes                       TEXT NULL,
    communication_channel       TEXT NULL,
    created_at                  TIMESTAMP WITH TIME ZONE NULL,
    updated_at                  TIMESTAMP WITH TIME ZONE NULL
);

-- 8. TABLA: onboarding_checklist_items (Ítems del checklist de 7 días)
CREATE TABLE IF NOT EXISTS onboarding_checklist_items (
    id                  BIGSERIAL PRIMARY KEY,
    onboarding_id       BIGINT NOT NULL REFERENCES onboardings(id) ON DELETE CASCADE,
    day_number          INTEGER NOT NULL DEFAULT 1,
    title               VARCHAR(255) NOT NULL,
    description         TEXT NULL,
    is_completed        BOOLEAN NOT NULL DEFAULT FALSE,
    completed_at        TIMESTAMP WITH TIME ZONE NULL,
    completed_by        BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    "order"             INTEGER NOT NULL DEFAULT 0,
    sort_order          INTEGER NOT NULL DEFAULT 0,
    created_at          TIMESTAMP WITH TIME ZONE NULL,
    updated_at          TIMESTAMP WITH TIME ZONE NULL
);

-- 9. TABLA: tickets (Tareas de producción y Kanban)
CREATE TABLE IF NOT EXISTS tickets (
    id                          BIGSERIAL PRIMARY KEY,
    ticket_number               VARCHAR(20) NOT NULL UNIQUE,
    client_id                   BIGINT NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
    type                        VARCHAR(255) NOT NULL,
    title                       VARCHAR(255) NOT NULL,
    description                 TEXT NULL,
    format                      VARCHAR(100) NULL,
    priority                    VARCHAR(255) NOT NULL DEFAULT 'medium',
    status                      VARCHAR(255) NOT NULL DEFAULT 'backlog',
    assigned_to                 BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_by                  BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    deadline                    DATE NULL,
    requires_client_approval    BOOLEAN NOT NULL DEFAULT FALSE,
    client_approved             BOOLEAN NOT NULL DEFAULT FALSE,
    client_approved_at          TIMESTAMP WITH TIME ZONE NULL,
    delivered_at                TIMESTAMP WITH TIME ZONE NULL,
    kanban_order                INTEGER NOT NULL DEFAULT 0,
    created_at                  TIMESTAMP WITH TIME ZONE NULL,
    updated_at                  TIMESTAMP WITH TIME ZONE NULL,
    deleted_at                  TIMESTAMP WITH TIME ZONE NULL
);

-- 10. TABLA: ticket_comments (Discusión interna del ticket)
CREATE TABLE IF NOT EXISTS ticket_comments (
    id                  BIGSERIAL PRIMARY KEY,
    ticket_id           BIGINT NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
    user_id             BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    body                TEXT NOT NULL,
    created_at          TIMESTAMP WITH TIME ZONE NULL,
    updated_at          TIMESTAMP WITH TIME ZONE NULL
);

-- 11. TABLA: ticket_attachments (Archivos adjuntos y entregables)
CREATE TABLE IF NOT EXISTS ticket_attachments (
    id                  BIGSERIAL PRIMARY KEY,
    ticket_id           BIGINT NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
    uploaded_by         BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    file_name           VARCHAR(255) NOT NULL,
    file_path           VARCHAR(500) NOT NULL,
    file_type           VARCHAR(50) NULL,
    file_size           BIGINT NULL,
    created_at          TIMESTAMP WITH TIME ZONE NULL,
    updated_at          TIMESTAMP WITH TIME ZONE NULL
);

-- 12. TABLA: ticket_history (Historial de transiciones de estado)
CREATE TABLE IF NOT EXISTS ticket_history (
    id                  BIGSERIAL PRIMARY KEY,
    ticket_id           BIGINT NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
    user_id             BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    field               VARCHAR(100) NOT NULL,
    old_value           VARCHAR(255) NULL,
    new_value           VARCHAR(255) NULL,
    created_at          TIMESTAMP WITH TIME ZONE NULL
);

-- 13. TABLA: qa_reviews (Evaluación de Control de Calidad en 3 bloques)
CREATE TABLE IF NOT EXISTS qa_reviews (
    id                          BIGSERIAL PRIMARY KEY,
    ticket_id                   BIGINT NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
    reviewed_by                 BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    is_approved                 BOOLEAN NOT NULL DEFAULT FALSE,
    -- Bloque 1: Estratégico
    strategic_message_aligned   BOOLEAN NOT NULL DEFAULT FALSE,
    strategic_cta_clear         BOOLEAN NOT NULL DEFAULT FALSE,
    strategic_funnel_correct    BOOLEAN NOT NULL DEFAULT FALSE,
    -- Bloque 2: Marca & Tono
    brand_tone_correct          BOOLEAN NOT NULL DEFAULT FALSE,
    brand_no_risky_claims       BOOLEAN NOT NULL DEFAULT FALSE,
    brand_policy_aligned        BOOLEAN NOT NULL DEFAULT FALSE,
    -- Bloque 3: Técnico
    tech_format_correct         BOOLEAN NOT NULL DEFAULT FALSE,
    tech_subtitles_legible      BOOLEAN NOT NULL DEFAULT FALSE,
    tech_duration_adequate      BOOLEAN NOT NULL DEFAULT FALSE,
    -- Resultado y feedback
    rejection_reason            TEXT NULL,
    notes                       TEXT NULL,
    reviewed_at                 TIMESTAMP WITH TIME ZONE NULL,
    created_at                  TIMESTAMP WITH TIME ZONE NULL,
    updated_at                  TIMESTAMP WITH TIME ZONE NULL
);

-- ÍNDICES DE RENDIMIENTO
CREATE INDEX IF NOT EXISTS idx_clients_account_manager ON clients(account_manager_id);
CREATE INDEX IF NOT EXISTS idx_tickets_client ON tickets(client_id);
CREATE INDEX IF NOT EXISTS idx_tickets_assigned ON tickets(assigned_to);
CREATE INDEX IF NOT EXISTS idx_tickets_status ON tickets(status);
CREATE INDEX IF NOT EXISTS idx_onboarding_items_order ON onboarding_checklist_items(onboarding_id, day_number, sort_order);
CREATE INDEX IF NOT EXISTS idx_ticket_comments_ticket ON ticket_comments(ticket_id);
CREATE INDEX IF NOT EXISTS idx_qa_reviews_ticket ON qa_reviews(ticket_id);


-- =====================================================================
-- DISPARADORES (TRIGGERS) Y FUNCIONES PL/pgSQL
-- =====================================================================

-- 1. Trigger de Validación Cronológica de Tickets (Restricción de Negocio)
CREATE OR REPLACE FUNCTION fn_validar_deadline_ticket()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.deadline IS NOT NULL AND NEW.deadline < NEW.created_at::date THEN
        RAISE EXCEPTION 'Error de Integridad: La fecha de entrega (%) no puede ser anterior a la creacion (%).', 
            NEW.deadline, NEW.created_at::date;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_validar_deadline_ticket ON tickets;
CREATE TRIGGER trg_validar_deadline_ticket
BEFORE INSERT OR UPDATE ON tickets
FOR EACH ROW
EXECUTE FUNCTION fn_validar_deadline_ticket();

-- 2. Trigger de Automatización de Progreso en Onboarding
CREATE OR REPLACE FUNCTION fn_actualizar_progreso_onboarding()
RETURNS TRIGGER AS $$
DECLARE
    v_total INTEGER;
    v_completados INTEGER;
    v_porcentaje INTEGER;
    v_onboarding_id BIGINT;
BEGIN
    v_onboarding_id := COALESCE(NEW.onboarding_id, OLD.onboarding_id);

    SELECT COUNT(*), COUNT(CASE WHEN is_completed = true THEN 1 END)
    INTO v_total, v_completados
    FROM onboarding_checklist_items
    WHERE onboarding_id = v_onboarding_id;

    IF v_total > 0 THEN
        v_porcentaje := ROUND((v_completados::numeric / v_total::numeric) * 100);
    ELSE
        v_porcentaje := 0;
    END IF;

    UPDATE onboardings
    SET progress_percentage = v_porcentaje,
        status = CASE WHEN v_porcentaje = 100 THEN 'completed' ELSE 'in_progress' END,
        updated_at = NOW()
    WHERE id = v_onboarding_id;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_actualizar_progreso_onboarding ON onboarding_checklist_items;
CREATE TRIGGER trg_actualizar_progreso_onboarding
AFTER INSERT OR UPDATE OF is_completed OR DELETE ON onboarding_checklist_items
FOR EACH ROW
EXECUTE FUNCTION fn_actualizar_progreso_onboarding();

-- 3. Trigger de Auditoría de Estado en Tickets (Kanban)
CREATE OR REPLACE FUNCTION fn_auditoria_estado_ticket()
RETURNS TRIGGER AS $$
BEGIN
    IF OLD.status IS DISTINCT FROM NEW.status THEN
        NEW.updated_at := NOW();
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_auditoria_estado_ticket ON tickets;
CREATE TRIGGER trg_auditoria_estado_ticket
BEFORE UPDATE OF status ON tickets
FOR EACH ROW
EXECUTE FUNCTION fn_auditoria_estado_ticket();
