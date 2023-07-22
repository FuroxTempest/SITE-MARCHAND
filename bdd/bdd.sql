drop schema if exists alizonbdd cascade;
create schema alizonbdd;
set schema 'alizonbdd';


-------------------------CATEGORIE-----------------------------------
CREATE TABLE _categorie(
    id_cat SERIAL,
    nom VARCHAR(50) not null,
    nb_art_cat INTEGER not null,
    tva FLOAT not null,
    constraint Pk_categorie primary key (id_cat)
);

---------------------------QUESTION-----------------------------------
CREATE TABLE _question(
    id_quest SERIAL UNIQUE,
    quest VARCHAR(250) not null,
    constraint Pk_quest primary key (id_quest)
);

insert into _question(quest) values('Quel est le nom et prénom de votre premier amour ?');
insert into _question(quest) values('Quel est le nom de famille de votre professeur d''enfance préféré ?');
insert into _question(quest) values('Quel est le prénom de votre arrière-grand-mère maternelle ?');
insert into _question(quest) values('Dans quelle ville se sont rencontrés vos parents ?');
insert into _question(quest) values('Qu''est-ce vous vouliez devenir plus grand, lorsque vous étiez enfant ?');

--------------------------VENDEUR--------------------------------

CREATE TABLE _vendeur(
  id_vendeur SERIAL UNIQUE,
  raison_sociale VARCHAR(100) not null,
  nom VARCHAR(100) not null,
  descriptif VARCHAR(300) not null,
  mdp VARCHAR(250) not null,
  adresse_postale VARCHAR(100) not null,
  mail VARCHAR(100) not null,
  SIRET VARCHAR(15) not null,
  tva_vendeur float not null,
  id_quest Integer not null,
  reponse_vendeur VARCHAR(250) not null,
  logo VARCHAR(250) not null,
  note float not null,
  constraint Pk_vendeur primary key (id_vendeur),
  constraint Fk_vendeur_question foreign key (id_quest) references _question(id_quest)
);

---------------------------PRODUIT---------------------------------
CREATE TABLE _produit(
    id_produit SERIAL UNIQUE,
    nomProd VARCHAR(50) not null,
    prix_ttc FLOAT not null,
    descriptif VARCHAR(2000) not null,
    quantite VARCHAR(50),
    poids FLOAT,
    volume FLOAT,
    code_tarif FLOAT,
    stock INTEGER,
    promotion BOOL DEFAULT False,
    id_categorie INTEGER not null,
    id_vendeur INTEGER not null,
    constraint Pk_produit primary key (id_produit),
    constraint fk_produit_cat foreign key (id_categorie) references _categorie(id_cat),
    constraint fk_produit_vendeur foreign key (id_vendeur) references _vendeur(id_vendeur)
);


--------------------------PROMOTION----------------------------------
CREATE TABLE _promotion(
    id_promotion SERIAL,
    id_produit INTEGER not null,
    reduction FLOAT not null,
    date_debut DATE not null,
    date_fin DATE not null,
    constraint Pk_promotion primary key (id_promotion),
    constraint fk_prom_prod foreign key (id_produit) references _produit(id_produit)
);

--------------------------PROMOTION SUR CATEGORIE----------------------
CREATE TABLE _reduc_cat(
  id_reduc_cat int,
  id_promotion int,
  id_cat int,
  constraint Pk_reduc_cat primary key (id_reduc_cat, id_promotion),
  constraint fk_reduc_cat_promo foreign key (id_promotion) references _promotion(id_promotion),
  constraint fk_reduc_cat foreign key (id_cat) references _categorie(id_cat)
);

---------------------------CLIENT-----------------------------------
CREATE TABLE _client(
    id_client SERIAL UNIQUE,
    nom VARCHAR(100) not null,
    prenom VARCHAR(100) not null,
    email VARCHAR(100) not null,
    rue VARCHAR(100) not null,
    code VARCHAR(100) not null,
    ville VARCHAR(100) not null,
    telephone VARCHAR(20) not null,
    mdp VARCHAR(250) not null,
    id_quest Integer not null,
    reponse VARCHAR(250) not null,
    constraint Pk_client primary key (id_client),
    constraint Fk_client_question foreign key (id_quest) references _question(id_quest)
);

-------------------------COMMANDE------------------------------------
CREATE TABLE _commande(
    id_commande SERIAL,
    prix_final FLOAT not null,
    statut_commande VARCHAR(20) not null,
    id_client SERIAL ,
    date_commande VARCHAR(100) not null,
    constraint Pk_commande primary key (id_commande),
    constraint  Fk_commande_client foreign key (id_client) REFERENCES _client(id_client)
);


