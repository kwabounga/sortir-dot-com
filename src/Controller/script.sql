INSERT INTO 
app_user 
( 
	id, 
	campus_id, 
	role_id, 
	username, 
	mail, 
	password, 
	firstname, 
	lastname, 
	phone, 
	date_created
)
VALUES
(5, 1, 2, 'kro', 'kro@nenbourg.com', '$2y$13$oDjs/pSVL7enPa6/pVBn5OWCVfZCNParzhBbJ8PPI1oOOAKB3JLUG', 'kro', 'nenbourg', '0000000001', '2020-11-03 10:00:00'),
(6, 1, 2, 'affli', 'affli@gem.com', '$2y$13$/74WeiUgPdQn87Uil/HdAOxfHNXcjy9oOmPT2900Rj9zc6BZObpcq' , 'affli', 'gem', '000000002', '2020-11-03 10:00:00'),
(7, 1, 2, 'lef', 'lef@fe.com', '$2y$13$JFlHkJtIkiOIL5bqhKN0KOCo15JJZ9pBcHAHmwnjWWL8I1at0kSWG' , 'lef', 'fe', '000000003', '2020-11-03 10:00:00')
;

INSERT INTO 
lieu
(
	id, 
	ville_id,
	nom,
	rue
)
VALUES
(1, 20, 'berlin', '95 Boulevard Gabriel Lauriol')
;

INSERT INTO
sortie
(
	id,
	etat_id,
	campus_id,
	organisateur_id,
	lieu_id,
	nom,
	debut,
	duree,
	limite_inscription,
	inscription_max,
	infos
)
VALUES
(1, 3, 1, 6, 1, 'berlin', '2020-11-10 20:00:00', '05:00:00', '2020-11-01 20:00:00', 10, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nec blandit metus, ac molestie purus. Praesent faucibus non sem vitae faucibus. Aenean non porta libero. Duis efficitur mi sit amet lectus gravida dapibus. Suspendisse mollis velit sit amet euismod ullamcorper. Mauris sollicitudin felis sed orci lacinia blandit. Curabitur in tincidunt dui, vel imperdiet mauris. Donec at diam risus. Suspendisse ac condimentum est. Proin ut mauris in tortor commodo eleifend sit amet at leo. Praesent enim tellus, venenatis ut elit eget, bibendum dignissim magna. Maecenas a rhoncus quam. Ut scelerisque elementum ipsum, id porttitor sapien. Suspendisse nec velit non nibh elementum faucibus. Praesent fermentum bibendum imperdiet.Donec imperdiet eros sed velit volutpat, at commodo eros rutrum. Cras non diam enim. Etiam orci diam, fermentum eu euismod vel, scelerisque id nisl. Cras volutpat, nulla nec porttitor fringilla, neque orci gravida erat, sit amet finibus elit neque id dolor. Vestibulum mattis consectetur vulputate.'),
(2, 2, 1, 5, 1, 'berlin1', '2020-11-26 20:00:00', '05:00:00', '2020-11-25 20:00:00', 10, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nec blandit metus, ac molestie purus. Praesent faucibus non sem vitae faucibus. Aenean non porta libero. Duis efficitur mi sit amet lectus gravida dapibus. Suspendisse mollis velit sit amet euismod ullamcorper. Mauris sollicitudin felis sed orci lacinia blandit. Curabitur in tincidunt dui, vel imperdiet mauris. Donec at diam risus. Suspendisse ac condimentum est. Proin ut mauris in tortor commodo eleifend sit amet at leo. Praesent enim tellus, venenatis ut elit eget, bibendum dignissim magna. Maecenas a rhoncus quam. Ut scelerisque elementum ipsum, id porttitor sapien. Suspendisse nec velit non nibh elementum faucibus. Praesent fermentum bibendum imperdiet.Donec imperdiet eros sed velit volutpat, at commodo eros rutrum. Cras non diam enim. Etiam orci diam, fermentum eu euismod vel, scelerisque id nisl. Cras volutpat, nulla nec porttitor fringilla, neque orci gravida erat, sit amet finibus elit neque id dolor. Vestibulum mattis consectetur vulputate.'),
(3, 5, 1, 7, 1, 'berlin2', '2020-11-02 20:00:00', '05:00:00', '2020-10-30 20:00:00', 10, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nec blandit metus, ac molestie purus. Praesent faucibus non sem vitae faucibus. Aenean non porta libero. Duis efficitur mi sit amet lectus gravida dapibus. Suspendisse mollis velit sit amet euismod ullamcorper. Mauris sollicitudin felis sed orci lacinia blandit. Curabitur in tincidunt dui, vel imperdiet mauris. Donec at diam risus. Suspendisse ac condimentum est. Proin ut mauris in tortor commodo eleifend sit amet at leo. Praesent enim tellus, venenatis ut elit eget, bibendum dignissim magna. Maecenas a rhoncus quam. Ut scelerisque elementum ipsum, id porttitor sapien. Suspendisse nec velit non nibh elementum faucibus. Praesent fermentum bibendum imperdiet.Donec imperdiet eros sed velit volutpat, at commodo eros rutrum. Cras non diam enim. Etiam orci diam, fermentum eu euismod vel, scelerisque id nisl. Cras volutpat, nulla nec porttitor fringilla, neque orci gravida erat, sit amet finibus elit neque id dolor. Vestibulum mattis consectetur vulputate.')
;

INSERT INTO 
sortie_user
(
	sortie_id,
	user_id
)
VALUES 
(1, 6),
(2, 5),
(3, 7),
(3, 6),
(3, 5),
(1, 5)
;





