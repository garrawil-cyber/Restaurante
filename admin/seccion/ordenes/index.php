<?php

//require_once("SecurityHelper.php");
require_once("../../bd.php");

class OrderController {
    private $conexion;

    // Añade esta constante
   const ESTADOS_ORDEN = [
        'Pendiente' => ['color' => '#92400e', 'bg' => '#fef3c7'],
        'En Proceso' => ['color' => '#1e40af', 'bg' => '#dbeafe'],
        'Entregado' => ['color' => '#166534', 'bg' => '#dcfce7'],
        'Anulado' => ['color' => '#991b1b', 'bg' => '#fee2e2']
    ];

    public function getEstadoBadgeClass($estado) {
        $estado = strtolower(str_replace(' ', '-', $estado));
        return 'estado-badge estado-' . $estado;
    }

    public function getEstadoLabel($estado) {
        return htmlspecialchars($estado);
    }

    public function __construct($conexion) {
        $this->conexion = $conexion;

    }

    public function getOrders() {
        $sql = "SELECT
            o.ID,
            o.fecha,
            o.hora,
            c.nombres,
            c.dni,
            c.telefono,
            c.email,
            o.estado
        FROM
            tbl_ordenes AS o
        INNER JOIN
            tbl_cliente AS c ON o.ID_cliente = c.ID
        ORDER BY o.fecha DESC, o.hora DESC";

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener órdenes: " . $e->getMessage());
            return [];
        }
    }
}

$controller = new OrderController($conexion);

// Obtener lista de órdenes
$lista_ordenes = $controller->getOrders();

// Incluir el header
include("../../templates/header.php");
?>

<!-- Estilos CSS -->
<style>
.btn-group-actions .btn {
    margin-right: 3px;
}
.btn-group-actions .btn:last-child {
    margin-right: 0;
}
.alert {
    margin-top: 1rem;
}
/* Estilos mejorados para los badges de estado */
.estado-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    transition: all 0.2s ease-in-out;
}

/* Estados específicos con hover effects */
.estado-pendiente {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #f59e0b;
}
.estado-pendiente:hover {
    background-color: #fde68a;
}

.estado-en-proceso {
    background-color: #dbeafe;
    color: #1e40af;
    border: 1px solid #3b82f6;
}
.estado-en-proceso:hover {
    background-color: #bfdbfe;
}

.estado-entregado {
    background-color: #dcfce7;
    color: #166534;
    border: 1px solid #22c55e;
}
.estado-entregado:hover {
    background-color: #bbf7d0;
}

.estado-anulado {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}
.estado-anulado:hover {
    background-color: #fecaca;
}
</style>


