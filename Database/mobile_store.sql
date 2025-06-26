-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-06-2025 a las 20:02:43
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
-- Base de datos: `mobile_store`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accessory_types`
--

CREATE TABLE `accessory_types` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `accessory_types`
--

INSERT INTO `accessory_types` (`id`, `name`) VALUES
(1, 'Auriculares'),
(2, 'Cargadores'),
(3, 'Fundas'),
(4, 'Cables');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `date_added`) VALUES
(178, 19, 23, 1, '2025-06-15 21:28:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Móviles', '2025-06-12 17:21:11', '2025-06-12 17:21:11'),
(2, 'Accesorios', '2025-06-12 17:21:11', '2025-06-12 17:21:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `province` varchar(30) NOT NULL,
  `locality` varchar(30) NOT NULL,
  `address` varchar(60) NOT NULL,
  `cost` decimal(5,2) NOT NULL,
  `status` enum('pending','paid','shipped','delivered','canceled') DEFAULT 'pending',
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `province`, `locality`, `address`, `cost`, `status`, `date`, `time`, `created_at`, `updated_at`) VALUES
(7, 11, 'Barcelona', 'Barcelona', 'Avenida Diagonal 123, 2º B', 159.50, 'paid', '2025-06-09', '10:15:00', '2025-06-10 17:01:16', '2025-06-10 17:01:16'),
(15, 11, 'Testing', 'Testing', 'TESTINGTESTING', 999.99, 'paid', '2025-06-14', '12:28:15', '2025-06-14 10:28:15', '2025-06-14 10:28:15'),
(16, 11, 'Testing2', 'Granada', 'Calle Cañaveral nº6', 999.99, 'paid', '2025-06-14', '12:45:23', '2025-06-14 10:45:23', '2025-06-14 10:45:23'),
(17, 11, 'aaaaaaaaaaaa', 'aaaaaaaaaaa', 'aaaaaaaaaaaaa', 809.98, 'canceled', '2025-06-14', '12:47:33', '2025-06-14 10:47:33', '2025-06-14 14:37:26'),
(18, 11, 'Granada', 'Purullena', 'Calle victor de la oliva, 1° A', 7.00, 'paid', '2025-06-15', '01:13:27', '2025-06-14 23:13:27', '2025-06-14 23:13:27'),
(19, 11, 'Granada', 'Purullena', 'Calle victor de la oliva, 1° A', 10.00, 'paid', '2025-06-15', '01:18:38', '2025-06-14 23:18:38', '2025-06-14 23:18:38'),
(20, 11, 'Testing', 'Testing', 'MegaTesting', 30.00, 'delivered', '2025-06-15', '11:51:01', '2025-06-15 09:51:01', '2025-06-15 09:51:41'),
(21, 16, 'Guadix', 'Guadix', 'Guadix@calledemicasa', 10.00, 'paid', '2025-06-15', '18:52:56', '2025-06-15 16:52:56', '2025-06-15 16:52:56'),
(29, 11, 'French', 'Frenches', 'OuiouiCroissonty', 919.98, 'paid', '2025-06-16', '09:25:05', '2025-06-16 07:25:05', '2025-06-16 07:25:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(1, 15, 5, 1),
(2, 16, 2, 1),
(3, 17, 4, 1),
(4, 17, 11, 1),
(5, 18, 19, 1),
(6, 19, 20, 1),
(7, 20, 20, 3),
(8, 21, 20, 1),
(16, 29, 19, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `accessory_type_id` int(11) DEFAULT NULL,
  `name` varchar(25) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` decimal(5,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `category_id`, `accessory_type_id`, `name`, `description`, `price`, `stock`, `image`, `created_at`, `updated_at`) VALUES
