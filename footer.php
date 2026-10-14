<footer class="footer-section bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <!-- Información del Restaurante -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-content pe-lg-5">
                    <h5 class="mb-4">Restaurante la Sombra</h5>
                    <p class="text-muted mb-4">Disfruta de la mejor experiencia culinaria con nuestros exquisitos platos preparados por chefs expertos.</p>
                    <div class="social-links">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>

<!-- Enlaces Rápidos -->
<div class="col-lg-2 col-md-6">
    <h5 class="mb-4">Enlaces Rápidos</h5>
    <ul class="footer-links">
        <li>
            <a href="#Menu">Menú del día</a>
            <ul>
                <li><a href="#entradas">Entradas</a></li>
                <li><a href="#principales">Plato Principal</a></li>
                <li><a href="#postres">Postres</a></li>
                <li><a href="#bebidas">Bebidas</a></li>
            </ul>
        </li>
        <li><a href="#Chefs">Nuestros Chefs</a></li>
        <li><a href="#Testimonios">Testimonios</a></li>
        <li><a href="contacto.php">Contacto</a></li>
    </ul>
</div>

            <!-- Contacto -->
            <div class="col-lg-3 col-md-6">
                <h5 class="mb-4">Contacto</h5>
                <ul class="footer-contact">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Av. Principal 123, Lima, Perú</span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>+51 123 456 789</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>info@lasombra.com</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="footer-bottom text-center mt-5 pt-4 border-top border-secondary">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Restaurante la Sombra. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<style>
.footer-section {
    position: relative;
    font-family: 'Nunito', sans-serif;
}

.footer-content p {
    font-size: 0.95rem;
    line-height: 1.6;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-icon {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-icon:hover {
    background: #007bff;
    color: #fff;
    transform: translateY(-3px);
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: #adb5bd;
    text-decoration: none;
    transition: color 0.3s ease;
    font-size: 0.95rem;
}

.footer-links a:hover {
    color: #fff;
    padding-left: 5px;
}

.footer-schedule {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-schedule li {
    margin-bottom: 12px;
    color: #adb5bd;
    font-size: 0.95rem;
    display: flex;
    flex-direction: column;
}

.footer-schedule .day {
    font-weight: 600;
    color: #fff;
}

.footer-contact {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-contact li {
    margin-bottom: 15px;
    color: #adb5bd;
    font-size: 0.95rem;
    display: flex;
    align-items: flex-start;
}

.footer-contact i {
    color: #007bff;
    margin-right: 10px;
    margin-top: 5px;
}

.footer-bottom {
    font-size: 0.9rem;
    color: #adb5bd;
}

@media (max-width: 768px) {
    .footer-content {
        text-align: center;
    }
    
    .social-links {
        justify-content: center;
        margin-bottom: 2rem;
    }
    
    .footer-schedule li,
    .footer-contact li {
        align-items: center;
        text-align: center;
    }
    
    .footer-contact li {
        flex-direction: column;
    }
    
    .footer-contact i {
        margin-bottom: 5px;
    }
}
</style>

</body>
</html>