<!-- Contenido principal -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gestión de Órdenes </h5>
        <!--<a class="btn btn-primary" href="crear.php" role="button">
            <i class="fas fa-plus"></i> Nueva Orden
        </a>-->
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_ordenes as $orden): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($orden['ID']); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($orden['fecha'])); ?></td>
                            <td><?php echo htmlspecialchars($orden['hora']); ?></td>
                            <td><?php echo htmlspecialchars($orden['nombres']); ?></td>
                            <td><?php echo htmlspecialchars($orden['dni']); ?></td>
                            <td><?php echo htmlspecialchars($orden['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($orden['email']); ?></td>
                            <td>
                            <span class="estado-badge <?php echo $controller->getEstadoBadgeClass($orden['estado']); ?>">
                                <?php echo $controller->getEstadoLabel($orden['estado']); ?>
                            </span>
                            </td>
                            <td>
                                <div class="btn-group-actions">
                                <button type="button" class="btn btn-info btn-sm ver-detalle" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detalleModal" 
                                            data-id="<?php echo $orden['ID']; ?>">
                                        <i class="fas fa-eye"></i> Ver
                                </button>
                                    <a href="procesar_estado.php?txtID=<?php echo $orden['ID']; ?>"
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Procesar Orden
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->

<!-- Modal de Detalles -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalleModalLabel">
                    Detalles del Pedido - <span id="ordenTitulo" style="color: #007bff; font-weight: bold;"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Cliente:</strong> <span id="clienteNombre"></span></p>
                        <p><strong>Fecha:</strong> <span id="ordenFecha"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Estado:</strong> 
                        <span id="ordenEstado" class="estado-badge"></span></p>
                        <p><strong>Email:</strong> <span id="clienteEmail"></span></p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Menú</th>
                                <th>Categoría</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detallesTabla"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total:</td>
                                <td class="text-end fw-bold" id="totalOrden"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            <a id="printOrderLink" href="#" class="btn btn-success btn-sm">
            <i class="fas fa-print"></i> Imprimir Orden</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>

<!-- JavaScript para el manejo de modales y acciones -->
<script>
class OrderManager {
    constructor() {
        this.detalleModal = document.getElementById('detalleModal');
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Manejo del modal de eliminación

        // Manejo del modal de detalles
        document.querySelectorAll('.ver-detalle').forEach(btn => {
            btn.addEventListener('click', (e) => this.loadOrderDetails(e));
        });

        this.detalleModal.addEventListener('hidden.bs.modal', () => this.clearModal());
    }

    async loadOrderDetails(event) {
    const id = event.currentTarget.dataset.id;
    try {
        fetch(`ver.php?txtID=${id}`)
        .then(response => response.json())
        const response = await fetch(`ver.php?txtID=${id}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Datos recibidos:', data); // Para depuración
        
        if (data.status === 'success') {
            this.populateModal(data);
        } else {
            throw new Error(data.message || 'Error al cargar detalles del pedido');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar detalles del pedido: ' + error.message);
    }
}

    populateModal(data) {
    // Verificar si tenemos todos los datos necesarios
    if (!data || !data.orden || !data.data) {
        console.error('Datos incompletos recibidos:', data);
        alert('Error: Datos incompletos recibidos del servidor');
        return;
    }

    const estado = data.orden.estado.toLowerCase().replace(' ', '-');
    const sanitize = (str) => String(str || '').replace(/[&<>"']/g, (m) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[m]);

        // Envoar el ID para Imprimir la orden del pedido en .pdf 
        const printLink = document.getElementById('printOrderLink');
        if (printLink) {
            printLink.href = `../../carrito/pedidoPdf.php?txtID=${data.orden.id}`;
        }

    // Obtener referencias a los elementos del DOM
    const elements = {
        ordenTitulo: document.getElementById('ordenTitulo'),
        clienteNombre: document.getElementById('clienteNombre'),
        ordenFecha: document.getElementById('ordenFecha'),
        ordenEstado: document.getElementById('ordenEstado'),
        clienteEmail: document.getElementById('clienteEmail'),
        detallesTabla: document.getElementById('detallesTabla'),
        totalOrden: document.getElementById('totalOrden')
    };

    // Verificar que todos los elementos existan
    for (const [key, element] of Object.entries(elements)) {
        if (!element) {
            console.error(`Elemento ${key} no encontrado en el DOM`);
            return;
        }
    }

    // Actualizar contenido
    elements.ordenTitulo.textContent = `Nro.${sanitize(data.orden.id)}`;
    elements.clienteNombre.textContent = sanitize(data.orden.cliente.nombre);
    elements.ordenFecha.textContent = `${sanitize(data.orden.fecha)} ${sanitize(data.orden.hora)}`;
    elements.ordenEstado.className = `estado-badge estado-${estado}`;
    elements.ordenEstado.textContent = data.orden.estado;
    elements.clienteEmail.textContent = sanitize(data.orden.cliente.email);

    // Limpiar y poblar la tabla
    elements.detallesTabla.innerHTML = '';
    let total = 0;

    data.data.forEach(detalle => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${sanitize(detalle.menu_nombre)}</td>
            <td>${sanitize(detalle.categoria_nombre)}</td>
            <td class="text-center">${sanitize(detalle.cant)}</td>
            <td class="text-end">S/ ${parseFloat(detalle.precio).toFixed(2)}</td>
            <td class="text-end">S/ ${parseFloat(detalle.subtotal).toFixed(2)}</td>
        `;
        elements.detallesTabla.appendChild(row);
        total += parseFloat(detalle.subtotal);
    });

    elements.totalOrden.textContent = `S/ ${total.toFixed(2)}`;
}

}

// Inicializar el gestor de órdenes cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
    new OrderManager();
});
</script>
