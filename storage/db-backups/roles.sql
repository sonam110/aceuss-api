INSERT INTO `roles` (`id`, `top_most_parent_id`, `user_type_id`, `name`, `guard_name`, `se_name`, `is_default`, `entry_mode`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 'Admin', 'api', 'Super Admin', 0, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-18 11:39:31'),
(2, NULL, 2, 'Company', 'api', 'Company', 0, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-18 11:37:08'),
(3, NULL, 3, 'Employee', 'api', 'Employee', 1, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-03 12:40:05'),
(4, NULL, 4, 'Hospital', 'api', 'Hospital', 1, NULL, '2022-09-29 16:01:37', '2022-09-29 16:01:37'),
(5, NULL, 5, 'Nuser', 'api', 'Nuser', 1, NULL, '2022-09-29 16:01:37', '2022-09-29 16:01:37'),
(6, NULL, 6, 'Patient', 'api', 'Patient', 1, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-03 12:40:16'),
(7, NULL, 7, 'careTaker', 'api', 'careTaker', 1, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-18 11:37:56'),
(8, NULL, 8, 'FamilyMember', 'api', 'FamilyMember', 1, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-18 11:15:58'),
(9, NULL, 9, 'ContactPerson', 'api', 'ContactPerson', 1, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-18 11:16:09'),
(10, NULL, 10, 'careTakerFamily', 'api', 'careTakerFamily', 1, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-18 11:16:21'),
(11, NULL, 11, 'Branch', 'api', 'Branch', 1, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-18 11:39:09'),
(12, NULL, 12, 'Guardian', 'api', 'Guardian', 1, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-18 13:29:13'),
(13, NULL, 13, 'Presented', 'api', 'Presented', 1, NULL, '2022-09-29 16:01:37', '2022-09-29 16:01:37'),
(14, NULL, 14, 'Participated', 'api', 'Participated', 1, NULL, '2022-09-29 16:01:37', '2022-09-29 16:01:37'),
(15, NULL, 15, 'Other', 'api', 'Other', 1, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-18 13:29:22'),
(16, NULL, 16, 'Admin Employee', 'api', 'Admin Employee', 0, 'web-0.0.1', '2022-09-29 16:01:37', '2022-10-15 11:50:52');
