SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `riovillanuevo` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `riovillanuevo`;

CREATE TABLE `admin` (
  `username` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `type` enum('Front-desk','Admin') NOT NULL DEFAULT 'Front-desk',
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `admin` (`username`, `password`, `type`, `firstName`, `lastName`, `status`) VALUES
('bienbobis', '$2y$10$gghrXdOLZkkY1YuFxCPmkuQ/XqJ8GpAQDYEYXU.MU4T88hf5g1S7G', 'Front-desk', 'Bien', 'Bobis', 1),
('irislipata', '$2y$10$gghrXdOLZkkY1YuFxCPmkuQ/XqJ8GpAQDYEYXU.MU4T88hf5g1S7G', 'Admin', 'Iris', 'Lipata', 1),
('mannyyoung', '$2y$10$vjCQHri..VdKurV9OmcchOZPR39hd4kV2QsmqpkOzuS6ijo9eFQz.', 'Admin', 'Manny', 'Young', 1),
('mayfatalla', '$2y$10$ethblSAPp.Hlnus1Sh..uOS4KOrf5rrleio6OGjCsMLs4KKConcKS', 'Front-desk', 'may', 'fatalla', 1),
('micadelmonte', '$2y$10$gghrXdOLZkkY1YuFxCPmkuQ/XqJ8GpAQDYEYXU.MU4T88hf5g1S7G', 'Front-desk', 'Micaela', 'Delmonte', 1);

CREATE TABLE `amenities` (
  `amenityID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `rate` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `forgot_password` (
  `emailAddress` varchar(256) NOT NULL,
  `token` varchar(256) NOT NULL,
  `used` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `guest` (
  `emailAddress` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `contactNumber` varchar(11) NOT NULL,
  `address` text NOT NULL,
  `verified` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `guest` (`emailAddress`, `password`, `firstName`, `lastName`, `contactNumber`, `address`, `verified`, `status`) VALUES
('asd@gmail.com', '$2y$10$/OsDYJ.xzmDeO9jnhtxq7uUVSJlmf8GZyqoVgqGbGoZXUVePA0num', 'asd', 'asd', 'asd', 'asd', 1, 1),
('eladelmonte@gmail.com', '$2y$10$Cju90tgrY/fYsi4KjomW.eZQ64Hfa1MiWha0/s6RQxghOtaE9hwkK', 'Mica', 'Delmonte', '4178909', 'B4 L10 Hyacinth St. F & E De Castro Village, Aniban V', 1, 1),
('iris.lipata@gmail.com', '$2y$10$gghrXdOLZkkY1YuFxCPmkuQ/XqJ8GpAQDYEYXU.MU4T88hf5g1S7G', 'Miracris', 'Lipata', '09970762891', 'marikina', 1, 1),
('lipata.mirabeth@gmail.com', '$2y$10$yH/Yur11kUB6kcTcz.SKkOA7OR8qv5KKFAgXtCBw4EEwkx1L5NE9.', 'ira', 'lipata', '09970762891', 'marikina city', 0, 1),
('youngskymann@gmail.com', '$2y$10$ZM0/aLseZuhBeYW8uehLYuuCeMhgiA.MTtnBLiHm/C9BIYKbPi9gy', 'Manny', 'Young', '2147483647', '953 wagas st tondo manila, 953', 1, 1),
('youngskymann@gmail.commn', '$2y$10$CK3hpf/uLpTHpC92LOJR7uxDIbCQl0QQyJePOkuSmd93XIv8c0aia', 'Manny', 'Young', '9772373397', '953 wagas st tondo manila, 953', 1, 1),
('youngskymannn@gmail.com', '$2y$10$120WvuPao00/JwGdcr.ExezsyaIHsgP/c.e9lFS0DfHzx2T8ZKKMS', 'Manny', 'Young', '9772373397', '953 wagas st tondo manila, 953', 1, 1);

CREATE TABLE `logs` (
  `ID` int(11) NOT NULL,
  `type` enum('User','Admin') NOT NULL,
  `name` varchar(256) NOT NULL,
  `action` varchar(256) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `notification` (
  `ID` int(11) NOT NULL,
  `message` varchar(256) NOT NULL,
  `unread` tinyint(4) NOT NULL DEFAULT '1',
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reservation` (
  `reservationID` int(11) NOT NULL,
  `emailAddress` varchar(256) NOT NULL,
  `checkIn` date NOT NULL,
  `checkOut` date NOT NULL,
  `adults` int(11) NOT NULL,
  `children` int(11) NOT NULL,
  `toddlers` int(11) NOT NULL,
  `paymentMethod` enum('Cash','Bank','MoneyRemittance') NOT NULL,
  `dateCreated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reservation_bank` (
  `reservationID` int(11) NOT NULL,
  `filename` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reservation_cancelled` (
  `reservationID` int(11) NOT NULL,
  `dateCancelled` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reservation_check` (
  `reservationID` int(11) NOT NULL,
  `checkIn` datetime NOT NULL,
  `checkOut` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reservation_expense` (
  `reservationID` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reservation_room` (
  `reservationID` int(11) NOT NULL,
  `roomID` int(11) NOT NULL,
  `roomRate` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reservation_transaction` (
  `reservationID` int(11) NOT NULL,
  `payment` int(11) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `room` (
  `roomID` int(11) NOT NULL,
  `roomTypeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `room` (`roomID`, `roomTypeID`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 2),
(6, 2),
(7, 2),
(8, 3),
(9, 3),
(10, 4),
(11, 5),
(14, 5),
(15, 6),
(16, 6),
(19, 7),
(20, 7),
(21, 7),
(22, 7),
(12, 8),
(13, 8),
(17, 8),
(18, 8);

CREATE TABLE `room_types` (
  `roomTypeID` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `filename` varchar(50) DEFAULT NULL,
  `description` varchar(256) NOT NULL,
  `feature` text NOT NULL,
  `capacity` int(11) NOT NULL,
  `rate` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `room_types` (`roomTypeID`, `name`, `filename`, `description`, `capacity`, `rate`) VALUES
(1, 'Standard Room', 'Standard Room.jpg', 'A simple yet very comfy-looking single bedroom.', 2, '1200.00'),
(2, 'Standard Room 2', 'Standard Room 2.jpg', 'A simple yet very comfy-looking single bedroom with refrigerator inside.', 2, '1500.00'),
(3, 'Double-decker Room', 'Double-decker Room.jpg', 'A room that consists of 2 double-deck beds that is a perfect accommodation for 6-10 guests', 8, '3000.00'),
(4, 'Suite Room', 'Suite Room.JPG', 'Airconditioned room with a king-sized bed with vanity area.', 3, '4000.00'),
(5, 'Family Room', 'Family Room.JPG', 'A air conditioned room with a king-sized bed.', 4, '3500.00'),
(6, 'Standard Room 3', 'Standard Room 3.JPG', 'A single bed room.', 2, '3000.00'),
(7, 'Twin Room', 'Twin Room.JPG', 'A room that consists of two beds.', 4, '3000.00'),
(8, 'Family Room 2', 'Family Room 2.JPG', 'A room with a king sized bed without aircon.', 3, '3500.00');


ALTER TABLE `admin`
  ADD PRIMARY KEY (`username`);

ALTER TABLE `forgot_password`
  ADD KEY `emailAddress` (`emailAddress`);

ALTER TABLE `guest`
  ADD PRIMARY KEY (`emailAddress`);

ALTER TABLE `logs`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `notification`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservationID`),
  ADD KEY `emailAddress` (`emailAddress`);

ALTER TABLE `reservation_bank`
  ADD KEY `reservationID` (`reservationID`);

ALTER TABLE `reservation_check`
  ADD PRIMARY KEY (`reservationID`);

ALTER TABLE `reservation_expense`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservationID` (`reservationID`);

ALTER TABLE `reservation_room`
  ADD KEY `reservationID` (`reservationID`),
  ADD KEY `roomID` (`roomID`);

ALTER TABLE `reservation_transaction`
  ADD KEY `reservationID` (`reservationID`);

ALTER TABLE `room`
  ADD PRIMARY KEY (`roomID`),
  ADD KEY `roomTypeID` (`roomTypeID`);

ALTER TABLE `room_types`
  ADD PRIMARY KEY (`roomTypeID`);


ALTER TABLE `room_types`
  MODIFY `roomTypeID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `logs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notification`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `reservation`
  MODIFY `reservationID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `reservation_expense`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `forgot_password`
  ADD CONSTRAINT `forgot_password_ibfk_1` FOREIGN KEY (`emailAddress`) REFERENCES `guest` (`emailAddress`);

ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`emailAddress`) REFERENCES `guest` (`emailAddress`);

ALTER TABLE `reservation_bank`
  ADD CONSTRAINT `reservation_bank_ibfk_1` FOREIGN KEY (`reservationID`) REFERENCES `reservation` (`reservationID`);

ALTER TABLE `reservation_expense`
  ADD CONSTRAINT `reservation_expense_ibfk_1` FOREIGN KEY (`reservationID`) REFERENCES `reservation` (`reservationID`);

ALTER TABLE `reservation_room`
  ADD CONSTRAINT `reservation_room_ibfk_1` FOREIGN KEY (`reservationID`) REFERENCES `reservation` (`reservationID`),
  ADD CONSTRAINT `reservation_room_ibfk_2` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`);

ALTER TABLE `reservation_transaction`
  ADD CONSTRAINT `reservation_transaction_ibfk_1` FOREIGN KEY (`reservationID`) REFERENCES `reservation` (`reservationID`);

ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`roomTypeID`) REFERENCES `room_types` (`roomTypeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
