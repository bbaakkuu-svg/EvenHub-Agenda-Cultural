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
     * Comprueba si ya existe una inscripción confirmada para un evento y un correo dados.
     * Evita inscripciones duplicadas en el mismo evento (Issue #11).
     *
     * @param int $eventoId
     * @param string $email
     * @return bool
     */
    public static function existeInscripcionActiva(int $eventoId, string $email): bool
    {
        $stmt = obtenerConexion()->prepare(
            "SELECT 1
             FROM inscripciones
             WHERE evento_id = :evento_id
               AND email_asistente = :email
               AND estado = 'confirmada'
             LIMIT 1"
        );
        $stmt->execute([
            ':evento_id' => $eventoId,
            ':email'     => strtolower(trim($email))
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Registra una inscripción de forma transaccional validando estado, duplicados y aforo restante.
     *
     * @throws DomainException Si el evento está agotado, no hay plazas suficientes o ya existe reserva para ese email.
     * @throws RuntimeException Si el evento no existe o no está activo.
     */
    public static function reservarConValidacion(
        int $eventoId,
        string $nombre,
        string $email,
        string $telefono,
        int $plazas = 1,
        ?string $observaciones = null
    ): string {
        $pdo = obtenerConexion();
        $emailNorm = strtolower(trim($email));
        $plazasSolicitadas = max(1, min(5, $plazas));

        $pdo->beginTransaction();
        try {
            $stmtEvento = $pdo->prepare(
                'SELECT id, aforo_total, estado
                 FROM eventos
                 WHERE id = :id
                 FOR UPDATE'
            );
            $stmtEvento->execute([':id' => $eventoId]);
            $evento = $stmtEvento->fetch(PDO::FETCH_ASSOC);

            if (!$evento || $evento['estado'] !== 'activo') {
                throw new RuntimeException('El evento no existe o no admite inscripciones actualmente.');
            }

            $stmtDup = $pdo->prepare(
                "SELECT 1
                 FROM inscripciones
                 WHERE evento_id = :evento_id
                   AND email_asistente = :email
                   AND estado = 'confirmada'
                 LIMIT 1"
            );
            $stmtDup->execute([
                ':evento_id' => $eventoId,
                ':email'     => $emailNorm
            ]);
            if ($stmtDup->fetchColumn()) {
                throw new DomainException('Ya existe una inscripción confirmada con este correo electrónico para el evento seleccionado.');
            }

            $stmtOcupadas = $pdo->prepare(
                "SELECT COALESCE(SUM(plazas_reservadas), 0)
                 FROM inscripciones
                 WHERE evento_id = :evento_id AND estado = 'confirmada'"
            );
            $stmtOcupadas->execute([':evento_id' => $eventoId]);
            $ocupadas = (int) $stmtOcupadas->fetchColumn();

            $restantes = max(0, (int) $evento['aforo_total'] - $ocupadas);
            if ($restantes === 0) {
                throw new DomainException('El evento ha alcanzado el aforo completo. No quedan plazas disponibles.');
            }
            if ($plazasSolicitadas > $restantes) {
                throw new DomainException("Solo quedan {$restantes} plazas disponibles para este evento.");
            }

            $localizador = 'FLK-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $stmtInsert = $pdo->prepare(
                'INSERT INTO inscripciones
                 (evento_id, localizador, nombre_asistente, email_asistente, telefono_asistente, plazas_reservadas, observaciones, estado)
                 VALUES (:evento_id, :localizador, :nombre, :email, :telefono, :plazas, :obs, "confirmada")'
            );
            $stmtInsert->execute([
                ':evento_id'   => $eventoId,
                ':localizador' => $localizador,
                ':nombre'      => trim($nombre),
                ':email'       => $emailNorm,
                ':telefono'    => trim($telefono),
                ':plazas'      => $plazasSolicitadas,
                ':obs'         => ($observaciones !== null && trim($observaciones) !== '') ? trim($observaciones) : null
            ]);

            $pdo->commit();
            return $localizador;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
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
                       e.precio AS evento_precio, c.nombre AS categoria_nombre, c.color_badge,
                       esp.nombre AS espacio_nombre, esp.direccion AS espacio_direccion
                FROM inscripciones i
                JOIN eventos e ON i.evento_id = e.id
                LEFT JOIN categorias c ON e.categoria_id = c.id
                JOIN espacios_municipales esp ON e.espacio_id = esp.id
                WHERE i.localizador = :localizador
                LIMIT 1';

        $stmt = obtenerConexion()->prepare($sql);
        $stmt->execute([':localizador' => strtoupper(trim($localizador))]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        return $res ?: null;
    }
}
