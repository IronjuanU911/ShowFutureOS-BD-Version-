<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Trigger de Validación Cronológica de Tickets
        DB::unprepared("
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
        ");

        // 2. Trigger de Automatización de Progreso en Onboarding
        DB::unprepared("
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
        ");

        // 3. Trigger de Auditoría de Estado en Tickets (Kanban)
        DB::unprepared("
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
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_validar_deadline_ticket ON tickets;
            DROP FUNCTION IF EXISTS fn_validar_deadline_ticket();

            DROP TRIGGER IF EXISTS trg_actualizar_progreso_onboarding ON onboarding_checklist_items;
            DROP FUNCTION IF EXISTS fn_actualizar_progreso_onboarding();

            DROP TRIGGER IF EXISTS trg_auditoria_estado_ticket ON tickets;
            DROP FUNCTION IF EXISTS fn_auditoria_estado_ticket();
        ");
    }
};
