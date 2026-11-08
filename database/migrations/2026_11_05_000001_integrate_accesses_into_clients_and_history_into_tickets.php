<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Añadir columnas de Bóveda de Accesos directamente a 'clients'
        Schema::table('clients', function (Blueprint $table) {
            $table->string('access_platform', 100)->nullable()->after('notes');
            $table->string('access_url', 500)->nullable()->after('access_platform');
            $table->string('access_username', 255)->nullable()->after('access_url');
            $table->text('access_password')->nullable()->after('access_username');
            $table->text('access_notes')->nullable()->after('access_password');
        });

        // 2. Añadir columnas de Trazabilidad e Historial directamente a 'tickets'
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('history_field', 100)->nullable()->after('kanban_order');
            $table->string('history_old_value')->nullable()->after('history_field');
            $table->string('history_new_value')->nullable()->after('history_old_value');
            $table->foreignId('history_user_id')->nullable()->after('history_new_value')->constrained('users')->nullOnDelete();
            $table->timestamp('history_updated_at')->nullable()->after('history_user_id');
        });

        // 3. Migrar datos existentes si las tablas previas contienen registros
        if (Schema::hasTable('client_accesses')) {
            $accesses = DB::table('client_accesses')->orderBy('id', 'asc')->get();
            foreach ($accesses as $access) {
                // Solo si el cliente aún no tiene un acceso asignado
                DB::table('clients')
                    ->where('id', $access->client_id)
                    ->whereNull('access_platform')
                    ->update([
                        'access_platform' => $access->platform,
                        'access_url' => $access->url,
                        'access_username' => $access->username,
                        'access_password' => $access->password ?: $access->credentials,
                        'access_notes' => $access->notes,
                    ]);
            }
            Schema::dropIfExists('client_accesses');
        }

        if (Schema::hasTable('ticket_history')) {
            $latestHistories = DB::table('ticket_history')
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('ticket_id');

            foreach ($latestHistories as $ticketId => $logs) {
                $lastLog = $logs->first();
                if ($lastLog) {
                    DB::table('tickets')
                        ->where('id', $ticketId)
                        ->update([
                            'history_field' => $lastLog->field,
                            'history_old_value' => $lastLog->old_value,
                            'history_new_value' => $lastLog->new_value,
                            'history_user_id' => $lastLog->user_id,
                            'history_updated_at' => $lastLog->created_at,
                        ]);
                }
            }
            Schema::dropIfExists('ticket_history');
        }
    }

    public function down(): void
    {
        // Recrear client_accesses si se hace rollback
        if (!Schema::hasTable('client_accesses')) {
            Schema::create('client_accesses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained()->cascadeOnDelete();
                $table->string('platform', 100);
                $table->string('url')->nullable();
                $table->string('username')->nullable();
                $table->string('password')->nullable();
                $table->text('credentials')->nullable();
                $table->string('access_type', 50)->nullable();
                $table->string('status')->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Recrear ticket_history si se hace rollback
        if (!Schema::hasTable('ticket_history')) {
            Schema::create('ticket_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('field', 100);
                $table->string('old_value')->nullable();
                $table->string('new_value')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'access_platform',
                'access_url',
                'access_username',
                'access_password',
                'access_notes',
            ]);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['history_user_id']);
            $table->dropColumn([
                'history_field',
                'history_old_value',
                'history_new_value',
                'history_user_id',
                'history_updated_at',
            ]);
        });
    }
};

