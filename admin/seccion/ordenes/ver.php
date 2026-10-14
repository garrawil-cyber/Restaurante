<?php
require_once("../../bd.php");

session_start();

class OrderDetailController {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function getOrderDetails($orderId) {
        try {

            $ordenInfo = $this->getOrderInfo($orderId);
            if (empty($ordenInfo)) {
                return $this->formatResponse('error', 'Orden no encontrada');
            }

            $detalles = $this->getOrderProducts($orderId);
            $total = $this->calculateTotal($detalles);

            return $this->formatResponse('success', null, [
                'orden' => $ordenInfo,
                'data' => $detalles,
                'total' => $total
            ]);

        } catch (PDOException $e) {
            error_log("Error en base de datos: " . $e->getMessage());
            return $this->formatResponse('error', 'Error al obtener los detalles de la orden');
        } catch (Exception $e) {
            error_log("Error general: " . $e->getMessage());
            return $this->formatResponse('error', 'Error interno del servidor');
        }
    }

    private function getOrderInfo($orderId) {
        $sql = "SELECT
                    o.ID as id,
                    o.fecha as fecha_orden,
                    o.hora as hora_orden,
                    o.estado as estado_orden,
                    c.nombres as cliente_nombre,
                    c.dni as cliente_dni,
                    c.telefono as cliente_telefono,
                    c.email as cliente_email
                FROM tbl_ordenes o
                INNER JOIN tbl_cliente c ON o.ID_cliente = c.ID
                WHERE o.ID = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $orderId, PDO::PARAM_INT);
        $stmt->execute();

        if ($orden = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return [
                'id' => $orden['id'],
                'fecha' => date('d-m-Y', strtotime($orden['fecha_orden'])),
                'hora' => $orden['hora_orden'],
                'estado' => $orden['estado_orden'],
                'cliente' => [
                    'nombre' => $orden['cliente_nombre'],
                    'dni' => $orden['cliente_dni'],
                    'telefono' => $orden['cliente_telefono'],
                    'email' => $orden['cliente_email']
                ]
            ];
        }
        return null;
    }

    private function getOrderProducts($orderId) {
        $sql = "SELECT
                    m.nombre AS menu_nombre,
                    ca.nomb_categoria AS categoria_nombre,
                    d.cant,
                    d.precio,
                    (d.cant * d.precio) AS subtotal
                FROM tbl_detalle_orden d
                INNER JOIN tbl_menu m ON d.id_menu = m.ID
                INNER JOIN tbl_categorias ca ON m.id_categoria = ca.ID
                WHERE d.id_orden = :id
                ORDER BY ca.nomb_categoria, m.nombre";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $orderId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function calculateTotal($detalles) {
        return array_reduce($detalles, function($carry, $item) {
            return $carry + floatval($item['subtotal']);
        }, 0.0);
    }

    private function formatResponse($status, $message = null, $data = null) {
        $response = ['status' => $status];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response = array_merge($response, $data);
        }

        return $response;
    }
}

if (isset($_GET['txtID'])) {

    $controller = new OrderDetailController($conexion);
    $response = $controller->getOrderDetails($_GET['txtID']);

    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');

    echo json_encode($response);
    exit();
} else {
 
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => 'ID de orden no proporcionado'
    ]);
    exit();
}
?>
