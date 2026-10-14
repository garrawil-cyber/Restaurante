<?php
include("admin/bd.php"); // Incluye tu archivo de conexión a la base de datos

$sentencia = $conexion->prepare("SELECT * FROM tbl_colaboradores ORDER BY id ASC LIMIT 4");
$sentencia->execute();
$lista_chefs = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestros Chefs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        #Chefs {
            padding: 60px 0;
        }
        .chef-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .chef-card:hover {
            transform: translateY(-10px);
        }
        .chef-img {
            transition: transform 0.5s ease;
        }
        .chef-card:hover .chef-img {
            transform: scale(1.05);
        }
        .chef-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .chef-card:hover .chef-overlay {
            opacity: 1;
        }
        .social-links {
            display: flex;
            gap: 15px;
        }
        .social-link {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .social-link:hover {
            background: #007bff;
            color: white;
            transform: translateY(-3px);
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }
        .text-muted {
            font-size: 0.9rem;
        }
        .separator {
            margin: 0 auto;
        }
        .line {
            height: 1px;
            width: 30px;
            background-color: #333;
        }
    </style>
</head>
<body>
    <section id="Chefs" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 mb-3">Nuestros Chefs</h2>
                <div class="separator d-flex justify-content-center align-items-center mb-4">
                    <span class="line mx-3"></span>
                    <i class="fas fa-hat-chef text-primary"></i>
                    <span class="line mx-3"></span>
                </div>
                <p class="text-muted">Conoce a nuestro equipo de expertos culinarios</p>
            </div>

            <div class="row g-4">
                <?php foreach ($lista_chefs as $lista) { ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="chef-card card border-0 shadow-sm h-100">
                            <div class="position-relative overflow-hidden">
                                <img src="images/colaboradores/<?php echo $lista['foto']; ?>"
                                     class="card-img-top chef-img"
                                     alt="<?php echo $lista['titulo']; ?>"
                                     style="height: 300px; object-fit: cover;">
                                <div class="chef-overlay">
                                    <div class="social-links">
                                        <?php if (!empty($lista['linkfacebook'])) { ?>
                                            <a href="<?php echo $lista['linkfacebook']; ?>" class="social-link" target="_blank">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if (!empty($lista['linkinstagram'])) { ?>
                                            <a href="<?php echo $lista['linkinstagram']; ?>" class="social-link" target="_blank">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if (!empty($lista['linklinkedin'])) { ?>
                                            <a href="<?php echo $lista['linklinkedin']; ?>" class="social-link" target="_blank">
                                                <i class="fab fa-linkedin-in"></i>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title mb-2"><?php echo $lista['titulo']; ?></h5>
                                <p class="card-text text-muted"><?php echo $lista['descripcion']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
