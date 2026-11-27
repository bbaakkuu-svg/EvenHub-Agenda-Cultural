<?php
declare(strict_types=1);

/**
 * Modelo Inscripcion
 * Gestiona el registro y consulta de reservas de plazas en la tabla 'inscripciones'.
 */

require_once __DIR__ . '/../includes/conexion.php';

class Inscripcion
{
    /**
     * Devuelve las métricas globales de reservas e inscripciones confirmadas.
     *
     * @return array{total_reservas: int, plazas_reservadas: int}
     */
    public static function obtenerMetricas(): array
    {
        $stmt = obtenerConexion()->query(
            "SELECT COUNT(*), COALESCE(SUM(plazas_reservadas), 0) 
             FROM inscripciones 
             WHERE estado = 'confirmada'"
        );
        $fila = $stmt->fetch(PDO::FETCH_NUM);

        return [
            'total_reservas'    => (int) ($fila[0] ?? 0),
            'plazas_reservadas' => (int) ($fila[1] ?? 0)
        ];
    }

    /**
     * Devuelve el total de plazas reservadas confirmadas para un evento específico.
     * Consumido por la Issue #8 para verificar aforo antes de reservar.
     *
     * @param int $eventoId
     * @return int
     */
    public static function plazasOcupadasPorEvento(int $eventoId): int
    {
        $stmt = obtenerConexion()->prepare(
            "SELECT COALESCE(SUM(plazas_reservadas), 0) 
             FROM inscripciones 
             WHERE evento_id = :id AND estado = 'confirmada'"
        );
        $stmt->execute([':id' => $eventoId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Registra una nueva inscripción generando un localizador alfanumérico único.
     * Punto de extensión para la Issue #11 (Sistema de Inscripción de Asistentes).
     *
     * @param int $eventoId
     * @param string $nombre
     * @param string $email
     * @param string $telefono
     * @param int $plazas Plazas solicitadas (1 a 5)
     * @param string|null $observaciones
     * @return string Código localizador generado (ej: FLK-2026-A1B2)
     */
    public static function registrar(
        int $eventoId,
        string $nombre,
        string $email,
        string $telefono,
        int $plazas = 1,
        ?string $observaciones = null
    ): string {
        $localizador = 'FLK-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $sql = 'INSERT INTO inscripciones 
                (evento_id, localizador, nombre_asistente, email_asistente, telefono_asistente, plazas_reservadas, observaciones, estado)
                VALUES (:evento_id, :localizador, :nombre, :email, :telefono, :plazas, :obs, "confirmada")';

        $stmt = obtenerConexion()->prepare($sql);
        $stmt->execute([
            ':evento_id'    => $eventoId,
            ':localizador'  => $localizador,
            ':nombre'       => trim($nombre),
            ':email'        => strtolower(trim($email)),
            ':telefono'     => trim($telefono),
            ':plazas'       => max(1, min(5, $plazas)),
            ':obs'          => $observaciones ? trim($observaciones) : null
        ]);

        return $localizador;
    }

    /**
     * Busca los datos de una inscripción por su localizador.
     * Consumido por la pantalla de confirmación o consulta de reserva (Issue #11).
     *
     * @param string $localizador
     * @return array<string, mixed>|null
     */
    public static function obtenerPorLocalizador(string $localizador): ?array
    {
        $sql = 'SELECT i.*, e.titulo AS evento_titulo, e.fecha AS evento_fecha, e.hora AS evento_hora,
                       esp.nombre AS espacio_nombre, esp.direccion AS espacio_direccion
                FROM inscripciones i
                JOIN eventos e ON i.evento_id = e.id
                JOIN espacios_municipales esp ON e.espacio_id = esp.id
                WHERE i.localizador = :localizador
                LIMIT 1';

        $stmt = obtenerConexion()->prepare($sql);
        $stmt->execute([':localizador' => strtoupper(trim($localizador))]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res ?: null;
    }
}
