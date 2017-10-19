<?php
/**
 * Created by IntelliJ IDEA.
 * User: yablanch
 * Date: 11/10/2016
 * Time: 12:54
 */

namespace Models;
use Silex\Application;


class QueriesModel
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    static public function getStatutLibelle(Application $app)
    {
        $requete = "SELECT DISTINCT LIBELLE_STAT FROM STATUT";
        return $app['db']->fetchAll($requete);
    }
    static public function getVueLSStatut(Application $app,$statut)
    {
        $requete = "SELECT * FROM VUE_LS_SYNTHESE WHERE STATUT = '" . $statut . "'";
        return $app['db']->fetchAll($requete);
    }
    static public function getStatutTableSatut(Application $app,$statut)
    {
        $requete = "SELECT * FROM GLS_R_STATUT_TABLE WHERE SATUT LIKE '%".$statut."%'";
        return $app['db']->fetchAll($requete);
    }
  static public function getLibelleEC(Application $app)
    {
        $requete = "SELECT DISTINCT LIBELLE_EC FROM V_RECHERCHE_COURS";
        return $app['db']->fetchAll($requete);
    }

  static public function getAllFromLibelleEC(Application $app,$coursEscape)
    {
        $requete = "SELECT * FROM V_RECHERCHE_COURS WHERE LIBELLE_EC LIKE '%".$coursEscape."%'";
        return $app['db']->fetchAll($requete);
    }
    static public function getLigneServiceFromLibelleEC(Application $app,$coursEscape)
    {
        $requete = "SELECT * FROM V_LIGNE_SERVICE_EC WHERE LIBELLE_EC LIKE '%" . $coursEscape . "%'";
        return $app['db']->fetchAll($requete);
    }
    static public function getAllVECFromLibelleEC(Application $app,$coursEscape,$annee)
    {
        $requete = "SELECT * FROM V_EC WHERE LIBELLE_EC ='" . $coursEscape . "'AND ANNEE = '$annee'";
        return $app['db']->fetchAll($requete);
    }

  static public function getNomFromPersonnel(Application $app)
    {
        $requete = "SELECT DISTINCT NOM FROM PERSONNEL";
        return $app['db']->fetchAll($requete);
    }
  static public function getAllFromEnseignantCHG(Application $app,$enseignant,$annee)
    {
        $requete = "SELECT * FROM V_R_ENSEIGNANT_CHG_FCT WHERE NOM like '%".$enseignant."%'";
        return $app['db']->fetchAll($requete);
    }
static public function getAllFromEnseignantCours(Application $app,$enseignant,$annee)
    {
        $requete = "SELECT * FROM V_R_ENSEIGNANT_COURS WHERE NOM like '%".$enseignant."%' ";
        return $app['db']->fetchAll($requete);
    }
static public function getAllFromPersonnelStatut(Application $app,$enseignant)
    {
        $requete = "select NOM, PRENOM, LIBELLE_STAT, NB_H_MIN, NB_H_MAX from personnel, statut where NOM like '%".$enseignant."%' AND personnel.fid_stat = statut.id_stat" ;
        return $app['db']->fetchAll($requete);
    }

static public function getLigneServiceCHG(Application $app)
    {
        $requete = "SELECT * FROM V_LIGNE_SERVICE_CHG";
        return $app['db']->fetchAll($requete);
    }
static public function getLigneServiceEC(Application $app)
    {
        $requete = "SELECT * FROM V_LIGNE_SERVICE_EC";
        return $app['db']->fetchAll($requete);
    }
static public function getGroupe(Application $app)
    {
        $requete = "SELECT ID_GROUPE, LIB_GROUPE FROM GROUPE ORDER BY LIB_GROUPE";
        return $app['db']->fetchAll($requete);
    }
static public function getPersonnel(Application $app)
    {
        $requete = "SELECT ID_PERS, NOM, PRENOM FROM PERSONNEL ORDER BY NOM";
        return $app['db']->fetchAll($requete);
    }
static public function getCharge(Application $app)
    {
        $requete = "SELECT ID_CHG, LIB_CHG FROM CHG_FCT ORDER BY LIB_CHG";
        return $app['db']->fetchAll($requete);
    }
static public function getMatiere(Application $app)
    {
        $requete = "SELECT ID_EC, LIBELLE_EC FROM EC ORDER BY LIBELLE_EC";
        return $app['db']->fetchAll($requete);
    }
static public function getLastIDLigneService(Application $app)
    {
        $requete = "SELECT MAX(ID_LIGNE) AS MAX FROM LIGNE_SERVICE ORDER BY ID_LIGNE";
        return $app['db']->fetchAll($requete);
    }
    static public function getLigneServiceByIDLigne(Application $app,$idligne)
    {
        $requete = "select * from LIGNE_SERVICE WHERE ID_LIGNE='".$idligne."'";
        return $app['db']->fetchAll($requete);
    }
    static public function countServiceLigne(Application $app,$fid_grp,$fid_ec,$sem,$annee)
    {
        $requete = "Select COUNT(*) as NB
				FROM ligne_service
				WHERE fid_pers = 404
				AND fid_groupe = '".$fid_grp."'
				AND fid_ec = '".$fid_ec."'
				AND sem='".$sem."'
				AND annee='".$annee."'";
        return $app['db']->fetchAll($requete);
    }
    static public function selectServiceLigne(Application $app,$fid_grp,$fid_ec,$sem,$annee)
    {
        $requete = "Select * as NB
				FROM ligne_service
				WHERE fid_pers = 404
				AND fid_groupe = '".$fid_grp."'
				AND fid_ec = '".$fid_ec."'
				AND sem='".$sem."'
				AND annee='".$annee."'";
        return $app['db']->fetchAll($requete);
    }

    static public function getLibelleECFromSearch(Application $app,$val)
    {
        $requete = "SELECT DISTINCT LIBELLE_EC FROM V_RECHERCHE_COURS WHERE LIBELLE_EC LIKE '%$val%'";
        return $app['db']->fetchAll($requete);
    }

    static public function getNomPersonnelFromSearch(Application $app,$val)
    {
        $requete = "SELECT DISTINCT NOM FROM PERSONNEL WHERE NOM LIKE '%$val%'";
        return $app['db']->fetchAll($requete);
    }

     static public function getStatutFromSearch(Application $app,$val)
    {
        $requete = "SELECT DISTINCT LIBELLE_STAT FROM STATUT WHERE LIBELLE_STAT LIKE '%$val%'";
        return $app['db']->fetchAll($requete);
    }


    static public function updateUserPass(Application $app,$mail,$pass)
    {

        $requete = "UPDATE PERSONNEL SET PASSWORD = '$pass' WHERE EMAIL = '".$mail."'   ";
        return $app['db']->executeUpdate($requete);

    }

}