---------------------------PANIER-----------------------------------
CREATE TABLE _panier(
    id_commande SERIAL ,
    id_produit SERIAL,
    nb_article INTEGER,
    prix_total FLOAT,
    reduction_totale FLOAT,
    constraint Pk_panier primary key (id_produit,id_commande),
    constraint fk_panier_produit foreign key (id_produit) references _produit(id_produit),
    constraint fk_panier_commande foreign key (id_commande) references _commande(id_commande)
);
-------------------------LIVRAISON-----------------------------------
CREATE TABLE _livraison(
    id_livraison SERIAL,
    statut VARCHAR(20) not null,
    id_commande SERIAL UNIQUE,
    constraint Pk_livraison primary key (id_livraison),
    constraint Fk_livraison_commande foreign key (id_commande) references _commande(id_commande)
);
-------------------------DOCUMENT------------------------------------
CREATE TABLE _document(
  iddoc SERIAL,
  contenu VARCHAR(1400) not null,
  create_date DATE not null,
  author int NOT NULL,
  id_produit SERIAL UNIQUE,
  constraint Pk_document primary key (iddoc),
  constraint fk_document_author foreign key (author) REFERENCES _client(id_client),
  constraint fk_document_produit foreign key (id_produit) REFERENCES _produit(id_produit)
);
--------------------------POST---------------------------------------
CREATE TABLE _post(
  iddoc int not null primary key references _document
);
-------------------------COMMENT-------------------------------------
CREATE TABLE  _comment(
  iddoc int primary key not null references _document,
  ref_ int not null references _document,
  constraint _comment_no_auto_ref check (iddoc <> ref_)
);

/*
--------------------- TRIGGER ---------------------

===============
PLUS UTILISABLE
===============

-- TRIGGER PRIX TTC

create or replace function fct_prix_ttc()
returns trigger
as $$
declare
  _tva float;
begin
  _tva := (select tva from _categorie where NEW.id_categorie = id_cat);
  NEW.prix_ttc := NEW.prix_ht * _tva; 
  return NEW;
end;
$$ language plpgsql;

create trigger trg_prix_ttc
before insert or update
on _produit
for each row
execute procedure fct_prix_ttc();
*/

--------------------- INSERT de vendeur et produit ---------------------

Insert into alizonbdd._vendeur(raison_sociale,nom,descriptif,mdp,adresse_postale,mail,SIRET,tva_vendeur,id_quest,reponse_vendeur,logo,note) VALUES ('victorcbon','victorcbon','Une entreprise traditionelle et fière proposant des produits issus de nos région','$2y$10$MNzs1VTLwjdi1Y1ez.AL4OiyeqSwhrDvR5zvbYnru7CJn0Wq0Q7b2','22300','victorcbon.fournitures@gmail.com','26781394528379',1.24,1,'moi-meme','pkpefk',3.5);
Insert into alizonbdd._vendeur(raison_sociale,nom,descriptif,mdp,adresse_postale,mail,SIRET,tva_vendeur,id_quest,reponse_vendeur,logo,note) VALUES ('mamaJane','mamaJane','On cherche à révolutionner le marché','$2y$10$MNzs1VTLwjdi1Y1ez.AL4OiyeqSwhrDvR5zvbYnru7CJn0Wq0Q7b2','15380','startfromthebottom@outlook.fr','12345678910111',1.32,2,'aaah','UYGUG',4.5);
Insert into alizonbdd._vendeur(raison_sociale,nom,descriptif,mdp,adresse_postale,mail,SIRET,tva_vendeur,id_quest,reponse_vendeur,logo,note) VALUES ('george rillette','george rillette','Allez viens boire un petit coup à la maison','$2y$10$MNzs1VTLwjdi1Y1ez.AL4OiyeqSwhrDvR5zvbYnru7CJn0Wq0Q7b2','56240','gwenlenaymonamour@orange.fr','19753648194537',1.11,1,'margueritemavache','ghiiodjg',2.5);
Insert into alizonbdd._vendeur(raison_sociale,nom,descriptif,mdp,adresse_postale,mail,SIRET,tva_vendeur,id_quest,reponse_vendeur,logo,note) VALUES ('tristanLeMet','tristanLeMet','raffiner la cuisine de nos grands-mères à toujours été un rêve et maintenant avec la compagnie trisatan le Met c est possible','$2y$10$MNzs1VTLwjdi1Y1ez.AL4OiyeqSwhrDvR5zvbYnru7CJn0Wq0Q7b2','22300','roadto4stars@gmail.com','16744648164537',1.22,3,'voila','pjdpojpq',3.9);



Insert into alizonbdd._categorie(nom,nb_art_cat,tva) VALUES ('sucré',0,1.10);
Insert into alizonbdd._categorie(nom,nb_art_cat,tva) VALUES ('salé',0,1.20);
Insert into alizonbdd._categorie(nom,nb_art_cat,tva) VALUES ('boisson',0,1.40);
Insert into alizonbdd._categorie(nom,nb_art_cat,tva) VALUES ('artisanat',0,1.30);


insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Kouign Amann',6,400,NULL,3.99,'Spécialité emblématique du Finistère, ce gâteau repose sur une base de pâte à pain copieusement enduite de beurre demi-sel et de sucre, qui est ensuite repliée (et souvent scarifiée de sorte à caraméliser en surface). Fondante à l''intérieur, croustillante à l''extérieur et divinement feuilletée : un petit plaisir pur beurre tout bonnement irrésistible à l''heure du dessert ou au goûter.',20,1,1);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Rillettes de thon',1,150.0,NULL,2.99,'Pour un apéritif frais, en dip ou en verrine, mais aussi dans un sandwich, sur une tartine gourmande ou pour accompagner des crudités, invitez les rillettes à votre table. Préparé avec des ingrédients du quotidien, c''est la recette polyvalente, qui plaît à tous, à dégainer en toutes circonstances ! ',20,2,3);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Crêpes',12,200,NULL,3.99,'La crêpe est un mets composé d''une couche plus ou moins fine de pâte, faite à base de farine et d''œufs agglomérés à un liquide, sans levain. Elle est généralement de forme ronde. La crêpe se mange chaude ou froide, sucrée ou salée, comme plat principal ou comme dessert, mais peut aussi constituer un en-cas.',20,1,4);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Crêpes dentelles',24,200,NULL,2.99,'Une crêpe dentelle ou gavotte est une crêpe bretonne très fine, roulée sur elle-même et croustillante.',20,1,2);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Galettes saucisses',4,500,NULL,4.99,'La galette saucisse est une recette bretonne à base de galette de sarrasin et de saucisse de porc. Elle est consommée depuis le XIXème siècle, notamment lors d''évènements sportifs ou de fêtes populaires. C''est un repas de restauration rapide très simple à faire, rapide à cuisiner et peu coûteux.',20,2,4);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Rilettes de porc',1,150.0,NULL,4.99,'Ahhhhh la fameuse Rillette de Porc de la Conserverie Saint Christophe. Composées à 99% de viande et de gras de porc, elle restitue tout simplement l''onctuosité et la vigueur de ce que doit être une rillette digne de ce nom. Avec un assaisonnement simple et vivifiant, ce produit essentiel est d''un plaisir sans égal !',20,2,2);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Palets bretons',12,150,NULL,2.5,'Le palet breton ou sablé breton est un biscuit sablé et une spécialité culinaire traditionnelle emblématique de la cuisine bretonne, à base de pâte sablée.',20,1,2);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Far breton',12,400,NULL,5.99,'Le far breton est une pâtisserie et spécialité culinaire traditionnelle emblématique de la cuisine bretonne, à base d''œuf, farine, beurre demi-sel, sucre, et éventuellement de rhum, pruneaux ou raisins secs.',20,1,3);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Caramel au beurre salé',1,150.0,NULL,5.99,'Le caramel au beurre salé est une confiserie et une spécialité culinaire gastronomique traditionnelle emblématique de la cuisine bretonne, à base de sucre caramelisé, de beurre salé, et de crème fraîche.',20,2,1);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Breizh Cola',1,NULL,1000,1.49,'Le Breizh Cola est une variante du Coca Cola, produite en bretagne',20,3,2);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Dremmwel Sans Alcool',6,NULL,330,5.25,'Créées il y a plus de 20 ans par la Brasserie de Bretagne, les bières Dremmwel, élaborées avec des ingrédients bio issus de filières locales, sont la pure expression de leur terroir celte. La blonde Dremmwel est une bière de dégustation sans alcool au corps léger et aux arômes maltés et fruités. Elle séduira les amateurs de blondes élaborées.',20,3,4);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Gateaux Breton',1,450.0,NULL,6.5,'Gâteau rond et moelleux avec une croûte dorée. Sa texture est très friable et se rapproche du palet breton. Vous le retrouverez nature, avec une crème de pruneaux,de chocolat, de framboise ou encore de caramel au beurre 2',35,1,2);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Miel de trèfle Breton',1,250.0,NULL,7.99,'Blanc à la récolte puis plus ambré avec le temps, découvrez ce miel au goût subtil, légèrement acidulé et aux notes légères de caramel. Cette  récolte est très rare en France au vu de sa production très aléatoire. ',12,1,3);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Jus de pomme artisanal',1,NULL,1000,2.95,'Notre jus de pomme se caractérise par un léger trouble naturel et un goût très intense de pommes fraîchement récoltées.',40,3,4);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Jus de pomme pétillant',1,NULL,1000,3.95,'Savourez notre pur jus de pomme pétillant BIO alliant la saveur des pommes fraîches et la pétillance d''une boisson festive.',10,3,2);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Bracelet cuir et métal Triskel',1,NULL,NULL,13.95,'Ce joli bracelet en cuir triskell réalisé à la main. Il est composé de cuir et d''un motif en alliage de métal. Proposé pour les hommes, il convient également aux femmes.',35,4,3);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Mug Drapeau breton',1,NULL,NULL,5.95,'Un mug breton avec le Gwen Ha Du pour enflammer votre amour de la Bretagne.',19,4,1);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Bougie artisanale',1,200,NULL,9.95,'Bougie artisanale en cire d''abeille bretonne, 11cm de hauteur pour 5.3cm de diamètre',23,4,2);
insert into _produit(nomProd, quantite, poids, volume, prix_ttc, descriptif, stock, id_categorie, id_vendeur) values ('Savon artisanal',1,100,NULL,5.95,'Savon de haute qualité, fabriqué artisanalement en Bretagne.',38,4,4);
