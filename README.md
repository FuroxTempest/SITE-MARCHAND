Ce repository contient le projet ALIZON.
Ce projet avait pour but de mettre en place un site de ventes de produits bretons de toute sorte.
Ce site est séparé en trois dossiers, le dossier contenant les pages "vendeur" pour les différents vendeurs qui vont pouvoir déposer leurs produits sur le site. Les pages "client" qui vont contenir un catalogue de produits, une gestion de compte du client concerné, une gestion de son panier , de son mot de passe ,etc..., bref, toutes les fonctions qu'un client peut avoir sur un site marchands. Et enfin les pages "admin" qui vont contenir tous les éléments de gestion des clients, des fournisseurs, des produits etc... .
Afin de fonctionner, ce site est en lien avec une base de données.

Ce projet conséquent a été réalisé en collaboration avec 6 autres étudiant de l'IUT.
Pour ma part, j'ai beaucoup travaillé sur la partie Back-End des pages "client", on retrouve notamment : 
- la gestion du panier du client en fonction de si l'on est connecté ou non, ce qui équivaut à l'ajout de produit au panier, la conservation du panier lors de la connexion du client à son compte, la mise à jour de la base de données en conséquences, l'affichage des produits du panier cela concernes les pages "client" : détail_produit.php , panier.php, finaliser_panier_client.php et finaliser_panier_visiteur.php.
  
- la gestion du paiement des commandes via un algorithme de validation de carte bancaire, cela concerne la page "client" : confirmations_cb.php

- J'ai mis en place 1 simulateur vendeur et un client qui permettait, via l'utilisation d'Ajax de pouvoir voir l'évolution de ses commandes en direct, cela concerne les pages "client" : commande.php et ajax_client.php, ainsi que les pages "vendeur" liste_commande.php et ajax_vendeur.php

- Enfin, j'ai travaillé sur la création du schéma SQL de la base de données ainsi que de sa mise en place, vous pouvez la retrouver dans le dossier "bdd" , bdd.sql

Egalement, par manque de temps, certaines pages ne sont pas commentés.
  
