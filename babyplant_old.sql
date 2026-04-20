-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: mariadb_babyplant_old
-- Tiempo de generación: 20-04-2026 a las 01:05:05
-- Versión del servidor: 10.6.23-MariaDB-ubu2204
-- Versión de PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `babyplant_old`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agenda`
--

CREATE TABLE `agenda` (
  `id_agenda` int(11) NOT NULL,
  `id_artpedido` int(11) NOT NULL,
  `fecha_agenda` date NOT NULL,
  `tipo_evento` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `completado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulospedidos`
--

CREATE TABLE `articulospedidos` (
  `id_artpedido` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_articulo` int(11) NOT NULL,
  `cant_band` int(11) NOT NULL DEFAULT 0,
  `cant_plantas` int(11) NOT NULL DEFAULT 0,
  `cant_semi` int(11) NOT NULL DEFAULT 0,
  `bandeja` varchar(5) DEFAULT NULL,
  `fecha_entrega` varchar(20) DEFAULT NULL,
  `fecha_planificacion` varchar(20) DEFAULT NULL,
  `con_semilla` int(1) NOT NULL DEFAULT 0,
  `estado` int(5) NOT NULL DEFAULT 0,
  `cod_sobre` varchar(100) DEFAULT NULL,
  `fila` int(11) DEFAULT NULL,
  `revision` int(1) DEFAULT NULL,
  `solucion` int(1) DEFAULT NULL,
  `fecha_entrega_original` varchar(20) DEFAULT NULL,
  `fecha_siembraestimada` varchar(20) DEFAULT NULL,
  `fila_siembra` int(11) DEFAULT NULL,
  `problema` int(1) DEFAULT NULL,
  `observacionproblema` varchar(255) DEFAULT NULL,
  `modoentrega` varchar(255) DEFAULT NULL,
  `cantidad_entregar` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `articulospedidos`
--

INSERT INTO `articulospedidos` (`id_artpedido`, `id_pedido`, `id_articulo`, `cant_band`, `cant_plantas`, `cant_semi`, `bandeja`, `fecha_entrega`, `fecha_planificacion`, `con_semilla`, `estado`, `cod_sobre`, `fila`, `revision`, `solucion`, `fecha_entrega_original`, `fecha_siembraestimada`, `fila_siembra`, `problema`, `observacionproblema`, `modoentrega`, `cantidad_entregar`) VALUES
(9, 3, 9, 2, 518, 576, '288', '17/05/2026', '20/04/2026', 0, 0, NULL, NULL, 1, NULL, '17/05/2026', '20/04/2026', NULL, 1, 'TIENE UN PROBLEMA', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `cuit` varchar(20) DEFAULT NULL,
  `domicilio` varchar(300) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_alta` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre`, `cuit`, `domicilio`, `telefono`, `email`, `localidad`, `provincia`, `activo`, `fecha_alta`) VALUES
