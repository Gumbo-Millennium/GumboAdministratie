INSERT INTO `groep` (`id`, `naam`, `type`, `email`, `omschrijving`) VALUES
(1, 'Bestuur', '-', NULL, NULL),
(2, 'Plaza Commissie', 'commissie', NULL, NULL),
(3, 'Activiteiten Commissie', 'commissie', NULL, NULL),
(4, 'Digitale Commissie', 'commissie', NULL, NULL),
(5, 'OpendagPG', 'projectgroep', NULL, NULL),
(6, 'Bras Commissie', 'commissie', 'bc@gumbo-millennium.nl', NULL);

INSERT INTO `lidstatus` (`id`, `naam`) VALUES
(1, 'Lid'),
(2, 'Oud-lid'),
(3, 'Ere-lid'),
(4, 'Begunstiger'),
(5, 'A-lid'),
(6, 'Ex-Lid'),
(7, 'Ex-begunstiger'),
(8, 'Onbekend // Onbetaald');

INSERT INTO `persoon` (`id`, `voornaam`, `achternaam`, `email`, `wachtwoord`, `lid_sinds`) VALUES
(1, 'Admin', 'Gumbo', 'admin@gumbo.nl', '$2a$10$lfJrk/3V9VumqPT21DV6HOxe5gIq0xDDVJuNwRr.TevazMWRAPJ6i', '2017-01-01'),

INSERT INTO `persoon_groep` (`persoon_id`, `groep_id`, `rol_id`) VALUES
(1, 1, 1),

INSERT INTO `persoon_lidstatus` (`id`, `persoon_id`, `jaar`, `lidstatus_id`) VALUES
(1, 1, '2016', 1),

INSERT INTO `rol` (`id`, `naam`) VALUES
(1, 'Voorzitter'),
(2, 'Vice-voorzitter'),
(3, 'Secretaris'),
(4, 'Penningmeester'),
(5, 'Commissaris'),
(6, 'Algemeen lid');