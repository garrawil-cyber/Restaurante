<?php
require_once "../bd.php";
//require_once 'fpdf/pdf/fpdf.php';
require_once "../carrito/fpdf/pdf/code128.php";

// Configuración de zona horaria
date_default_timezone_set("America/Lima");

class PedidoPDF extends PDF_Code128 {
    // Constantes de configuración
    const RESTAURANT_NAME = 'RESTAURANT LA SOMBRA';
    const RESTAURANT_ADDRESS = 'AV. LOS INCAS NRO.410-CHICLAYO';
    const RESTAURANT_PHONE = '914914644';
    const RESTAURANT_EMAIL = 'lasombra@gmail.com';
    
    private $conexion;
    private $numeroPedido;
    
    public function __construct($conexion) {
        parent::__construct('P', 'mm', array(80, 150));
        $this->conexion = $conexion;
        $this->SetMargins(4, 10, 4);
    }

    public function setNumeroPedido($numero) {
        $this->numeroPedido = $numero;
    }
    
    public function Header() {
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(0, 0, 0);
        $this->MultiCell(0, 5, $this->convertToISO(strtoupper(self::RESTAURANT_NAME)), 0, 'C', false);
        
        // Número de pedido destacado
        $this->SetFont('Arial', 'B', 10);
        $this->MultiCell(0, 5, $this->convertToISO('PEDIDO N° ' . str_pad($this->numeroPedido, 8, '0', STR_PAD_LEFT)), 0, 'C', false);
        
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(0, 5, $this->convertToISO(self::RESTAURANT_ADDRESS), 0, 'C', false);
        $this->MultiCell(0, 5, $this->convertToISO("Teléfono: " . self::RESTAURANT_PHONE), 0, 'C', false);
        $this->MultiCell(0, 5, $this->convertToISO("Email: " . self::RESTAURANT_EMAIL), 0, 'C', false);
        $this->Ln(9);
    }

    private function dibujarPiePagina() {
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(39, 39, 51);
        
        // Agregar fecha y hora de impresión
        $fechaHora = date('d/m/Y H:i:s');
        $this->MultiCell(0, 5, $this->convertToISO(
            "Fecha y hora de impresión: " . $fechaHora
        ), 0, 'L', false);
        
        $this->MultiCell(0, 5, $this->convertToISO(
            "*** Precios incluyen impuestos. Para reclamos o devoluciones debe presentar esta factura ***"
        ), 0, 'C', false);
    }