(1, 'TEST', '', 'TEST', 'test', 'test@test.com', NULL, NULL, 1, '2026-04-10 19:54:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregas`
--

CREATE TABLE `entregas` (
  `id_entrega` int(11) NOT NULL,
  `id_artpedido` int(11) NOT NULL,
  `id_remito` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT 0,
  `fecha_entrega` datetime DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesadas`
--

CREATE TABLE `mesadas` (
  `id_mesada` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `capacidad` int(11) DEFAULT 0,
  `ubicacion` varchar(100) DEFAULT NULL,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_mesadas`
--

CREATE TABLE `ordenes_mesadas` (
  `id_orden_mesada` int(11) NOT NULL,
  `id_orden` int(11) NOT NULL,
  `id_mesada` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT 0,
  `fecha_asignacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_siembra`
--

CREATE TABLE `ordenes_siembra` (
  `id_orden` int(11) NOT NULL,
  `id_artpedido` int(11) NOT NULL,
  `id_orden_alternativa` int(11) DEFAULT NULL,
  `fecha_siembra` datetime DEFAULT current_timestamp(),
  `fecha_estimada` date DEFAULT NULL,
  `obsproduccion` varchar(255) DEFAULT NULL,
  `obsiembra` varchar(255) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'pendiente',
  `cant_band_reales` int(11) DEFAULT 0,
  `fecha_camara_in` datetime DEFAULT NULL,
  `fecha_mesada_in` datetime DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `obscamara` varchar(255) DEFAULT NULL,
  `problemacamara` int(4) DEFAULT NULL,
  `dataproblema` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp(),
  `concepto` varchar(200) DEFAULT NULL,
  `forma_pago` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `FECHA` date DEFAULT NULL,
  `fecha_pedido` datetime DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL,
  `fecha_real` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_cliente`, `FECHA`, `fecha_pedido`, `observaciones`, `fecha_real`) VALUES
(2, 1, '2026-04-20', '2026-04-20 00:44:56', NULL, '2026-04-20 00:44:56'),
(3, 1, '2026-04-20', '2026-04-20 00:48:05', NULL, '2026-04-20 00:48:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `nivel_acceso` varchar(20) DEFAULT 'lectura'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `id_usuario`, `modulo`, `nivel_acceso`) VALUES
(1, 1, 'todos', 'administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `problemas`
--

CREATE TABLE `problemas` (
  `id_problema` int(11) NOT NULL,
  `id_artpedido` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_problema` datetime DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'abierto',
  `solucion` text DEFAULT NULL,
  `fecha_solucion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `remitos`
--

CREATE TABLE `remitos` (
  `id_remito` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `tipo` int(11) DEFAULT 0,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock`
--

CREATE TABLE `stock` (
  `id_stock` int(11) NOT NULL,
  `id_articulo` int(11) NOT NULL,
  `cantidad_disponible` int(11) DEFAULT 0,
  `cantidad_reservada` int(11) DEFAULT 0,
  `ubicacion` varchar(100) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_bandejas`
--

CREATE TABLE `stock_bandejas` (
  `id_stock` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `cantidad_original` int(11) DEFAULT NULL,
  `fecha_stock` datetime DEFAULT NULL,
  `id_mesada` int(11) DEFAULT NULL,
  `id_variedad` int(11) NOT NULL,
  `tipo_bandeja` varchar(5) NOT NULL,
  `tipo_stock` varchar(4) DEFAULT NULL,
  `id_artpedido` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subtipos_producto`
--

CREATE TABLE `subtipos_producto` (
  `id_articulo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `eliminado` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `subtipos_producto`
--

INSERT INTO `subtipos_producto` (`id_articulo`, `nombre`, `id_tipo`, `descripcion`, `activo`, `eliminado`) VALUES
(14, 'TEST', 1, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_producto`
--

CREATE TABLE `tipos_producto` (
  `id_articulo` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `dias_en_camara` int(11) NOT NULL,
  `precio_288` decimal(10,2) DEFAULT 0.00,
  `precio_200` decimal(10,2) DEFAULT 0.00,
  `precio_162` decimal(10,2) DEFAULT 0.00,
  `precio_128` decimal(10,2) DEFAULT 0.00,
  `precio_72` decimal(10,2) DEFAULT 0.00,
  `precio_50` decimal(10,2) DEFAULT 0.00,
  `precio_25` decimal(10,2) DEFAULT 0.00,
  `precio_49` decimal(10,2) DEFAULT 0.00,
  `precio_288_s` decimal(10,2) DEFAULT 0.00,
  `precio_200_s` decimal(10,2) DEFAULT 0.00,
  `precio_162_s` decimal(10,2) DEFAULT 0.00,
  `precio_128_s` decimal(10,2) DEFAULT 0.00,
  `precio_72_s` decimal(10,2) DEFAULT 0.00,
  `precio_50_s` decimal(10,2) DEFAULT 0.00,
  `precio_25_s` decimal(10,2) DEFAULT 0.00,
  `precio_49_s` decimal(10,2) DEFAULT 0.00,
  `eliminado` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `tipos_producto`
--

INSERT INTO `tipos_producto` (`id_articulo`, `nombre`, `dias_en_camara`, `precio_288`, `precio_200`, `precio_162`, `precio_128`, `precio_72`, `precio_50`, `precio_25`, `precio_49`, `precio_288_s`, `precio_200_s`, `precio_162_s`, `precio_128_s`, `precio_72_s`, `precio_50_s`, `precio_25_s`, `precio_49_s`, `eliminado`) VALUES
(1, 'TEST', 2, 2.00, 2.00, 2.00, NULL, NULL, NULL, NULL, NULL, 2.00, 2.00, 2.00, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `usuario`, `password`, `email`, `activo`, `fecha_creacion`) VALUES
(1, 'Administrador', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@babyplant.com', 1, '2026-04-10 19:49:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `variedades_producto`
--

CREATE TABLE `variedades_producto` (
  `id_articulo` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `id_subtipo` int(11) NOT NULL,
  `eliminado` tinyint(1) DEFAULT NULL,
  `precio_288` decimal(10,2) DEFAULT NULL,
  `precio_200` decimal(10,2) DEFAULT NULL,
  `precio_162` decimal(10,2) DEFAULT NULL,
  `precio_128` decimal(10,2) DEFAULT NULL,
  `precio_72` decimal(10,2) DEFAULT NULL,
  `precio_50` decimal(10,2) DEFAULT NULL,
  `precio_25` decimal(10,2) DEFAULT NULL,
  `precio_49` decimal(10,2) DEFAULT NULL,
  `precio_288_s` decimal(10,2) DEFAULT NULL,
  `precio_200_s` decimal(10,2) DEFAULT NULL,
  `precio_162_s` decimal(10,2) DEFAULT NULL,
  `precio_128_s` decimal(10,2) DEFAULT NULL,
  `precio_72_s` decimal(10,2) DEFAULT NULL,
  `precio_50_s` decimal(10,2) DEFAULT NULL,
  `precio_25_s` decimal(10,2) DEFAULT NULL,
  `precio_49_s` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `variedades_producto`
--

INSERT INTO `variedades_producto` (`id_articulo`, `nombre`, `id_subtipo`, `eliminado`, `precio_288`, `precio_200`, `precio_162`, `precio_128`, `precio_72`, `precio_50`, `precio_25`, `precio_49`, `precio_288_s`, `precio_200_s`, `precio_162_s`, `precio_128_s`, `precio_72_s`, `precio_50_s`, `precio_25_s`, `precio_49_s`) VALUES
(9, 'TEST', 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id_agenda`),
  ADD KEY `id_artpedido` (`id_artpedido`);

--
-- Indices de la tabla `articulospedidos`
--
ALTER TABLE `articulospedidos`
  ADD PRIMARY KEY (`id_artpedido`),
  ADD KEY `idx_articulos_pedido` (`id_pedido`),
  ADD KEY `idx_articulos_articulo` (`id_articulo`),
  ADD KEY `idx_articulos_estado` (`estado`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `idx_clientes_nombre` (`nombre`);

--
-- Indices de la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD PRIMARY KEY (`id_entrega`),
  ADD KEY `id_artpedido` (`id_artpedido`),
  ADD KEY `id_remito` (`id_remito`),
  ADD KEY `idx_entregas_fecha` (`fecha_entrega`);

--
-- Indices de la tabla `mesadas`
--
ALTER TABLE `mesadas`
  ADD PRIMARY KEY (`id_mesada`);

--
-- Indices de la tabla `ordenes_mesadas`
--
ALTER TABLE `ordenes_mesadas`
  ADD PRIMARY KEY (`id_orden_mesada`),
  ADD KEY `id_orden` (`id_orden`),
  ADD KEY `id_mesada` (`id_mesada`);

--
-- Indices de la tabla `ordenes_siembra`
--
ALTER TABLE `ordenes_siembra`
  ADD PRIMARY KEY (`id_orden`),
  ADD KEY `id_artpedido` (`id_artpedido`),
  ADD KEY `idx_ordenes_siembra_fecha` (`fecha_siembra`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `idx_pedidos_cliente` (`id_cliente`),
  ADD KEY `idx_pedidos_fecha` (`fecha_pedido`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `problemas`
--
ALTER TABLE `problemas`
  ADD PRIMARY KEY (`id_problema`),
  ADD KEY `id_artpedido` (`id_artpedido`);

--
-- Indices de la tabla `remitos`
--
ALTER TABLE `remitos`
  ADD PRIMARY KEY (`id_remito`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `idx_remitos_fecha` (`fecha`);

--
-- Indices de la tabla `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id_stock`),
  ADD KEY `id_articulo` (`id_articulo`);

--
-- Indices de la tabla `stock_bandejas`
--
ALTER TABLE `stock_bandejas`
  ADD PRIMARY KEY (`id_stock`),
  ADD KEY `id_mesada` (`id_mesada`),
  ADD KEY `id_variedad` (`id_variedad`),
  ADD KEY `fk_id_artpedido_stock` (`id_artpedido`);

--
-- Indices de la tabla `subtipos_producto`
--
ALTER TABLE `subtipos_producto`
  ADD PRIMARY KEY (`id_articulo`),
  ADD KEY `subtipos_producto_ibfk_1` (`id_tipo`);

--
-- Indices de la tabla `tipos_producto`
--
ALTER TABLE `tipos_producto`
  ADD PRIMARY KEY (`id_articulo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `variedades_producto`
--
ALTER TABLE `variedades_producto`
  ADD PRIMARY KEY (`id_articulo`),
  ADD KEY `id_subtipo` (`id_subtipo`),
  ADD KEY `idx_variedades_nombre` (`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id_agenda` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `articulospedidos`
--
ALTER TABLE `articulospedidos`
  MODIFY `id_artpedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `entregas`
--
ALTER TABLE `entregas`
  MODIFY `id_entrega` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mesadas`
--
ALTER TABLE `mesadas`
  MODIFY `id_mesada` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ordenes_mesadas`
--
ALTER TABLE `ordenes_mesadas`
  MODIFY `id_orden_mesada` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ordenes_siembra`
--
ALTER TABLE `ordenes_siembra`
  MODIFY `id_orden` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `problemas`
--
ALTER TABLE `problemas`
  MODIFY `id_problema` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `remitos`
--
ALTER TABLE `remitos`
  MODIFY `id_remito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `stock`
--
ALTER TABLE `stock`
  MODIFY `id_stock` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `stock_bandejas`
--
ALTER TABLE `stock_bandejas`
  MODIFY `id_stock` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT de la tabla `subtipos_producto`
--
ALTER TABLE `subtipos_producto`
  MODIFY `id_articulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `tipos_producto`
--
ALTER TABLE `tipos_producto`
  MODIFY `id_articulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `variedades_producto`
--
ALTER TABLE `variedades_producto`
  MODIFY `id_articulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `agenda`
--
ALTER TABLE `agenda`
  ADD CONSTRAINT `agenda_ibfk_1` FOREIGN KEY (`id_artpedido`) REFERENCES `articulospedidos` (`id_artpedido`);

--
-- Filtros para la tabla `articulospedidos`
--
ALTER TABLE `articulospedidos`
  ADD CONSTRAINT `articulospedidos_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `articulospedidos_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `variedades_producto` (`id_articulo`);

--
-- Filtros para la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD CONSTRAINT `entregas_ibfk_1` FOREIGN KEY (`id_artpedido`) REFERENCES `articulospedidos` (`id_artpedido`),
  ADD CONSTRAINT `entregas_ibfk_2` FOREIGN KEY (`id_remito`) REFERENCES `remitos` (`id_remito`);

--
-- Filtros para la tabla `ordenes_mesadas`
--
ALTER TABLE `ordenes_mesadas`
  ADD CONSTRAINT `ordenes_mesadas_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes_siembra` (`id_orden`),
  ADD CONSTRAINT `ordenes_mesadas_ibfk_2` FOREIGN KEY (`id_mesada`) REFERENCES `mesadas` (`id_mesada`);

--
-- Filtros para la tabla `ordenes_siembra`
--
ALTER TABLE `ordenes_siembra`
  ADD CONSTRAINT `ordenes_siembra_ibfk_1` FOREIGN KEY (`id_artpedido`) REFERENCES `articulospedidos` (`id_artpedido`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `problemas`
--
ALTER TABLE `problemas`
  ADD CONSTRAINT `problemas_ibfk_1` FOREIGN KEY (`id_artpedido`) REFERENCES `articulospedidos` (`id_artpedido`);

--
-- Filtros para la tabla `remitos`
--
ALTER TABLE `remitos`
  ADD CONSTRAINT `remitos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Filtros para la tabla `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`id_articulo`) REFERENCES `variedades_producto` (`id_articulo`);

--
-- Filtros para la tabla `stock_bandejas`
--
ALTER TABLE `stock_bandejas`
  ADD CONSTRAINT `fk_id_artpedido_stock` FOREIGN KEY (`id_artpedido`) REFERENCES `articulospedidos` (`id_artpedido`),
  ADD CONSTRAINT `stock_bandejas_ibfk_1` FOREIGN KEY (`id_mesada`) REFERENCES `mesadas` (`id_mesada`),
  ADD CONSTRAINT `stock_bandejas_ibfk_2` FOREIGN KEY (`id_variedad`) REFERENCES `variedades_producto` (`id_articulo`);

--
-- Filtros para la tabla `variedades_producto`
--
ALTER TABLE `variedades_producto`
  ADD CONSTRAINT `variedades_producto_ibfk_1` FOREIGN KEY (`id_subtipo`) REFERENCES `subtipos_producto` (`id_articulo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
