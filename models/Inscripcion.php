<?php
declare(strict_types=1);

/**
 * Modelo Inscripcion (models/Inscripcion.php)
 * FLOCK — Agenda Cultural Municipal
 *
 * Encapsula la persistencia y consultas sobre la tabla 'inscripciones'.
 */

require_once __DIR__ . '/../includes/conexion.php';

class Inscripcion
{
    /**
     * Obtiene métricas agregadas de reservas e inscripciones confirmadas.
     *
     * @return array{total_reservas: int, plazas_reservadas: int}
     */
    public static function obtenerMetricas(): array
    {
        $pdo = obtenerConexion();
        $stmt = $pdo->query("SELECT COUNT(*), COALESCE(SUM(plazas_reservadas), 0) FROM inscripciones WHERE estado = 'confirmada'");
        $fila = $stmt->fetch(PDO::FETCH_NUM);

        return [
            'total_reservas'    => (int) ($fila[0] ?? 0),
            'plazas_reservadas' => (int) ($fila[1] ?? 0)
        ];
    }

    /**
     * Devuelve el total de plazas reservadas confirmadas para un evento.
     *
     * @param int $eventoId
     * @return int
     */
    public static function plazasOcupadasPorEvento(int $eventoId): int
    {
        $pdo = obtenerConexion();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(plazas_reservadas), 0) FROM inscripciones WHERE evento_id = :id AND estado = 'confirmada'");
        $stmt->execute([':id' => $eventoId]);

        return (int) $stmt->fetchColumn();
    }
}