    private function numeroALetras($numero) {
        $unidades = array('', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE');
        $decenas = array('', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA');
        $diecis = array('DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE');
        $centenas = array('', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS');

        $numero = number_format($numero, 2, '.', '');
        $entero = floor($numero);
        $decimal = round(($numero - $entero) * 100);
        $letras = "";

        if ($entero == 0) {
            $letras = "CERO";
        }

        if ($entero == 100) {
            $letras = "CIEN";
        } else if ($entero > 100) {
            $cent = floor($entero / 100);
            $entero = $entero % 100;
            $letras = $centenas[$cent] . " ";
        }

        if ($entero > 0) {
            if ($entero < 10) {
                $letras .= $unidades[$entero];
            } else if ($entero < 20) {
                $letras .= $diecis[$entero - 10];
            } else {
                $dec = floor($entero / 10);
                $uni = $entero % 10;
                if ($uni == 0) {
                    $letras .= $decenas[$dec];
                } else {
                    $letras .= $decenas[$dec] . " Y " . $unidades[$uni];
                }
            }
        }

        return $letras . " CON " . str_pad($decimal, 2, "0", STR_PAD_LEFT) . "/100 SOLES";
    }
    
    
    private function convertToISO($text) {
        return iconv("UTF-8", "ISO-8859-1", $text);
    }
    
    public function generarPDF($idPedido) {
        try {
            $this->setNumeroPedido($idPedido);
            $clienteInfo = $this->obtenerDatosCliente($idPedido);
            if (!$clienteInfo) {
                throw new Exception("No se encontraron datos del cliente");
            }
            
            $this->AddPage();
            $this->dibujarEncabezadoTabla();
            $this->dibujarDetallesPedido($idPedido);
            $this->dibujarPiePagina();
            
            $this->Output("I", "OrdenPedido_Nro" . $idPedido . ".pdf", true);
            
        } catch (Exception $e) {
            $this->mostrarError($e->getMessage());
        }
    }
    
    private function obtenerDatosCliente($idPedido) {
        $stmt = $this->conexion->prepare("
            SELECT c.nombres, c.dni, c.telefono, o.fecha, o.hora 
            FROM tbl_cliente c 
            INNER JOIN tbl_ordenes o ON c.ID = o.ID 
            WHERE o.ID = :id
        ");
        $stmt->bindParam(":id", $idPedido);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function dibujarEncabezadoTabla() {
        $this->SetFillColor(23, 83, 201);
        $this->SetDrawColor(23, 83, 201);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 9);
        
        $this->Cell(30, 8, $this->convertToISO('Producto'), 1, 0, 'C', true);
        $this->Cell(15, 8, $this->convertToISO('Cant.'), 1, 0, 'C', true);
        $this->Cell(15, 8, $this->convertToISO('Precio'), 1, 0, 'C', true);
        $this->Cell(15, 8, $this->convertToISO('Total'), 1, 0, 'C', true);
        $this->Ln(8);
    }
    
    private function dibujarDetallesPedido($idPedido) {
        $stmt = $this->conexion->prepare("
            SELECT 
                m.nombre AS menu_nombre,
                d.cant AS cantidad,
                d.precio AS precio,
                (d.cant * d.precio) AS subtotal
            FROM tbl_detalle_orden d
            INNER JOIN tbl_menu m ON d.id_menu = m.ID
            WHERE d.id_orden = :id
            ORDER BY m.nombre
        ");
        $stmt->bindParam(":id", $idPedido);
        $stmt->execute();
        
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(39, 39, 51);
        
        $total = 0;
        while ($detalle = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->Cell(30, 7, $this->convertToISO($detalle['menu_nombre']), 1, 0, 'L');
            $this->Cell(15, 7, $detalle['cantidad'], 1, 0, 'C');
            $this->Cell(15, 7, number_format($detalle['precio'], 2), 1, 0, 'R');
            $this->Cell(15, 7, number_format($detalle['subtotal'], 2), 1, 0, 'R');
            $this->Ln();
            
            $total += $detalle['subtotal'];
        }
        
        // Total en números
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(60, 7, $this->convertToISO('TOTAL A PAGAR'), 1, 0, 'R');
        $this->Cell(15, 7, number_format($total, 2), 1, 0, 'R');
        $this->Ln(10);

        // Total en letras
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(0, 5, $this->convertToISO('SON: ' . $this->numeroALetras($total)), 0, 'L');
        $this->Ln(5);
    }
    

    private function mostrarError($mensaje) {
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error - Restaurante</title>
            <style>
                .error-container {
                    text-align: center;
                    padding: 20px;
                    background-color: #f8d7da;
                    border: 1px solid #f5c6cb;
                    border-radius: 4px;
                    margin: 20px;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1>¡Error!</h1>
                <p>' . htmlspecialchars($mensaje) . '</p>
            </div>
        </body>
        </html>';
        exit;
    }
}

// Uso del código
try {
    // Reemplazamos FILTER_SANITIZE_STRING con una alternativa segura
    $idPedido = isset($_GET['txtID']) ? htmlspecialchars(strip_tags($_GET['txtID']), ENT_QUOTES, 'UTF-8') : '';
    
    // Validación adicional para asegurar que sea un número
    if (!empty($idPedido) && !is_numeric($idPedido)) {
        throw new Exception("ID de pedido inválido");
    }
    
    if (empty($idPedido)) {
        throw new Exception("ID de pedido no proporcionado");
    }
    
    $pdf = new PedidoPDF($conexion);
    $pdf->generarPDF($idPedido);
    
} catch (Exception $e) {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<div class="error-container"><p>Error: ' . htmlspecialchars($e->getMessage()) . '</p></div>';
}
?>