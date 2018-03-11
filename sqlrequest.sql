INSERT INTO `gym_accessright` (`id`, `controller`, `action`, `menu`, `menu_icon`, `menu_title`, `member`, `staff_member`, `accountant`, `page_link`) VALUES ('24', 'Dashboard', 'map', 'map', 'map.png', 'Map', '1', '1', '1', '/gym/dashboard/map');

02/22
ALTER TABLE `class_schedule` ADD `color` VARCHAR(20) NOT NULL AFTER `club_name`;


03/11
ALTER TABLE `gym_reservation` ADD `color` VARCHAR(255) NOT NULL DEFAULT '#121212' AFTER `created_date`; 
