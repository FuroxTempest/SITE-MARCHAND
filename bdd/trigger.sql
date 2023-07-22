set schema 'alizonbdd';

/*
=======================
TRIGGER D'INSERTION CSV
=======================
*/

-- TRIGGER INSERTION/CREATION CATEGORIE

create view _import_categorie as
select nom from _categorie;

create or replace function fct_ajout_categorie()
returns trigger
as $$
begin
  insert into _categorie(nom, nb_art_cat,tva) values(NEW.nom, 0, 1.20);
  return null;
end;
$$ language plpgsql;

create trigger trg_ajout_categorie
instead of insert
on _import_categorie
for each row
execute procedure fct_ajout_categorie();

-- TRIGGER NBR ART CAT

create or replace function fct_nb_art_cat()
returns trigger
as $$
declare
  new_nb_art_cat integer;
  old_nb_art_cat integer;
begin
  new_nb_art_cat := (select nb_art_cat from _categorie where NEW.id_categorie = id_cat);
  update _categorie set nb_art_cat = (new_nb_art_cat + 1) where NEW.id_categorie = id_cat;
  old_nb_art_cat := (select nb_art_cat from _categorie where OLD.id_categorie = id_cat);
  update _categorie set nb_art_cat = (old_nb_art_cat - 1) where OLD.id_categorie = id_cat;
  return NEW;
end;
$$ language plpgsql;

create trigger trg_nb_art_cat
after insert or update
on _produit
for each row
execute procedure fct_nb_art_cat();

-- TRIGGER INSERTION PRODUIT

create view _import_produit as
select _produit.nomProd, prix_ttc, descriptif, quantite, poids, volume, stock, _categorie.nom as nom_cat, id_vendeur from _produit inner join _categorie on _produit.id_categorie = _categorie.id_cat;

create or replace function fct_ajout_produit()
returns trigger
as $$
declare
  _id_cat integer;
begin
  perform id_cat from _categorie where NEW.nom_cat = _categorie.nom;
  if not found then
    insert into _import_categorie(nom) values(NEW.nom_cat);
  end if;
  _id_cat = (select id_cat from _categorie where NEW.nom_cat = _categorie.nom);
  insert into _produit(nomProd, prix_ttc, descriptif, quantite, poids, volume, stock, id_categorie, id_vendeur) values(NEW.nomProd, NEW.prix_ttc, NEW.descriptif, NEW.quantite, NEW.poids, NEW.volume, NEW.stock, _id_cat, NEW.id_vendeur);
  return NULL;
end;
$$ language plpgsql;

create trigger trg_ajout_produit
instead of insert
on _import_produit
for each row
execute procedure fct_ajout_produit();

-- TRIGGER INSERTION PROMOTION



-- TRIGGER INSERTION COMMENTAIRE



-- TRIGGER MODIFICATION NBPRODUIT CATEGORIE


