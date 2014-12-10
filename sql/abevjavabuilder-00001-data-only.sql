INSERT INTO `package` (`paid`, `short_name`, `company_id`, `category_id`, `description`) VALUES
(1, 'abevjava-resource', '1', '2147483647', 'Erőforrásfájl-gyűjtemény az Általános Nyomtatványkitöltő (ÁNYK - AbevJava) programhoz.');

INSERT INTO `download` (`doid`, `company_id`, `package_id`, `category_id`, `version_major`, `version_minor`, `version_micro`, `version_build`, `url_1`, `filename_1`, `url_2`, `filename_2`, `url_3`, `filename_3`) VALUES
(NULL, '1', '1', '2147483647', '1', '0', '0', '0', 'http://ooop.itc.hu/', 'abevjava_resource.jar', NULL, NULL, NULL, NULL);
