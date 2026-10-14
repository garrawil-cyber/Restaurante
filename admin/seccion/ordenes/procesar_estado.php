<?php
require_once("../../bd.php");

class OrderController {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function getOrderDetails($orderId) {
        $ordenInfo = $this->getOrderInfo($orderId);
        if (empty($ordenInfo)) {
            return $this->formatResponse('error', 'Orden no encontrada');
        }
        return $this->formatResponse('success', 'Orden encontrada', $ordenInfo);
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

        $orden = $stmt->fetch(PDO::FETCH_ASSOC);
        return $orden ?: null;
    }

    private function formatResponse($status, $message, $data = null) {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estadoOrden = $_POST['estado'] ?? "";
    $txtID = $_POST['txtID'] ?? "";

    if ($estadoOrden && $txtID) {
        try {
            $sentencia = $conexion->prepare("UPDATE tbl_ordenes SET estado = :estado WHERE ID = :id");
            $sentencia->bindParam(":estado", $estadoOrden);
            $sentencia->bindParam(":id", $txtID);
            $sentencia->execute();
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            echo "Error al actualizar la orden: " . $e->getMessage();
        }
    } else {
        echo "Datos insuficientes para actualizar la orden.";
    }
}

include("../../templates/header.php");

$orderDetails = [];
if (isset($_GET['txtID'])) {
    $controller = new OrderController($conexion);
    $response = $controller->getOrderDetails($_GET['txtID']);
    if ($response['status'] === 'success') {
        $orderDetails = $response['data'];
    }
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => 'ID de orden no proporcionado'
    ]);
    exit;
}
?>

<div class="card">
<div class="card-header" style="font-size: 1.5rem; font-weight: bold;">Actualizar Orden</div>
    <div class="card-body">
        <form action="" method="post">
            <input type="hidden" name="txtID" value="<?php echo htmlspecialchars($_GET['txtID'] ?? ''); ?>">

            <div class="mb-3">
                <label for="fecha" class="form-label" style="font-weight: bold;">Fecha:</label>
                <input type="text" class="form-control" name="fecha" id="fecha" value="<?php echo htmlspecialchars($orderDetails['fecha_orden'] ?? ''); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="hora" class="form-label" style="font-weight: bold;">Hora:</label>
                <input type="text" class="form-control" name="hora" id="hora" value="<?php echo htmlspecialchars($orderDetails['hora_orden'] ?? ''); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="cliente_nombre" class="form-label" style="font-weight: bold;">Nombre Cliente:</label>
                <input type="text" class="form-control" name="cliente_nombre" id="cliente_nombre" value="<?php echo htmlspecialchars($orderDetails['cliente_nombre'] ?? ''); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="cliente_dni" class="form-label" style="font-weight: bold;">DNI:</label>
                <input type="text" class="form-control" name="cliente_dni" id="cliente_dni" value="<?php echo htmlspecialchars($orderDetails['cliente_dni'] ?? ''); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="cliente_telefono" class="form-label" style="font-weight: bold;">Teléfono:</label>
                <input type="text" class="form-control" name="cliente_telefono" id="cliente_telefono" value="<?php echo htmlspecialchars($orderDetails['cliente_telefono'] ?? ''); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label" style="font-weight: bold;">Estado:</label>
                <select class="form-control" name="estado" id="estado">
                    <option value="Pendiente" <?php echo (isset($orderDetails['estado_orden']) && $orderDetails['estado_orden'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="En Proceso" <?php echo (isset($orderDetails['estado_orden']) && $orderDetails['estado_orden'] === 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
                    <option value="Entregado" <?php echo (isset($orderDetails['estado_orden']) && $orderDetails['estado_orden'] === 'Entregado') ? 'selected' : ''; ?>>Entregado</option>
                    <option value="Anulado" <?php echo (isset($orderDetails['estado_orden']) && $orderDetails['estado_orden'] === 'Anulado') ? 'selected' : ''; ?>>Anulado</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Guardar Cambios</button>
            <a href="index.php" class="btn btn-primary">Cancelar</a>
        </form>
    </div>
    <div class="card-footer text-muted"></div>
</div>

<?php include("../../templates/footer.php"); ?>
