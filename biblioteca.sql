-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-06-2025 a las 14:23:50
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `biblioteca`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesos_usuario`
--

CREATE TABLE `accesos_usuario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_acceso` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(5, 'Arte'),
(3, 'Ciencia'),
(1, 'Ficción'),
(4, 'Historia'),
(2, 'No Ficción');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `clave`, `valor`) VALUES
(1, 'pdf_password', 'tucontraseñaactual');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_restables`
--

CREATE TABLE `historial_restables` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fecha_restablecimiento` datetime DEFAULT current_timestamp(),
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_restables`
--

INSERT INTO `historial_restables` (`id`, `usuario_id`, `email`, `fecha_restablecimiento`, `fecha`) VALUES
(1, 6, 'david.gomez@email.com', '2025-06-12 20:00:42', '2025-06-12 20:00:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `autor` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `genero` varchar(100) DEFAULT NULL,
  `año` year(4) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `categoria_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `ruta_pdf` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `autor`, `descripcion`, `categoria`, `genero`, `año`, `isbn`, `disponible`, `categoria_id`, `imagen`, `ruta_pdf`, `stock`) VALUES
(1, 'Cien años de soledad', 'Gabriel García Márquez', 'Novela emblemática del realismo mágico que narra la historia de la familia Buendía.', 'Novela', 'Realismo mágico', '1967', '978-0307474728', 1, NULL, 'libro_684f10446f549.png', 'cien_años_soledad.pdf', 10),
(2, 'Don Quijote de la Mancha', 'Miguel de Cervantes', 'La obra cumbre de la literatura española, una sátira de los libros de caballerías.', 'Clásico', 'Aventura', '0000', '978-8491050290', 1, NULL, 'libro2.png', 'don_quijote.pdf', 10),
(3, 'La sombra del viento', 'Carlos Ruiz Zafón', 'Misterio y aventura en la Barcelona de la posguerra.', 'Novela', 'Misterio', '2001', '978-8408139484', 1, NULL, 'libro9.png', 'la_sombra.pdf', 10),
(4, '1984', 'George Orwell', '1984 es una novela distópica escrita por George Orwell en 1949, que describe un futuro sombrío y totalitario bajo el control del Gran Hermano y el Partido', 'Ciencia ficción', 'Distopía', '1949', '978-0451524935', 1, NULL, 'libro_684f0eed60c25.jpg', '1984.pdf', 10),
(5, 'El principito', 'Antoine de Saint-Exupéry', 'Fábula filosófica sobre la inocencia y la naturaleza humana.', 'Fábula', 'Infantil', '1943', '978-0156012195', 1, NULL, 'libro_684f0ea59fda6.png', 'el_principito.pdf', 10),
(6, 'Ficciones', 'Jorge Luis Borges', 'Colección de relatos cortos con elementos fantásticos y filosóficos.', 'Relatos', 'Fantasía', '1944', '978-8420639805', 1, NULL, 'libro7.png', 'ficciones.pdf', 10),
(8, 'Breve historia del tiempo', 'Stephen Hawking', NULL, NULL, NULL, '0000', NULL, 1, 3, 'libro3.png', '8.pdf', 10),
(9, 'El arte de la guerra', 'Sun Tzu', NULL, NULL, NULL, '0000', NULL, 1, 4, 'libro5.png', 'arte_guerra.pdf', 10),
(10, 'La historia de México', 'Autor Desconocido', NULL, NULL, NULL, '0000', NULL, 1, 4, 'libro10.png', 'mexico.pdf', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pdf_prestamos`
--

CREATE TABLE `pdf_prestamos` (
  `id` int(11) NOT NULL,
  `prestamo_id` int(11) NOT NULL,
  `contraseña_pdf` varchar(255) NOT NULL,
  `archivo_pdf` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_prestamo` date NOT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado','devuelto') NOT NULL DEFAULT 'pendiente',
  `archivo_pdf` varchar(255) DEFAULT NULL,
  `password_pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id`, `libro_id`, `usuario_id`, `fecha_prestamo`, `fecha_devolucion`, `estado`, `archivo_pdf`, `password_pdf`) VALUES
(1, 1, 1, '2025-05-01', '2025-05-08', 'aprobado', NULL, NULL),
(2, 2, 2, '2025-05-03', NULL, 'aprobado', NULL, NULL),
(3, 3, 3, '2025-05-05', '2025-05-12', 'pendiente', NULL, NULL),
(4, 4, 4, '2025-05-10', NULL, 'aprobado', NULL, NULL),
(5, 5, 1, '2025-05-15', '2025-05-22', 'devuelto', NULL, NULL),
(6, 6, 5, '2025-05-20', NULL, 'pendiente', NULL, NULL),
(7, 4, 3, '2025-06-02', '2025-06-16', 'aprobado', NULL, NULL),
(8, 8, 4, '2025-06-02', '2025-06-16', 'aprobado', NULL, '$2y$10$ABPdKI.9Uet4b./SHqxOduxjEaJEablRo6C1n/7fguSZlFw5xVwuG'),
(9, 1, 3, '2025-06-02', '2025-06-16', 'pendiente', NULL, NULL),
(10, 5, 4, '2025-06-02', '2025-06-16', 'aprobado', NULL, '$2y$10$FerxbtW.W5jGxRcH17zNs.f4qMTiK2YfoMwMxRWueSe7vPECM6xp.'),
(11, 4, 4, '2025-06-02', '2025-06-16', 'aprobado', NULL, '$2y$10$x7F8fBA3tIkOSO5I7ZnNmu11AtmfRsGsJoarUeHFAEWyMCAp/f1Ba'),
(12, 3, 4, '2025-06-02', '2025-06-16', 'pendiente', NULL, '$2y$10$x2.njBSvPtmW6x51FQoerusY8mj0GUAaD4cJXAIez2.cBa5h7F9oy'),
(13, 9, 4, '2025-06-02', '2025-06-16', 'aprobado', NULL, '$2y$10$WCmikvcdSbPbmGQpQ427qupGPhB/hQt0bgMT5nYlsLWBYZ1.PAkke');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `restablecimientos`
--

CREATE TABLE `restablecimientos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado` enum('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
  `fecha_solicitud` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_recuperacion`
--

CREATE TABLE `solicitudes_recuperacion` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado` enum('pendiente','rechazada','completada') DEFAULT 'pendiente',
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_recuperacion`
--

INSERT INTO `solicitudes_recuperacion` (`id`, `usuario_id`, `estado`, `fecha`) VALUES
(1, 9, '', '2025-06-12 17:39:58'),
(2, 9, '', '2025-06-12 18:32:30'),
(3, 9, 'pendiente', '2025-06-12 18:34:04'),
(4, 4, 'pendiente', '2025-06-12 18:52:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_recuperacion`
--

CREATE TABLE `tokens_recuperacion` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiracion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','bibliotecario','usuario') NOT NULL DEFAULT 'usuario',
  `ultimo_acceso` datetime DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('Masculino','Femenino','Otro') DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `password_pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `telefono`, `password`, `rol`, `ultimo_acceso`, `direccion`, `fecha_nacimiento`, `genero`, `foto_perfil`, `password_pdf`) VALUES
(1, 'Ana García', 'ana.garcia@email.com', '555-1234', '123456', 'usuario', '2025-05-25 10:00:00', NULL, NULL, NULL, NULL, NULL),
(2, 'Esteban Alejandro', 'estebancito@gmail.com', '555-5678', '87654321', 'usuario', '2025-05-30 15:30:00', NULL, NULL, NULL, NULL, NULL),
(3, 'Luis Marino', 'luis@gmail.com', '555-8765', '11111111', 'admin', '2025-05-31 22:14:02', 'Alfonso Ugarte 120', '2006-03-20', 'Masculino', 'uploads/perfil_3.jpg', '1234567890'),
(4, 'Parker', 'parker@gmail.com', '555-4321', 'parkergay', 'usuario', '2025-05-29 09:45:00', NULL, NULL, NULL, 'uploads/perfil_4.jpeg', '123456789'),
(5, 'Laura Fernández', 'laura.fernandez@email.com', '555-2345', '333333', 'usuario', '2025-05-27 14:20:00', NULL, NULL, NULL, NULL, NULL),
(6, 'David Gómez', 'david.gomez@email.com', '555-3456', '12344444', 'usuario', '2025-05-26 11:10:00', NULL, NULL, NULL, NULL, NULL),
(7, 'Sofía Ramírez', 'sofia.ramirez@email.com', '555-4567', '555555', 'usuario', '2025-05-30 08:00:00', NULL, NULL, NULL, NULL, NULL),
(8, 'Miguel Torres', 'miguel.torres@email.com', '555-5679', '666666', 'usuario', '2025-05-25 17:25:00', NULL, NULL, NULL, NULL, NULL),
(9, 'Luis Solorzano', 'solorzanoluismarino@gmail.com', NULL, 'luismarino2006', 'usuario', NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos_usuario`
--
ALTER TABLE `accesos_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `historial_restables`
--
ALTER TABLE `historial_restables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `fk_categoria` (`categoria_id`);

--
-- Indices de la tabla `pdf_prestamos`
--
ALTER TABLE `pdf_prestamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prestamo_id` (`prestamo_id`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `fk_libro_id` (`libro_id`);

--
-- Indices de la tabla `restablecimientos`
--
ALTER TABLE `restablecimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `solicitudes_recuperacion`
--
ALTER TABLE `solicitudes_recuperacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos_usuario`
--
ALTER TABLE `accesos_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historial_restables`
--
ALTER TABLE `historial_restables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `pdf_prestamos`
--
ALTER TABLE `pdf_prestamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `restablecimientos`
--
ALTER TABLE `restablecimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `solicitudes_recuperacion`
--
ALTER TABLE `solicitudes_recuperacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesos_usuario`
--
ALTER TABLE `accesos_usuario`
  ADD CONSTRAINT `accesos_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial_restables`
--
ALTER TABLE `historial_restables`
  ADD CONSTRAINT `historial_restables_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `libros`
--
ALTER TABLE `libros`
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pdf_prestamos`
--
ALTER TABLE `pdf_prestamos`
  ADD CONSTRAINT `pdf_prestamos_ibfk_1` FOREIGN KEY (`prestamo_id`) REFERENCES `prestamos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `fk_libro_id` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `restablecimientos`
--
ALTER TABLE `restablecimientos`
  ADD CONSTRAINT `restablecimientos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes_recuperacion`
--
ALTER TABLE `solicitudes_recuperacion`
  ADD CONSTRAINT `solicitudes_recuperacion_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD CONSTRAINT `tokens_recuperacion_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