(2, 1, NULL, 'Samsung Galaxy A56', 'Inteligencia Alucinante para una experiencia única. El Galaxy A56 5G incorpora Inteligencia Alucinante para llevar tu experiencia tecnológica al siguiente nivel.', 362.17, 7, 'product_684f0ecfe1cc8.webp', '2025-05-28 17:27:50', '2025-06-16 07:26:26'),
(4, 1, NULL, 'Google Pixel 8 Pro', 'Con la IA de Google y la mejor Cámara Pixel, Google Pixel 8 Pro es el Pixel más potente y personal hasta la fecha. Con una nueva cámara que ridiculiza los Iphone', 349.99, 18, 'product_684f0e958fe69.webp', '2025-05-28 17:27:50', '2025-06-16 07:26:19'),
(5, 1, NULL, 'Xiaomi 13T Negro', 'Xiaomi 13T: La esencia de tu obra maestra. Más potente de lo que puedas imaginar, mejora la experiencia de imágenes insignia. No hay mejor opción calidad-precio', 370.97, 9, 'product_684f0e6710a24.webp', '2025-05-28 17:27:50', '2025-06-15 21:34:47'),
(6, 2, 2, 'Cargador Inalámbrico', 'El cargador MagSafe inalámbrico incorpora imanes perfectamente alineados para fijarse al instante a tu iPhone, proporciona una carga rápida', 75.99, 25, 'product_684f0e2401330.webp', '2025-05-28 17:27:50', '2025-06-15 21:11:10'),
(7, 2, 1, 'Auriculares 6 Play Gris', 'Auriculares over-ear con la mejor cancelación de ruido del mercado. Resistentes al agua, son capaces de soportar presiones de hasta 15 metros sumerjidos', 12.99, 18, 'product_684f0d943025f.webp', '2025-05-28 17:27:50', '2025-06-15 21:10:44'),
(8, 1, 2, 'OPPO Reno13 FS', 'Disfruta de una experiencia visual envolvente con OPPO Reno13 FS 5G y su pantalla de 6,67. Su cámara innovadora puede rivalizar con las más altas gamas', 349.99, 27, 'product_684f0d23e942a.webp', '2025-05-28 17:27:50', '2025-06-15 21:34:19'),
(9, 2, 1, 'Auriculares Inalámbricos', 'No pesan nada y ofrecen un ajuste anatómico. Se colocan en el ángulo perfecto para darte un mayor confort y su sonido estéreo proporciona una sensación sonora única.', 14.99, 37, 'product_684f0ccd55040.webp', '2025-05-28 17:27:50', '2025-06-15 21:09:57'),
(10, 2, 3, 'Funda Apple iPhone 16', 'Protege y complementa tu iPhone 16 con la funda de silicona MagSafe de Apple color azul cielo. Resistente a golpes contundentes y arañazos.', 24.99, 50, 'product_684f0c6e3531b.webp', '2025-05-28 17:27:50', '2025-06-15 21:09:09'),
(11, 2, 2, 'Cargador Carga Rápida', 'Más potente que los adaptadores de viaje anteriores, aprovecha el poder de Super Fast Charging 2.0, un nuevo hardware que sobrepasa los límites de la carga rápida', 19.99, 30, 'product_684f0bee3e7d6.webp', '2025-05-28 17:27:50', '2025-06-15 18:07:42'),
(12, 2, 4, 'PcCom Essential Cable USB', 'Accesorio indispensable para tu portátil, ordenador, tablet o Smartphone con salida USB-C para conectarlos a una TV o monitor con HDMI', 19.99, 100, 'product_684f0b10812ad.webp', '2025-05-28 17:27:50', '2025-06-15 18:04:00'),
(18, 1, NULL, 'Samsung Galaxy A05s', 'El nuevo Galaxy A05s hereda el cuidado diseño de la Serie A para que puedas tener un dispositivo móvil acorde a tu estilo. Este nuevo smartphone está fabricado con materiales de alta calidad y se presenta con un actualizado SO.', 129.99, 4, 'product_684f0a7a664b7.webp', '2025-06-14 15:44:08', '2025-06-15 18:01:30'),
(19, 1, NULL, 'iPhone 16 Rosa', 'Lo principal. iOS 18. Personal en cada detalle. Presentamos el iPhone 16. Por si fuera poco, en rosa. Innovación, funcionalidad, tecnología y chicle son adjetivos perfectos para este dispositivo', 359.99, 3, 'product_684f0969ab0bd.webp', '2025-06-14 17:32:42', '2025-06-16 07:26:35'),
(20, 1, NULL, 'Xiaomi Redmi 13C', 'Los elegantes bordes rectos y una sección redondeada natural entre la parte delantera y trasera brindan una sensación de calidad absoluta, y el diseño triangular del flash y las lentes es simple y limpio.', 120.97, 17, 'product_684f0907c770c.webp', '2025-06-14 21:16:59', '2025-06-15 21:36:17'),
(23, 1, NULL, 'Xiaomi Redmi Note 14 Pro', 'Diseñado para ofrecer estética y comodidad. El nuevo Redmi Note 14 Pro 4G cuenta con una elegante curvatura para una apariencia fresca y vibrante.', 249.98, 8, 'product_684f08aa3c820.webp', '2025-06-15 16:56:12', '2025-06-15 17:53:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `surnames` varchar(40) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `role` enum('client','admin') NOT NULL DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `surnames`, `email`, `password`, `role`) VALUES
(11, 'Antonio', 'Cruz Garcia', 'acg.purullena@gmail.com', '$2y$10$J/0SdlqlMF.EPoy4mBhlyepmDORZBC2rsVzlgoQx0uQZtoYQXJeQ.', 'admin'),
(16, 'Víctor', 'Baca Martínez', 'victinixd11@gmail.com', '$2y$10$eAI4dtSMOSxFrZe1S0yxJewwF1elwCJbrDfdCmf/kMWGhU/kORdSm', 'client'),
(19, 'Rafa', 'Cruz', 'Rafacrulop@gmail.com', '$2y$10$Y/YKH.Ht7taMYVvo3TuIXuJF2vHSgPMmM.0a4siyQIQl3eHFxzu/y', 'client');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accessory_types`
--
ALTER TABLE `accessory_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `cart_ibfk_1` (`user_id`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_ibfk_1` (`user_id`);

--
-- Indices de la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_items_ibfk_1` (`order_id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `accessory_type_id` (`accessory_type_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accessory_types`
--
ALTER TABLE `accessory_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`accessory_type_id`) REFERENCES `accessory_types` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
